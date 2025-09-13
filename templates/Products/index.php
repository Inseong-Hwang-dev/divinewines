<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Product> $products
 */
?>
<div class="container pb-3">
    <!-- top products section -->
    <div class="top-products-section">
        <h3 class="admin-headings">Current Top Products</h3>
        <div class="top-product-row">
            <?php foreach ($topProducts as $product): ?>
                <div class="top-product-item">
                    <?= $this->Html->image($product->image ?? 'default.jpg', ['alt' => h($product->name), 'class' => 'top-product-img']) ?>
                    <div class="top-product-info">
                        <i class="bi bi-droplet me-3 fs-4"></i><span><strong><?= h($product->name) ?></strong></span>
                        <?= $this->Html->tag('hr','',[ 'class'=>'top-product-div']) ?>
                        <i class="bi bi-basket me-3 fs-4"></i><span><?= h($product->total_sold) ?> sold</span>
                        <?= $this->Html->tag('hr','',['class'=>'top-product-div']) ?>
                        <i class="bi bi-box2 me-3 fs-4"></i><span><?= h($product->stock_quantity) ?> bottles in stock</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?= $this->Html->tag('hr','',['style' => 'width: 80%; border: 1px solid #ccc; margin: 0 auto;']) ?>
    </div>
    <!--Products table section-->
    <h3 class="admin-headings pt-3">Products List</h3>
    <!--    render the feedback message for any successful crud functions-->
    <div class="alert alert-dismissible fade show" role="alert">
        <?= $this->Flash->render('productFeedback');?>
    </div>
    <div class="dropdown-filter">
        <div class="filter-row">
            <div class="list-add-new">
                <?= $this->Html->link(
                    '<i class="bi bi-plus-lg"></i>Add New Product',
                    ['action' => 'add'],
                    ['class'=> "add-button", 'escape' => false],
                ) ?>
            </div>
            <p class="fw-bold filter-col">Filters:</p>
            <div class="filter-col">
                <p class="filter-label">Stock Quantity:</p>
                <?= $this->Form->control('stock_quantity', [
                    'id' => 'stock-filter',
                    'type' => 'select',
                    'label' => false,
                    'options' => ['' => 'All', '1' => 'Low Stock', '2' => 'Out of Stock'],
                    'class' => 'stock-filter-dropdown',
                    'value' => isset($stockFilter) ? $stockFilter : ''
                ]) ?>
            </div>
            <div class="filter-col">
                <p class="filter-label">Status:</p>
                <?= $this->Form->control('status', [
                    'id' => 'status-filter',
                    'type' => 'select',
                    'label' => false,
                    'options' => ['' => 'All', '1' => 'Listed', '2' => 'Unlisted'],
                    'class' => 'status-filter-dropdown',
                    'value' => isset($statusFilter) ? $statusFilter : ''
                ]) ?>
            </div>
            <div class="filter-col">
                <p class="filter-label">Style:</p>
                <?= $this->Form->control('style', [
                    'id' => 'style-filter',
                    'type' => 'select',
                    'label' => false,
                    'options' =>  ['' => 'All'] + $wineStyles,
                    'class' => 'style-filter-dropdown',
                    'value' => isset($styleFilter) ? $styleFilter : ''
                ]) ?>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table id="ProductsTable" class="display responsive nowrap">
            <thead>
            <tr>
                <th><?= $this->Paginator->sort('name') ?></th>
                <th><?= $this->Paginator->sort('brand') ?></th>
                <th><?= $this->Paginator->sort('region') ?></th>
                <th><?= $this->Paginator->sort('style') ?></th>
                <th><?= $this->Paginator->sort('price') ?></th>
                <th><?= $this->Paginator->sort('stock_quantity') ?></th>
                <th><?= $this->Paginator->sort('total_sold') ?></th>
                <th><?= $this->Paginator->sort('status') ?></th>
                <th class="actions" style="color: var(--text-label-color); font-weight: 500"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr class="products-row" data-stock="<?= h($product->stock_quantity) ?>" data-status="<?= h($product->status) ?>" data-style="<?= h($product->wine_style->id ?? '') ?>">

                    <td><?= $this->Html->link(h($product->name), ['action' => 'adminView', $product->id], ['class'=> 'list-name']) ?></td>
                    <td><?= h($product->brand) ?></td>
                    <td><?= h($product->region) ?></td>
                    <td><?= h($product->wine_style->name ?? 'N/A') ?></td>
                    <td><?= $this->Number->format($product->price, ['places' => 2, 'before' => '$']) ?></td>

                    <td class="<?= $product->stock_quantity === 0 ? 'out-of-stock' : ($product->stock_quantity < 10 ? 'low-stock' : '') ?>"
                        data-order="<?= h($product->stock_quantity) ?>">
                        <?= $product->stock_quantity === 0 ? 'Out of Stock' : $this->Number->format($product->stock_quantity) ?>
                    </td>
                    <td><?= $this->Number->format($product->total_sold, ['after' => ' sold']) ?></td>

                    <?php if(h($product->status) === 'Listed'):?>
                        <td class="table-status"><?= h($product->status) ?></td>
                    <?php else: ?>
                        <td><?= h($product->status) ?></td>
                    <?php endif;?>
                    <td class="actions">
                        <?= $this->Html->link('<i class="bi bi-eyeglasses"></i>', ['action' => 'adminView', $product->id], ['class' => 'btn btn-view btn-large', 'title' => 'View','escape'=> false]) ?>

                        <?php if($product->status ==='Listed'):
                            echo $this->Html->link('<i class="bi bi-eye"></i>', ['action' => 'changeStatus', $product->id], ['class' => 'btn btn-status-listed btn-large',
                                'title'=>'Change Status',
                                'escape' => false]);
                            ?>
                        <?php elseif($product->status ==='Unlisted'):
                            echo $this->Html->link('<i class="bi bi-eye-slash"></i>', ['action' => 'changeStatus', $product->id], ['class' => 'btn btn-status-unlisted btn-large',
                                'title'=>'Change Status',
                                'escape' => false]);
                        endif;
                        ?>
                        <?= $this->Html->link('<i class="bi bi-pencil"></i>', ['action' => 'edit', $product->id], ['class' => 'btn btn-edit btn-large', 'title' => 'Edit', 'escape'=>false] ) ?>
                        <?= $this->Form->postLink(
                            __('<i class="bi bi-trash3"></i>'),
                            ['action' => 'delete', $product->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete {0}?', $product->name),
                                'class' => 'btn btn-delete btn-large',
                                'title' => 'Delete',
                                'escape' => false
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.dataTables.min.css">
<script src="https://cdn.datatables.net/2.3.0/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.4/css/responsive.dataTables.min.css">


<script>
    $(document).ready(function() {

        //use datatables
        $('#ProductsTable').DataTable(
            {     responsive: {
                    details: {
                        type: 'inline',
                        target: 0
                    }
                },
                columnDefs: [
                    {
                        targets: 0,
                        className: 'all'
                    },
                    {
                        targets: [1, 2, 3, 4, 5, 6, 7, 8],
                        className: 'min-tablet'
                    }
                ],

                autoWidth: false,
                ordering: true,
                stateSave: true,
                responsive:true,
                drawCallback: function() {
                    // Restore scroll position after operations
                    var scrollPos = $(window).data('scrollPos') || 0;
                    $('html, body').stop().animate({scrollTop: scrollPos}, 100);

                },
                initComplete: function() {
                    // Apply filters once DataTable is initialized
                    applyFilters();
                },

            }

        );

        //javascript filter

        function applyFilters() {
            previousScrollPosition = $(window).scrollTop();

            let stockFilter = $('#stock-filter').val();
            let statusFilter = $('#status-filter').val();
            let styleFilter = $('#style-filter').val()?.toString().trim();
            console.log('Selected Style Filter:', styleFilter);

            $('.products-row').each(function () {
                let stockQuantity = parseInt($(this).data('stock'),10) //get stock from data
                let status = $(this).data('status')?.toString().trim(); //get status from data
                let style = $(this).data('style')?.toString().trim(); //get wine_style_id from data

                let matchStock = (stockFilter === "" ||
                    (stockFilter === "1" && stockQuantity <= 10) || (stockFilter === "2" && stockQuantity === 0));

                let matchStatus = (statusFilter === "" ||
                    (statusFilter === "1" && status === "Listed") || (statusFilter === "2" && status === "Unlisted"));

                // Compare wine_style_id directly since we're storing the ID in data-style attribute
                let matchStyle = (styleFilter === "" || styleFilter === style);

                $(this).toggle(matchStock && matchStatus && matchStyle);
            });
        }

        $('#stock-filter, #status-filter, #style-filter').on('input change', function () {
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



