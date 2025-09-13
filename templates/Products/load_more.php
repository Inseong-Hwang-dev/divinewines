<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Product> $products
 */
?>

<?php foreach ($products as $product): ?>
    <div class="product-card" data-product-id="<?= h($product->id) ?>">
        <div class="image-container">
            <?php if (!empty($product->image)): ?>
                <?= $this->Html->link(
                    $this->Html->image($product->image, ['alt' => h($product->name)]),
                    ['action' => 'view', $product->id],
                    ['escape' => false, 'class' => 'product-image-link']
                ) ?>
            <?php else: ?>
                <?= $this->Html->link(
                    $this->Html->image('sample.jpg', ['alt' => h($product->name), 'class' => 'product-image blurred']),
                    ['action' => 'view', $product->id],
                    ['escape' => false, 'class' => 'product-image-link']
                ) ?>
                <div class="no-image-overlay">No image available</div>
            <?php endif; ?>
            <?php if ($product->stock_quantity == 0): ?>
                <div class="out-of-stock-label">Out of Stock</div>
            <?php endif; ?>
        </div>
        <div class="product-info">
            <h3 class="product-name"><?= h($product->name) ?></h3>
            <div class="product-price">$<?= number_format($product->price, 2) ?></div>
            <div class="quick-view-container">
                <button class="quick-view-btn" onclick="openQuickView(<?= h($product->id) ?>)">Quick View</button>
            </div>
        </div>
    </div>

    <!-- Quick View Modal -->
    <div id="quick-view-modal-<?= h($product->id) ?>" class="quick-view-modal" tabindex="-1" role="dialog" aria-labelledby="quick-view-modal-label-<?= h($product->id) ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quick-view-modal-label-<?= h($product->id) ?>">
                        <?= h($product->name) ?>
                    </h5>
                    <button type="button" class="close" onclick="closeQuickView(<?= h($product->id) ?>)" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?= $this->element('Products/quick_view', ['product' => $product]) ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?> 