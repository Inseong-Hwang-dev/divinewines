<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order[]|\Cake\Collection\CollectionInterface $orders
 */
?>
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bolder">All Orders</h1>
    </div>

    <div class="dropdown-filter">
        <div class="filter-row">
            <p class="fw-bold filter-col">Filter By:</p>
            <div class="filter-col">
                <p class="filter-label">Status:</p>
                <?= $this->Form->control('status', [
                    'id' => 'status-filter',
                    'type' => 'select',
                    'label' => false,
                    'options' => ['' => 'All', 'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled'],
                    'class' => 'status-filter-dropdown',
                    'value' => isset($statusFilter) ? $statusFilter : ''
                ]) ?>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="OrdersTable" class="display responsive nowrap">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('order_id') ?></th>
                    <th><?= $this->Paginator->sort('user_id') ?></th>
                    <th><?= $this->Paginator->sort('total_amount') ?></th>
                    <th><?= $this->Paginator->sort('shipping_state') ?></th>
                    <th><?= $this->Paginator->sort('shipping_country') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th class="actions" style="color: var(--text-label-color); font-weight: 500"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr class="orders-row" data-status="<?= $order->status ?>">
                    <td><?= $this->Number->format($order->order_id) ?></td>
                    <td><?= $this->Html->link(h($order->user->email), ['action' => 'userOrders', $order->user_id], ['class'=> 'list-name']) ?></td>
                    <td><?= $this->Number->currency($order->total_amount) ?></td>
                    <td><?= h($order->shipping_state) ?></td>
                    <td><?= h($order->shipping_country) ?></td>
                    <td><?= $this->Time->format($order->created, 'dd-MM-yyyy hh:mm a') ?></td>
                    <td>
                        <span class="status <?= strtolower(h($order->status)) ?>"><?= h($order->status) ?></span>
                    </td>
                    <td class="actions">
                        <?= $this->Html->link(
                            '<i class="bi bi-eyeglasses me-2"></i>  View/update status',
                            ['action' => 'adminView', $order->order_id],
                            ['class' => 'btn btn-view btn-large', 'title' => 'View', 'escape' => false]
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
        $('#OrdersTable').DataTable({
            responsive: true,
            order: [[0, 'desc']] // Sort by created date descending by default
        });

        function applyFilters() {
            let statusFilter = $('#status-filter').val();

            $('.orders-row').each(function () {
                let orderStatus = $(this).data('status')?.toString().trim();
                let matchStatus = (statusFilter === "" || statusFilter === orderStatus);
                $(this).toggle(matchStatus);
            });
        }

        $('#status-filter').on('input change', function () {
            applyFilters();
        });

        // Apply filters on page load
        applyFilters();

        // Prevent automatic scrolling when page reloads
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
    });
</script>

<style>
    .status{
        display: inline-block;
        padding: 2px 10px;
        border-radius: 40px;
        font-size: 0.85rem;
        color: white;
    }
    .status.pending {
        background: lightgrey;
    }

    .status.processing {
        background: #E1AD41FF;
    }

    .status.shipped {
        background: var(--secondary-products-light);
    }
    .status.delivered {
        background: var(--secondary-products-color);
    }

    .status.cancelled {
        background: var(--primary-products-color);
    }
</style>
