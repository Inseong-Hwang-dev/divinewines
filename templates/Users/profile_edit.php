<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="users profile-edit content">
    <!-- Header Section -->
    <div class="header-image">
        <?= $this->Html->image('contact-banner.jpg', ['alt' => 'Contact Form Banner']) ?>
    </div>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bolder">Edit My Profile</h1>
            <p class="text-muted">Update your personal information</p>
        </div>

        <!-- Profile Edit Form -->
        <div class="profile-edit-form">
            <?= $this->Form->create($user, ['url' => ['controller' => 'Users', 'action' => 'profileEdit'], 'class' => 'needs-validation', 'novalidate' => true]) ?>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('first_name', [
                            'label' => ['class' => 'form-label', 'text' => 'First Name'],
                            'class' => 'form-control',
                            'required' => true,
                            'placeholder' => 'Enter your first name'
                        ]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('last_name', [
                            'label' => ['class' => 'form-label', 'text' => 'Last Name'],
                            'class' => 'form-control',
                            'required' => true,
                            'placeholder' => 'Enter your last name'
                        ]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('email', [
                            'label' => ['class' => 'form-label', 'text' => 'Email'],
                            'class' => 'form-control',
                            'type' => 'email',
                            'required' => true,
                            'placeholder' => 'Enter your email'
                        ]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('phone', [
                            'label' => ['class' => 'form-label', 'text' => 'Phone'],
                            'class' => 'form-control',
                            'type' => 'tel',
                            'required' => true,
                            'placeholder' => 'Enter your phone number'
                        ]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('birth_month', [
                            'type' => 'select',
                            'label' => ['class' => 'form-label', 'text' => 'Birth Month'],
                            'class' => 'form-control',
                            'options' => [
                                '1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April',
                                '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August',
                                '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
                            ],
                            'value' => $user->birthday ? $user->birthday->format('n') : null,
                            'empty' => 'Select month'
                        ]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('birth_day', [
                            'type' => 'select',
                            'label' => ['class' => 'form-label', 'text' => 'Birth Day'],
                            'class' => 'form-control',
                            'options' => array_combine(range(1, 31), range(1, 31)),
                            'value' => $user->birthday ? $user->birthday->format('j') : null,
                            'empty' => 'Select day'
                        ]) ?>
                    </div>
                </div>
                <hr>
                <p style="font-weight: bold; font-size: 20px;">Address</p>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('user_address1', [
                            'label' => ['class' => 'form-label', 'text' => 'Address Line 1 *'],
                            'class' => 'form-control',
                            'placeholder' => 'Enter your address',
                            'value' => $user->user_address1 ? $user->user_address1 : null,
                            'required' => true,
                        ]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('user_address2', [
                            'label' => ['class' => 'form-label', 'text' => 'Address Line 2'],
                            'class' => 'form-control',
                            'placeholder' => 'Enter your address',
                            'value' => $user->user_address2 ? $user->user_address2 : null,
                        ]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('user_city', [
                            'label' => ['class' => 'form-label', 'text' => 'City*'],
                            'class' => 'form-control',
                            'placeholder' => 'Enter your city',
                            'value' => $user->user_city ? $user->user_city : null,
                            'required' => true,
                        ]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('user_state', [
                            'label' => ['class' => 'form-label', 'text' => 'State *'],
                            'class' => 'form-control',
                            'placeholder' => 'Enter your state or region',
                            'value' => $user->user_state? $user->user_state : null,
                            'required' => true,
                        ]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('user_country', [
                            'label' => ['class' => 'form-label', 'text' => 'Country *'],
                            'class' => 'form-control',
                            'type' => 'select',
                            'placeholder' => 'Enter your country',
                            'options'=> $shippingCountries,
                            'value' => $user->user_country ? $user->user_country : 'AU',
                            'required' => true,
                        ]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= $this->Form->control('user_postcode', [
                            'label' => ['class' => 'form-label', 'text' => 'Postcode *'],
                            'class' => 'form-control',
                            'placeholder' => 'Enter your postcode',
                            'value' => $user->user_postcode ? $user->user_postcode : null,
                            'required' => true,
                        ]) ?>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="text-center mt-5">
                <?= $this->Form->button('Save Changes',
                    ['class' => 'btn btn-save-changes btn-large me-2',
                    'type' => 'submit',
                    'escape' => false]
                ) ?>
                <?= $this->Html->tag('button', 'Cancel',
                    ['type' => 'button',
                        'onclick' => 'if(confirm("Are you sure you want to cancel? Your changes will not be saved.")){window.history.back();}',
                        'class' => 'btn admin-btn-cancel']) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS for form validation -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
    /* ==========================================================================
       Header Image
       ========================================================================== */
    .header-image {
        width: 100%;
        max-width: 1380px;
        margin: 0 auto 20px auto;
    }

    .header-image img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* ==========================================================================
       Profile Edit Form
       ========================================================================== */
    .profile-edit-form {
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #2d3436;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #dfe6e9;
        padding: 10px;
        font-size: 1rem;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
        border-color: #6c5ce7;
        box-shadow: 0 0 0 0.2rem rgba(108, 92, 231, 0.25);
        outline: none;
    }

    .form-control:invalid:focus {
        border-color: #e74c3c;
        box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25);
    }

    /* ==========================================================================
       Buttons
       ========================================================================== */
    .btn-primary {
        background: linear-gradient(90deg, #6c5ce7, #0984e3);
        color: white !important;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #5a4cd6, #0774c3);
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .btn-secondary {
        background: linear-gradient(90deg, #636e72, #b2bec3);
        color: white !important;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
    }

    .btn-secondary:hover {
        background: linear-gradient(90deg, #535c60, #a0aeb3);
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    /* ==========================================================================
   Buttons
   ========================================================================== */
    .users.profile-edit .btn-save-changes {
        background: linear-gradient(90deg, #6c5ce7, #0984e3) !important;
        color: white !important;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
    }

    .users.profile-edit .btn-save-changes:hover {
        background: linear-gradient(90deg, #5a4cd6, #0774c3);
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .btn-secondary {
        background: linear-gradient(90deg, #636e72, #b2bec3);
        color: white !important;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
    }

    .btn-secondary:hover {
        background: linear-gradient(90deg, #535c60, #a0aeb3);
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    /* ==========================================================================
       Responsive Design
       ========================================================================== */
    @media (max-width: 768px) {
        .profile-edit-form {
            padding: 20px;
        }

        .form-control {
            font-size: 0.9rem;
            padding: 8px;
        }

        .btn-primary, .btn-secondary {
            padding: 8px 16px;
            font-size: 0.9rem;
        }
    }
</style>

<script>
    // Bootstrap form validation
    (function () {
        'use strict';
        window.addEventListener('load', function () {
            var forms = document.getElementsByClassName('needs-validation');
            Array.prototype.filter.call(forms, function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
