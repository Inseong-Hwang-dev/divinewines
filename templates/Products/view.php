<?= $this->Html->css('styles', ['block' => true]) ?>

<div class="product-details-container">
    <div class="product-details-grid">
        <div class="product-image-section">
            <?php if (isset($product->image) && $product->image): ?>
                <?= $this->Html->image($product->image, ['alt' => h($product->name), 'class' => 'product-image']) ?>
            <?php else: ?>
                <?= $this->Html->image('sample.jpg', ['alt' => h($product->name), 'class' => 'product-image blurred']) ?>
                <div class="no-image-overlay">No image available</div>
            <?php endif; ?>
        </div>

        <div class="product-info-section">
            <?= $this->Flash->render('add2CartFeedback') ?>
            <?= $this->Flash->render('cartQuantityFeedback') ?>

            <h1 class="product-name"><?= h($product->name) ?></h1>

            <?php
            $totalReviews = 0;
            $totalRating = 0;

            foreach ($product->reviews as $review) {
                if ($review->status === 'approved') {
                    $totalReviews++;
                    $totalRating += $review->rating;
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

            <div class="product-description">
                <p class="description-text"><?= h($product->product_description) ?: 'No description available' ?></p>
            </div>

            <div class="accordion-container">
                <!-- Product Details Accordion -->
                <div class="accordion-item">
                    <button class="accordion-header" onclick="toggleAccordion('product-details')">
                        Product Details
                        <span class="accordion-icon">▼</span>
                    </button>
                    <div id="product-details" class="accordion-content">
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
                                <span class="meta-value"><?= h($product->wine_style->name) ?: 'N/A' ?></span>
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
                    </div>
                </div>
            </div>

            <?php if ($product->stock_quantity > 0): ?>
                <div class="add-to-cart-form">
                    <?= $this->Form->create(null, [
                        'url' => ['controller' => 'CartItems', 'action' => 'add'],
                        'class' => 'add-to-cart',
                        'id' => 'add-to-cart-form-' . $product->id,
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

                    <?= $this->Flash->render() ?>

                    <?= $this->Form->button('Add to Cart', ['class' => 'add-to-cart-btn', 'type' => 'submit']) ?>
                    <?= $this->Form->end() ?>
                </div>
            <?php else: ?>
                <div class="out-of-stock-message">Out of Stock</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Customer Reviews Section -->
<section class="customer-reviews-section">
    <h2 class="section-title">Customer Reviews</h2>

    <div class="customer-reviews">
        <?= $this->Flash->render('reviewFeedback') ?>
        <?php if (!empty($product->reviews)): ?>
            <?php
            $totalReviews = 0;
            $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
            $totalRating = 0;

            foreach ($product->reviews as $review) {
                if ($review->status === 'approved') {
                    $totalReviews++;
                    $ratingCounts[$review->rating]++;
                    $totalRating += $review->rating;
                }
            }
            $averageRating = $totalReviews > 0 ? round($totalRating / $totalReviews, 1) : 0;
            ?>

            <div class="rating-summary">
                <div class="average-rating">
                    <div class="big-rating"><?= $averageRating ?></div>
                    <div class="star-rating large">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?= $i <= $averageRating ? 'filled' : '' ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <div class="total-reviews">(<?= $totalReviews ?>)</div>
                </div>

                <div class="rating-breakdown">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <div class="rating-bar">
                            <span class="stars"><?= str_repeat('★', $i) ?></span>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?= $totalReviews > 0 ? ($ratingCounts[$i] / $totalReviews * 100) : 0 ?>%"></div>
                            </div>
                            <span class="count"><?= $ratingCounts[$i] ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="reviews-controls">
                <div class="sort-reviews">
                    <label for="sort-reviews">Sort by:</label>
                    <select id="sort-reviews" onchange="sortReviews(this.value)">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="highest">Highest Rated</option>
                        <option value="lowest">Lowest Rated</option>
                    </select>
                </div>
            </div>

            <div class="reviews-list">
                <?php
                $displayedReviews = 0;
                foreach ($product->reviews as $review):
                    if ($review->status === 'approved'):
                        $displayedReviews++;
                        $displayStyle = $displayedReviews <= 3 ? 'block' : 'none';
                ?>
                        <div class="review-item" style="display: <?= $displayStyle ?>;">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <span class="reviewer-name"><?= h($review->user->first_name) ?> <?= h($review->user->last_name) ?></span>
                                    <div class="star-rating">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <span class="star <?= $i <= $review->rating ? 'filled' : '' ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <span class="review-date"><?= $this->Time->format($review->created, 'dd-MM-yyyy') ?></span>
                            </div>
                            <div class="review-content">
                                <div class="review-text" id="review-text-<?= $review->id ?>">
                                    <?= nl2br(h($review->review_text)) ?>
                                </div>
                                <button class="see-more-btn" onclick="toggleReviewText(<?= $review->id ?>)" style="display: none;">See more</button>
                            </div>
                        </div>
                <?php
                    endif;
                endforeach;
                ?>
                <?php if ($displayedReviews > 3): ?>
                    <div class="load-more-container">
                        <button class="load-more-btn" onclick="loadMoreReviews()">Load More Reviews</button>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>No reviews yet. Be the first to review this product!</p>
        <?php endif; ?>
    </div>

    <div id="review-form" class="review-form" style="display: none;">
        <?= $this->Form->create(null, [
            'url' => ['controller' => 'Reviews', 'action' => 'add'],
            'type' => 'post',
            'id' => 'review-form-element'
        ]) ?>
            <?= $this->Form->hidden('product_id', ['value' => $product->id]) ?>
            <?= $this->Form->hidden('user_id', ['value' => $this->Identity->get('id')]) ?>

            <div class="form-group">
                <label>Rating</label>
                <div class="star-rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
                        <label for="star<?= $i ?>">★</label>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="form-group">
                <?= $this->Form->control('review_text', [
                    'type' => 'textarea',
                    'label' => false,
                    'placeholder' => 'Write your review here...',
                    'required' => true,
                    'class' => 'review-textarea'
                ]) ?>
            </div>

            <?= $this->Form->button('Submit Review', ['class' => 'submit-review-btn']) ?>
        <?= $this->Form->end() ?>
    </div>

    <?php if ($this->Identity->isLoggedIn()): ?>
        <button class="write-review-btn" onclick="toggleReviewForm()">Write a Review</button>
    <?php else: ?>
        <?= $this->Html->link('Login to Review', ['controller' => 'Auth', 'action' => 'login', '?' => ['from' => 'product']], ['class' => 'write-review-btn']) ?>
    <?php endif; ?>
</section>

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
        justify-content: center;
        align-items: center;
        background: #f9f9f9;
        height: 700px;
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
        object-fit: contain;
    }

    .product-image-section:hover .product-image.blurred {
        filter: blur(5px);
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

    .product-name {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 42px;
        color: #333;
        margin-bottom: 10px;
    }

    .product-price {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 38px;
        color: #b12704;
        margin-bottom: 20px;
    }

    .product-description {
        margin-bottom: 30px;
    }

    .description-text {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 16px;
        color: #666;
        line-height: 1.6;
    }

    .accordion-container {
        margin-bottom: 30px;
    }

    .accordion-item {
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    .accordion-header {
        width: 100%;
        padding: 15px 20px;
        background: #f9f9f9;
        border: none;
        text-align: left;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 18px;
        color: #333;
        transition: background-color 0.2s;
    }

    .accordion-header:hover {
        background: #f0f0f0;
    }

    .accordion-icon {
        font-size: 12px;
        transition: transform 0.3s;
    }

    .accordion-header.active .accordion-icon {
        transform: rotate(180deg);
    }

    .accordion-content {
        display: none;
        padding: 20px;
        background: #fff;
    }

    .accordion-content.active {
        display: block;
    }

    .product-meta {
        display: grid;
        gap: 15px;
    }

    .meta-item {
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

    .reviews-placeholder {
        text-align: center;
        padding: 20px;
        color: #666;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 16px;
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
        border-radius: 8px;
        cursor: pointer;
        font-size: 18px;
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
        border-radius: 8px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 16px;
    }

    .add-to-cart-btn {
        width: 100%;
        padding: 15px 30px;
        background-color: #b12704;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.2s;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
    }

    .add-to-cart-btn:hover {
        background-color: #9a1f00;
    }

    .out-of-stock-message {
        text-align: center;
        padding: 15px;
        background-color: #f8d7da;
        color: #721c24;
        border-radius: 4px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .product-details-grid {
            grid-template-columns: 1fr;
        }

        .product-image {
            height: 300px;
        }

        .product-name {
            font-size: 32px;
        }

        .product-price {
            font-size: 28px;
        }
    }

    .customer-reviews-section {
        max-width: 1200px;
        margin: 50px auto;
        padding: 0 20px;
    }

    .section-title {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 32px;
        color: #333;
        margin-bottom: 30px;
    }

    .customer-reviews {
        margin-top: 2rem;
        padding: 1rem;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .rating-summary {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .average-rating {
        text-align: center;
        min-width: 150px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .big-rating {
        font-size: 48px;
        font-weight: bold;
        color: var(--secondary-products-color);
        line-height: 1;
    }

    .star-rating.large {
        display: flex;
        gap: 2px;
    }

    .star-rating.large .star {
        font-size: 32px;
        color: #ccc;
    }

    .star-rating.large .star.filled {
        color: #ffd700;
    }

    .total-reviews {
        font-size: 24px;
        color: #666;
        font-family: 'Comfortaa', sans-serif;
    }

    .rating-breakdown {
        flex: 1;
    }

    .rating-bar {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }

    .rating-bar .stars {
        color: #ffd700;
        min-width: 60px;
    }

    .rating-bar .progress {
        flex: 1;
        height: 8px;
        background: #eee;
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }

    .rating-bar .progress-bar {
        height: 100%;
        background: #b12704;
        border-radius: 4px;
        transition: width 0.3s ease;
        position: absolute;
        left: 0;
        top: 0;
    }

    .rating-bar .count {
        min-width: 40px;
        text-align: right;
        color: #666;
    }

    .reviews-list {
        margin-top: 2rem;
    }

    .review-item {
        padding: 1rem;
        border-bottom: 1px solid #eee;
    }

    .review-item:last-child {
        border-bottom: none;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }

    .reviewer-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .reviewer-name {
        font-weight: 600;
        color: var(--secondary-products-color);
    }

    .review-date {
        color: #666;
        font-size: 0.9rem;
    }

    .review-content {
        color: #333;
        line-height: 1.5;
        position: relative;
        margin-top: 10px;
    }

    .review-text {
        white-space: pre-line;
        max-height: 150px;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        margin-bottom: 5px;
        word-wrap: break-word;
        line-height: 1.5;
    }

    .review-text.expanded {
        max-height: none;
    }

    .see-more-btn {
        background: none;
        border: none;
        color: #b12704;
        cursor: pointer;
        padding: 5px 0;
        font-size: 0.9em;
        text-decoration: underline;
        display: inline-block;
        transition: color 0.2s;
    }

    .see-more-btn:hover {
        color: #9a1f00;
    }

    .star-rating {
        display: flex;
        align-items: center;
        gap: 2px;
    }

    .star-rating .star {
        color: #ddd;
        font-size: 18px;
    }

    .star-rating .star.filled {
        color: #ffd700;
    }

    .star-rating.large .star {
        font-size: 24px;
    }

    .review-item {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        margin: 15px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
    }

    .reviewer-info {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .reviewer-name {
        font-weight: bold;
        color: var(--primary-color);
    }

    .review-date {
        font-size: 0.9em;
        color: #666;
    }

    .write-review-btn {
        padding: 12px 24px;
        background-color: #b12704;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.2s;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        text-decoration: none;
        display: inline-block;
    }

    .write-review-btn:hover {
        background-color: #9a1f00;
        color: white;
        text-decoration: none;
    }

    .review-form {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        cursor: pointer;
        font-size: 24px;
        color: #ddd;
        padding: 0 2px;
    }

    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #ffd700;
    }

    .review-textarea {
        width: 100%;
        min-height: 100px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 10px 0;
        resize: vertical;
    }

    .submit-review-btn {
        background: #b12704;
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 8px;
        cursor: pointer;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 16px;
        width: 100%;
        transition: background-color 0.2s;
    }

    .submit-review-btn:hover {
        background: #9a1f00;
    }

    @media (max-width: 768px) {
        .rating-summary {
            flex-direction: column;
            gap: 1rem;
        }

        .average-rating {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .big-rating {
            font-size: 2rem;
        }
    }

    .load-more-container {
        text-align: center;
        margin-top: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .load-more-btn {
        background-color: #b12704;
        border: none;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 16px;
        transition: background-color 0.2s;
    }

    .load-more-btn:hover {
        background-color: #9a1f00;
    }

    .reviews-controls {
        margin: 20px 0;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        display: flex;
        justify-content: flex-end;
    }

    .sort-reviews {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sort-reviews label {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        color: #666;
    }

    .sort-reviews select {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        color: #333;
        background-color: white;
        cursor: pointer;
    }

    .sort-reviews select:focus {
        outline: none;
        border-color: #b12704;
    }

    .flash-message {
        margin-bottom: 20px;
        padding: 15px;
        border-radius: 8px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
    }

    .flash-message.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .flash-message.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
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

    .flash-message-container {
        margin: 10px 0;
        min-height: 20px;
        transition: all 0.3s ease;
    }

    .flash-message-container.has-message {
        animation: fadeIn 0.3s ease-in-out;
    }

    .flash-message-container .message {
        padding: 10px;
        margin: 0;
        border-radius: 4px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
    }

    .flash-message-container .message.error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
function toggleAccordion(id) {
    const content = document.getElementById(id);
    const header = content.previousElementSibling;

    // Toggle active class on header
    header.classList.toggle('active');

    // Toggle content visibility
    if (content.style.display === 'block') {
        content.style.display = 'none';
        content.classList.remove('active');
    } else {
        content.style.display = 'block';
        content.classList.add('active');
    }
}

// Initialize first accordion as open
document.addEventListener('DOMContentLoaded', function() {
    const firstAccordion = document.querySelector('.accordion-content');
    if (firstAccordion) {
        firstAccordion.style.display = 'block';
        firstAccordion.classList.add('active');
        firstAccordion.previousElementSibling.classList.add('active');
    }
});

function toggleReviewForm() {
    const form = document.getElementById('review-form');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function toggleReviewText(reviewId) {
    const textElement = document.getElementById(`review-text-${reviewId}`);
    const button = textElement.nextElementSibling;

    if (textElement.classList.contains('expanded')) {
        textElement.classList.remove('expanded');
        button.textContent = 'See more';
    } else {
        textElement.classList.add('expanded');
        button.textContent = 'See less';
    }
}

// Add this new function to check text height on page load
document.addEventListener('DOMContentLoaded', function() {
    const reviewTexts = document.querySelectorAll('.review-text');
    reviewTexts.forEach(textElement => {
        const button = textElement.nextElementSibling;
        if (textElement.scrollHeight > textElement.clientHeight) {
            button.style.display = 'inline-block';
        }
    });
});

function loadMoreReviews() {
    const hiddenReviews = document.querySelectorAll('.review-item[style="display: none;"]');
    const reviewsToShow = Math.min(3, hiddenReviews.length);

    for (let i = 0; i < reviewsToShow; i++) {
        hiddenReviews[i].style.display = 'block';
    }

    // Hide load more button if no more reviews to show
    if (hiddenReviews.length <= reviewsToShow) {
        document.querySelector('.load-more-container').style.display = 'none';
    }
}

// Update the DOMContentLoaded event to include both see more and load more functionality
document.addEventListener('DOMContentLoaded', function() {
    // Check for review text overflow
    const reviewTexts = document.querySelectorAll('.review-text');
    reviewTexts.forEach(textElement => {
        const button = textElement.nextElementSibling;
        if (textElement.scrollHeight > textElement.clientHeight) {
            button.style.display = 'inline-block';
        }
    });

    // Initialize first accordion as open
    const firstAccordion = document.querySelector('.accordion-content');
    if (firstAccordion) {
        firstAccordion.style.display = 'block';
        firstAccordion.classList.add('active');
        firstAccordion.previousElementSibling.classList.add('active');
    }
});

function sortReviews(sortBy) {
    const reviewsList = document.querySelector('.reviews-list');
    const reviews = Array.from(document.querySelectorAll('.review-item'));
    const loadMoreContainer = document.querySelector('.load-more-container');

    // Store the number of currently visible reviews
    const currentlyVisible = reviews.filter(review => review.style.display !== 'none').length;

    // Remove load more container temporarily
    if (loadMoreContainer) {
        loadMoreContainer.remove();
    }

    // Sort reviews based on selected option
    reviews.sort((a, b) => {
        switch(sortBy) {
            case 'newest':
                return new Date(b.querySelector('.review-date').textContent) - new Date(a.querySelector('.review-date').textContent);
            case 'oldest':
                return new Date(a.querySelector('.review-date').textContent) - new Date(b.querySelector('.review-date').textContent);
            case 'highest':
                return b.querySelector('.star-rating').querySelectorAll('.star.filled').length -
                       a.querySelector('.star-rating').querySelectorAll('.star.filled').length;
            case 'lowest':
                return a.querySelector('.star-rating').querySelectorAll('.star.filled').length -
                       b.querySelector('.star-rating').querySelectorAll('.star.filled').length;
            default:
                return 0;
        }
    });

    // Clear and reappend sorted reviews
    reviews.forEach(review => {
        reviewsList.appendChild(review);
    });

    // Show the same number of reviews that were visible before sorting
    reviews.forEach((review, index) => {
        review.style.display = index < currentlyVisible ? 'block' : 'none';
    });

    // Reappend load more container if there are more reviews than currently visible
    if (reviews.length > currentlyVisible && loadMoreContainer) {
        reviewsList.appendChild(loadMoreContainer);
    }
}

// Update the DOMContentLoaded event to include sorting functionality
document.addEventListener('DOMContentLoaded', function() {
    // Check for review text overflow
    const reviewTexts = document.querySelectorAll('.review-text');
    reviewTexts.forEach(textElement => {
        const button = textElement.nextElementSibling;
        if (textElement.scrollHeight > textElement.clientHeight) {
            button.style.display = 'inline-block';
        }
    });

    // Initialize first accordion as open
    const firstAccordion = document.querySelector('.accordion-content');
    if (firstAccordion) {
        firstAccordion.style.display = 'block';
        firstAccordion.classList.add('active');
        firstAccordion.previousElementSibling.classList.add('active');
    }

    // Initialize reviews with default sort (newest first)
    sortReviews('newest');
});

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
