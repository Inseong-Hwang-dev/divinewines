<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Coupons Controller
 *
 * @property \App\Model\Table\CouponsTable $Coupons
 */
class CouponsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Coupons->find()
            ->contain(['Users']);
        $coupons = $this->paginate($query);

        $this->set(compact('coupons'));
    }

    /**
     * View method
     *
     * @param string|null $id Coupon id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $coupon = $this->Coupons->get($id, contain: ['Users']);
        $this->set(compact('coupon'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $coupon = $this->Coupons->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            // Set default values
            $data['is_active'] = true;
            $data['times_used'] = 0;

            $coupon = $this->Coupons->patchEntity($coupon, $data);
            if ($this->Coupons->save($coupon)) {
                $this->Flash->success(__('The coupon has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The coupon could not be saved. Please, try again.'));
        }
        $users = $this->Coupons->Users->find('list', limit: 200)->all();
        $this->set(compact('coupon', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Coupon id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $coupon = $this->Coupons->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $coupon = $this->Coupons->patchEntity($coupon, $this->request->getData());
            if ($this->Coupons->save($coupon)) {
                $this->Flash->success(__('The coupon has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The coupon could not be saved. Please, try again.'));
        }
        $users = $this->Coupons->Users->find('list', [
            'keyField' => 'id',
            'valueField' => function ($user) {
                return $user->first_name . ' ' . $user->last_name . ' (' . $user->email . ')';
            },
            'limit' => 200
        ])
        ->order(['first_name' => 'ASC'])
        ->all();
        $this->set(compact('coupon', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Coupon id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $coupon = $this->Coupons->get($id);
        if ($this->Coupons->delete($coupon)) {
            $this->Flash->success(__('The coupon has been deleted.'));
        } else {
            $this->Flash->error(__('The coupon could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Only allow admins
        $user = $this->request->getAttribute('identity');
        if (!$user || $user->user_type !== 'Admin') {

            //if user not logged in as admin, redirect to login page
            $this->Flash->error(__('Please log in to access this page.'), ['key' => 'authFeedback']);
            return $this->redirect(['controller' => 'Auth','action' => 'login']);
        }
        // Use admin layout
        $this->viewBuilder()->setLayout('admin');
    }

    public function sendBirthdayCoupons()
    {
        $this->request->allowMethod(['post']);

        $month = (int)$this->request->getData('month');
        if ($month < 1 || $month > 12) {
            $this->Flash->error(__('Invalid month selected.'));
            return $this->redirect(['action' => 'index']);
        }

        $usersTable = $this->fetchTable('Users');
        $users = $usersTable->find()
            ->where([
                'MONTH(birthday)' => $month,
                'archived' => 0
            ])
            ->all();

        if ($users->isEmpty()) {
            $this->Flash->error(__('No users found with birthdays in the selected month.'));
            return $this->redirect(['action' => 'index']);
        }

        $successCount = 0;
        $errorCount = 0;
        $alreadyHasCouponCount = 0;

        foreach ($users as $user) {
            // Check if user already has a birthday coupon this year
            $existingCoupon = $this->Coupons->find()
                ->where([
                    'user_id' => $user->id,
                    'coupon_code LIKE' => 'BDAY%',
                    'created >=' => date('Y-01-01')
                ])
                ->first();

            if (!$existingCoupon) {
                // Generate unique coupon code
                $couponCode = 'BDAY' . strtoupper(substr(md5(uniqid()), 0, 6));

                // Create birthday coupon
                $coupon = $this->Coupons->newEmptyEntity();
                $coupon = $this->Coupons->patchEntity($coupon, [
                    'coupon_code' => $couponCode,
                    'discount_amount' => 15, // 15% discount
                    'user_id' => $user->id,
                    'is_active' => true,
                    'expiry_date' => date('Y-m-d 23:59:59', strtotime($user->birthday->format('Y-m-d'))),
                    'min_purchase_amount' => 50, // Minimum $50 purchase
                    'max_discount_amount' => 100, // Maximum $100 discount
                    'usage_limit' => 1, // Can only be used once
                    'times_used' => 0
                ]);

                if ($this->Coupons->save($coupon)) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            } else {
                $alreadyHasCouponCount++;
            }
        }

        if ($successCount > 0) {
            $this->Flash->success(__('Successfully sent {0} birthday coupons.', $successCount));
        }
        if ($errorCount > 0) {
            $this->Flash->error(__('Failed to send {0} birthday coupons.', $errorCount));
        }
        if ($alreadyHasCouponCount > 0) {
            $this->Flash->warning(__('{0} users already have birthday coupons for this year.', $alreadyHasCouponCount));
        }

        return $this->redirect(['action' => 'index']);
    }
}
