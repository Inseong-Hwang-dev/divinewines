<?php
?>
<?php if (!empty($newsletter)): ?>
    <div class="column newsletter-popup">

        <!--        header section-->
        <div class="header-banner">
            <button class="btn close-btn" id="close-newsletter" style="float: right;">Close</button>
            <?= $this->Html->image('newsletter-banner.jpg', ['alt' => 'Newsletter Banner']) ?>
            <h1 class="title"><?= h($newsletter->title) ?></h1>
        </div>
        <p class="edition">#<?= h($newsletter->id)?> Edition</p>
        <div class="date"> <?= $this->Time->format($newsletter->date_published,'EEEE, MMMM dd, yyyy') ?></div>

        <h4 class="subheading pb-2 pt-2"><?= h($newsletter->subheading) ?></h4>
        <hr style="color:#FFFFF0; width: 80%; justify-self: center">
        <div class="content-section">

            <!--            check if there is an image uploaded-->
            <?php if (!empty($newsletter->image)): ?>
                <?= $this->Html->image($newsletter->image, ['alt' => h($newsletter->subheading)]) ?>
            <?php endif; ?>

            <div class="body">
                <p><?= nl2br(h($newsletter->body)) ?></p>
            </div>
        </div>
    </div>
<?php endif;?>
<?= $this->Html->css('newsletter.css') ?>

