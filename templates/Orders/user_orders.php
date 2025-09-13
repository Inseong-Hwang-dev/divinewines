<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order[] $orders
 * @var \App\Model\Entity\User $user
 */
?>
<div class="user-orders admin-view content">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bolder">Orders for <?= h($user->first_name . ' ' . $user->last_name) ?></h1>
            <p class="text-muted"><?= h($user->email) ?></p>
        </div>

        <!-- User Info Summary -->
        <div class="user-info-summary mb-5">
            <div class="summary-card">
                <i class="bi bi-person"></i>
                <h3>Customer</h3>
                <p><?= h($user->first_name . ' ' . $user->last_name) ?></p>
            </div>
            <div class="summary-card">
                <i class="bi bi-envelope"></i>
                <h3>Email</h3>
                <p><?= h($user->email) ?></p>
            </div>
            <div class="summary-card">
                <i class="bi bi-basket"></i>
                <h3>Total Orders</h3>
                <p><?= count($orders) ?></p>
            </div>
            <div class="summary-card">
                <i class="bi bi-trophy"></i>
                <h3>Loyalty Points</h3>
                <p><?= $user->loyalty_points === null ? '0' : $this->Number->format($user->loyalty_points) ?></p>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="orders-list-section">
            <h3 class="mb-4">Order History</h3>

            <?php if (count($orders) > 0): ?>
                <div class="table-responsive">
                    <table class="orders-table">
                        <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <span class="order-id">#<?= h($order->order_id) ?></span>
                                </td>
                                <td>
                                    <span class="order-date"><?= h($order->created->format('Y-m-d H:i')) ?></span>
                                </td>
                                <td>
                                    <span class="order-amount"><?= $this->Number->currency($order->total_amount) ?></span>
                                </td>
                                <td class="actions">
                                    <?= $this->Html->link(
                                        '<i class="bi bi-eye"></i> View',
                                        ['action' => 'adminView', $order->order_id],
                                        ['class' => 'btn btn-sm btn-view', 'escape' => false]
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-orders-message">
                    <i class="bi bi-bag-x"></i>
                    <p>This user has not placed any orders yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Back Buttons -->
        <div class="action-buttons mt-5">
            <?= $this->Html->link(
                '<i class="bi bi-arrow-left"></i> Back to User',
                ['controller' => 'Users', 'action' => 'view', $user->id],
                ['class' => 'btn btn-back', 'escape' => false]
            ) ?>

            <?= $this->Html->link(
                '<i class="bi bi-list"></i> All Orders',
                ['action' => 'adminIndex'],
                ['class' => 'btn btn-all-orders', 'escape' => false]
            ) ?>
        </div>
    </div>
</div>

<?= $this->Html->css('admin-table.css') ?>

<style>
    /* ==========================================================================
       User Info Summary Cards
       ========================================================================== */
    .user-info-summary {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 30px;
    }

    .summary-card {
        flex: 1;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease;
    }

    .summary-card:hover {
        transform: translateY(-5px);
    }

    .summary-card i {
        font-size: 2rem;
        color: var(--secondary-products-color);
        margin-bottom: 10px;
    }

    .summary-card h3 {
        font-size: 1rem;
        color: var(--product-text-color);
        margin-bottom: 5px;
    }

    .summary-card p {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-products-color);
        margin: 0;
    }

    /* ==========================================================================
       Orders List Table
       ========================================================================== */
    .orders-list-section {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
    }

    .orders-table th {
        background-color: #f8f9fa;
        padding: 12px;
        text-align: left;
        font-weight: 500;
        color: #1e2125;
    }

    .orders-table td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
    }

    .order-id {
        font-weight: 500;
        color: var(--primary-products-color);
    }

    .order-date {
        color: var(--product-text-color);
    }

    .order-amount {
        font-weight: bold;
        color: var(--primary-products-color);
    }

    .order-status {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-completed {
        background-color: rgba(46, 204, 113, 0.2);
        color: #27ae60;
    }

    .status-pending {
        background-color: rgba(243, 156, 18, 0.2);
        color: #f39c12;
    }

    .status-processing {
        background-color: rgba(52, 152, 219, 0.2);
        color: #3498db;
    }

    .status-cancelled {
        background-color: rgba(231, 76, 60, 0.2);
        color: #e74c3c;
    }

    .btn-view {
        background-color: var(--secondary-products-color);
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .btn-view:hover {
        background-color: var(--primary-products-color);
        transform: translateY(-2px);
    }

    /* No Orders Message */
    .no-orders-message {
        text-align: center;
        padding: 40px 0;
    }

    .no-orders-message i {
        font-size: 3rem;
        color: #e0e0e0;
        margin-bottom: 15px;
    }

    .no-orders-message p {
        font-size: 1.2rem;
        color: #888;
    }

    /* ==========================================================================
       Action Buttons
       ========================================================================== */
    .action-buttons {
        margin: 30px;
        display: flex;
        justify-content: space-between;
    }

    .btn-back {
        background-color: var(--secondary-products-color);
        color: white !important;
        padding: 10px 20px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background-color: var(--primary-products-color);
        transform: translateY(-2px);
    }

    .btn-all-orders {
        background-color: #6c757d;
        color: white !important;
        padding: 10px 20px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .btn-all-orders:hover {
        background-color: #495057;
        transform: translateY(-2px);
    }

    /* ==========================================================================
       Responsive Design
       ========================================================================== */
    @media (max-width: 768px) {
        .user-info-summary {
            flex-direction: column;
        }

        .summary-card {
            margin-bottom: 15px;
        }

        .orders-table {
            display: block;
            overflow-x: auto;
        }

        .action-buttons {
            flex-direction: column;
            gap: 10px;
        }

        .btn-back, .btn-all-orders {
            width: 100%;
            text-align: center;
        }
    }
</style>
