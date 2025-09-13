<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * WineStyles Controller
 *
 * @property \App\Model\Table\WineStylesTable $WineStyles
 */
class WineStylesController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();

        $this->viewBuilder()->setLayout('admin');

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

        // Admin-only actions
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->user_type !== 'Admin') {

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
        $query = $this->WineStyles->find('all')
            ->where(['WineStyles.archived' => 0]);

        $wineStyles = $this->paginate($query);

        $this->set(compact('wineStyles'));
    }

//    /**
//     * View method
//     *
//     * @param string|null $id Wine Style id.
//     * @return \Cake\Http\Response|null|void Renders view
//     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
//     */
//    public function view($id = null)
//    {
//        $wineStyle = $this->WineStyles->get($id, contain: []);
//        $this->set(compact('wineStyle'));
//    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $wineStyle = $this->WineStyles->newEmptyEntity();
        if ($this->request->is('post')) {
            $wineStyle = $this->WineStyles->patchEntity($wineStyle, $this->request->getData());
            if ($this->WineStyles->save($wineStyle)) {
                $this->Flash->success(__('The wine style has been saved.'), ['key' => 'styleFeedback']);

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The wine style could not be saved. Please, try again.'), ['key' => 'addStyleFeedback']);
        }
        $this->set(compact('wineStyle'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Wine Style id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $wineStyles = $this->WineStyles->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $wineStyle = $this->WineStyles->patchEntity($wineStyles, $this->request->getData());
            if ($this->WineStyles->save($wineStyle)) {
                $this->Flash->success(__('The wine style has been saved.'), ['key' => 'styleFeedback']);

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The wine style could not be saved. Please, try again.'), ['key' => 'editStyleFeedback']);
        }
        $this->set(compact('wineStyles'));
    }

    /**
     * Gets all the archived wine styles
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function archived()
    {
        $query = $this->WineStyles->find()
            ->where(['archived' => true]);

        $wineStyles = $this->paginate($query);

        $this->set(compact('wineStyles'));
        $this->render('index');
    }

    /**
     * Archives a wine style
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function archive($id = null)
    {
        $this->request->allowMethod(['post']);

        $wineStyle = $this->WineStyles->get($id);
        $wineStyle->archived = true; // set archive status to true

        if ($this->WineStyles->save($wineStyle)) {
            $this->Flash->success(__('The wine style has been archived.'),  ['key' => 'styleFeedback'] );
        } else {
            $this->Flash->error(__('The wine style could not be archived. Please, try again.'),  ['key' => 'styleFeedback']);
        }

        return $this->redirect(['action' => 'index']);
    }


    /**
     * Retores a wine style
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function restore($id = null)
    {
        $this->request->allowMethod(['post']);

        $wineStyle = $this->WineStyles->get($id);
        $wineStyle->archived = false; // set archive status to false

        if ($this->WineStyles->save($wineStyle)) {
            $this->Flash->success(__('The wine style has been restored.'), ['key' => 'styleFeedback']);
        } else {
            $this->Flash->error(__('The wine style could not be restored. Please, try again.'), ['key' => 'styleFeedback']);
        }

        return $this->redirect(['action' => 'index']);
    }
}
