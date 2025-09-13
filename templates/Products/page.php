<?= $this->Html->css('styles', ['block' => true]) ?>
<?= $this->Html->css('mobile', ['block' => true]) ?>
<!-- Header Section -->
<header class="page-header"></header>

<div class="products-container">
    <!-- Header Image Section -->
    <div class="header-image">
        <img src="https://images.unsplash.com/photo-1519671282429-b44660ead0a7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1380&h=200&q=80" alt="Wine Shop Header">
    </div>

    <!-- Sticky Filter Container -->
    <div class="sticky-filter-container">
        <!-- Combined Header Section -->
        <div class="header-controls">
            <div class="filter-toggle">
                <button id="toggle-filters">
                    <span class="filter-icon"></span>
                    <span id="toggle-text">Hide filters</span>
                </button>
            </div>
            <div class="sort">
                <span class="sort-label">Sort:</span>
                <select onchange="location = this.value;">
                    <option value="<?= $this->Url->build(['?' => array_merge($this->request->getQuery(), ['sort' => null])]) ?>" <?= !$this->request->getQuery('sort') ? 'selected' : '' ?>>Default</option>
                    <option value="<?= $this->Url->build(['?' => array_merge($this->request->getQuery(), ['sort' => 'price_low_high'])]) ?>" <?= $this->request->getQuery('sort') === 'price_low_high' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="<?= $this->Url->build(['?' => array_merge($this->request->getQuery(), ['sort' => 'price_high_low'])]) ?>" <?= $this->request->getQuery('sort') === 'price_high_low' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="<?= $this->Url->build(['?' => array_merge($this->request->getQuery(), ['sort' => 'created_at_asc'])]) ?>" <?= $this->request->getQuery('sort') === 'created_at_asc' ? 'selected' : '' ?>>Old to New</option>
                    <option value="<?= $this->Url->build(['?' => array_merge($this->request->getQuery(), ['sort' => 'created_at_desc'])]) ?>" <?= $this->request->getQuery('sort') === 'created_at_desc' ? 'selected' : '' ?>>New to Old</option>
                    <option value="<?= $this->Url->build(['?' => array_merge($this->request->getQuery(), ['sort' => 'name_asc'])]) ?>" <?= $this->request->getQuery('sort') === 'name_asc' ? 'selected' : '' ?>>A-Z</option>
                    <option value="<?= $this->Url->build(['?' => array_merge($this->request->getQuery(), ['sort' => 'name_desc'])]) ?>" <?= $this->request->getQuery('sort') === 'name_desc' ? 'selected' : '' ?>>Z-A</option>
                </select>
            </div>
        </div>

        <!-- Selected Filters -->
        <div class="selected-filters">
            <div id="filter-bubbles"></div>
            <button id="clear-all" style="display: none;" onclick="location.href='<?= $this->Url->build(['action' => 'clearAllFilters']) ?>'">Clear All</button>
        </div>
    </div>

    <!-- Combined Sidebar and Main Content -->
    <div class="content-wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <!-- Price Filter -->
            <div class="filter-group">
                <div class="filter-title" onclick="toggleDropdown('price-options')">
                    <h3>Price</h3>
                    <span class="arrow"></span>
                </div>
                <div id="price-options" class="filter-options">
                    <?php foreach ($allPrices as $price): ?>
                        <label class="option <?= in_array($price['value'], $availablePrices) ? '' : 'unavailable' ?>">
                            <input type="checkbox" name="Price" value="<?= h($price['value']) ?>" onchange="applyFilter('Price', this)" <?= in_array($price['value'], $this->request->getQuery('price', [])) ? 'checked' : '' ?>> <?= h($price['label']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Variety Filter -->
            <div class="filter-group">
                <div class="filter-title" onclick="toggleDropdown('variety-options')">
                    <h3>Variety</h3>
                    <span class="arrow"></span>
                </div>
                <div id="variety-options" class="filter-options">
                    <?php foreach ($allStyles as $style): ?>
                        <label class="option <?= in_array($style->wine_style_id, $availableStyles) ? '' : 'unavailable' ?>">
                            <input type="checkbox" name="Variety" value="<?= h($style->wine_style_id) ?>" onchange="applyFilter('Variety', this)" <?= in_array($style->wine_style_id, $this->request->getQuery('style', [])) ? 'checked' : '' ?>>  <?= h($wineStyles[$style->wine_style_id] ?? 'Unknown') ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Region Filter -->
            <div class="filter-group">
                <div class="filter-title" onclick="toggleDropdown('region-options')">
                    <h3>Region</h3>
                    <span class="arrow"></span>
                </div>
                <div id="region-options" class="filter-options">
                    <?php foreach ($allRegions as $region): ?>
                        <label class="option <?= in_array($region->region, $availableRegions) ? '' : 'unavailable' ?>">
                            <input type="checkbox" name="Region" value="<?= h($region->region) ?>" onchange="applyFilter('Region', this)" <?= in_array($region->region, $this->request->getQuery('region', [])) ? 'checked' : '' ?>> <?= h($region->region) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Vintage Filter -->
            <div class="filter-group">
                <div class="filter-title" onclick="toggleDropdown('vintage-options')">
                    <h3>Vintage</h3>
                    <span class="arrow"></span>
                </div>
                <div id="vintage-options" class="filter-options">
                    <?php foreach ($allVintages as $vintage): ?>
                        <label class="option <?= in_array($vintage->vintage, $availableVintages) ? '' : 'unavailable' ?>">
                            <input type="checkbox" name="Vintage" value="<?= h($vintage->vintage) ?>" onchange="applyFilter('Vintage', this)" <?= in_array($vintage->vintage, $this->request->getQuery('vintage', [])) ? 'checked' : '' ?>> <?= h($vintage->vintage) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Volume Filter -->
            <div class="filter-group">
                <div class="filter-title" onclick="toggleDropdown('volume-options')">
                    <h3>Volume</h3>
                    <span class="arrow"></span>
                </div>
                <div id="volume-options" class="filter-options">
                    <?php
                    $volumeOptions = [
                        '0.375' => '375 ml (Half)',
                        '0.750' => '750 ml (Standard)',
                        '1.5' => '1.5 L (Magnum)',
                        '3.0' => '3 L (Double Magnum)',
                        '4.5' => '4.5 L (Jeroboam)'
                    ];
                    foreach ($volumeOptions as $value => $label): ?>
                        <label class="option <?= in_array($value, $availableVolumes) ? '' : 'unavailable' ?>">
                            <input type="checkbox" name="Volume" value="<?= h($value) ?>" onchange="applyFilter('Volume', this)" <?= in_array($value, $this->request->getQuery('volume', [])) ? 'checked' : '' ?>> <?= h($label) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Brand Filter -->
            <div class="filter-group">
                <div class="filter-title" onclick="toggleDropdown('brand-options')">
                    <h3>Brand</h3>
                    <span class="arrow"></span>
                </div>
                <div id="brand-options" class="filter-options">
                    <?php foreach ($allBrands as $brand): ?>
                        <label class="option <?= in_array($brand->brand, $availableBrands) ? '' : 'unavailable' ?>">
                            <input type="checkbox" name="Brand" value="<?= h($brand->brand) ?>" onchange="applyFilter('Brand', this)" <?= in_array($brand->brand, $this->request->getQuery('brand', [])) ? 'checked' : '' ?>> <?= h($brand->brand) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <?php if ($this->Identity->isLoggedIn()): ?>
                <div class="alert alert-dismissible fade show" role="alert">
                    <?= $this->Flash->render('add2CartFeedback');?>
                </div>
            <?php endif; ?>
            <!-- Product Grid -->
            <div class="product-grid" id="product-grid">
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
            </div>

            <?php if ($page < $totalPages): ?>
                <div class="load-more-container">
                    <button id="load-more-btn" class="load-more-btn" data-page="<?= $page ?>" data-total-pages="<?= $totalPages ?>">
                        Load More Products
                    </button>
                </div>
            <?php endif; ?>

            <!-- Product Counter -->
            <div class="product-counter">
                Showing <?= count($products) ?> of <?= $totalCount ?> products
            </div>
        </div>
    </div>
</div>

<style>
    body, p, a, span, div, .navbar, .nav-link, .navbar-brand {
        font-family: 'Comfortaa', sans-serif !important;
        font-weight: 300 !important;
    }

    /* Exclude navigation links from global font size */
    .navbar-nav .nav-link {
        font-size: 16px !important;
        font-family: 'Comfortaa', sans-serif !important;
        font-weight: 300 !important;
    }

    body {
        min-height: 100vh;
        overflow-y: auto;
        padding-top: 60px; /* Add padding to account for fixed navbar */
    }

    .products-container {
        max-width: 1380px;
        margin: 0 auto;
        padding: 30px 30px;
        position: relative;
    }

    .header-image {
        width: 100%;
        max-width: 1380px;
        margin: 0 auto 20px auto;
    }

    .header-image img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .sticky-filter-container {
        position: sticky;
        top: 60px;
        background: rgba(255, 255, 255, 0.9);
        z-index: 1000;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
    }

    .header-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .filter-toggle {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .filter-toggle button {
        background: none;
        border: none;
        padding: 8px 16px;
        cursor: pointer;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: color 0.3s ease;
    }

    .filter-toggle button:hover {
        color: #540b0e;
    }

    .filter-icon {
        display: inline-block;
        width: 20px;
        height: 20px;
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>') no-repeat center center;
        background-size: contain;
    }

    .filter-toggle p {
        margin: 0;
        color: #666;
    }

    .sort {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sort-label {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 14px;
        color: #333;
    }

    .sort select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 8px 30px 8px 12px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 14px;
        color: #333;
        cursor: pointer;
        transition: border-color 0.2s ease, background-color 0.2s ease;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="%23333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>');
        background-repeat: no-repeat;
        background-position: right 10px center;
    }

    .sort select:hover {
        border-color: #540b0e;
        background-color: #fff;
    }

    .sort select:focus {
        outline: none;
        border-color: #540b0e;
        box-shadow: 0 0 0 2px rgba(84, 11, 14, 0.1);
    }

    .selected-filters {
        width: 100%;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    #filter-bubbles {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-bubble {
        display: inline-flex;
        align-items: center;
        background: #e0e0e0;
        border-radius: 20px;
        padding: 5px 10px;
        font-size: 14px;
        gap: 5px;
    }

    .remove-filter {
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        padding: 0 2px;
        transition: color 0.2s ease;
    }

    .remove-filter:hover {
        color: #540b0e;
    }

    #clear-all {
        background: transparent;
        border: 1px solid #540b0e;
        color: #540b0e;
        padding: 5px 10px;
        border-radius: 20px;
        cursor: pointer;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 14px;
        transition: background 0.2s ease, color 0.2s ease;
    }

    #clear-all:hover {
        background: #540b0e;
        color: white;
    }

    .content-wrapper {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        width: 100%;
        min-height: 100vh;
    }

    .sidebar {
        flex: 0 0 20%;
        padding-right: 20px;
        background: rgba(255, 255, 255, 0.9);
        border-right: 1px solid #eee;
        position: sticky;
        top: 110px;
        z-index: 1;
        align-self: flex-start;
        min-height: 300px;
        transition: all 0.3s ease;
    }

    .sidebar h3 {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 15px;
        margin: 0;
        color: #333;
    }

    .filter-group {
        margin-bottom: 15px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .filter-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        padding: 10px 12px;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 15px;
        color: #333;
        transition: color 0.2s ease;
    }

    .filter-title:hover {
        color: #540b0e;
    }

    .arrow {
        width: 12px;
        height: 12px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="%23666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        transition: transform 0.3s ease;
    }

    .filter-title:hover .arrow {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="%23540b0e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>');
    }

    .filter-title.open .arrow {
        transform: rotate(180deg);
    }

    .filter-options {
        max-height: 0;
        overflow: hidden;
        background: #fff;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 4px 4px;
        margin-top: -1px;
        padding: 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: max-height 0.3s ease-out, padding 0.3s ease-out;
    }

    .filter-options.show {
        max-height: 300px;
        padding: 8px 0;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #540b0e #f9f9f9;
    }

    /* Custom scrollbar styles for Webkit browsers */
    .filter-options.show::-webkit-scrollbar {
        width: 6px;
    }

    .filter-options.show::-webkit-scrollbar-track {
        background: #f9f9f9;
        border-radius: 3px;
    }

    .filter-options.show::-webkit-scrollbar-thumb {
        background-color: #540b0e;
        border-radius: 3px;
    }

    .filter-options.show::-webkit-scrollbar-thumb:hover {
        background-color: #3a070a;
    }

    .option {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        cursor: pointer;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 14px;
        color: #333;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .option:hover {
        background: #f9f9f9;
        color: #540b0e;
    }

    .option input[type="checkbox"] {
        margin-right: 8px;
        accent-color: #540b0e;
        cursor: pointer;
    }

    .option.unavailable {
        color: #999;
        opacity: 0.6;
    }

    .option.unavailable:hover {
        background: #f9f9f9;
        color: #540b0e;
        opacity: 1;
    }

    .option.unavailable input[type="checkbox"] {
        accent-color: #999;
    }

    .option.unavailable input[type="checkbox"]:checked {
        accent-color: #540b0e;
    }

    .main-content {
        flex: 0 0 80%;
        transition: all 0.3s ease;
        padding-right: 30px;
        display: flex;
        flex-direction: column;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, 250px);
        gap: 15px;
        transition: all 0.3s ease;
        width: 100%;
        justify-content: start;
        align-content: start;
        margin-top: 0;
    }

    .product-card {
        flex: 0 0 250px;
        width: 250px;
        text-align: center;
        position: relative;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        overflow: visible;
        margin-bottom: 20px;
        align-self: start;
    }

    .product-card.expanded {
        width: 300px;
        flex: 0 0 300px;
    }

    .product-card .image-container {
        overflow: hidden;
        border-radius: 8px;
        margin-bottom: 10px;
        width: 100%;
        height: 300px;
        position: relative;
        background: #f9f9f9;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .product-card.expanded .image-container {
        height: 360px;
    }

    .product-card img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        border-radius: 8px;
        transition: transform 0.3s ease;
        padding: 10px;
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
        height: 100%;
    }

    .product-name {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 16px;
        color: #333;
        margin: 10px 0;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-price {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 16px;
        margin-bottom: 10px;
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
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .quick-view-btn:hover {
        background-color: #fff;
        border-color: #540b0e;
        color: #540b0e;
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
    }

    .product-image-link img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
        padding: 10px;
    }

    .product-image-link:hover img {
        transform: scale(1.05);
    }

    .product-footer {
        text-align: center;
        margin-top: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .product-counter {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 14px;
        color: #333;
    }

    .product-image.blurred {
        filter: blur(5px);
        transform: scale(1.1);
        transition: filter 0.3s ease, transform 0.3s ease;
    }

    .product-card:hover .product-image.blurred {
        filter: blur(5px);
        transform: scale(1.1);
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
        margin: 1.75rem auto;
        max-width: 800px;
    }

    .modal-content {
        position: relative;
        background-color: #fff;
        border-radius: 0.3rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
    }

    .modal-body {
        padding: 0;
    }

    .close {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        color: #000;
        opacity: 0.5;
        background: none;
        border: 0;
        padding: 0;
        cursor: pointer;
    }

    .close:hover {
        opacity: 0.75;
    }

    .load-more-container {
        text-align: center;
        margin: 30px 0;
    }

    .load-more-btn {
        background-color: #f9f9f9;
        color: #333;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 12px 24px;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .load-more-btn:hover {
        background-color: #fff;
        border-color: #540b0e;
        color: #540b0e;
    }

    .load-more-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .product-counter {
        text-align: center;
        margin: 20px 0;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 14px;
        color: #666;
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

    @media (max-width: 768px) {
        .sidebar {
            position: fixed;
            left: -100%;
            top: 0;
            height: 100vh;
            width: 80%;
            max-width: 300px;
            z-index: 1000;
            background: white;
            padding: 20px;
            transition: left 0.3s ease;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
        }
    }
</style>

<?= $this->Html->script('https://code.jquery.com/jquery-3.6.0.min.js', ['block' => true]) ?>
<script>
    let activeFilters = {
        Price: [],
        Variety: [],
        Region: [],
        Vintage: [],
        Volume: [],
        Brand: []
    };
    let filtersVisible = true;
    const csrfToken = <?= json_encode($this->request->getAttribute('csrfToken')) ?>;

    function toggleDropdown(id) {
        const options = document.getElementById(id);
        const filterTitle = options.previousElementSibling;
        const isVisible = options.classList.contains('show');

        if (isVisible) {
            options.classList.remove('show');
            filterTitle.classList.remove('open');
        } else {
            options.classList.add('show');
            filterTitle.classList.add('open');
        }
    }

    function toggleFilters() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const productGrid = document.querySelector('.product-grid');
        const productCards = document.querySelectorAll('.product-card');
        const toggleText = document.getElementById('toggle-text');

        if (window.innerWidth > 768) {
            if (filtersVisible) {
                sidebar.style.display = 'none';
                mainContent.style.flex = '0 0 100%';
                mainContent.style.paddingRight = '0';
                productGrid.style.gridTemplateColumns = 'repeat(auto-fit, 300px)';
                productGrid.style.justifyContent = 'space-between';
                productCards.forEach(card => card.classList.add('expanded'));
                toggleText.textContent = 'Show Filters';
            } else {
                sidebar.style.display = 'block';
                mainContent.style.flex = '0 0 80%';
                mainContent.style.paddingRight = '30px';
                productGrid.style.gridTemplateColumns = 'repeat(4, 250px)';
                productGrid.style.justifyContent = 'start';
                productCards.forEach(card => card.classList.remove('expanded'));
                toggleText.textContent = 'Hide Filters';
            }
            filtersVisible = !filtersVisible;
        }
    }

    function applyFilter(filterName, checkbox) {
        const value = checkbox.value;
        const isChecked = checkbox.checked;

        if (isChecked) {
            if (!activeFilters[filterName].includes(value)) {
                activeFilters[filterName].push(value);
            }
        } else {
            activeFilters[filterName] = activeFilters[filterName].filter(v => v !== value);
        }

        let queryParams = {};
        for (const [name, values] of Object.entries(activeFilters)) {
            if (values.length > 0) {
                switch (name.toLowerCase()) {
                    case 'price':
                        queryParams['price'] = values;
                        break;
                    case 'variety':
                        queryParams['wine_style_id'] = values;
                        break;
                    case 'region':
                        queryParams['region'] = values;
                        break;
                    case 'vintage':
                        queryParams['vintage'] = values;
                        break;
                    case 'volume':
                        queryParams['volume'] = values;
                        break;
                    case 'brand':
                        queryParams['brand'] = values;
                        break;
                }
            }
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('sort')) {
            queryParams['sort'] = urlParams.get('sort');
        }

        const queryString = new URLSearchParams();
        for (const [key, value] of Object.entries(queryParams)) {
            if (Array.isArray(value)) {
                value.forEach(val => queryString.append(key + '[]', val));
            } else {
                queryString.append(key, value);
            }
        }

        location.href = '/products/page' + (queryString.toString() ? '?' + queryString.toString() : '');
    }

    function removeFilter(filterName, filterValue) {
        activeFilters[filterName] = activeFilters[filterName].filter(v => v !== filterValue);

        let queryParams = {};
        for (const [name, values] of Object.entries(activeFilters)) {
            if (values.length > 0) {
                switch (name.toLowerCase()) {
                    case 'price':
                        queryParams['price'] = values;
                        break;
                    case 'variety':
                        queryParams['wine_style_id'] = values;
                        break;
                    case 'region':
                        queryParams['region'] = values;
                        break;
                    case 'vintage':
                        queryParams['vintage'] = values;
                        break;
                    case 'volume':
                        queryParams['volume'] = values;
                        break;
                    case 'brand':
                        queryParams['brand'] = values;
                        break;
                }
            }
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('sort')) {
            queryParams['sort'] = urlParams.get('sort');
        }

        const queryString = new URLSearchParams();
        for (const [key, value] of Object.entries(queryParams)) {
            if (Array.isArray(value)) {
                value.forEach(val => queryString.append(key + '[]', val));
            } else {
                queryString.append(key, value);
            }
        }

        location.href = '/products/page' + (queryString.toString() ? '?' + queryString.toString() : '');
    }

    function updateFilterBubbles() {
        const filterBubbles = document.getElementById('filter-bubbles');
        const clearAllButton = document.getElementById('clear-all');
        const resultCount = document.querySelector('.filter-toggle p');
        filterBubbles.innerHTML = '';
        let hasFilters = false;

        for (const [filterName, values] of Object.entries(activeFilters)) {
            values.forEach(value => {
                let displayValue = value;
                if (filterName === 'Price') {
                    displayValue = value === '0_50' ? '$0 - $50' : value === '50_100' ? '$50 - $100' : value === '100_plus' ? '$100+' : value;
                }
                const bubble = document.createElement('div');
                bubble.className = 'filter-bubble';
                bubble.innerHTML = `${filterName}: ${displayValue} <span class="remove-filter" onclick="removeFilter('${filterName}', '${value}')">×</span>`;
                filterBubbles.appendChild(bubble);
                hasFilters = true;
            });
        }

        clearAllButton.style.display = hasFilters ? 'inline-block' : 'none';
        if (resultCount) {
            resultCount.style.display = hasFilters ? 'block' : 'none';
        }
        updateSidebarPosition();
    }

    function updateSidebarPosition() {
        const stickyFilterContainer = document.querySelector('.sticky-filter-container');
        const sidebar = document.getElementById('sidebar');
        const navbarHeight = 60;
        if (stickyFilterContainer && sidebar) {
            const stickyHeight = stickyFilterContainer.offsetHeight;
            const newTop = navbarHeight + stickyHeight;
            sidebar.style.top = `${newTop}px`;
        }
    }

    function updateProductCounter() {
        console.log('Updating counter: displayed=', displayedProducts, 'total=', totalProducts);
        const counter = document.getElementById('product-counter');
        if (counter) {
            counter.textContent = `You're viewing ${displayedProducts} of ${totalProducts} products`;
        } else {
            console.error('Product counter not found');
        }
    }

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
            const productId = event.target.id.split('-').pop();
            closeQuickView(productId);
        }
    }

    // Function to initialize add to cart forms
    function initializeAddToCartForms() {
        // No need to add event listeners for form submission
        // The forms will submit normally
    }

    // Initialize add to cart forms when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        activeFilters = {
            Price: [],
            Variety: [],
            Region: [],
            Vintage: [],
            Volume: [],
            Brand: []
        };

        <?php
        $queryParams = $this->request->getQueryParams();
        if (isset($queryParams['price'])) {
            $prices = is_array($queryParams['price']) ? $queryParams['price'] : [$queryParams['price']];
            foreach ($prices as $price) {
                $displayPrice = $price === '0_50' ? '$0 - $50' : ($price === '50_100' ? '$50 - $100' : ($price === '100_plus' ? '$100+' : h($price)));
                echo "activeFilters['Price'].push('$price');";
            }
        }
        if (isset($queryParams['wine_style_id'])) {
            $styles = is_array($queryParams['wine_style_id']) ? $queryParams['wine_style_id'] : [$queryParams['wine_style_id']];
            foreach ($styles as $style) {
                echo "activeFilters['Variety'].push('" . h($style) . "');";
            }
        }
        if (isset($queryParams['region'])) {
            $regions = is_array($queryParams['region']) ? $queryParams['region'] : [$queryParams['region']];
            foreach ($regions as $region) {
                echo "activeFilters['Region'].push('" . h($region) . "');";
            }
        }
        if (isset($queryParams['vintage'])) {
            $vintages = is_array($queryParams['vintage']) ? $queryParams['vintage'] : [$queryParams['vintage']];
            foreach ($vintages as $vintage) {
                echo "activeFilters['Vintage'].push('" . h($vintage) . "');";
            }
        }
        if (isset($queryParams['volume'])) {
            $volumes = is_array($queryParams['volume']) ? $queryParams['volume'] : [$queryParams['volume']];
            foreach ($volumes as $volume) {
                echo "activeFilters['Volume'].push('" . h($volume) . "');";
            }
        }
        if (isset($queryParams['brand'])) {
            $brands = is_array($queryParams['brand']) ? $queryParams['brand'] : [$queryParams['brand']];
            foreach ($brands as $brand) {
                echo "activeFilters['Brand'].push('" . h($brand) . "');";
            }
        }
        ?>

        // Check if any filters are applied
        const urlParams = new URLSearchParams(window.location.search);
        const hasFilters = urlParams.has('price') || urlParams.has('wine_style_id') || urlParams.has('region') || urlParams.has('vintage') || urlParams.has('volume') || urlParams.has('brand');

        // Show/hide result count based on filters
        const resultCount = document.querySelector('.filter-toggle p');
        if (resultCount) {
            resultCount.style.display = hasFilters ? 'block' : 'none';
        }

        updateFilterBubbles();

        const filterToggle = document.getElementById('toggle-filters');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleText = document.getElementById('toggle-text');

        // Function to handle mobile sidebar
        function handleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleText = document.getElementById('toggle-text');
            const filterToggle = document.getElementById('toggle-filters');

            // Create overlay
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);

            // Update button text
            if (toggleText) {
                toggleText.textContent = 'Filters';
            }

            // Remove any existing click handlers
            const newFilterToggle = filterToggle.cloneNode(true);
            filterToggle.parentNode.replaceChild(newFilterToggle, filterToggle);

            // Set up click handlers
            newFilterToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            });

            overlay.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }

        // Function to handle desktop sidebar
        function handleDesktopSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleText = document.getElementById('toggle-text');
            const filterToggle = document.getElementById('toggle-filters');

            // Remove overlay
            const overlay = document.querySelector('.sidebar-overlay');
            if (overlay) {
                overlay.remove();
            }

            // Reset sidebar classes
            sidebar.classList.remove('active');

            // Update button text
            if (toggleText) {
                toggleText.textContent = 'Hide filters';
            }

            // Remove any existing click handlers
            const newFilterToggle = filterToggle.cloneNode(true);
            filterToggle.parentNode.replaceChild(newFilterToggle, filterToggle);

            // Set up click handler
            newFilterToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleFilters();
            });
        }

        // Set up appropriate view
        function setupSidebar() {
            if (window.innerWidth <= 767) {
                handleMobileSidebar();
            } else {
                handleDesktopSidebar();
            }
        }

        // Initial setup
        setupSidebar();

        // Handle resize
        window.addEventListener('resize', setupSidebar);
    });

    document.addEventListener('DOMContentLoaded', function() {
        const loadMoreBtn = document.getElementById('load-more-btn');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function() {
                const currentPage = parseInt(this.dataset.page);
                const totalPages = parseInt(this.dataset.totalPages);
                const nextPage = currentPage + 1;

                // Show loading state
                this.textContent = 'Loading...';
                this.disabled = true;

                // Get current URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('page', nextPage);

                // Make AJAX request
                fetch(`<?= $this->Url->build(['action' => 'loadMore']) ?>?${urlParams.toString()}`)
                    .then(response => response.text())
                    .then(html => {
                        // Append new products
                        const productGrid = document.getElementById('product-grid');
                        productGrid.insertAdjacentHTML('beforeend', html);

                        // Apply expanded class to new cards if filters are hidden
                        if (!filtersVisible) {
                            const newCards = productGrid.querySelectorAll('.product-card:not(.expanded)');
                            newCards.forEach(card => card.classList.add('expanded'));
                        }

                        // Update button state
                        this.dataset.page = nextPage;
                        this.textContent = 'Load More Products';
                        this.disabled = false;

                        // Hide button if we've reached the last page
                        if (nextPage >= totalPages) {
                            this.parentElement.style.display = 'none';
                        }

                        // Update product counter
                        const productCounter = document.querySelector('.product-counter');
                        if (productCounter) {
                            const currentCount = document.querySelectorAll('.product-card').length;
                            productCounter.textContent = `Showing ${currentCount} of ${<?= $totalCount ?>} products`;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading more products:', error);
                        this.textContent = 'Load More Products';
                        this.disabled = false;
                    });
            });
        }
    });
</script>

