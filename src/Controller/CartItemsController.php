<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * CartItems Controller
 *
 * @property \App\Model\Table\CartItemsTable $CartItems
 */
class CartItemsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        // Allow unauthenticated access to add action
        $this->Authentication->addUnauthenticatedActions(['add', 'index', 'view', 'edit', 'delete']);
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // $this->Authentication->allowUnauthenticated(['add']); // Allow guest access
        $userId = $this->request->getAttribute('identity')?->getIdentifier();
        if(!$userId) {
            $this->Flash->error(__('Almost there! Log in or create an account to access your cart.'), ['key' => 'authFeedback']);
            return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
        }
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $userId = $this->request->getAttribute('identity')?->getIdentifier();

        if (!$userId) {
            $this->Flash->error(__('Almost there! Log in or create an account to access your cart.'), ['key' => 'authFeedback']);
            return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
        }

        $query = $this->CartItems->find()
            ->contain(['Products'])
            ->where(['CartItems.user_id' => $userId]);

        $cartItems = $this->paginate($query);

        $this->set(compact('cartItems'));
    }

    /**
     * View method
     *
     * @param string|null $id Cart Item id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cartItem = $this->CartItems->get($id, contain: ['Products', 'Users']);
        $this->set(compact('cartItem'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cartItem = $this->CartItems->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Check if user is logged in
            $userId = $this->request->getAttribute('identity')?->getIdentifier();

            if (!empty($userId)) {
                // Logged in: Save to DB
                $data['user_id'] = $userId;

                // Get the product to check stock
                $product = $this->CartItems->Products->get($data['product_id']);

                // Check if product already exists in DB cart
                $existingItem = $this->CartItems->find()
                    ->where([
                        'product_id' => $data['product_id'],
                        'user_id' => $userId
                    ])
                    ->first();

                // Calculate total quantity (existing + new)
                $totalQuantity = $data['product_quantity'];
                if ($existingItem) {
                    $totalQuantity += $existingItem->product_quantity;
                }

                // Check if total quantity exceeds stock
                if ($totalQuantity > $product->stock_quantity) {
                    $this->Flash->error(__('Cannot add more items. Total quantity would exceed available stock.'), ['key' => 'add2CartFeedback']);

                    if ($this->request->is('ajax')) {
                        $this->response = $this->response->withType('application/json')
                            ->withStringBody(json_encode([
                                'success' => false,
                                'message' => 'Cannot add more items. Total quantity would exceed available stock.'
                            ]));
                        return $this->response;
                    }

                    return $this->redirect($this->referer());
                }

                if ($existingItem) {
                    // Update existing cart item
                    $existingItem->product_quantity = $totalQuantity;
                    $cartItem = $existingItem;
                } else {
                    // Create new cart item
                    $cartItem = $this->CartItems->patchEntity($cartItem, $data);
                }

                // Save the cart item to DB
                if ($this->CartItems->save($cartItem)) {
                    // Get updated cart count
                    $cartCount = $this->CartItems->find()
                        ->select(['total_quantity' => 'SUM(product_quantity)'])
                        ->where(['user_id' => $userId])
                        ->first()
                        ->total_quantity ?? 0;

                    // Set flash message for both AJAX and non-AJAX requests
                    $this->Flash->success(__('Item added to cart.'), ['key' => 'add2CartFeedback']);

                    // Check if this is an AJAX request
                    if ($this->request->is('ajax')) {
                        $this->response = $this->response->withType('application/json')
                            ->withStringBody(json_encode([
                                'success' => true,
                                'message' => 'Item added to cart successfully!',
                                'cartCount' => (int)$cartCount
                            ]));
                        return $this->response;
                    }

                    return $this->redirect($this->referer());
                }

                // Log error for debugging
                $errors = $cartItem->getErrors();
                \Cake\Log\Log::error('Failed to save cart item: ' . json_encode($errors));

                $this->Flash->error(__('The cart item could not be saved. Please, try again.'), ['key' => 'add2CartFeedback']);

                // Check if this is an AJAX request
                if ($this->request->is('ajax')) {
                    $this->response = $this->response->withType('application/json')
                        ->withStringBody(json_encode([
                            'success' => false,
                            'message' => 'The cart item could not be saved. Please, try again.',
                            'errors' => $errors
                        ]));
                    return $this->response;
                }
            } else {
                // Guest user: Redirect to login
                $this->Flash->error(__('Ready to shop? Just sign in to add this to your cart.'), ['key' => 'authFeedback']);

                // Check if this is an AJAX request
                if ($this->request->is('ajax')) {
                    $this->response = $this->response->withStatus(401)
                        ->withType('application/json')
                        ->withStringBody(json_encode([
                            'success' => false,
                            'message' => 'You need to be logged in to add items to your cart.',
                            'redirect' => '/auth/login'
                        ]));
                    return $this->response;
                }

                return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
            }
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Cart Item id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cartItem = $this->CartItems->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cartItem = $this->CartItems->patchEntity($cartItem, $this->request->getData());
            if ($this->CartItems->save($cartItem)) {
                $this->Flash->success(__('The cart item has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cart item could not be saved. Please, try again.'));
        }
        $products = $this->CartItems->Products->find('list', limit: 200)->all();
        $users = $this->CartItems->Users->find('list', limit: 200)->all();
        $this->set(compact('cartItem', 'products', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Cart Item id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cartItem = $this->CartItems->get($id);
        $userId = $this->request->getAttribute('identity')?->getIdentifier();

        if ($this->CartItems->delete($cartItem)) {
            // Get updated cart count
            $cartCount = $this->CartItems->find()
                ->select(['total_quantity' => 'SUM(product_quantity)'])
                ->where(['user_id' => $userId])
                ->first()
                ->total_quantity ?? 0;

            $this->Flash->success(__('The cart item has been deleted.'), ['key' => 'shoppingCartFeedback']);

            if ($this->request->is('ajax')) {
                $this->response = $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'message' => 'The cart item has been deleted.',
                        'cartCount' => (int)$cartCount
                    ]));
                return $this->response;
            }

            return $this->redirect($this->referer());
        } else {
            $this->Flash->error(__('The cart item could not be deleted. Please, try again.'), ['key' => 'shoppingCartFeedback']);

            if ($this->request->is('ajax')) {
                $this->response = $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'The cart item could not be deleted. Please, try again.'
                    ]));
                return $this->response;
            }

            return $this->redirect($this->referer());
        }
    }

    /**
     * Decrease Quantity method
     *
     * @param string|null $id Cart Item id.
     * @return \Cake\Http\Response|null Redirects to referer.
     */
    public function decreaseQuantity($id = null)
    {
        $cartItem = $this->CartItems->get($id);
        $userId = $this->request->getAttribute('identity')?->getIdentifier();

        if ($cartItem->product_quantity > 1) {
            $cartItem->product_quantity--;
        } else {
            // Optionally delete the item if quantity becomes 0
            $this->CartItems->delete($cartItem);
        }

        if ($this->CartItems->save($cartItem) || $cartItem->isDeleted()) {
            // Get updated cart count
            $cartCount = $this->CartItems->find()
                ->select(['total_quantity' => 'SUM(product_quantity)'])
                ->where(['user_id' => $userId])
                ->first()
                ->total_quantity ?? 0;

            $this->Flash->success(__('Quantity updated.'), ['key' => 'cartQuantityFeedback']);

            if ($this->request->is('ajax')) {
                $this->response = $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'message' => 'Quantity updated.',
                        'cartCount' => (int)$cartCount
                    ]));
                return $this->response;
            }

            return $this->redirect($this->referer());
        } else {
            $this->Flash->error(__('Could not update quantity.'), ['key' => 'cartQuantityFeedback']);

            if ($this->request->is('ajax')) {
                $this->response = $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Could not update quantity.'
                    ]));
                return $this->response;
            }

            return $this->redirect($this->referer());
        }
    }

    /**
     * Increase Quantity method
     *
     * @param string|null $id Cart Item id.
     * @return \Cake\Http\Response|null Redirects to referer.
     */
    public function increaseQuantity($id = null)
    {
        $cartItem = $this->CartItems->get($id, ['contain' => ['Products']]);
        $userId = $this->request->getAttribute('identity')?->getIdentifier();

        if ($cartItem->product_quantity < $cartItem->product->stock_quantity) {
            $cartItem->product_quantity++;

            if ($this->CartItems->save($cartItem)) {
                // Get updated cart count
                $cartCount = $this->CartItems->find()
                    ->select(['total_quantity' => 'SUM(product_quantity)'])
                    ->where(['user_id' => $userId])
                    ->first()
                    ->total_quantity ?? 0;

                $this->Flash->success(__('Quantity updated.'), ['key' => 'cartQuantityFeedback']);

                if ($this->request->is('ajax')) {
                    $this->response = $this->response->withType('application/json')
                        ->withStringBody(json_encode([
                            'success' => true,
                            'message' => 'Quantity updated.',
                            'cartCount' => (int)$cartCount
                        ]));
                    return $this->response;
                }

                return $this->redirect($this->referer());
            } else {
                $this->Flash->error(__('Could not update quantity.'), ['key' => 'cartQuantityFeedback']);

                if ($this->request->is('ajax')) {
                    $this->response = $this->response->withType('application/json')
                        ->withStringBody(json_encode([
                            'success' => false,
                            'message' => 'Could not update quantity.'
                        ]));
                    return $this->response;
                }

                return $this->redirect($this->referer());
            }
        } else {
            $this->Flash->error(__('Cannot increase quantity. Maximum stock limit reached.'), ['key' => 'cartQuantityFeedback']);

            if ($this->request->is('ajax')) {
                $this->response = $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Cannot increase quantity. Maximum stock limit reached.'
                    ]));
                return $this->response;
            }

            return $this->redirect($this->referer());
        }
    }

    /**
     * Count method
     *
     * @return \Cake\Http\Response Returns JSON with cart count
     */
    public function count()
    {
        $this->request->allowMethod(['get']);

        $userId = $this->request->getAttribute('identity')?->getIdentifier();
        $cartCount = 0;

        if ($userId) {
            $cartCount = $this->CartItems->find()
                ->select(['total_quantity' => 'SUM(product_quantity)'])
                ->where(['user_id' => $userId])
                ->first()
                ->total_quantity ?? 0;
        }

        $this->response = $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'cartCount' => (int)$cartCount
            ]));
        return $this->response;
    }

    /**
     * Clear all items from the cart
     *
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function clearAll()
    {
        $this->request->allowMethod(['post']);
        $userId = $this->request->getAttribute('identity')?->getIdentifier();

        if (!$userId) {
            $this->Flash->error(__('You need to be logged in to clear your cart.'), ['key' => 'shoppingCartFeedback']);
            return $this->redirect(['action' => 'index']);
        }

        $cartItems = $this->CartItems->find()
            ->where(['user_id' => $userId]);

        $success = true;
        foreach ($cartItems as $cartItem) {
            if (!$this->CartItems->delete($cartItem)) {
                $success = false;
                break;
            }
        }

        if ($success) {
            $this->Flash->success(__('All items have been removed from your cart.'), ['key' => 'shoppingCartFeedback']);
        } else {
            $this->Flash->error(__('Some items could not be removed from your cart. Please try again.'), ['key' => 'shoppingCartFeedback']);
        }

        return $this->redirect(['action' => 'index']);
    }

    public function updateQuantity($id = null)
    {
        $this->request->allowMethod(['post']);

        $cartItem = $this->CartItems->get($id, [
            'contain' => ['Products'],
        ]);

        $quantity = (int)$this->request->getData('quantity');

        if ($quantity < 1) {
            $quantity = 1;
            $this->Flash->error(__('Quantity cannot be less than 1.'), ['key' => 'cartQuantityFeedback']);
        } elseif ($quantity > $cartItem->product->stock_quantity) {
            $quantity = $cartItem->product->stock_quantity;
            $this->Flash->error(__('Cannot increase quantity. Maximum stock limit reached.'), ['key' => 'cartQuantityFeedback']);
        }

        $cartItem->product_quantity = $quantity;

        if ($this->CartItems->save($cartItem)) {
            $this->Flash->success(__('Quantity updated successfully.'), ['key' => 'cartQuantityFeedback']);
        } else {
            $this->Flash->error(__('Failed to update quantity. Please try again.'), ['key' => 'cartQuantityFeedback']);
        }

        return $this->redirect(['action' => 'index']);
    }
}
