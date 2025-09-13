<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Mailer\Mailer;

/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 */
class OrdersController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        // Allow unauthenticated access to add action
        $this->Authentication->addUnauthenticatedActions(['view', 'index', 'checkout', 'createCheckoutSession']);
    }

    /**
     * Before filter method
     *
     * @param \Cake\Event\EventInterface $event Event object
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        if ($this->getRequest()->getParam('action') === 'webhook') {
            $this->getEventManager()->off($this->Security);
            $this->getEventManager()->off($this->Csrf);
        }

        $user = $this->Authentication->getIdentity();

        if ($user && $user->user_type === 'Admin') {
            $this->viewBuilder()->setLayout('admin');
        }

        $adminOnlyActions = ['adminIndex', 'adminView', 'userOrders'];

        if (in_array($this->request->getParam('action'), $adminOnlyActions) && $user && $user->user_type !== 'Admin') {

            //if user not logged in as admin, redirect to login page
            $this->Flash->error(__('Please log in to access this page.'), ['key' => 'authFeedback']);
            return $this->redirect(['controller' => 'Auth','action' => 'login']);

        }
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Orders->find()
            ->contain(['Users', 'OrderProducts.Products'])
            ->where(['user_id' => $this->Authentication->getIdentity()->getIdentifier()])
            ->order(['Orders.created' => 'DESC']);
        $orders = $this->paginate($query);

        $this->set(compact('orders'));
    }

    /**
     * View method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $order = $this->Orders->get($id, [
            'contain' => ['Users', 'OrderProducts.Products'],
        ]);

        $this->set(compact('order'));
    }

    public function updateStatus($id = null){

        $this->request->allowMethod(['post', 'put']);
        $order = $this->Orders->get($id);

        $order = $this->Orders->patchEntity($order, $this->request->getData());

        if ($this->Orders->save($order)) {
            $this->Flash->success('Order status updated.',['key' => 'statusUpdateFeedback']);
        } else {
            $this->Flash->error('Unable to update status.', ['key' => 'statusUpdateFeedback']);
        }

        return $this->redirect($this->referer());

    }

    /**
     * Checkout method - Creates an order from the current cart
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function checkout()
    {
        if ($this->request->is('get')) {
            $cartItems = $this->fetchTable('CartItems')->find()
                ->where(['user_id' => $this->Authentication->getIdentity()->getIdentifier()])
                ->contain(['Products'])
                ->all();

            if ($cartItems->isEmpty()) {
                $this->Flash->error(__('Your cart is empty.'), ['key' => 'shoppingCartFeedback']);

                return $this->redirect(['controller' => 'CartItems', 'action' => 'index']);
            }

            $totalAmount = 0;
            $totalCartItems = 0;
            foreach ($cartItems as $item) {
                $totalAmount += $item->product->price * $item->product_quantity;
                $totalCartItems += $item->product_quantity;
            }

            // Get user
            $user = $this->fetchTable('Users')->get($this->Authentication->getIdentity()->getIdentifier());


            // Fetch shipping countries from the database
            $shippingCountries = $this->fetchTable('ShippingCountries')->find('list', [
                'keyField' => 'country_code',
                'valueField' => 'country_name',
            ])->toArray();

            $stripePublishableKey = \Cake\Core\Configure::read('Stripe.publishable_key');

            // Fetch available coupons for the user
            $userId = $this->Authentication->getIdentity()->getIdentifier();
            $availableCoupons = $this->fetchTable('Coupons')->find()
                ->where([
                    'OR' => [
                        ['user_id' => $userId],
                        ['user_id IS' => null]
                    ]
                ])
                ->where([
                    'is_active' => true,
                    'OR' => [
                        'expiry_date IS NULL',
                        'expiry_date >=' => date('Y-m-d')
                    ],
                    'OR' => [
                        'usage_limit IS NULL',
                        'times_used < usage_limit'
                    ]
                ])
                ->all();

            $this->set(compact('cartItems', 'totalAmount', 'user', 'totalCartItems', 'shippingCountries', 'stripePublishableKey', 'availableCoupons'));

        }

        if ($this->request->is('post')) {
            $cartItems = $this->fetchTable('CartItems')->find()
                ->where(['user_id' => $this->Authentication->getIdentity()->getIdentifier()])
                ->contain(['Products'])
                ->all();

            if ($cartItems->isEmpty()) {
                $this->Flash->error(__('Your cart is empty.'));
                return $this->redirect(['controller' => 'CartItems', 'action' => 'index']);
            }

            // Validate stock quantities before processing the order
            foreach ($cartItems as $item) {
                if ($item->product_quantity > $item->product->stock_quantity) {
                    $this->Flash->error(__(
                        'Not enough stock available for {0}. Available: {1}, Requested: {2}',
                        $item->product->name,
                        $item->product->stock_quantity,
                        $item->product_quantity,
                    ));
                    return $this->redirect(['controller' => 'CartItems', 'action' => 'index']);
                }
            }

            $data = $this->request->getData();
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $totalAmount += $item->product->price * $item->product_quantity;
            }

            $shippingCost = (float)$this->request->getData('shipping_cost');
            $discountAmount = 0;
            $couponId = null;

            if ($this->request->getData('coupon_code')) {
                $coupon = $this->fetchTable('Coupons')->find()
                    ->where([
                        'coupon_code' => $this->request->getData('coupon_code'),
                        'is_active' => true,
                        'OR' => [
                            'expiry_date IS NULL',
                            'expiry_date >=' => date('Y-m-d')
                        ],
                        'OR' => [
                            'usage_limit IS NULL',
                            'times_used < usage_limit'
                        ],
                        'OR' => [
                            'min_purchase_amount IS NULL',
                            'min_purchase_amount <=' => $totalAmount
                        ]
                    ])
                    ->first();

                if ($coupon) {
                    $discountAmount = $totalAmount * ($coupon->discount_amount / 100);
                    if ($coupon->max_discount_amount !== null && $discountAmount > $coupon->max_discount_amount) {
                        $discountAmount = $coupon->max_discount_amount;
                    }
                    $couponId = $coupon->coupon_id;
                }
            }

            $totalAmount = $totalAmount - $discountAmount + $shippingCost;

            // Create Stripe session
            \Stripe\Stripe::setApiKey(Configure::read('Stripe.secret_key'));
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'aud',
                        'product_data' => ['name' => 'Order Total'],
                        'unit_amount' => $totalAmount * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => Router::url(['controller' => 'Orders', 'action' => 'success'], true) . '?' . http_build_query([
                    'session_id' => '{CHECKOUT_SESSION_ID}',
                    'shipping_cost' => $shippingCost,
                    'discount_amount' => $discountAmount,
                    'coupon_code' => $this->request->getData('coupon_code'),
                    'total_amount' => $totalAmount,
                    'save_address' => $this->request->getData('save_address'),
                    'shipping_name' => $this->request->getData('shipping_name'),
                    'shipping_address1' => $this->request->getData('shipping_address1'),
                    'shipping_address2' => $this->request->getData('shipping_address2'),
                    'shipping_city' => $this->request->getData('shipping_city'),
                    'shipping_state' => $this->request->getData('shipping_state'),
                    'shipping_postcode' => $this->request->getData('shipping_postcode'),
                    'shipping_country' => $this->request->getData('shipping_country')
                ]),
                'cancel_url' => Router::url(['controller' => 'Orders', 'action' => 'cancel'], true),
            ]);

            return $this->redirect($session->url);
        }
    }

    public function cancel($id = null)
    {
        \Cake\Log\Log::write('debug', 'cancel page and method');
        // $this->Flash->error(__('Didnt work, bub.'));
        //also we need to create a error file to show this success payembt
        return $this->redirect(['controller' => 'CartItems', 'action' => 'index']);
    }

    public function success()
    {
        \Cake\Log\Log::write('debug', 'success page and method');

        $sessionId = $this->request->getQuery('session_id');
        if (!$sessionId) {
            $this->Flash->error(__('Invalid session.'));
            return $this->redirect(['action' => 'index']);
        }

        // Get cart items
        $cartItems = $this->fetchTable('CartItems')->find()
            ->where(['user_id' => $this->Authentication->getIdentity()->getIdentifier()])
            ->contain(['Products'])
            ->all();

        if ($cartItems->isEmpty()) {
            $this->Flash->error(__('Your cart is empty.'));
            return $this->redirect(['controller' => 'CartItems', 'action' => 'index']);
        }

        // Create and save the order
        $order = $this->Orders->newEmptyEntity();
        $order = $this->Orders->patchEntity($order, [
            'user_id' => $this->Authentication->getIdentity()->getIdentifier(),
            'total_amount' => $this->request->getQuery('total_amount'),
            'shipping_cost' => $this->request->getQuery('shipping_cost'),
            'discount_amount' => $this->request->getQuery('discount_amount'),
            'coupon_code' => $this->request->getQuery('coupon_code'),
            'stripe_session_id' => $sessionId,
            'shipping_name' => $this->request->getQuery('shipping_name'),
            'shipping_address1' => $this->request->getQuery('shipping_address1'),
            'shipping_address2' => $this->request->getQuery('shipping_address2'),
            'shipping_city' => $this->request->getQuery('shipping_city'),
            'shipping_state' => $this->request->getQuery('shipping_state'),
            'shipping_postcode' => $this->request->getQuery('shipping_postcode'),
            'shipping_country' => $this->request->getQuery('shipping_country')
        ]);

        if ($this->Orders->save($order)) {
            // Increment coupon usage
            if ($order->coupon_code) {
                $coupon = $this->fetchTable('Coupons')->find()
                    ->where(['coupon_code' => $order->coupon_code])
                    ->first();
                if ($coupon) {
                    $coupon->times_used += 1;
                    $this->fetchTable('Coupons')->save($coupon);
                }
            }

            // Add each cart item to OrderProductsTable
            $orderProductsTable = $this->fetchTable('OrderProducts');
            foreach ($cartItems as $item) {
                $orderProduct = $orderProductsTable->newEmptyEntity();
                $orderProduct->order_id = $order->order_id;
                $orderProduct->product_id = $item->product_id;
                $orderProduct->quantity = $item->product_quantity;
                $orderProduct->price = $item->product->price;
                $orderProductsTable->save($orderProduct);

                // Update product stock and total_sold
                $productsTable = $this->fetchTable('Products');
                $product = $productsTable->get($item->product_id);
                $product->stock_quantity -= $item->product_quantity;
                $product->total_sold = ($product->total_sold ?? 0) + $item->product_quantity;
                $productsTable->save($product);
            }

            // If user wants to save the address from the form to their account
            if ($this->request->getQuery('save_address')) {
                $currentUser = $this->Authentication->getIdentity();
                $usersTable = $this->fetchTable('Users');
                $user = $usersTable->get($currentUser->getIdentifier());

                $user->user_address1 = $this->request->getQuery('shipping_address1');
                $user->user_address2 = $this->request->getQuery('shipping_address2');
                $user->user_city = $this->request->getQuery('shipping_city');
                $user->user_state = $this->request->getQuery('shipping_state');
                $user->user_postcode = $this->request->getQuery('shipping_postcode');
                $user->user_country = $this->request->getQuery('shipping_country');

                $usersTable->save($user);
            }

            // Clear the user's cart after order is processed
            $cartItemsTable = $this->fetchTable('CartItems');
            foreach ($cartItems as $item) {
                $cartItemsTable->delete($item);
            }

            // Fetch order with related user and products for the email
            $order = $this->Orders->get($order->order_id, [
                'contain' => ['Users', 'OrderProducts.Products']
            ]);

            // Build product list HTML for the email
            $productListHtml = '';
            foreach ($order->order_products as $orderProduct) {
                $productName = $orderProduct->product->name ?? 'No product name';
                $productListHtml .= "<li>{$productName} (Quantity: {$orderProduct->quantity}, Price: $" . $orderProduct->price . ")</li>";
            }

            // Send confirmation email with product details
            $email = new Mailer('default');
            try {
                $email->setTo($order->user->email)
                    ->setSubject('Order Confirmation - Order #' . $order->order_id)
                    ->setEmailFormat('html')
                    ->deliver("
                        <p>Dear {$order->user->first_name},</p>
                        <p>Thank you for your order. Your order number is {$order->order_id}.</p>
                        <p>Order items:</p>
                        <ul>
                            {$productListHtml}
                        </ul>
                        <p>Total amount (including shipping cost): $" . $order->total_amount . "</p>
                        <p>We will process your order soon.</p>
                        <p>Thank you for your order.</p>
                    ");
                \Cake\Log\Log::write('debug', 'Email sent successfully');
            } catch (\Exception $e) {
                \Cake\Log\Log::write('error', 'Failed to send email: ' . $e->getMessage());
            }

            $this->set(compact('order'));
        } else {
            $this->Flash->error(__('The order could not be processed. Please contact support.'));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function adminIndex()
    {
        $this->viewBuilder()->setLayout('admin');

        $query = $this->Orders->find()
            ->contain(['Users', 'OrderProducts.Products'])
            ->order(['Orders.created' => 'DESC']);
        $orders = $this->paginate($query);

        $this->set(compact('orders'));
    }

    public function adminView($id = null)
    {
        $this->viewBuilder()->setLayout('admin');

        $order = $this->Orders->get($id, [
            'contain' => ['Users', 'OrderProducts.Products'],
        ]);

        $this->set(compact('order'));
    }

    /**
     * UserOrders method - Displays orders for a specific user
     * Only accessible by Admin users
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When user is not an admin
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function userOrders(?string $id = null)
    {
        // Check if the current user is an admin
        $this->viewBuilder()->setLayout('admin');

        // Find the user
        $user = $this->fetchTable('Users')->get($id, [
            'contain' => [],
        ]);

        // Find all orders for this user
        $orders = $this->Orders->find()
            ->where(['Orders.user_id' => $id])
            ->contain(['Users', 'OrderProducts.Products'])
            ->order(['Orders.created' => 'DESC'])
            ->all();

        $this->set(compact('orders', 'user'));
    }

    public function getDomesticPostcodeDetails()
    {
        $this->autoRender = false;
        $apiKey = Configure::read('AusPost.apiKey');;

        //get postcode from frontend
        $this->request->allowMethod(['get']);
        $this->response = $this->response->withType('application/json');

        // Define the service input parameters
        $fromPostcode = '3800'; // Set to Clayton for now
        $toPostcode = $this->request->getQuery('to_postcode');
        $parcelLengthInCMs = 33;
        $parcelWidthInCMs = 9;
        $parcelHeighthInCMs = 9;
        $parcelWeightInKGs = 1.3;

        // Set the query params
        $queryParams = [
            'from_postcode' => $fromPostcode,
            'to_postcode' => $toPostcode,
            'length' => $parcelLengthInCMs,
            'width' => $parcelWidthInCMs,
            'height' => $parcelHeighthInCMs,
            'weight' => $parcelWeightInKGs,
        ];

        // Set the URL for the Domestic Parcel Size service
        $urlPrefix = 'digitalapi.auspost.com.au';
        $postageTypesURL = 'https://' . $urlPrefix . '/postage/parcel/domestic/service.json?' .
            http_build_query($queryParams);

        // Lookup available domestic parcel delivery service types
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postageTypesURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['AUTH-KEY: ' . $apiKey]);
        $rawBody = curl_exec($ch);

        // Check the response; if the body is empty then an error has occurred
        if (!$rawBody) {
            die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
        }

        curl_close($ch);

        $services = json_decode($rawBody, true);

        // Get the first service code
        $serviceCode = $services['services']['service'][0]['code'] ?? 'AUS_PARCEL_REGULAR';

        // Call the next function with real data
        $price = $this->getDomesticShippingCost($fromPostcode, $toPostcode, $parcelLengthInCMs, $parcelWidthInCMs, $parcelHeighthInCMs, $parcelWeightInKGs, $serviceCode);

        // Return JSON response
        return $this->response->withStringBody(json_encode(['price' => $price]));
    }

    public function getDomesticShippingCost($fromPostcode, $toPostcode, $length, $width, $height, $weight, $serviceCode)
    {
        $apiKey = Configure::read('AusPost.apiKey');;

        // Define the service input parameters
        $fromPostcode = $fromPostcode; // Set to Clayton Postcode for now
        $toPostcode = $toPostcode;
        $parcelLengthInCMs = $length;
        $parcelWidthInCMs = $width;
        $parcelHeighthInCMs = $height;
        $parcelWeightInKGs = $weight;

        // Set the query params
        $queryParams = [
            'from_postcode' => $fromPostcode,
            'to_postcode' => $toPostcode,
            'length' => $parcelLengthInCMs,
            'width' => $parcelWidthInCMs,
            'height' => $parcelHeighthInCMs,
            'weight' => $parcelWeightInKGs,
            'service_code' => $serviceCode,
        ];

        // Set the URL for the Domestic Parcel Calculation service
        $urlPrefix = 'digitalapi.auspost.com.au';
        $calculateRateURL = 'https://' . $urlPrefix . '/postage/parcel/domestic/calculate.json?' .
            http_build_query($queryParams);

        // Calculate the final domestic parcel delivery price
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $calculateRateURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['AUTH-KEY: ' . $apiKey]);
        $rawBody = curl_exec($ch);

        // Check the response; if the body is empty then an error has occurred
        if (!$rawBody) {
            die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
        }
        curl_close($ch);

        $data = json_decode($rawBody, true);

        return $data['postage_result']['total_cost'] ?? null;
    }

    public function getInternationalShippingRate()
    {
        $this->request->allowMethod(['get']);
        $this->response = $this->response->withType('application/json');

        $countryCode = $this->request->getQuery('shipping_country');

        if (!$countryCode) {
            return $this->response->withStringBody(json_encode([
                'success' => false,
                'message' => 'Country code is required.',
            ]));
        }

        $apiKey = Configure::read('AusPost.apiKey');;
        $parcelWeightInKGs = 1.3;

        // Step 1: Get available international services
        $servicesUrl = 'https://digitalapi.auspost.com.au/postage/parcel/international/service.json?' . http_build_query([
                'country_code' => $countryCode,
                'weight' => $parcelWeightInKGs,
            ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $servicesUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['AUTH-KEY: ' . $apiKey]);
        $rawBody = curl_exec($ch);

        // Check the response; if the body is empty then an error has occurred
        if (!$rawBody) {
            die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
        }

        // All good, lets parse the response into a JSON object
        $services = json_decode($rawBody, true);
        $serviceCode = $services['services']['service'][0]['code'] ?? 'INT_PARCEL_STD_OWN_PACKAGING';

        if (!$serviceCode) {
            return $this->response->withStringBody(json_encode([
                'success' => false,
                'message' => 'No available international shipping service.',
            ]));
        }

        // Step 2: Get shipping cost
        $calculateRateURL = 'https://digitalapi.auspost.com.au/postage/parcel/international/calculate.json?' . http_build_query([
                'country_code' => $countryCode,
                'weight' => $parcelWeightInKGs,
                'service_code' => $serviceCode,
            ]);

        // Calculate the final international parcel delivery price
        curl_setopt($ch, CURLOPT_URL, $calculateRateURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['AUTH-KEY: ' . $apiKey]);
        $costResponse = curl_exec($ch);

        // Check the response; if the body is empty then an error has occurred
        if (!$costResponse) {
            die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
        }
        curl_close($ch);

        $costData = json_decode($costResponse, true);
        $shippingCost = floatval($costData['postage_result']['total_cost'] ?? 0);

        return $this->response->withStringBody(json_encode([
            'price' => $shippingCost,
        ]));
    }

    public function webhook()
    {
        $this->autoRender = false;
        $this->request->allowMethod(['post']);

        $payload = $this->request->getBody()->getContents();
        $sig_header = $this->request->getHeaderLine('Stripe-Signature');
        $endpoint_secret = Configure::read('Stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            http_response_code(400);
            exit();
        }

        // Handle the event
        switch ($event['type']) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                // Get the order from the database
                $order = $this->Orders->find()
                    ->where(['stripe_session_id' => $session->id])
                    ->contain(['Users', 'OrderProducts.Products'])
                    ->first();

                // Update order status
                $order->status = 'paid';
                $this->Orders->save($order);

                // Increment coupon usage only after successful payment
                if ($order->coupon_code) {
                    $coupon = $this->fetchTable('Coupons')->find()
                        ->where(['coupon_code' => $order->coupon_code])
                        ->first();

                    if ($coupon) {
                        $coupon->times_used += 1;
                        $this->fetchTable('Coupons')->save($coupon);
                    }
                }

                // Send confirmation email
                $this->sendOrderConfirmationEmail($order);

                break;
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        http_response_code(200);
    }

    private function sendOrderConfirmationEmail($order)
    {
        $email = new \Cake\Mailer\Email('default');
        $email->setTo($order->user->email)
            ->setSubject('Order Confirmation - Order #' . $order->order_id)
            ->setEmailFormat('html')
            ->setViewVars([
                'order' => $order,
                'user' => $order->user
            ])
            ->setTemplate('order_confirmation')
            ->send();
    }

    public function validateCoupon()
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false;

        $couponCode = $this->request->getData('coupon_code');
        $totalAmount = (float)$this->request->getData('total_amount');

        $coupon = $this->fetchTable('Coupons')->find()
            ->where([
                'coupon_code' => $couponCode,
                'is_active' => true,
                'OR' => [
                    'expiry_date IS NULL',
                    'expiry_date >=' => date('Y-m-d')
                ],
                'OR' => [
                    'usage_limit IS NULL',
                    'times_used < usage_limit'
                ],
                'OR' => [
                    'min_purchase_amount IS NULL',
                    'min_purchase_amount <=' => $totalAmount
                ]
            ])
            ->first();

        if ($coupon) {
            // Calculate discount amount
            $discountAmount = $totalAmount * ($coupon->discount_amount / 100);

            // Apply max discount limit if set
            if ($coupon->max_discount_amount !== null && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }

            return $this->response->withStringBody(json_encode([
                'valid' => true,
                'coupon' => [
                    'code' => $coupon->coupon_code,
                    'discount_amount' => $coupon->discount_amount,
                    'discount_value' => $discountAmount
                ]
            ]));
        }

        return $this->response->withStringBody(json_encode([
            'valid' => false,
            'message' => 'Invalid or expired coupon code'
        ]));
    }

    public function createCheckoutSession()
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false;

        $cartItems = $this->fetchTable('CartItems')->find()
            ->where(['user_id' => $this->Authentication->getIdentity()->getIdentifier()])
            ->contain(['Products'])
            ->all();

        if ($cartItems->isEmpty()) {
            return $this->response->withStringBody(json_encode([
                'error' => 'Your cart is empty'
            ]));
        }

        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item->product->price * $item->product_quantity;
        }

        // Apply coupon discount if provided
        $couponCode = $this->request->getData('coupon_code');
        $discountAmount = 0;
        if ($couponCode) {
            $coupon = $this->fetchTable('Coupons')->find()
                ->where([
                    'code' => $couponCode,
                    'is_active' => true,
                    'OR' => [
                        'expiry_date IS NULL',
                        'expiry_date >=' => date('Y-m-d')
                    ]
                ])
                ->first();

            if ($coupon) {
                $discountAmount = $totalAmount * ($coupon->discount_percentage / 100);
                $totalAmount -= $discountAmount;
            }
        }

        // Add shipping cost
        $shippingCost = (float)$this->request->getData('shipping_cost');
        $totalAmount += $shippingCost;

        \Stripe\Stripe::setApiKey(Configure::read('Stripe.secret_key'));
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'aud',
                    'product_data' => ['name' => 'Order Total'],
                    'unit_amount' => $totalAmount * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => Router::url(['controller' => 'Orders', 'action' => 'success'], true),
            'cancel_url' => Router::url(['controller' => 'Orders', 'action' => 'cancel'], true),
        ]);

        return $this->response->withStringBody(json_encode([
            'sessionId' => $session->id
        ]));
    }
}
