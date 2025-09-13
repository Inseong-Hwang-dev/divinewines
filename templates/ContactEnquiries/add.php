<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ContactEnquiry $contactEnquiry
 * @var \App\Model\Entity\ContactEnquiry $recaptcha_user
 */

?>
<link href="https://fonts.googleapis.com/css2?family=Gwendolyn:wght@400;700&display=swap" rel="stylesheet">

<!--Google widget script-->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<div class="container mt-6 mb-5">

<!--    header section for contact page-->
    <div class="header-image">
        <?= $this->Html->image('contact-banner.jpg', ['alt' => 'Contact Form Banner']) ?>
    </div>
    <div class="contact-header">
        <h1 class="text-center pt-4"><?= $this->ContentBlock->text('contact-us-title'); ?></h1>
    </div>

<!--    render the feedback message when successful or unsuccessful submission of form-->
    <div class="alert alert-dismissible fade show pb-1" role="alert">
        <?= $this->Flash->render('contactFeedback');?>
    </div>

<!--    information + contact form section-->
    <div class="row justify-content-center">

        <!--        information section-->
        <div class="col-12 col-md-6 d-flex justify-content-center align-items-md-center mb-4 mb-md-0">
            <div class="info-box ps-md-5 mt-3 mt-md-0">
                <h3 class="mb-3">Meet Us</h3>
                <div class="contact-info mb-4">
                    <div class="info-item info-label">
                        <strong><i class="bi bi-telephone"></i> Phone:</strong>
                        <span>(123) 456-7890</span>
                    </div>
                    <div class="info-item info-label">
                        <strong><i class="bi bi-envelope"></i> Email:</strong>
                        <span>support@example.com</span>
                    </div>
                    <div class="info-item info-label">
                        <strong><i class="bi bi-geo-alt"></i> Address:</strong>
                        <span>123 Vineyard Road, Melbourne VIC</span>
                    </div>
                    <div class="info-item info-label">
                        <strong><i class="bi bi-clock"></i> Hours:</strong>
                        <span>Mon–Fri: 9am–5pm<br>Sat: 10am–3pm</span>
                    </div>
                </div>

<!--                google maps embed-->
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3147.8272905619924!2d145.13179685381834!3d-37.91109939985095!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad66acbf64673b9%3A0xfaf9b169a587104!2sMonash%20University%20Clayton%20Campus!5e0!3m2!1sen!2sau!4v1747148456088!5m2!1sen!2sau"
                        width="500"
                        height="400"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">

                </iframe>
            </div>
        </div>

        <!--                contact form section-->
        <div class="col-md-6">
            <div class="card-contact-form mt-5 mb-2 shadow p-4 rounded border">
                <div class="contact-card-header text-black text-center pt-1 pb-1 mb-3">
                    <h4><?= __(' _______ ') ?></h4>
<!--                    <p class="text-center pb-1">--><?php //= $this->ContentBlock->text('contact-us-message'); ?><!--</p>-->
                </div>
                <div class="card-body-form">
                    <?= $this->Form->create($contactEnquiry) ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $this->Form->control('first_name', ['label' => 'First Name *', 'class' => 'form-control', 'required'=>true,
                                'pattern' =>  '[A-Za-z\-]+',
                                'title' => 'Only alphabetic characters and hyphens are allowed']); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $this->Form->control('last_name', ['label' => 'Last Name', 'class' => 'form-control',
                                'pattern' =>  '[A-Za-z\-]+',
                                'title' => 'Only alphabetic characters and hyphens are allowed']); ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <?= $this->Form->control('email', ['label' => 'Email *', 'class' => 'form-control', 'type'=> 'email','required' =>true]); ?>
                        <?= $this->Form->error('email') ?> <!-- display error from backend -->
                    </div>
                    <div class="mb-3">
                        <?= $this->Form->control('phone_number', [
                            'label' => 'Phone Number', 
                            'class' => 'form-control',
                            'placeholder' => 'e.g. 01234356789',
                            'type' => 'tel',
                            'maxlength' => '10',
                            'pattern' => '[0-9]{10}',
                            'inputmode' => 'numeric'
                        ]); ?>
                    </div>
                    <div class="mb-3">
                        <?= $this->Form->control('message', ['label' => 'Message *', 'class' => 'form-control', 'placeholder'=> 'Your Message', 'required'=>true]);?>
                        <?= $this->Form->error('message') ?> <!-- display error from backend -->

                    </div>

                    <!-- Google reCAPTCHA widget -->
                    <div class="container-recaptcha mb-3">
                        <?= $this->Recaptcha -> display() ?>
                    </div>

                    <div class="text-center">
                        <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-contact w-100']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>

    </div>
</div>

<style>

    /*header section*/

    .header-image {
        position: relative;
        width: 100%;
        max-width: 1380px;
        height: 200px;
        margin: 0 auto 20px auto;
        display: inline-block;
        overflow: hidden;
        margin-top: 20px;
    }

    .header-image img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }


    .contact-header h1{
        font-family: "Gwendolyn", cursive;
        font-weight: 700;
        font-size: 80px;
        /*color: #e2e3e5;*/
        text-transform: none;
    }

    /*information section*/
    .info-box{
        font-family: Comfortaa, sans-serif;
    }
    .info-box iframe {
        width: 100%;
        height: auto;
        aspect-ratio: 4 / 3; /* maintains width-to-height ratio */
        max-width: 100%;
    }

    .contact-info{
        display: flex;
        flex-direction: column;
        gap: 15px;
        font-size: 18px;
    }

    .info-item{
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .info-label strong{
        display: inline-block;
        transition: transform 0.3s ease;
    }

    .info-label:hover strong{
        transform: translateY(-5px); /* Moves up 5px */
    }

    .info-item strong {
        min-width: 100px;
    }

    .info-item i {
        color: var(--wine-red);
    }

    /*contact form section*/
    .contact-card-header {
        font-family: 'Comfortaa', sans-serif
    }
    .card-body-form{
        font-family: 'Comfortaa', sans-serif

    }

    .card-contact-form {

        position: relative;
        display: flex;
        flex-direction: column;
        min-width: 0;
        height: var(--bs-card-height);
        word-wrap: break-word;
        background-color: #A4BA8EFF;
        background-clip: border-box;
        border: var(--bs-card-border-width) solid #6F8759FF;
        border-radius: 8px
        font-family: Comfortaa, sans-serif ;
    }

    .form-control:focus {
        border-color: #6F8759FF;
        box-shadow: 0 0 0 0.2rem rgba((111, 135, 89, 1), 0.25);
    }

    input.form-control, select.form-control, textarea.form-control {
        border: 2px solid #dddddd ; /* Adds stronger styling */
        border-radius: 8px;
    }

    input.form-control:focus, select.form-control:focus, textarea.form-control:focus {
        border: 3px solid #6F8759FF !important;
        outline: none;
        box-shadow: 0 0 10px rgb(139, 149, 132); /* Optional glow effect */
    }

    .btn-contact {
        --bs-btn-color: #fff;
        --bs-btn-bg: #c6c6c6;
        --bs-btn-border-color: #c6c6c6;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: var(--wine-red);
        --bs-btn-hover-border-color: var(--wine-red);
        --bs-btn-focus-shadow-rgb: 225, 83, 97;
        --bs-btn-active-color: #fff;
        --bs-btn-active-bg:var(--wine-red);
        --bs-btn-active-border-color: var(--wine-red);
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #fff;
        --bs-btn-disabled-bg: #c6c6c6;
        --bs-btn-disabled-border-color: #c6c6c6;
        color: black;
    }

    /*media responsive*/
    @media (max-width: 768px) {
        .info-box {
            padding-left: 0;
            padding-right: 0;
        }

        .contact-info {
            font-size: 16px;
            gap: 10px;
        }

        .contact-header h1 {
            font-size: 48px;
        }

        .info-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .info-item strong {
            min-width: auto;
        }
    }

</style>



