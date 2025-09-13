<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Newsletter $newsletter
 */
?>
<div class="row">
    <?= $this->Html->tag('button', '<i class="bi bi-arrow-left"></i>  Back',
        ['type' => 'button',
            'onclick' => 'if(confirm("Are you sure you want to go back? Your changes will not be saved.")){window.history.back();}',
            'class' => 'btn back-btn text-start w-auto']) ?>
    <div class="alert alert-dismissible fade show" role="alert">
        <?= $this->Flash->render('editNewsletterFeedback');?>
    </div>

    <div class="row pb-5 mb-5 justify-content-center">
        <div class="col-md-8">
            <div class="admin-form mt-3 mb-2 mx-auto">

                <!--                form header section-->
                <div class="admin-card-header text-black text-center pt-4 mb-3">
                    <?= __('Edit Newsletter') ?>
                </div>

                <!--                add newsletter form-->
                <div class=card-body>
                    <?= $this->Form->create($newsletter, ['type' => 'file']) ?>
                    <div class="mb-3"><?= $this->Form->control('title', ['label' => 'Title / Name of Newsletter * *',
                            'class' => 'form-control',
                            'placeholder'=>'Enter title of the newsletter',
                            'maxlength'=>'100',
                            'required' => true]);?>
                    </div>
                    <div class="mb-3"><?= $this->Form->control('subheading', ['label' => 'Subheading *',
                            'class' => 'form-control',
                            'placeholder'=>'Enter subheading',
                            'maxlength'=>'100',
                            'required' => true]);?>
                    </div>
                    <div class="mb-3"><?= $this->Form->control('body', ['label' => 'Body/Content *',
                            'class' => 'form-control',
                            'placeholder'=>'Enter body text of the newsletter',
                            'maxlength'=>'10000',
                            'required' => true]);?>
                    </div>
                    <div class="mb-3">
<!--                        show current image-->
                        <?php if (!empty($newsletter->image)): ?>
                            <div class="mb-2">
                                <p>Current image:</p>
                                <?= $this->Html->image($newsletter->image, ['style' => 'max-width: 200px; height: auto;']) ?>
                            </div>
                        <?php endif; ?>
<!--                        prompt to upload new image-->
                        <div class="mb-3"><?= $this->Form->control('image', ['type' => 'file', 'label' => 'Upload Image', 'required' => false]) ?></div>
                        <small class="form-text text-muted">Allowed formats: JPG, JPEG, PNG, GIF</small>

<!--                        checkbox to completely remove image-->
                        <?php if (!empty($newsletter->image)): ?>
                            <div class="form-check mb-3">
                                <?= $this->Form->checkbox('remove_image', ['value' => '1', 'class' => 'form-check-input']) ?>
                                <?= $this->Form->label('remove_image', 'Remove current image', ['class' => 'form-check-label']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3 pb-3"><?= $this->Form->control('status', ['label' => 'Status *',
                            'class' => 'form-control status',
                            'required' => true,
                            'options' => [
                                'published' => 'published',
                                'unpublished' => 'unpublished',
                            ],
                            'empty' => 'Select a status',
                        ]);?>
                    </div>

                    <div class="text-end">
                        <!-- cancel button-->
                        <?= $this->Html->tag('button', 'Cancel',
                            ['type' => 'button',
                                'onclick' => 'if(confirm("Are you sure you want to cancel? Your changes will not be saved.")){window.history.back();}',
                                'class' => 'btn admin-btn-cancel']) ?>

                        <?= $this->Form->button(__('Save Changes'), ['class' => 'btn  admin-btn-submit']) ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
