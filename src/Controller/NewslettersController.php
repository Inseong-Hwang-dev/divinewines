<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use Cake\I18n\FrozenTime;


/**
 * Newsletters Controller
 *
 * @property \App\Model\Table\NewslettersTable $Newsletters
 */
class NewslettersController extends AppController
{

    /**
     * initialize method
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Allow unauthenticated access to add action
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

        $this->viewBuilder()->setLayout('admin');
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Newsletters->find();
        $newsletters = $this->paginate($query);

        $this->set(compact('newsletters'));
    }

    /**
     * View method
     *
     * @param string|null $id Newsletter id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $newsletter = $this->Newsletters->get($id, contain: []);
        $this->set(compact('newsletter'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $newsletter = $this->Newsletters->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Remove image from data to prevent patchEntity() from processing it
            $image = $data['image'] ?? null;
            unset($data['image']);

            $newsletter = $this->Newsletters->patchEntity($newsletter, $data);

            if($newsletter->getErrors()){
                $this->Flash->error(__('Please fix the errors below.'), ['key' => 'addNewsletterFeedback']);
            }else{

                // Handle file upload
                if ($image && $image->getClientFilename() !== '') {
                    if ($image->getError() === UPLOAD_ERR_OK) {
                        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '', $image->getClientFilename());
                        $uploadDir = WWW_ROOT . 'img' . DS . 'Newsletters' . DS;

                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0775, true);
                        }

                        $targetPath = $uploadDir . $filename;
                        $image->moveTo($targetPath);

                        $newsletter->image = 'Newsletters/' . $filename;
                    } else {
                        $this->Flash->error(__('Image could not be uploaded. Please, try again.'), ['key' => 'addNewsletterFeedback']);

                        return $this->redirect($this->referer());
                    }
                }

                if ($this->Newsletters->save($newsletter)) {
                    $this->Flash->success(__('The newsletter has been saved.'),['key' => 'viewNewsletterFeedback']);

                    return $this->redirect(['action' => 'view', $newsletter->id]);
                }
                $this->Flash->error(__('The newsletter could not be saved. Please, try again.'), ['key' => 'addNewsletterFeedback']);
                return $this->redirect($this->referer());

            }
        }
        $this->set(compact('newsletter'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Newsletter id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $newsletter = $this->Newsletters->get($id, contain: []);

        $existingImage = $newsletter->image ?? null;


        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            $removeImage = !empty($data['remove_image']);

            // Remove image from data to prevent patchEntity() from processing it
            $image = $data['image'] ?? null;
            unset($data['image']);
            // Set image to null if user clicks on remove_image checkbox on the edit form
            if ($removeImage) {
                $newsletter->image = null; // or delete the file from disk if needed
            }




            $newsletter = $this->Newsletters->patchEntity($newsletter, $data);

            // Handle file upload
            if ($image && $image->getClientFilename() !== '') {
                if ($image->getError() === UPLOAD_ERR_OK) {
                    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '', $image->getClientFilename());
                    $uploadDir = WWW_ROOT . 'img' . DS . 'Newsletters' . DS;

                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0775, true);
                    }

                    $targetPath = $uploadDir . $filename;
                    $image->moveTo($targetPath);

                    $newsletter->image = 'Newsletters/' . $filename;
                } else {
                    $this->Flash->error(__('Image could not be uploaded. Please, try again.'), ['key' => 'editNewsletterFeedback']);

                    return $this->redirect($this->referer());
                }
            }elseif (!$removeImage) {
                $newsletter->image = $existingImage;
            }

            if ($this->Newsletters->save($newsletter)) {
                $this->Flash->success(__('The newsletter #{0} has been saved.', $newsletter->id), ['key' => 'viewNewsletterFeedback']);

                return $this->redirect(['action' => 'view', $newsletter->id]);
            }
            $this->Flash->error(__('The newsletter could not be saved. Please, try again.'), ['key' => 'editNewsletterFeedback']);
        }
        $this->set(compact('newsletter'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Newsletter id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $newsletter = $this->Newsletters->get($id);
        if ($this->Newsletters->delete($newsletter)) {
            $this->Flash->success(__('The newsletter has been deleted.'), ['key' => 'newsletterFeedback']);
        } else {
            $this->Flash->error(__('The newsletter could not be deleted. Please, try again.'), ['key' => 'newsletterFeedback']);
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Changes the product status from listed to unlisted and vice versa
     * @param $id
     * @return \Cake\Http\Response|null
     */
    public function publishNewsletter($id = null)
    {
        $newsletter = $this->Newsletters->get($id);

        // Toggle status
        $isPublishing = $newsletter->status !== 'published';
//        $newsletter->status = $isPublishing ? 'published' : 'unpublished';

        if ($isPublishing) {
            // Unpublish all other Newsletters
            $this->Newsletters->updateAll(
                ['status' => 'unpublished'],
                ['status' => 'published']
            );

            $newsletter->status = 'published'; // Set this newsletter to published
            $newsletter->date_published = FrozenTime::now(); // Pass the current timestamp
        } else {
            $newsletter->status = 'unpublished'; // If unpublish, set the status to unpublished
        }

        if ($this->Newsletters->save($newsletter)) {
            $this->Flash->success(__('The status of newsletter #{0} has been changed.', $newsletter->id), ['key' => 'newsletterFeedback']);

            return $this->redirect($this->referer());
        }

        $this->Flash->error(__('The status of newsletter #{0} could not be changed. Please, try again.', $newsletter->subheading), ['key' => 'newsletterFeedback']);

        return $this->redirect($this->referer());
    }
}
