<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Coupon $coupon
 * @var string[]|\Cake\Collection\CollectionInterface $users
 */
?>
<div class="container" style="padding-top: 60px; max-width: 700px;">
    <div class="card admin-view mb-4">
        <div class="card-header admin-card-header text-black text-center pt-4 mb-3">
            <h3><?= __('Edit Coupon') ?></h3>
        </div>
        <div class="card-body">
            <?= $this->Form->create($coupon) ?>
            <div class="mb-3">
                <?= $this->Form->control('coupon_code', ['class' => 'form-control', 'label' => 'Coupon Code']) ?>
            </div>
            <div class="mb-3">
                <?= $this->Form->control('discount_amount', ['class' => 'form-control', 'label' => 'Discount Percentage (%)']) ?>
            </div>
            <div class="mb-3">
                <?= $this->Form->control('expiry_date', ['class' => 'form-control', 'type' => 'datetime-local', 'label' => 'Expiry Date (Optional)']) ?>
            </div>
            <div class="mb-3">
                <?= $this->Form->control('min_purchase_amount', ['class' => 'form-control', 'label' => 'Minimum Purchase Amount (Optional)']) ?>
            </div>
            <div class="mb-3">
                <?= $this->Form->control('max_discount_amount', ['class' => 'form-control', 'label' => 'Maximum Discount Amount (Optional)']) ?>
            </div>
            <div class="mb-3">
                <?= $this->Form->control('usage_limit', ['class' => 'form-control', 'label' => 'Usage Limit (Optional)']) ?>
            </div>
            <div class="mb-3">
                <?= $this->Form->control('is_active', ['class' => 'form-check-input', 'type' => 'checkbox', 'label' => 'Active']) ?>
            </div>
            <div class="mb-3">
                <?= $this->Form->control('user_id', ['options' => $users, 'empty' => 'Generic Coupon (Any User)', 'class' => 'form-select', 'label' => 'User (optional)']) ?>
            </div>
            <div class="text-end">
                <?= $this->Form->button('<i class="bi bi-pencil"></i> Update Coupon', ['class' => 'btn btn-edit', 'escapeTitle' => false]) ?>
                <?= $this->Html->link('<i class="bi bi-arrow-left"></i> Back', ['action' => 'index'], ['class' => 'btn btn-secondary ms-2', 'escape' => false]) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
