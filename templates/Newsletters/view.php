<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Newsletter $newsletter
 */
?>
<div class="row">
    <div>
        <?= $this->Html->tag(
            'button',
            '<i class="bi bi-arrow-left"></i>  Back to List',
            ['type' => 'button',
                'onclick' => 'window.location.href = "' . $this->Url->build(['controller' => 'Newsletters', 'action' => 'index']) . '"; return false;',
                'class' => 'btn back-btn text-start w-auto'],
        ) ?>
    </div>
    <div class="newsletter-view-buttons d-flex align-items-end justify-content-end">
        <?= $this->Html->link('<i class="bi bi-pencil me-2"></i> Edit Newsletter',
            ['action' => 'edit', $newsletter->id],
            ['class' => 'btn btn-edit btn-large', 'title' => 'Edit', 'escape'=>false] ) ?>
    </div>
    <div class="alert alert-dismissible fade show" role="alert">
        <?= $this->Flash->render('viewNewsletterFeedback');?>
    </div>

<!--    newsletter section-->
    <div class="column newsletter-popup">

        <!--        header section-->
        <div class="header-banner">
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
</div>

<style>
    .newsletter-popup {
        width:80%;
        max-width: 1380px;
        height: auto;
        text-align: center;
        margin: 20px auto;
        padding: 1px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        background: var(--secondary-products-light);

    }

    /*banner section*/

    .header-banner {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        max-width: 1380px;
        height: 200px;
        overflow: hidden;
        margin: 0 auto 20px auto;
        border-top-right-radius: 8px;
        border-top-left-radius: 8px;

    }
    .header-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .header-banner .title {
        position: absolute;
        color: #FFFFF0;
        text-shadow: 0 0 10px rgba(0,0,0,0.7);
        text-align: center;
        padding: 10px;
        margin: 0;
        font-family: "Gwendolyn", cursive;
        font-size: 120px;
    }

    .header-banner .close-btn{
        position: absolute;
        border: 1px solid var(--wine-red);
        border-radius: 8px;
        background: white;
        z-index: 9999;
        top: 10px;
        right: 10px;
        font-family: Comfortaa, sans-serif;
    }


    .subheading {
        margin-top: 10px;
        overflow: hidden;
        color: #FFFFF0;
        font-family: "Lora", sans-serif;
        font-size: 36px;
    }

    /*content body section*/

    .content-section {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: flex-start;
        width: 100%
        max-width: 1000px;
        margin: auto;
        padding: 20px;
        gap: 20px;
    }

    .content-section img {
        max-width: 300px;
        width: 100%;
        height: auto;
        flex-shrink: 0;
    }

    .date, .edition{
        text-align: start;
        color: #FFFFF0;
        font-family: "Lora", sans-serif;
        padding-left:20px

    }
    .body {
        flex: 1 1 300px;
        min-width: 200px;
        text-align: left;
        color: #FFFFF0;
        font-family: "Comfortaa", sans-serif;
    }

    @media (max-width: 768px) {
        .header-banner .title {
            font-size: 80px;
        }
    }
</style>


