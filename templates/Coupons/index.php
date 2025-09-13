<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Coupon[]|\Cake\Collection\CollectionInterface $coupons
 */
?>
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bolder">Coupons</h1>
    </div>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="list-add-new">
                <?= $this->Html->link(
                    '<i class="bi bi-plus-lg"></i>Add Coupon',
                    ['action' => 'add'],
                    ['class'=> "add-button fw-bold", 'escape' => false]
                ) ?>
            </div>
            <h3 class="admin-headings mb-0">Coupons List</h3>
        </div>
    </div>
    <div class="alert alert-dismissible fade show" role="alert">
        <?= $this->Flash->render();?>
    </div>
    <div class="birthday-coupons-section">
        <h4><?= __('Send Birthday Coupons') ?></h4>
        <?= $this->Form->create(null, ['url' => ['action' => 'sendBirthdayCoupons']]) ?>
        <div class="form-group">
            <?= $this->Form->control('month', [
                'type' => 'select',
                'options' => [
                    1 => 'January',
                    2 => 'February',
                    3 => 'March',
                    4 => 'April',
                    5 => 'May',
                    6 => 'June',
                    7 => 'July',
                    8 => 'August',
                    9 => 'September',
                    10 => 'October',
                    11 => 'November',
                    12 => 'December'
                ],
                'label' => 'Select Month',
                'class' => 'form-control'
            ]) ?>
        </div>
        <?= $this->Form->button(__('Send Birthday Coupons'), ['class' => 'button']) ?>
        <?= $this->Form->end() ?>
    </div>
    <div class="table-responsive">
        <table id="CouponsTable" class="display responsive nowrap">
            <thead>
                <tr>
                    <th style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= $this->Paginator->sort('coupon_code', 'Code') ?></th>
                    <th style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= $this->Paginator->sort('discount_amount', 'Discount %') ?></th>
                    <th style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= $this->Paginator->sort('expiry_date', 'Expires') ?></th>
                    <th style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= $this->Paginator->sort('min_purchase_amount', 'Min. Purchase') ?></th>
                    <th style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= $this->Paginator->sort('max_discount_amount', 'Max. Discount') ?></th>
                    <th style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= $this->Paginator->sort('usage_limit', 'Usage Limit') ?></th>
                    <th style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= $this->Paginator->sort('times_used', 'Times Used') ?></th>
                    <th style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= $this->Paginator->sort('is_active', 'Status') ?></th>
                    <th class="actions" style="color: var(--secondary-products-color) !important; font-weight: 600;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coupons as $coupon): ?>
                <tr>
                    <td><?= h($coupon->coupon_code) ?></td>
                    <td><?= h($coupon->discount_amount) ?>%</td>
                    <td><?= $coupon->expiry_date ? $coupon->expiry_date->format('Y-m-d H:i') : 'No expiry' ?></td>
                    <td><?= $coupon->min_purchase_amount ? '$' . h($coupon->min_purchase_amount) : 'None' ?></td>
                    <td><?= $coupon->max_discount_amount ? '$' . h($coupon->max_discount_amount) : 'None' ?></td>
                    <td><?= $coupon->usage_limit ? h($coupon->usage_limit) : 'Unlimited' ?></td>
                    <td><?= h($coupon->times_used) ?></td>
                    <td>
                        <span class="badge <?= $coupon->is_active ? 'bg-success' : 'bg-danger' ?>">
                            <?= $coupon->is_active ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td class="actions">
                        <?= $this->Html->link('<i class="bi bi-eyeglasses"></i>', ['action' => 'view', $coupon->coupon_id], ['class' => 'btn btn-view btn-large', 'title' => 'View','escape'=> false]) ?>
                        <?= $this->Html->link('<i class="bi bi-pencil"></i>', ['action' => 'edit', $coupon->coupon_id], ['class' => 'btn btn-edit btn-large', 'title' => 'Edit','escape'=> false]) ?>
                        <?= $this->Form->postLink(
                            '<i class="bi bi-trash3"></i>',
                            ['action' => 'delete', $coupon->coupon_id],
                            [
                                'class'=>'btn btn-delete btn-large',
                                'title' =>'Delete',
                                'escape'=> false,
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $coupon->coupon_id),
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!--DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#CouponsTable').DataTable({responsive: true});
    });
</script>