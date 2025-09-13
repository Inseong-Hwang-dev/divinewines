<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->addUnauthenticatedActions(['login']);
    }

    /**
     * Before filter method
     *
     * @param \Cake\Event\EventInterface $event Event object
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->viewBuilder()->setLayout('admin');

        $user = $this->Authentication->getIdentity();

        if ($user && $user->user_type === 'Admin') {
            $this->viewBuilder()->setLayout('admin');
        } elseif ($user && $user->user_type === 'Customer') {
            // Allow Customers to access the profile action
            if ($this->request->getParam('action') !== 'profile' &&
                $this->request->getParam('action') !== 'profileEdit') {

                //if user not logged, redirect to login page
                $this->Flash->error(__('Please log in to access your profile.'), ['key' => 'authFeedback']);
                return $this->redirect(['controller' => 'Auth','action' => 'login']);

            }
            $this->viewBuilder()->setLayout('default'); // Use default layout for Customers
        } else {
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
        // Get filter parameters from query string
        $statusFilter = $this->request->getQuery('status');

        // Base query
        $query = $this->Users->find()
            ->where(['user_type !=' => 'Admin']);

        // By default, show only non-archived users
        if ($statusFilter !== 'archived') {
            $query = $query->where(['archived' => false]);
        } else {
            $query = $query->where(['archived' => true]);
        }

        $users = $this->paginate($query);

        $this->set(compact('users', 'statusFilter'));
    }

    /**
     * Archived users method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function archived()
    {
        $query = $this->Users->find()
            ->where(['user_type !=' => 'Admin'])
            ->where(['archived' => true]);

        $users = $this->paginate($query);

        $this->set(compact('users'));
        $this->render('index');
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, contain: []);

        if ($user->user_type === 'Admin') {
            $this->response = $this->response->withStringBody(
                '<script>alert("Admin users cannot be viewed."); window.location.href = "/users";</script>'
            );
            return $this->response;
        }

        $this->set(compact('user'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Archive method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function archive($id = null)
    {
        $this->request->allowMethod(['post']);
        $user = $this->Users->get($id);
        $user->archived = true;

        if ($this->Users->save($user)) {
            $this->Flash->success(__('The user has been archived.'));
        } else {
            $this->Flash->error(__('The user could not be archived. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Profile method
     *
     * Displays the profile page for authenticated Customer users.
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function profile()
    {
        $user = $this->Authentication->getIdentity();

        if (!$user || $user->user_type !== 'Customer') {
            $this->response = $this->response->withStringBody(
            '<script>alert("You do not have permission to access this page."); window.location.href = "/";</script>'
            );
            return $this->response;
        }

        // Fetch user data
        $userData = $this->Users->get($user->id, contain: []);

        // Get coupon count for the user
        $couponCount = $this->fetchTable('Coupons')->find()
            ->where([
                'OR' => [
                    'user_id' => $user->id,
                    'user_id IS' => null
                ],
                'is_active' => true,
                'AND' => [
                    'OR' => [
                        'expiry_date IS NULL',
                        'expiry_date >=' => date('Y-m-d')
                    ],
                    'OR' => [
                        'usage_limit IS NULL',
                        'times_used < usage_limit'
                    ]
                ]
            ])
            ->count();

        $this->set(compact('userData', 'couponCount'));
    }

    /**
     * Profile Edit method
     *
     * Allows Customers to edit their own profile.
     *
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     */
    public function profileEdit()
    {
        // Fetch shipping countries from the database
        $shippingCountries = $this->fetchTable('ShippingCountries')->find('list', [
            'keyField' => 'country_code',
            'valueField' => 'country_name',
        ])->toArray();

        $currentUser = $this->Authentication->getIdentity();

        if (!$currentUser || $currentUser->user_type !== 'Customer') {
            $this->response = $this->response->withStringBody(
                '<script>alert("You do not have permission to access this page."); window.location.href = "/";</script>'
            );
            return $this->response;
        }

        $user = $this->Users->get($currentUser->id, contain: []);

        if ($this->request->is(['patch', 'post', 'put'])) {
            // Restrict editable fields for Customers
            $allowedFields = ['first_name', 'last_name', 'email', 'phone',
                'user-address1','user-address2','user-city','user-state', 'user-country','user-postcode',
                'birth_month', 'birth_day'];
            $data = array_intersect_key($this->request->getData(), array_flip($allowedFields));

            // Convert birth_month and birth_day to birthday
            if (!empty($data['birth_month']) && !empty($data['birth_day'])) {
                // Use a default year (e.g., 1970) since year is not provided
                $birthday = \Cake\I18n\Date::create(1970, (int)$data['birth_month'], (int)$data['birth_day']);
                $data['birthday'] = $birthday;
            } else {
                $data['birthday'] = null; // Clear birthday if month or day is missing
            }
            unset($data['birth_month'], $data['birth_day']);

            $user = $this->Users->patchEntity($user, $data);

            if ($this->Users->save($user)) {
                $this->Flash->success(__('Your profile has been updated.'));
                return $this->redirect(['action' => 'profile']);
            }
            $this->Flash->error(__('The profile could not be updated. Please, try again.'));
        }

        $this->set(compact('user', 'shippingCountries'));
        $this->render('profile-edit');
    }
}
