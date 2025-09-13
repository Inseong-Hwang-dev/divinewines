<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Review $review
 */
?>
<div class="row">
    <?= $this->Html->tag('button', '<i class="bi bi-arrow-left"></i>  Back to List',
        ['type' => 'button',
            'onclick' => 'window.location.href = "'.$this->Url->build(['controller' => 'Reviews', 'action' => 'index']).'"; return false;',
            'class' => 'btn back-btn text-start w-auto']) ?>
</div>

<div class="column column-80">
    <div class="admin-view mt-3 mb-2 mx-auto">
        <!-- Action Buttons -->
        <div class="action-buttons-container">
            <?= $this->Html->link(
                '<i class="bi bi-pencil"></i> ' . __('Edit'),
                ['action' => 'edit', $review->id],
                [
                    'class' => 'btn btn-sm btn-view-replied',
                    'escape' => false,
                    'title' => __('Edit Review')
                ]
            ) ?>

            <?= $this->Form->postLink(
                '<i class="bi bi-trash"></i> ' . __('Delete'),
                ['action' => 'delete', $review->id],
                [
                    'confirm' => __('Are you sure you want to delete # {0}?', $review->id),
                    'class' => 'col btn btn-view-delete',
                    'escape' => false
                ]
            ) ?>
        </div>

        <div class="admin-card-header text-black text-start px-4 pt-4 mb-3">Review #<?= h($review->id) ?></div>

        <div class="card-body">
            <table class="contact-details">
                <tr>
                    <th class="label-col"><?= __('Product') ?></th>
                    <td class="value-col"><?= h($review->product->name) ?></td>
                </tr>
                <tr>
                    <th class="label-col"><?= __('User') ?></th>
                    <td class="value-col"><?= h($review->user->email) ?></td>
                </tr>
                <tr>
                    <th class="label-col"><?= __('Rating') ?></th>
                    <td class="value-col">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?= $i <= $review->rating ? 'filled' : '' ?>">★</span>
                        <?php endfor; ?>
                    </td>
                </tr>
                <tr>
                    <th class="label-col"><?= __('Status') ?></th>
                    <td class="value-col"><?= h($review->status) ?></td>
                </tr>
                <tr>
                    <th class="label-col"><?= __('Created') ?></th>
                    <td class="value-col"><?= h($review->created) ?></td>
                </tr>
                <tr>
                    <th class="label-col"><?= __('Modified') ?></th>
                    <td class="value-col"><?= h($review->modified) ?></td>
                </tr>
            </table>

            <div class="message">
                <strong><?= __('Review Text') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($review->review_text)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>