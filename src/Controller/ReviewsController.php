<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Reviews Controller
 *
 * @property \App\Model\Table\ReviewsTable $Reviews
 */
class ReviewsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Reviews->find()
            ->contain(['Users', 'Products']);
        $reviews = $this->paginate($query);

        $this->set(compact('reviews'));
    }

    /**
     * View method
     *
     * @param string|null $id Review id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $review = $this->Reviews->get($id, contain: ['Users', 'Products']);
        $this->set(compact('review'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->request->allowMethod(['post']);
        $review = $this->Reviews->newEmptyEntity();
        
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $review = $this->Reviews->patchEntity($review, $data);
            
            // Set default status for new reviews
            $review->status = 'pending';
            
            if ($this->Reviews->save($review)) {
                $this->Flash->success(__('Thank you for your review! It has been submitted and is pending approval by our team. This process helps us maintain the quality of our reviews.'), ['key' => 'reviewFeedback']);
            } else {
                $this->Flash->error(__('The review could not be saved. Please, try again.'), ['key' => 'reviewFeedback']);
            }
        }
        return $this->redirect($this->referer());
    }

    /**
     * Edit method
     *
     * @param string|null $id Review id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $review = $this->Reviews->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $review = $this->Reviews->patchEntity($review, $this->request->getData());
            if ($this->Reviews->save($review)) {
                $this->Flash->success(__('The review has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The review could not be saved. Please, try again.'));
        }
        $users = $this->Reviews->Users->find('list', limit: 200)->all();
        $products = $this->Reviews->Products->find('list', limit: 200)->all();
        $this->set(compact('review', 'users', 'products'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Review id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $review = $this->Reviews->get($id);
        if ($this->Reviews->delete($review)) {
            $this->Flash->success(__('The review has been deleted.'));
        } else {
            $this->Flash->error(__('The review could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        // Set admin layout for admin actions
        if (in_array($this->request->getParam('action'), ['index', 'view', 'edit', 'delete'])) {
            $this->viewBuilder()->setLayout('admin');
        }
    }

    public function initialize(): void
    {
        parent::initialize();
        
        // Configure form protection for the add action
        $this->FormProtection->setConfig('unlockedFields', ['rating', 'review_text']);
    }
}
