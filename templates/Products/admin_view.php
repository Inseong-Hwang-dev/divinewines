<?= $this->Html->css('styles', ['block' => true]) ?>

<!--back button-->
<div class="row">
    <?= $this->Html->tag(
        'button',
        '<i class="bi bi-arrow-left"></i>  Back to List',
        ['type' => 'button',
            'onclick' => 'window.location.href = "' . $this->Url->build(['controller' => 'Products', 'action' => 'index']) . '"; return false;',
        'class' => 'btn back-btn text-start w-auto'],
    ) ?>
</div>

<!--product details section-->
<div class="product-details-container">
    <div class="product-details-grid">

        <div class="product-image-section">
            <?php if (isset($product->image) && $product->image) : ?>
                <?= $this->Html->image($product->image, ['alt' => h($product->name), 'class' => 'product-image']) ?>
            <?php else : ?>
                <?= $this->Html->image('sample.jpg', ['alt' => h($product->name), 'class' => 'product-image blurred']) ?>
                <div class="no-image-overlay">No image available</div>
            <?php endif; ?>
        </div>

        <div class="product-info-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="product-name mb-0"><?= h($product->name) ?></h1>
                <?= $this->Html->link('<i class="bi bi-pencil me-1"></i> Edit', ['action' => 'edit', $product->id], ['class' => 'btn btn-edit btn-large', 'title' => 'Edit', 'escape' => false]) ?>
            </div>
            <div class="product-price">$<?= number_format($product->price, 2) ?></div>

            <div class="description">
                <strong><?= __('Description') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($product->product_description)); ?>
                </blockquote>
            </div>

            <div class="row">

                <div class="col meta-item">
                    <span class="meta-label">Brand:</span>
                    <span class="meta-value"><?= h($product->brand) ?: 'N/A' ?></span>
                </div>

                <div class="col meta-item">
                    <span class="meta-label">Stock:</span>
                    <?php if ($product->stock_quantity > 10) :?>
                        <span class="meta-value"><?= h($product->stock_quantity) ?> available</span>
                    <?php elseif ($product-> stock_quantity <= 10 && $product-> stock_quantity > 0 ) :?>
                        <span class="meta-value view-low-stock"><?= h($product->stock_quantity) ?> available</span>
                    <?php else :?>
                        <span class="meta-value view-out-of-stock">Out of Stock</span>
                    <?php endif;?>
                </div>
            </div>
            <div class="row">

                <div class="col meta-item">
                    <span class="meta-label">Volume:</span>
                    <span class="meta-value"> <?= rtrim(rtrim(number_format($product->volume, 3), '0'), '.') ?> L</span>
                </div>

                <div class="col meta-item">
                    <span class="meta-label">Total Sold:</span>
                    <span class="meta-value"><?= h($product->total_sold) ?> sold</span>
                </div>
            </div>

            <div class="product-meta">
                <div class="meta-item">
                    <span class="meta-label">Vintage:</span>
                    <span class="meta-value"><?= $product->vintage == 0000 ? 'N/A' : h($product->vintage) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Alcohol Content:</span>
                    <span class="meta-value"><?= h($product->alcohol_content) ? h($product->alcohol_content) . '%' : 'N/A' ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Region:</span>
                    <span class="meta-value"><?= h($product->region) ?: 'N/A' ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Style:</span>
                    <span class="meta-value"><?= h($product->wine_style->name) ?: 'N/A' ?></span>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .product-details-container {
        max-width: 1200px;
        margin: 50px auto;
        padding: 0 20px;
    }

    .product-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

    .product-image-section {
        position: relative;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 500px;
        background: #f9f9f9;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        border-radius: 4px;
        padding: 20px;
    }

    .product-image.blurred {
        filter: blur(5px);
        transform: scale(1.1);
        transition: filter 0.3s ease, transform 0.3s ease;
    }

    .product-image-section:hover .product-image.blurred {
        filter: blur(5px);
        transform: scale(1.1);
    }

    .no-image {
        width: 100%;
        height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9f9f9;
        border-radius: 4px;
        color: #666;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
    }

    .product-info-section {
        padding: 20px;
    }

    .product-name {
        font-family: 'Lora', 'serif';
        font-weight: 300;
        font-size: 32px;
        color: #333;
        margin-bottom: 10px;
    }

    .product-price {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 28px;
        color: #2c3e50;
        margin-bottom: 20px;
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
        color: #666;
        width: 120px;
        font-size: 14px;
        padding-right: 5px;
    }

    .meta-value {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        color: #333;
        font-size: 16px;
        padding-left:5px;
    }

    .view-low-stock{
        color: #E1AD41FF;
    }

    .view-out-of-stock {
        color: var(--product-text-color);
        background: transparent;
        position: relative;
        z-index: 1;
    }

    .view-out-of-stock::before{
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 100%;
        height: 90%;
        background: var(--primary-products-light); /* red highlight */
        transform: translate(-50%, -50%);
        filter: blur(10px); /* the diffusion */
        z-index: -1;
        border-radius: 5px;

    }

    .add-to-cart-btn {
        width: 100%;
        padding: 12px 20px;
        background-color: #f9f9f9;
        color: #333;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
    }

    .add-to-cart-btn:hover {
        background-color: #fff;
        border-color: #b12704;
        color: #b12704;
    }

    .out-of-stock-message {
        padding: 12px 20px;
        background-color: #f9f9f9;
        color: #666;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-align: center;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
    }

    .admin-actions {
        margin-top: 20px;
    }

    .add-stock-form {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .stock-quantity-input {
        flex: 1;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
    }

    .add-stock-btn {
        padding: 8px 16px;
        background-color: #f9f9f9;
        color: #333;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
    }

    .add-stock-btn:hover {
        background-color: #fff;
        border-color: #b12704;
        color: #b12704;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        width: 100%;
    }

    .quantity-btn {
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
        width: 40px;
        height: 40px;
        font-size: 18px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
    }

    .quantity-btn:hover {
        background-color: #fff;
        border-color: #b12704;
        color: #b12704;
    }

    .quantity-input {
        width: 60px;
        height: 40px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 0 10px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 16px;
    }

    .quantity-input::-webkit-inner-spin-button,
    .quantity-input::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    @media (max-width: 768px) {
        .product-details-grid {
            grid-template-columns: 1fr;
        }

        .product-details-container {
            margin: 20px auto;
        }
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
        z-index: 1;
    }
</style>


