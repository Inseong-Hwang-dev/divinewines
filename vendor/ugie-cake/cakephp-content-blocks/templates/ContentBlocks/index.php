<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ContentBlock> $contentBlocksGrouped
 */

$this->assign('title', 'Content Blocks');
$this->layout = 'admin';

$this->Html->css('ContentBlocks.content-blocks', ['block' => true]);

$slugify = function($text) {
    return preg_replace('/[^A-Za-z0-9-]+/', '-', $text);
}

?>

<div class="contentBlocks index content">
    <div class="content-blocks-header">
        <h3 class="content-blocks-title"><?= __('Content Blocks') ?></h3>
        <div class="content-blocks-actions">
            <div class="quick-links">
                <?php foreach(array_keys($contentBlocksGrouped) as $parent) { ?>
                    <a href="#<?= $slugify($parent) ?>" class="quick-link-item">
                        <?= $parent ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php
    /*
    // TODO: Think of a way to allow this for developers, but not for the actual website. Adding a content block doesn't
    //       mean anything for end users - it needs to be hard coded into a template somewhere to make sense. Perhaps
    //       it can just be guarded behind a DEBUG flag with an appropriate confirm() dialog warning that it needs to
    //       be used in a template after being added...
    echo $this->Html->link(__('New Content Block'), ['action' => 'add'], ['class' => 'button button-outline float-right'])
    */
    ?>

    <?php foreach($contentBlocksGrouped as $parent => $contentBlocks) { ?>
        <div class="content-block-section">
            <h4 class="content-blocks--list-subheading" id="<?= $slugify($parent)?>">
                <?= $parent ?>
            </h4>

            <div class="content-blocks-grid">
                <?php foreach($contentBlocks as $contentBlock) { ?>
                    <div class="content-block-card">
                        <div class="content-block-header">
                            <h5 class="content-blocks--display-name">
                                <?= $contentBlock['label'] ?>
                            </h5>
                        </div>
                        <div class="content-block-body">
                            <p class="content-blocks--description">
                                <?= $contentBlock['description'] ?>
                            </p>
                        </div>
                        <div class="content-block-footer">
                            <?= $this->Html->link(
                                __('Edit'),
                                ['action' => 'edit', $contentBlock->id],
                                ['class' => 'btn btn-primary btn-sm']
                            ) ?>
                            <?php if (!empty($contentBlock->previous_value)): ?>
                                <?= $this->Form->postLink(
                                    __('Restore'),
                                    ['action' => 'restore', $contentBlock->id],
                                    [
                                        'class' => 'btn btn-warning btn-sm',
                                        'confirm' => __("Are you sure you want to restore the previous version for this item?\n{0}/{1}\nNote: You cannot cancel this action!", $contentBlock->parent, $contentBlock->slug)
                                    ]
                                ) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>

<style>
.content-blocks-header {
    margin-bottom: 2rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.content-blocks-title {
    margin-bottom: 1rem;
    color: #2c3e50;
    font-weight: 600;
}

.quick-links {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.quick-link-item {
    padding: 0.5rem 1rem;
    background: #fff;
    border-radius: 20px;
    color: #6c757d;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.quick-link-item:hover {
    background: #007bff;
    color: #fff;
    border-color: #007bff;
}

.content-block-section {
    margin-bottom: 2rem;
}

.content-blocks--list-subheading {
    color: #495057;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
}

.content-blocks-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.content-block-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #e9ecef;
}

.content-block-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.content-block-header {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.content-blocks--display-name {
    margin: 0;
    color: #2c3e50;
    font-size: 1.1rem;
    font-weight: 600;
}

.content-block-body {
    padding: 1rem;
}

.content-blocks--description {
    color: #6c757d;
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.5;
}

.content-block-footer {
    padding: 1rem;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 0.5rem;
}

.btn {
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.btn-primary {
    background: #007bff;
    border-color: #007bff;
    color: #fff;
}

.btn-warning {
    background: #ffc107;
    border-color: #ffc107;
    color: #212529;
}

.btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}
</style>
