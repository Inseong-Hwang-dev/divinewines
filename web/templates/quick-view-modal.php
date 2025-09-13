<!-- Quick View Modal -->
<div id="quickViewModal" class="quick-view-modal">
    <div class="quick-view-content">
        <button type="button" class="quick-view-close">&times;</button>
        <div class="quick-view-body">
            <div class="quick-view-image">
                <img id="quickViewImage" src="" alt="Product Image">
            </div>
            <div class="quick-view-details">
                <h2 id="quickViewTitle" class="quick-view-title"></h2>
                <div id="quickViewPrice" class="quick-view-price"></div>
                <div class="quick-view-meta">
                    <div class="quick-view-meta-item">
                        <span class="quick-view-meta-label">Category:</span>
                        <span id="quickViewCategory" class="quick-view-meta-value"></span>
                    </div>
                    <div class="quick-view-meta-item">
                        <span class="quick-view-meta-label">Stock:</span>
                        <span id="quickViewStock" class="quick-view-meta-value"></span>
                    </div>
                    <div class="quick-view-meta-item">
                        <span class="quick-view-meta-label">SKU:</span>
                        <span id="quickViewSku" class="quick-view-meta-value"></span>
                    </div>
                    <div class="quick-view-meta-item">
                        <span class="quick-view-meta-label">Volume:</span>
                        <span id="quickViewVolume" class="quick-view-meta-value"></span>
                    </div>
                    <div class="quick-view-meta-item">
                        <span class="quick-view-meta-label">Brand:</span>
                        <span id="quickViewBrand" class="quick-view-meta-value"></span>
                    </div>
                </div>
                
                <div class="quick-view-description">
                    <h3 class="description-title">Description</h3>
                    <p id="quickViewDescription" class="description-text"></p>
                </div>
                
                <form id="quickViewForm" class="quick-view-form">
                    <input type="hidden" id="quickViewProductId" name="product_id">
                    <div class="quick-view-quantity">
                        <label for="quickViewQuantity">Quantity:</label>
                        <input type="number" id="quickViewQuantity" name="quantity" value="1" min="1" max="99" required>
                    </div>
                    <button type="submit" class="quick-view-submit">Add to Cart</button>
                </form>
                
                <div id="quickViewAlert" class="alert" style="display: none;"></div>
            </div>
        </div>
    </div>
</div> 