<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Log\Log;
use Exception;

/**
 * Products Controller
 *
 * @property \App\Model\Table\ProductsTable $Products
 */
class ProductsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');
        $this->Authentication->addUnauthenticatedActions(['index', 'view', 'quickView', 'flashMessage']);
        // Allow unauthenticated access to customer-facing actions
        $this->Authentication->addUnauthenticatedActions([
            'page',
            'autocomplete',
            'search',
            'loadMore',
            'applyFilter',
            'removeFilter',
            'clearAllFilters',
            'cartForm',
        ]);
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        // Customer-facing actions use default layout and are accessible to all
        $customerActions = [
            'page',
            'view',
            'autocomplete',
            'search',
            'loadMore',
            'applyFilter',
            'removeFilter',
            'clearAllFilters',
            'cartForm',
        ];

        $action = strtolower($this->request->getParam('action'));
        if (in_array($action, array_map('strtolower', $customerActions))) {
            $this->viewBuilder()->setLayout('default');
            Log::debug("Allowing action: $action");

            return;
        }

        // Admin-only actions
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->user_type !== 'Admin') {

            //if user not logged in as admin, redirect to login page
            $this->Flash->error(__('Please log in to access this page.'), ['key' => 'authFeedback']);
            return $this->redirect(['controller' => 'Auth','action' => 'login']);


        }

        $this->viewBuilder()->setLayout('admin');
    }

    /**
     * Index method
     * @return void
     */
    public function index()
    {
        $query = $this->Products->find('all', [
            'contain' => ['WineStyles']
        ]);
        $products = $this->paginate($query);


        $this->set('stockFilter', $this->request->getQuery('stock_filter'));
        $this->set('statusFilter', $this->request->getQuery('status'));
        $this->set('styleFilter', $this->request->getQuery('wine_style_id'));

        $topProducts = $this->Products->find()
            ->orderBy(['total_sold' => 'DESC'])
            ->limit(2)
            ->all();
        if ($topProducts->isEmpty()) {
            $this->Flash->error(__('There are currently no products in the database'), ['key' => 'topProductFeedback']);
        }
        $wineStyles = $this->Products->WineStyles->find('list',
            keyField: 'id',
            valueField: 'name'
        )->toArray();
        $this->set(compact('products', 'topProducts', 'wineStyles'));
    }

    /**
     * View method to view a product
     * @param $id
     * @return void
     */
    public function view($id = null)
    {
        $product = $this->Products->get($id, [
            'contain' => ['Reviews' => ['Users'],'WineStyles']
        ]);
        $wineStyles = $this->Products->WineStyles->find('list',
            keyField: 'id',
            valueField: 'name'
        )->toArray();
        $this->set(compact('product','wineStyles'));
    }

    /**
     * View method for admin side
     * @param $id
     * @return void
     */
    public function adminView($id = null)
    {
        $this->viewBuilder()->setLayout('admin');
        $product = $this->Products->get($id, [
            'contain' => ['WineStyles']
        ]);
        $this->set(compact('product'));
    }

    /**
     * Add method to add new products
     * @return \Cake\Http\Response|void|null
     */
    public function add()
    {

        $product = $this->Products->newEmptyEntity();

        $wineStyles = $this->Products->WineStyles->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();


        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Remove image from data to prevent patchEntity() from processing it
            $image = $data['image'] ?? null;
            unset($data['image']);

            $product = $this->Products->patchEntity($product, $data);

            if ($product->getErrors()) {
                $this->Flash->error(__('Please fix the errors below.'), ['key' => 'productAddFeedback']);
            } elseif ($data['stock_quantity'] < 0) {
                $this->Flash->error(__('Stock quantity cannot be negative. Please, try again.'), ['key' => 'productAddFeedback']);
            } else {

                $product->stock_quantity = $data['stock_quantity'];

                // Handle file upload
                if ($image && $image->getClientFilename() !== '') {
                    if ($image->getError() === UPLOAD_ERR_OK) {
                        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '', $image->getClientFilename());
                        $uploadDir = WWW_ROOT . 'img' . DS . 'products' . DS;

                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0775, true);
                        }

                        $targetPath = $uploadDir . $filename;
                        $image->moveTo($targetPath);

                        $product->image = 'products/' . $filename;
                    } else {
                        $this->Flash->error(__('Image could not be uploaded. Please, try again.'), ['key' => 'productAddFeedback']);

                        return $this->redirect($this->referer());
                    }
                }

                if ($this->Products->save($product)) {
                    $this->Flash->success(__('{0} has been saved.', $product->name), ['key' => 'viewProductsFeedback']);

                    return $this->redirect(['action' => 'adminView', $product->id]);
                }
                $this->Flash->error(__('This product could not be saved. Please, try again.', $product->name), ['key' => 'productAddFeedback']);

                return $this->redirect($this->referer());
            }
        }

        $this->set(compact('product', 'wineStyles'));
    }

    /**
     * Edit method to edit details of a product
     * @param $id
     * @return \Cake\Http\Response|void|null
     */
    public function edit($id = null)
    {
        $product = $this->Products->get($id, [
            'contain' => ['WineStyles']
        ]);

        $wineStyles = $this->Products->WineStyles->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();

        $existingImage = $product->image;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Remove image from data to prevent patchEntity() from processing it
            $image = $data['image'] ?? null;
            unset($data['image']);

            $product = $this->Products->patchEntity($product, $data);

            // Handle file upload
            if ($image && $image->getClientFilename() !== '') {
                if ($image->getError() === UPLOAD_ERR_OK) {

                    //prepare the file
                    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '', $image->getClientFilename());
                    $uploadDir = WWW_ROOT . 'img' . DS . 'products' . DS;

                    // if the img/products directory does not exist, make a new products directory
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0775, true);
                    }

                    $targetPath = $uploadDir . $filename;
                    $image->moveTo($targetPath);

                    $product->image = 'products/' . $filename;
                } else {
                    $this->Flash->error(__('Image could not be uploaded. Please, try again.'), ['key' => 'productAddFeedback']);

                    return $this->redirect($this->referer());
                }
            } else {
                $product->image = $existingImage;
            }

            if ($this->Products->save($product)) {
                $this->Flash->success(__('{0} has been modified.', $product->name), ['key' => 'viewProductsFeedback']);

                return $this->redirect(['action' => 'adminView', $product->id]);
            }
            $this->Flash->error(__('{0} could not be modified. Please, try again.', $product->name), ['key' => 'productEditFeedback']);
        }
        $this->set(compact('product', 'wineStyles'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $product = $this->Products->get($id);

        if ($this->Products->delete($product)) {
            $this->Flash->success(__('{0} has been deleted.', $product->name), ['key' => 'productFeedback']);
        } else {
            $this->Flash->error(__('{0} could not be deleted. Please, try again.', $product->name), ['key' => 'productFeedback']);
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Cancel method that cancels an action
     * @param $id
     * @return \Cake\Http\Response|null
     */
    public function cancel($id = null)
    {
        $product = $this->Products->get($id);
        $this->Flash->warning(__('Editing of {0} has been cancelled.', $product->name), ['key' => 'productFeedback']);

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Changes the product status from listed to unlisted and vice versa
     * @param $id
     * @return \Cake\Http\Response|null
     */
    public function changeStatus($id = null)
    {
        $product = $this->Products->get($id);
        $product->status = $product->status === 'Listed' ? 'Unlisted' : 'Listed';

        if ($this->Products->save($product)) {
            $this->Flash->success(__('The status of {0} has been changed.', $product->name), ['key' => 'viewProductsFeedback']);

            return $this->redirect($this->referer());
        }

        $this->Flash->error(__('The status of {0} could not be changed. Please, try again.', $product->name), ['key' => 'viewProductsFeedback']);

        return $this->redirect($this->referer());
    }

    private function applySorting($query)
    {
        $sort = $this->request->getQuery('sort');
        if ($sort === 'price_low_high') {
            $query->order(['price' => 'ASC']);
        } elseif ($sort === 'price_high_low') {
            $query->order(['price' => 'DESC']);
        } elseif ($sort === 'created_at_asc') {
            $query->order(['created_at' => 'ASC']);
        } elseif ($sort === 'created_at_desc') {
            $query->order(['created_at' => 'DESC']);
        } elseif ($sort === 'name_asc') {
            $query->order(['name' => 'ASC']);
        } elseif ($sort === 'name_desc') {
            $query->order(['name' => 'DESC']);
        }

        return $query;
    }

    private function applyFilters($query)
    {
        // Handle multiple filter values
        if ($this->request->getQuery('wine_style_id')) {
            $styles = is_array($this->request->getQuery('wine_style_id')) ? $this->request->getQuery('wine_style_id') : [$this->request->getQuery('wine_style_id')];
            $query->where(['wine_style_id IN' => $styles]);
        }
        if ($this->request->getQuery('price')) {
            $priceRanges = is_array($this->request->getQuery('price')) ? $this->request->getQuery('price') : [$this->request->getQuery('price')];
            $priceConditions = [];
            foreach ($priceRanges as $priceRange) {
                if ($priceRange === '0_50') {
                    $priceConditions[] = ['price >=' => 0, 'price <=' => 50];
                } elseif ($priceRange === '50_100') {
                    $priceConditions[] = ['price >' => 50, 'price <=' => 100];
                } elseif ($priceRange === '100_plus') {
                    $priceConditions[] = ['price >' => 100];
                }
            }
            if ($priceConditions) {
                $query->where(['OR' => $priceConditions]);
            }
        }
        if ($this->request->getQuery('region')) {
            $regions = is_array($this->request->getQuery('region')) ? $this->request->getQuery('region') : [$this->request->getQuery('region')];
            $query->where(['region IN' => $regions]);
        }
        if ($this->request->getQuery('vintage')) {
            $vintages = is_array($this->request->getQuery('vintage')) ? $this->request->getQuery('vintage') : [$this->request->getQuery('vintage')];
            $query->where(['vintage IN' => $vintages]);
        }
        if ($this->request->getQuery('volume')) {
            $volumes = is_array($this->request->getQuery('volume')) ? $this->request->getQuery('volume') : [$this->request->getQuery('volume')];
            $query->where(['volume IN' => $volumes]);
        }
        if ($this->request->getQuery('brand')) {
            $brands = is_array($this->request->getQuery('brand')) ? $this->request->getQuery('brand') : [$this->request->getQuery('brand')];
            $query->where(['brand IN' => $brands]);
        }
        return $query;
    }

    public function page()
    {
        $cartItems = $this->request->getSession()->read('Cart.items');
        $query = $this->Products->find()
            ->select(['id', 'name', 'price', 'image', 'wine_style_id', 'region', 'vintage', 'alcohol_content', 'stock_quantity', 'status', 'created_at', 'volume', 'brand', 'product_description'])
            ->where(['status' => 'Listed'])
            ->contain(['Reviews','WineStyles']);

        // Retrieve the names of wine styles
        $wineStyles = $this->Products->WineStyles->find('list',
            keyField: 'id',
            valueField: 'name'
        )->toArray();

        // Apply filters and sorting
        $query = $this->applyFilters($query);
        $query = $this->applySorting($query);

        // Set up pagination
        $page = $this->request->getQuery('page', 1);
        $limit = 12; // Number of products per page
        $totalCount = $query->count();
        $totalPages = ceil($totalCount / $limit);

        // Apply pagination
        $query->limit($limit)
              ->offset(($page - 1) * $limit);

        // Fetch all possible filter options
        $allStyles = $this->Products->find()
            ->select(['wine_style_id'])
            ->distinct(['wine_style_id'])
            ->where(['wine_style_id IS NOT NULL', 'status' => 'Listed'])
            ->order(['wine_style_id' => 'ASC'])
            ->all()
            ->toArray();
        $allRegions = $this->Products->find()
            ->select(['region'])
            ->distinct(['region'])
            ->where(['region IS NOT NULL', 'status' => 'Listed'])
            ->order(['region' => 'ASC'])
            ->all()
            ->toArray();
        $allVintages = $this->Products->find()
            ->select(['vintage'])
            ->distinct(['vintage'])
            ->where(['vintage IS NOT NULL', 'status' => 'Listed'])
            ->order(['vintage' => 'DESC'])
            ->all()
            ->toArray();
        $allVolumes = $this->Products->find()
            ->select(['volume'])
            ->distinct(['volume'])
            ->where(['volume IS NOT NULL', 'status' => 'Listed'])
            ->order(['volume' => 'ASC'])
            ->all()
            ->toArray();
        $allBrands = $this->Products->find()
            ->select(['brand'])
            ->distinct(['brand'])
            ->where(['brand IS NOT NULL', 'status' => 'Listed'])
            ->order(['brand' => 'ASC'])
            ->all()
            ->toArray();
        $allPrices = [
            ['value' => '0_50', 'label' => '$0 - $50'],
            ['value' => '50_100', 'label' => '$50 - $100'],
            ['value' => '100_plus', 'label' => '$100+'],
        ];

        // Fetch available filter options based on filtered products
        // Create a new query with the same conditions as $query
        $availableQuery = $this->Products->find()
            ->select(['wine_style_id', 'region', 'vintage', 'price', 'volume', 'brand'])
            ->where(['status' => 'Listed']);

        // Apply the same filter conditions
        if ($this->request->getQuery('wine_style_id')) {
            $styles = is_array($this->request->getQuery('wine_style_id')) ? $this->request->getQuery('wine_style_id') : [$this->request->getQuery('wine_style_id')];
            $availableQuery->where(['wine_style_id IN' => $styles]);
        }
        if ($this->request->getQuery('price')) {
            $priceRanges = is_array($this->request->getQuery('price')) ? $this->request->getQuery('price') : [$this->request->getQuery('price')];
            $priceConditions = [];
            foreach ($priceRanges as $priceRange) {
                if ($priceRange === '0_50') {
                    $priceConditions[] = ['price >=' => 0, 'price <=' => 50];
                } elseif ($priceRange === '50_100') {
                    $priceConditions[] = ['price >' => 50, 'price <=' => 100];
                } elseif ($priceRange === '100_plus') {
                    $priceConditions[] = ['price >' => 100];
                }
            }
            if ($priceConditions) {
                $availableQuery->where(['OR' => $priceConditions]);
            }
        }
        if ($this->request->getQuery('region')) {
            $regions = is_array($this->request->getQuery('region')) ? $this->request->getQuery('region') : [$this->request->getQuery('region')];
            $availableQuery->where(['region IN' => $regions]);
        }
        if ($this->request->getQuery('vintage')) {
            $vintages = is_array($this->request->getQuery('vintage')) ? $this->request->getQuery('vintage') : [$this->request->getQuery('vintage')];
            $availableQuery->where(['vintage IN' => $vintages]);
        }
        if ($this->request->getQuery('volume')) {
            $volumes = is_array($this->request->getQuery('volume')) ? $this->request->getQuery('volume') : [$this->request->getQuery('volume')];
            $availableQuery->where(['volume IN' => $volumes]);
        }
        if ($this->request->getQuery('brand')) {
            $brands = is_array($this->request->getQuery('brand')) ? $this->request->getQuery('brand') : [$this->request->getQuery('brand')];
            $availableQuery->where(['brand IN' => $brands]);
        }

        // Get available options for each filter
        $availableStyles = $availableQuery->select(['wine_style_id'])->distinct(['wine_style_id'])->all()->extract('wine_style_id')->toArray();
        $availableRegions = $availableQuery->select(['region'])->distinct(['region'])->all()->extract('region')->toArray();
        $availableVintages = $availableQuery->select(['vintage'])->distinct(['vintage'])->all()->extract('vintage')->toArray();
        $availableVolumes = $availableQuery->select(['volume'])->distinct(['volume'])->all()->extract('volume')->toArray();
        $availableBrands = $availableQuery->select(['brand'])->distinct(['brand'])->all()->extract('brand')->toArray();

        // Get available price ranges
        $availablePrices = [];
        $products = $availableQuery->all();
        foreach ($products as $product) {
            if ($product->price >= 0 && $product->price <= 50) {
                $availablePrices[] = '0_50';
            } elseif ($product->price > 50 && $product->price <= 100) {
                $availablePrices[] = '50_100';
            } elseif ($product->price > 100) {
                $availablePrices[] = '100_plus';
            }
        }
        $availablePrices = array_unique($availablePrices);

        // Get all products with pagination
        $products = $query->all();

        $this->set(compact(
            'products',
            'allStyles',
            'allRegions',
            'allVintages',
            'allVolumes',
            'allBrands',
            'allPrices',
            'availableStyles',
            'availableRegions',
            'availableVintages',
            'availableVolumes',
            'availableBrands',
            'availablePrices',
            'cartItems',
            'totalCount',
            'page',
            'totalPages',
            'limit',
            'wineStyles'
        ));
    }

    public function applyFilter($filterType = null, $filterValue = null)
    {
        $queryParams = $this->request->getQueryParams();
        switch (strtolower($filterType)) {
            case 'price':
                if (!isset($queryParams['price'])) {
                    $queryParams['price'] = [];
                }
                if (!is_array($queryParams['price'])) {
                    $queryParams['price'] = [$queryParams['price']];
                }
                if (in_array($filterValue, $queryParams['price'])) {
                    $queryParams['price'] = array_diff($queryParams['price'], [$filterValue]);
                } else {
                    $queryParams['price'][] = $filterValue;
                }
                if (empty($queryParams['price'])) {
                    unset($queryParams['price']);
                }
                break;
            case 'variety':
                if (!isset($queryParams['wine_style_id'])) {
                    $queryParams['wine_style_id'] = [];
                }
                if (!is_array($queryParams['wine_style_id'])) {
                    $queryParams['wine_style_id'] = [$queryParams['wine_style_id']];
                }
                if (in_array($filterValue, $queryParams['wine_style_id'])) {
                    $queryParams['wine_style_id'] = array_diff($queryParams['wine_style_id'], [$filterValue]);
                } else {
                    $queryParams['wine_style_id'][] = $filterValue;
                }
                if (empty($queryParams['wine_style_id'])) {
                    unset($queryParams['wine_style_id']);
                }
                break;
            case 'region':
                if (!isset($queryParams['region'])) {
                    $queryParams['region'] = [];
                }
                if (!is_array($queryParams['region'])) {
                    $queryParams['region'] = [$queryParams['region']];
                }
                if (in_array($filterValue, $queryParams['region'])) {
                    $queryParams['region'] = array_diff($queryParams['region'], [$filterValue]);
                } else {
                    $queryParams['region'][] = $filterValue;
                }
                if (empty($queryParams['region'])) {
                    unset($queryParams['region']);
                }
                break;
            case 'vintage':
                if (!isset($queryParams['vintage'])) {
                    $queryParams['vintage'] = [];
                }
                if (!is_array($queryParams['vintage'])) {
                    $queryParams['vintage'] = [$queryParams['vintage']];
                }
                if (in_array($filterValue, $queryParams['vintage'])) {
                    $queryParams['vintage'] = array_diff($queryParams['vintage'], [$filterValue]);
                } else {
                    $queryParams['vintage'][] = $filterValue;
                }
                if (empty($queryParams['vintage'])) {
                    unset($queryParams['vintage']);
                }
                break;
            default:
                return $this->redirect(['action' => 'page', '?' => $queryParams]);
        }

        return $this->redirect(['action' => 'page', '?' => $queryParams]);
    }

    public function removeFilter($filterType = null)
    {
        $queryParams = $this->request->getQueryParams();
        switch (strtolower($filterType)) {
            case 'price':
                unset($queryParams['price']);
                break;
            case 'variety':
                unset($queryParams['wine_style_id']);
                break;
            case 'region':
                unset($queryParams['region']);
                break;
            case 'vintage':
                unset($queryParams['vintage']);
                break;
            default:
                return $this->redirect(['action' => 'page', '?' => $queryParams]);
        }

        return $this->redirect(['action' => 'page', '?' => $queryParams]);
    }

    public function clearAllFilters()
    {
        $queryParams = $this->request->getQueryParams();
        $sort = $queryParams['sort'] ?? null;
        $queryParams = $sort ? ['sort' => $sort] : [];

        return $this->redirect(['action' => 'page', '?' => $queryParams]);
    }

    /**
     * Get quick view element for a product
     *
     * @return void
     */
    public function getQuickView()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $productId = $this->request->getQuery('product_id');
        if (!$productId) {
            $this->response = $this->response->withStatus(400);
            $this->response = $this->response->withStringBody('Product ID is required');
            return;
        }

        // Always contain WineStyles for quick view
        $product = $this->Products->get($productId, [
            'contain' => [
                'Categories',
                'WineStyles' => [
                    'fields' => ['id', 'name'],
                ],
            ],
            'fields' => [
                'Products.id', 'Products.name', 'Products.price', 'Products.image',
                'Products.vintage', 'Products.alcohol_content', 'Products.region',
                'Products.wine_style_id', 'Products.stock_quantity', 'Products.volume',
                'Products.brand', 'Products.product_description', 'Products.status',
            ],
        ]);
        $wineStyles = $this->Products->WineStyles->find('list',
            keyField: 'id',
            valueField: 'name'
        )->toArray();
        $this->set(compact( 'product', 'wineStyles'));

        $html = $this->element('Products/quick_view', [
            'product' => $product,
            'wineStyles' => $wineStyles,
        ]);
        $this->response = $this->response->withType('html')->withStringBody($html);
    }

    public function autocomplete()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;
        $query = $this->request->getQuery('query', '');
        $suggestions = [];

        if (!empty($query)) {
            $suggestions = $this->Products->find()
                ->select(['name'])
                ->where([
                    'name LIKE' => '%' . $query . '%',
                    'status' => 'Listed',
                ])
                ->limit(5)
                ->all()
                ->extract('name')
                ->toArray();
        }

        $this->response = $this->response->withType('application/json')
            ->withStringBody(json_encode(['suggestions' => $suggestions]));

        return $this->response;
    }

    public function search()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;
        $query = trim($this->request->getQuery('query', ''));
        $products = [];

        Log::debug("Search: query='$query'");

        if (strlen($query) >= 2) {
            try {
                $products = $this->Products->find()
                    ->select(['id', 'name', 'price', 'image', 'status'])
                    ->where([
                        'name LIKE' => '%' . $query . '%',
                        'status' => 'Listed',
                    ])
                    ->limit(10)
                    ->all()
                    ->map(function ($product) {
                        Log::debug("Search product: id={$product->id}, name={$product->name}, status={$product->status}");

                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => $product->price,
                            'image' => $product->image ?: null,
                        ];
                    })
                    ->toArray();
            } catch (Exception $e) {
                Log::error('Search error: ' . $e->getMessage());
                $this->response = $this->response->withStatus(500)
                    ->withType('application/json')
                    ->withStringBody(json_encode(['error' => 'Search failed']));

                return $this->response;
            }
        }

        Log::debug("Search query: '$query', found " . count($products) . ' products');
        $this->response = $this->response->withType('application/json')
            ->withStringBody(json_encode(['products' => $products]));

        return $this->response;
    }

    public function cartForm()
    {
        $this->request->allowMethod(['get']);
        $productId = $this->request->getQuery('product_id');

        if (!$productId || !is_numeric($productId)) {
            return $this->response->withStatus(400)
                ->withType('application/json')
                ->withStringBody(json_encode(['error' => 'Invalid product ID']));
        }

        $product = $this->Products->find()
            ->select(['id', 'status'])
            ->where(['id' => $productId, 'status' => 'Listed'])
            ->first();

        if (!$product) {
            return $this->response->withStatus(404)
                ->withType('application/json')
                ->withStringBody(json_encode(['error' => 'Product not found or not listed']));
        }

        $this->set('product_id', (int)$productId);
        $this->viewBuilder()->setLayout('ajax');
        $this->render('cart_form');
    }

    public function getCsrfToken()
    {
        $this->request->allowMethod(['get']);

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'token' => $this->request->getAttribute('csrfToken'),
            ]));
    }

    public function loadMore()
    {
        $this->request->allowMethod(['get']);
        $this->viewBuilder()->setLayout('ajax');

        $page = $this->request->getQuery('page', 1);
        $limit = 12;

        $query = $this->Products->find()
            ->select(['id', 'name', 'price', 'image', 'wine_style_id', 'region', 'vintage', 'alcohol_content', 'stock_quantity', 'status', 'created_at', 'volume', 'brand', 'product_description'])
            ->where(['status' => 'Listed'])
            ->contain(['Reviews']);

        // Apply filters and sorting
        $query = $this->applyFilters($query);
        $query = $this->applySorting($query);

        // Apply pagination
        $query->limit($limit)
              ->offset(($page - 1) * $limit);

        $products = $query->all();
        $totalCount = $query->count();
        $totalPages = ceil($totalCount / $limit);

        $this->set(compact('products', 'page', 'totalPages'));
    }

    public function flashMessage()
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false;

        $message = $this->request->getData('message') ?? 'Maximum stock limit reached';
        $type = $this->request->getData('type') ?? 'error';

        // Set the flash message with a specific key
        $this->Flash->$type($message, ['key' => 'stockLimit']);

        // Get the rendered flash message HTML
        $flashHtml = $this->Flash->render('stockLimit');

        return $this->response->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'message' => $message,
                'type' => $type,
                'flashHtml' => $flashHtml
            ]));
    }
}
