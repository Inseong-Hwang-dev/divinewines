<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Coupon $coupon
 */
?>
<div class="container" style="padding-top: 60px; max-width: 700px;">
    <div class="card admin-view mb-4">
        <div class="card-header admin-card-header text-black text-center pt-4 mb-3">
            <h3><?= __('Coupon Details') ?></h3>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-4">Coupon Code</dt>
                <dd class="col-sm-8"><?= h($coupon->coupon_code) ?></dd>

                <dt class="col-sm-4">Discount Percentage</dt>
                <dd class="col-sm-8"><?= h($coupon->discount_amount) ?>%</dd>

                <dt class="col-sm-4">Expiry Date</dt>
                <dd class="col-sm-8"><?= $coupon->expiry_date ? $coupon->expiry_date->format('Y-m-d H:i') : 'No expiry' ?></dd>

                <dt class="col-sm-4">Minimum Purchase</dt>
                <dd class="col-sm-8"><?= $coupon->min_purchase_amount ? '$' . h($coupon->min_purchase_amount) : 'None' ?></dd>

                <dt class="col-sm-4">Maximum Discount</dt>
                <dd class="col-sm-8"><?= $coupon->max_discount_amount ? '$' . h($coupon->max_discount_amount) : 'None' ?></dd>

                <dt class="col-sm-4">Usage Limit</dt>
                <dd class="col-sm-8"><?= $coupon->usage_limit ? h($coupon->usage_limit) : 'Unlimited' ?></dd>

                <dt class="col-sm-4">Times Used</dt>
                <dd class="col-sm-8"><?= h($coupon->times_used) ?></dd>

                <dt class="col-sm-4">Status</dt>
                <dd class="col-sm-8">
                    <span class="badge <?= $coupon->is_active ? 'bg-success' : 'bg-danger' ?>">
                        <?= $coupon->is_active ? 'Active' : 'Inactive' ?>
                    </span>
                </dd>

                <dt class="col-sm-4">Assigned User</dt>
                <dd class="col-sm-8"><?= $coupon->has('user') && $coupon->user ? $this->Html->link($coupon->user->email, ['controller' => 'Users', 'action' => 'view', $coupon->user->id]) : 'Generic Coupon (Any User)' ?></dd>
            </dl>
        </div>
        <div class="card-footer text-end">
            <?= $this->Html->link('<i class="bi bi-pencil"></i> Edit', ['action' => 'edit', $coupon->coupon_id], ['class' => 'btn btn-edit btn-large me-2', 'escape' => false]) ?>
            <?= $this->Form->postLink('<i class="bi bi-trash3"></i> Delete', ['action' => 'delete', $coupon->coupon_id], [
                'class' => 'btn btn-delete btn-large me-2',
                'escape' => false,
                'confirm' => __('Are you sure you want to delete # {0}?', $coupon->coupon_id)
            ]) ?>
            <?= $this->Html->link('<i class="bi bi-arrow-left"></i> Back', ['action' => 'index'], ['class' => 'btn btn-secondary', 'escape' => false]) ?>
        </div>
    </div>
</div>