<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ContactEnquiry $contactEnquiry
 */
//echo $this->Html->css(['cake', 'fonts', 'milligram.min', 'normalize.min']);

?>
<div class="row">
        <?= $this->Html->tag('button', '<i class="bi bi-arrow-left"></i>  Back to List',
            ['type' => 'button',
                'onclick' => 'window.location.href = "'.$this->Url->build(['controller' => 'ContactEnquiries', 'action' => 'index']).'"; return false;',
                'class' => 'btn back-btn text-start w-auto']) ?>
    </div>

    <div class=" column column-80">
        <!-- Contact Details -->
        <div class="admin-view mt-3 mb-2 mx-auto">
            <!-- Action Buttons -->
            <div class="action-buttons-container">

                <?= $this->Html->link(
                    '<i class="bi bi-reply-fill"></i> ' . __('Reply'),
                    'mailto:' . $contactEnquiry->email,
                    [
                        'class' => 'btn btn-sm btn-view-reply',
                        'escape' => false,
                        'title' => __('Reply to {0}', h($contactEnquiry->first_name))
                    ]
                ) ?>

                <?php if($contactEnquiry->replied != 'Replied'): ?>
                    <?= $this->Html->link(
                        '<i class="bi bi-check-circle"></i> Mark as Replied',
                        ['action' => 'changeRepliedStatus', $contactEnquiry->id],
                        [
                            'class' => 'btn btn-sm btn-view-replied',
                            'title' => 'Mark as Replied',
                            'escape'=> false
                        ]
                    ) ?>
                <?php else: ?>
                    <?= $this->Html->link(
                        '<i class="bi bi-x-circle"></i> Mark as Not Replied',
                        ['action' => 'changeRepliedStatus', $contactEnquiry->id],
                        [
                            'class' => 'btn btn-sm btn-view-replied',
                            'title' => 'Mark as Not Replied',
                            'escape'=> false
                        ]
                    ) ?>
                <?php endif; ?>

                <?= $this->Form->postLink(
                    '<i class="bi bi-trash"></i> ' . __('Delete'),
                    ['action' => 'delete', $contactEnquiry->id],
                    [
                        'confirm' => __('Are you sure you want to delete # {0}?', $contactEnquiry->id),
                        'class' => 'col btn btn-view-delete',
                        'escape' => false
                    ]
                ) ?>
            </div>
            <div class="admin-card-header text-black text-start px-4 pt-4 mb-3"><?= h($contactEnquiry->first_name) ?> <?= h($contactEnquiry->last_name) ?></div>

            <div class="card-body">
                <table class="contact-details">
                    <tr>
                        <th class="label-col"><?= __('First Name') ?></th>
                        <td class="value-col"><?= h($contactEnquiry->first_name) ?></td>
                    </tr>
                    <tr>
                        <th class="label-col"><?= __('Last Name') ?></th>
                        <td class="value-col"><?= h($contactEnquiry->last_name) ?></td>
                    </tr>
                    <tr>
                        <th class="label-col"><?= __('Email') ?></th>
                        <td class="value-col"><?= h($contactEnquiry->email) ?></td>
                    </tr>
                    <tr>
                        <th class="label-col"><?= __('Phone Number') ?></th>
                        <td class="value-col"><?= h($contactEnquiry->phone_number) ?></td>
                    </tr>
                    <tr>
                        <th class="label-col"><?= __('Reply Status') ?></th>
                        <td class="value-col"><?= h($contactEnquiry->replied) ?></td>
                    </tr>
                    <tr>
                        <th class="label-col"><?= __('Date Sent') ?></th>
                        <td class="value-col"><?= h($contactEnquiry->date_sent) ?></td>
                    </tr>
                </table>

                <div class="message">
                    <strong><?= __('Message') ?></strong>
                    <blockquote>
                        <?= $this->Text->autoParagraph(h($contactEnquiry->message)); ?>
                    </blockquote>
                </div>
            </div>
        </div>
    </div>
</div>
