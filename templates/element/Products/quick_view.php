<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Product $product
 */
?>

<!-- Quick View Modal Content -->
<div class="quick-view-content">
    <div class="product-image-section">
        <?php if (isset($product->image) && $product->image): ?>
            <?= $this->Html->image($product->image, ['alt' => h($product->name), 'class' => 'product-image']) ?>
        <?php else: ?>
            <?= $this->Html->image('sample.jpg', ['alt' => h($product->name), 'class' => 'product-image blurred']) ?>
            <div class="no-image-overlay">No image available</div>
        <?php endif; ?>
    </div>

    <div class="product-info-section">
        <div class="product-header">
            <h1 class="product-name"><?= h($product->name) ?></h1>
            <div class="product-price">$<?= number_format($product->price, 2) ?></div>
        </div>

        <div class="product-meta">
            <div class="meta-item">
                <span class="meta-label"><i class="bi bi-calendar"></i> Vintage:</span>
                <span class="meta-value"><?= h($product->vintage) ?: 'N/A' ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label"><i class="bi bi-droplet"></i> Alcohol Content:</span>
                <span class="meta-value"><?= h($product->alcohol_content) ? h($product->alcohol_content) . '%' : 'N/A' ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label"><i class="bi bi-geo-alt"></i> Region:</span>
                <span class="meta-value"><?= h($product->region) ?: 'N/A' ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label"><i class="bi bi-tag"></i> Style:</span>
                <span class="meta-value">
                    <?php // Show wine style name if available, otherwise show N/A ?>
                    <?= h($product->wine_style ? $product->wine_style->name : 'N/A') ?>
                </span>
            </div>
            <div class="meta-item">
                <span class="meta-label"><i class="bi bi-box"></i> Stock:</span>
                <span class="meta-value"><?= h($product->stock_quantity) ?> available</span>
            </div>
            <div class="meta-item">
                <span class="meta-label"><i class="bi bi-archive"></i> Volume:</span>
                <span class="meta-value">
                    <?php
                    if ($product->volume) {
                        switch ($product->volume) {
                            case '0.375':
                                echo '375 ml (Half)';
                                break;
                            case '0.750':
                                echo '750 ml (Standard)';
                                break;
                            case '1.5':
                                echo '1.5 L (Magnum)';
                                break;
                            case '3.0':
                                echo '3 L (Double Magnum)';
                                break;
                            case '4.5':
                                echo '4.5 L (Jeroboam)';
                                break;
                            default:
                                echo h($product->volume) . ' L';
                        }
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </span>
            </div>
            <div class="meta-item">
                <span class="meta-label"><i class="bi bi-shop"></i> Brand:</span>
                <span class="meta-value"><?= h($product->brand) ?: 'N/A' ?></span>
            </div>
        </div>

        <?php if ($product->stock_quantity > 0): ?>
            <div class="add-to-cart-form">
                <?= $this->Form->create(null, [
                    'url' => ['controller' => 'CartItems', 'action' => 'add'],
                    'id' => 'add-to-cart-form-' . $product->id,
                    'class' => 'add-to-cart',
                    'data-handled' => 'true'
                ]) ?>
                <?= $this->Form->hidden('product_id', ['value' => $product->id]) ?>
                <?= $this->Form->hidden('_csrfToken', ['value' => $this->request->getAttribute('csrfToken')]) ?>

                <div class="quantity-controls">
                    <button type="button" class="quantity-btn decrease" data-input-id="product_quantity_<?= $product->id ?>">-</button>
                    <?= $this->Form->number('product_quantity', [
                        'value' => 1,
                        'min' => 1,
                        'max' => $product->stock_quantity,
                        'class' => 'quantity-input',
                        'id' => 'product_quantity_' . $product->id
                    ]) ?>
                    <button type="button" class="quantity-btn increase" data-input-id="product_quantity_<?= $product->id ?>">+</button>
                </div>

                <div class="flash-message-container">
                    <?= $this->Flash->render('stockLimit') ?>
                </div>

                <?= $this->Form->button('Add to Cart', ['class' => 'add-to-cart-btn', 'type' => 'submit']) ?>
                <?= $this->Form->end() ?>
            </div>
        <?php else: ?>
            <div class="out-of-stock-message">Out of Stock</div>
        <?php endif; ?>
    </div>
</div>

<style>
    .quick-view-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .product-image-section {
        position: relative;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #f9f9f9;
        height: 400px;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        border-radius: 4px;
        padding: 20px;
    }

    .product-description {
        padding: 20px;
        background: #f9f9f9;
        border-radius: 8px;
    }

    .description-title {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 18px;
        color: #333;
        margin-bottom: 10px;
    }

    .description-text {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 14px;
        color: #666;
        line-height: 1.6;
    }

    .product-image.blurred {
        filter: blur(5px);
        transform: scale(1.1);
        transition: filter 0.3s ease, transform 0.3s ease;
        object-fit: contain;
    }

    .no-image-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 14px;
        z-index: 2;
    }

    .product-info-section {
        padding: 20px;
    }

    .product-header {
        margin-bottom: 30px;
    }

    .product-name {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 32px;
        color: #333;
        margin-bottom: 10px;
    }

    .product-price {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 28px;
        color: #b12704;
    }

    .product-meta {
        margin-bottom: 30px;
    }

    .meta-item {
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }

    .meta-label {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 16px;
        color: #666;
        width: 140px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .meta-value {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 16px;
        color: #333;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .quantity-btn {
        width: 40px;
        height: 40px;
        border: 1px solid #ddd;
        background: #f9f9f9;
        border-radius: 4px;
        cursor: pointer;
        font-size: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .quantity-btn:hover {
        background: #fff;
        border-color: #b12704;
        color: #b12704;
    }

    .quantity-input {
        width: 60px;
        height: 40px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 18px;
    }

    .quantity-input::-webkit-inner-spin-button,
    .quantity-input::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .add-to-cart-btn {
        width: 100%;
        padding: 15px 30px;
        background-color: #b12704;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.2s;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
    }

    .add-to-cart-btn:hover:not(:disabled) {
        background-color: #9a1f00;
    }

    .add-to-cart-btn:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }

    .out-of-stock-message {
        text-align: center;
        padding: 15px;
        background-color: #f8d7da;
        color: #721c24;
        border-radius: 4px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 18px;
    }

    .stock-alert {
        margin-top: 10px;
        padding: 10px;
        border-radius: 4px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 14px;
        display: none;
    }

    .stock-alert.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @media (max-width: 768px) {
        .quick-view-content {
            grid-template-columns: 1fr;
        }

        .product-image {
            height: 300px;
        }

        .product-name {
            font-size: 28px;
        }

        .product-price {
            font-size: 24px;
        }
    }

    .flash-message-container {
        margin: 10px 0;
        min-height: 20px;
    }

    .flash-message-container.has-message {
        animation: fadeIn 0.3s ease-in-out;
    }

    .flash-message-container .message {
        padding: 10px;
        margin: 0;
        border-radius: 4px;
    }

    .flash-message-container .message.error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productId = '<?= $product->id ?>';
    const quantityButtons = document.querySelectorAll(`.quantity-btn[data-input-id="product_quantity_${productId}"]`);
    
    quantityButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const inputId = this.getAttribute('data-input-id');
            const input = document.getElementById(inputId);
            if (!input) return;

            const currentValue = parseInt(input.value) || 1;
            const max = parseInt(input.getAttribute('max')) || 99;
            const min = parseInt(input.getAttribute('min')) || 1;

            if (this.classList.contains('decrease')) {
                if (currentValue > min) {
                    input.value = currentValue - 1;
                }
            } else if (this.classList.contains('increase')) {
                if (currentValue < max) {
                    input.value = currentValue + 1;
                } else {
                    alert('Maximum stock limit reached');
                }
            }
        });
    });
});
</script>
