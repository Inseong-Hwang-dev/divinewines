<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Import Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400&family=Comfortaa:wght@300&display=swap" rel="stylesheet">

    <!-- Other meta tags, stylesheets, and scripts -->
    <link rel="stylesheet" href="styles.css">
    <?= $this->Html->css('landing.css') ?>
</head>

<header class="py-7">
    <div class="container px-5">
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-6">
                <div class="text-center my-5">
<!--                    <h1 class="display-5 text-white mb-2">FINE WINES, JUST A CLICK AWAY</h1>-->
                    <div class="display-5 text-white mb-2">
                        <?= $this->ContentBlock->html('home-business-name'); ?>
                    </div>
<!--                    <p class="lead text-white-50 mb-4">ELEVATE YOUR COLLECTION TODAY</p>-->
                    <p class="lead text-white-50 mb-4"><?= $this->ContentBlock->text('home-display-message'); ?></p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                        <a class="btn btn-lg px-4 me-sm-3" href="<?= $this->Url->build(['controller' => 'Products', 'action' => 'page']) ?>">Shop Now</a>
                        <a class="btn btn-lg px-4" href="#latest-arrivals">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Product Gallery Section -->
<section class="product-gallery" id="latest-arrivals">
    <div class="container">
        <h2 class="gallery-title">LATEST ARRIVALS</h2>
        <div class="view-all">
            <a href="<?= $this->Url->build(['controller' => 'Products', 'action' => 'page']) ?>" class="btn">View All</a>
            <div class="arrow-container">
                <button class="arrow-nav left">←</button>
                <button class="arrow-nav right">→</button>
            </div>
        </div>

        <div class="gallery-wrapper">
            <!-- Gallery Container -->
            <div class="gallery-container">
                <div class="gallery">
                    <!-- Product Items -->
                    <?php if (isset($products) && !empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="image-container">
                                    <?php if ($product->image): ?>
                                        <a href="<?= $this->Url->build(['controller' => 'Products', 'action' => 'view', $product->id]) ?>" class="product-image-link">
                                            <?= $this->Html->image($product->image, ['alt' => $product->name, 'class' => 'product-image']) ?>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= $this->Url->build(['controller' => 'Products', 'action' => 'view', $product->id]) ?>" class="product-image-link">
                                            <?= $this->Html->image('sample.jpg', ['alt' => $product->name, 'class' => 'product-image blurred']) ?>
                                            <div class="no-image-overlay">No image available</div>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($product->stock_quantity == 0): ?>
                                        <div class="out-of-stock-label">Out of Stock</div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name"><?= h($product->name) ?></h3>
                                    <?php
                                    $totalReviews = 0;
                                    $totalRating = 0;

                                    if (isset($product->reviews) && is_array($product->reviews)) {
                                        foreach ($product->reviews as $review) {
                                            if (isset($review->status) && $review->status === 'approved') {
                                                $totalReviews++;
                                                $totalRating += $review->rating;
                                            }
                                        }
                                    }
                                    $averageRating = $totalReviews > 0 ? round($totalRating / $totalReviews, 1) : 0;

                                    if ($totalReviews > 0): ?>
                                        <div class="product-rating">
                                            <div class="stars">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <span class="star <?= $i <= $averageRating ? 'filled' : '' ?>">★</span>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="rating-count">(<?= $totalReviews ?>)</span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="product-price">$<?= number_format($product->price, 2) ?></div>
                                    <div class="quick-view-container">
                                        <button class="quick-view-btn" onclick="openQuickView(<?= $product->id ?>)">Quick View</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick View Modal -->
                            <div id="quick-view-modal-<?= $product->id ?>" class="quick-view-modal" tabindex="-1" role="dialog" aria-labelledby="quick-view-modal-label-<?= $product->id ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="quick-view-modal-label-<?= $product->id ?>">
                                                <?= h($product->name) ?>
                                            </h5>
                                            <button type="button" class="close" onclick="closeQuickView(<?= $product->id ?>)" aria-label="Close">
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
                    <?php else: ?>
                        <p>No latest products available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->Html->script('gallery-scroll.js') ?>

<style>
    .product-card {
        flex: 0 0 250px;
        width: 250px;
        text-align: center;
        position: relative;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        overflow: visible;
    }

    .product-card .image-container {
        overflow: hidden;
        border-radius: 8px;
        margin-bottom: 10px;
        width: 100%;
        height: 300px;
        position: relative;
    }

    .product-card img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        border-radius: 8px;
        transition: transform 0.3s ease;
    }

    .product-card:hover img {
        transform: scale(1.05);
    }

    .product-card:hover {
        border: 2px solid rgba(0, 0, 0, 0.05);
    }

    .out-of-stock-label {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(255, 0, 0, 0.8);
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 12px;
    }

    .product-info {
        padding: 10px;
        background: white;
        text-align: center;
        position: relative;
    }

    .product-name {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 16px;
        margin: 10px 0;
        line-height: 1.4;
        min-height: 44px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        word-wrap: break-word;
        white-space: normal;
        text-overflow: ellipsis;
    }

    .product-price {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 18px;
        margin-bottom: 10px;
        word-wrap: break-word;
        white-space: normal;
    }

    .gallery-title {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 24px;
        margin-bottom: 20px;
        word-wrap: break-word;
        white-space: normal;
    }

    .quick-view-container {
        position: absolute;
        top: -40px;
        left: 0;
        right: 0;
        opacity: 0;
        transition: opacity 0.2s ease;
        padding: 0 10px;
        z-index: 2;
    }

    .product-card:hover .quick-view-container {
        opacity: 1;
    }

    .quick-view-btn {
        width: 100%;
        padding: 10px 20px;
        background-color: #f9f9f9;
        color: #333;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .quick-view-btn:hover {
        background-color: #fff;
        border-color: #b12704;
        color: #b12704;
    }

    .no-image {
        width: 100%;
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9f9f9;
        border-radius: 4px;
        color: #666;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
    }

    .product-image-link {
        display: block;
        width: 100%;
        height: 100%;
        overflow: hidden;
        border-radius: 4px;
        background: #f9f9f9;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-image-link img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        transition: transform 0.3s ease;
        padding: 10px;
    }

    .product-image-link:hover img {
        transform: scale(1.05);
    }

    .quick-view-modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-dialog {
        max-width: 800px;
        margin: 30px auto;
    }

    .modal-content {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 18px;
        color: #333;
        margin: 0;
        word-wrap: break-word;
        white-space: normal;
        line-height: 1.4;
        max-width: 80%;
    }

    .close {
        background: none;
        border: none;
        font-size: 20px;
        color: #666;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }

    .close:hover {
        color: #b12704;
    }

    .modal-body {
        padding: 0;
    }

    @media (max-width: 768px) {
        .modal-dialog {
            margin: 15px;
        }
    }

    .quick-view-content {
        word-wrap: break-word;
        white-space: normal;
    }

    .description-text {
        white-space: normal;
        word-wrap: break-word;
        line-height: 1.6;
    }

    .meta-value {
        white-space: normal;
        word-wrap: break-word;
        line-height: 1.4;
    }

    .product-rating {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin: 10px 0;
    }

    .stars {
        display: flex;
        gap: 2px;
    }

    .star {
        font-size: 24px;
        color: #ccc;
    }

    .star.filled {
        color: #ffd700;
    }

    .rating-count {
        font-size: 18px;
        color: #666;
        font-family: 'Comfortaa', sans-serif;
    }
</style>

<script>
    function openQuickView(productId) {
        const modal = document.getElementById(`quick-view-modal-${productId}`);
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeQuickView(productId) {
        const modal = document.getElementById(`quick-view-modal-${productId}`);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('quick-view-modal')) {
            event.target.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // Close modal when pressing Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = document.querySelectorAll('.quick-view-modal');
            modals.forEach(modal => {
                if (modal.style.display === 'block') {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        }
    });
</script>
</html>
