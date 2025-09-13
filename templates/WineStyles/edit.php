<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\WineStyle $wineStyle
 */
?>
<div class="row">
    <?= $this->Html->tag('button', '<i class="bi bi-arrow-left"></i>  Back',
        ['type' => 'button',
            'onclick' => 'if(confirm("Are you sure you want to go back? Your changes will not be saved.")){window.history.back();}',
            'class' => 'btn back-btn text-start w-auto']) ?>
    <div class="alert alert-dismissible fade show" role="alert">
        <?= $this->Flash->render('editStyleFeedback');?>
    </div>

    <div class="row pb-5 mb-5 justify-content-center">
        <div class="col-md-8">
            <div class="admin-form mt-3 mb-2 mx-auto">
                <div class="admin-card-header text-black text-center pt-4 mb-3">
                    <?= __('Edit Style') ?>
                </div>
                <p class="text-center text-muted">Fill out this form to edit the style.</p>

                <div class=card-body>
                    <?= $this->Form->create($wineStyles) ?>
                    <div class="mb-3"><?= $this->Form->control('name', ['label' => 'Name of Wine *',
                            'class' => 'form-control',
                            'placeholder'=>'Enter name of wine',
                            'pattern' => '^[A-Za-z\s\-]+$',
                            'title'=>'Only letters, spaces, and hyphens are allowed',
                            'maxlength'=>'100',
                            'required' => true]);?></div>

                    <div class="text-end">
                        <!-- cancel button-->
                        <?= $this->Html->tag('button', 'Cancel',
                            ['type' => 'button',
                                'onclick' => 'if(confirm("Are you sure you want to cancel? Your changes will not be saved.")){window.history.back();}',
                                'class' => 'btn admin-btn-cancel']) ?>

                        <?= $this->Form->button(__('Save'), ['class' => 'btn  admin-btn-submit']) ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
