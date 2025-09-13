<?php
// Hero Section with Parallax Effect
?>
<div class="about-hero position-relative d-flex align-items-center text-white text-center">
    <div class="about-hero-overlay"></div>
    <div class="container z-1">
        <h1 class="display-2 fw-bold mb-3"><?= $this->ContentBlock->text('about-header'); ?></h1>
        <p class="lead fs-5 mx-auto" style="max-width: 700px;"><?= $this->ContentBlock->text('about-header-message'); ?></p>
    </div>
</div>

<!-- Mission Section -->
<section class="py-5">
    <div class="container px-4 px-lg-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="display-5 fw-bold mb-4"><?= $this->ContentBlock->text('about-body-title'); ?></h2>
                <p class="lead text-muted"><?= $this->ContentBlock->text('about-body-message'); ?></p>

                <div class="icon-box bg-gradient-primary me-3">
                    <i class="bi bi-award fs-4 text-white"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1"><span class="bullet-point">•</span><?= $this->ContentBlock->text('about-body-first-bullet'); ?></h3>
                    <p class="mb-0 text-muted"><?= $this->ContentBlock->text('about-body-first-bullet-message'); ?></p>
                </div>


                <div class="icon-box bg-gradient-primary me-3">
                    <i class="bi bi-globe fs-4 text-white"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1"><span class="bullet-point">•</span><?= $this->ContentBlock->text('about-body-second-bullet'); ?></h3>
                    <p class="mb-0 text-muted"><?= $this->ContentBlock->text('about-body-second-bullet-message'); ?></p>
                </div>

            </div>
            <div class="col-lg-6">
                <!-- Wine-related image for visual appeal -->
                <div class="position-relative text-center">
                    <img class="img-fluid rounded shadow-lg" src="<?= $this->Url->assetUrl('img/about-mission.jpg') ?>" alt="Wine tasting experience" style="max-height:340px; object-fit:cover;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="py-5 bg-light">
    <div class="container px-4 px-lg-5">
        <h2 class="text-center display-5 fw-bold mb-5"><?= $this->ContentBlock->text('about-footer-title'); ?></h2>
        <div class="row g-4">
            <?php
            $values = [
                ['icon' => 'bi-heart', 'title' => 'Passion', 'text' => 'Our love for wine drives everything we do, ensuring an exceptional experience.'],
                ['icon' => 'bi-shield-check', 'title' => 'Integrity', 'text' => 'We maintain the highest standards of quality and authenticity.'],
                ['icon' => 'bi-people', 'title' => 'Community', 'text' => 'We build lasting relationships with customers and winemakers.']
            ];
            foreach ($values as $value): ?>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="icon-box bg-primary text-white mx-auto mb-3">
                                <i class="bi <?= $value['icon'] ?> fs-3"></i>
                            </div>
                            <h5 class="fw-bold"><?= $value['title'] ?></h5>
                            <p class="text-muted"><?= $value['text'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Custom CSS -->
<style>
.about-hero {
    background-image: url('<?= $this->Url->assetUrl('img/sample-pop-up.jpg') ?>');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    min-height: 60vh;
    position: relative;
    z-index: 0;
}
.about-hero-overlay {
    background: rgba(0, 0, 0, 0.6);
    position: absolute;
    inset: 0;
    z-index: 0;
}
.icon-box {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.badge-circle {
    position: absolute;
    top: -10px;
    left: -10px;
    background: #007bff;
    padding: 10px;
    border-radius: 50%;
}
.bullet-point {
    color: #007bff;
    font-size: 1.2em;
    margin-right: 0.5em;
    vertical-align: middle;
}
</style>

<head>
    <!-- ... existing head content ... -->
    <?= $this->Html->css('landing.css') ?>
</head>
