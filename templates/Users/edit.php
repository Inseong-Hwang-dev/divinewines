<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="row">
    <?= $this->Html->tag('button', '<i class="bi bi-arrow-left"></i>  Back',
        ['type' => 'button',
            'onclick' => 'if(confirm("Are you sure you want to go back? Your changes will not be saved.")){window.history.back();}',
            'class' => 'btn back-btn text-start w-auto']) ?>
    <div class="alert alert-dismissible fade show" role="alert">
        <?= $this->Flash->render('userEditFeedback');?>
    </div>
    <div class="row pb-5 mb-5 justify-content-center">
        <div class="col-md-8">
            <div class="admin-form mt-3 mb-2 mx-auto">
                <div class="admin-card-header text-black text-center pt-4 mb-3">
                    <?= __('Edit User Details') ?>
                </div>
                <div class=card-body>
                    <?= $this->Form->create($user) ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $this->Form->control('first_name', [
                                'label' => 'First Name *',
                                'class' => 'form-control',
                                'placeholder' => 'Enter first name',
                                'required' => true
                            ]); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $this->Form->control('last_name', [
                                'label' => 'Last Name *',
                                'class' => 'form-control',
                                'placeholder' => 'Enter last name',
                                'required' => true
                            ]); ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <?= $this->Form->control('email', [
                            'label' => 'Email Address *',
                            'class' => 'form-control',
                            'placeholder' => 'Enter email address',
                            'required' => true
                        ]); ?>
                    </div>

                    <div class="mb-3">
                        <?= $this->Form->control('phone', [
                            'label' => 'Phone Number',
                            'class' => 'form-control',
                            'placeholder' => 'Enter phone number'
                        ]); ?>
                    </div>

                    <div class="mb-3">
                        <?= $this->Form->control('address', [
                            'label' => 'Address',
                            'class' => 'form-control',
                            'placeholder' => 'Enter address'
                        ]); ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $this->Form->control('birthday', [
                                'label' => 'Birthday',
                                'class' => 'form-control',
                                'type' => 'date'
                            ]); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $this->Form->control('loyalty_points', [
                                'label' => 'Loyalty Points',
                                'class' => 'form-control',
                                'type' => 'number',
                                'min' => 0,
                                'max' => 100,
                                'placeholder' => 'Enter loyalty points'
                            ]); ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <?= $this->Form->control('user_type', [
                            'label' => 'User Type *',
                            'class' => 'form-control',
                            'required' => true,
                            'options' => [
                                'Customer' => 'Customer',
                                'Admin' => 'Admin',
                                'Staff' => 'Staff'
                            ],
                            'empty' => 'Select user type'
                        ]); ?>
                    </div>

                    <div class="mb-3 pb-3">
                        <?= $this->Form->control('archived', [
                            'label' => 'Account Status',
                            'class' => 'form-check-input',
                            'type' => 'checkbox',
                            'templates' => [
                                'inputContainer' => '<div class="form-check">{{content}}</div>',
                                'checkboxFormGroup' => '{{input}}{{label}}'
                            ],
                            'label' => [
                                'class' => 'form-check-label ms-2',
                                'text' => 'Archive this user account'
                            ]
                        ]); ?>
                    </div>

                    <!-- Hidden fields that may be needed but not shown to the user -->
                    <?= $this->Form->control('nonce', ['type' => 'hidden']); ?>
                    <?= $this->Form->control('nonce_expiry', ['type' => 'hidden', 'empty' => true]); ?>

                    <div class="text-end">
                        <!-- cancel button-->
                        <?= $this->Html->tag('button', 'Cancel',
                            ['type' => 'button',
                                'onclick' => 'if(confirm("Are you sure you want to cancel? Your changes will not be saved.")){window.history.back();}',
                                'class' => 'btn admin-btn-cancel']) ?>

                        <?= $this->Form->button(__('Update User'), ['class' => 'btn admin-btn-submit']) ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
