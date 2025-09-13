<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\CartItem> $cartItems
 * @var float $totalAmount
 */
?>
<div class="orders checkout content" style="padding-top: 70px;">
    <!-- Header Section -->
    <div class="header-image">
        <img src="https://images.unsplash.com/photo-1519671282429-b44660ead0a7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1380&h=200&q=80" alt="Checkout Header">
    </div>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bolder page-header">Checkout</h1>
            <p class="text-muted" style="font-family: Comfortaa, sans-serif">-- Complete your purchase --</p>
        </div>

        <!-- Cart Summary Cards -->
        <div class="cart-summary-cards mb-5">
            <div class="summary-card">
                <i class="bi bi-cart"></i>
                <h3 style="font-family: Comfortaa, sans-serif">Total Items</h3>
                <p><?= $totalCartItems ?></p>
            </div>
            <div class="summary-card">
<!--                <i class="bi bi-currency-dollar"></i> this icon is not working-->
                <i class="bi bi-cash"></i>
                <h3  style="font-family: Comfortaa, sans-serif">Total Amount</h3>
                <p id="cart-total" data-base-total="<?= $totalAmount ?>"><?= $this->Number->currency($totalAmount) ?></p>
            </div>
        </div>

        <!-- CheckOut Progress Bar-->
        <div class="text-center pb-2 pt-4">
            <span class="previous"> Cart </span><span> > </span><span class="current"> Shipping </span> > <span class="next"> Payment </span>
        </div>

        <div class="row info-row rounded m-3">
            <!-- Shipping Information Form -->
            <div class="col shipping-form-section">
                <div class="text-center mb-2">
                    <h3 class="mb-2 shipping-header">Shipping Information</h3>
                    <p class="shipping-subheading">Please enter your shipping information before checking out.</p>
                </div>

                <?= $this->Form->create(null, ['class' => 'shipping-form']);
                $this->Form->unlockField('shipping_cost');
                $this->Form->unlockField('coupon_code');
                $this->Form->unlockField('discount_amount');?> <!--allows shipping cost, coupon code and discount amount to be submitted without triggering cakephp security-->
                <div class="form-group text-center pb-5" style="font-family: Comfortaa, sans-serif;">
                    <div class="mb-3"><?= $this->Form->control('shipping_country', ['label' => 'Country *',
                            'class' => 'form-control',
                            'type'=> 'select',
                            'options'=> $shippingCountries,
                            'id'=>'shipping_country',
//                            'default' => 'AU',
                            'value'=> $user -> user_country ?? 'AU',
                            'required' => true]);?>
                    </div>
                    <div class="mb-3"><?= $this->Form->control('shipping_name', ['label' => 'Full Name *',
                            'class' => 'form-control',
                            'id'=>'shipping_name',
                            'maxlength' => '100',
                            'pattern' => '^[A-Za-z\s\-]+$',
                            'title'=>'Only letters, spaces, and hyphens are allowed',
                            'value'=> ($user -> first_name .' ' . $user->last_name) ?? '',
                            'required' => true]);?>
                    </div>
                    <div class="mb-3"><?= $this->Form->control('shipping_address1', [
                        'label' => 'Address *',
                        'class' => 'form-control',
                        'id'=>'shipping_address1',
                        'required' => true,
                        'maxlength' => '255',
                        'placeholder' => 'Address Line 1',
                        'value' =>  $user -> user_address1 ?? '',
                    ]) ?>
                    </div>
                    <div class="mb-3"><?= $this->Form->control('shipping_address2', [
                        'label' => 'Address line 2',
                        'class' => 'form-control',
                        'maxlength' => '255',
                        'placeholder' => 'Apartment, suite, etc.',
                        'value' => $user-> user_address2 ?? '',
                    ]) ?>
                    </div>
                    <div class="mb-3"><?= $this->Form->control('shipping_state', ['label' => 'State/Province *',
                            'class' => 'form-control',
                            'id'=>'shipping_state',
                            'type'=> 'select',
                            'options'=>[],
//                            'empty'=>'-- Select State --',
                            'value'=> $user -> user_state ?? 'Victoria',
                            'required' => true]);?>
                    </div>
                    <div class="row">
                        <div class="col mb-3"><?= $this->Form->control('shipping_city', ['label' => 'City *',
                                'class' => 'form-control',
                                'id'=>'shipping_city',
                                'maxlength' => '100',
                                'pattern' => '^[A-Za-z\s\-]+$',
                                'title'=>'Only letters, spaces, and hyphens are allowed',
                                'value'=> $user -> user_city ?? '',
                                'required' => true
                            ]);?>
                        </div>
                        <div class="col mb-3"><?= $this->Form->control('shipping_postcode', ['label' => 'Postcode *',
                                'class' => 'form-control',
                                'id'=> 'shipping_postcode',
                                'maxlength' => '10',
                                'pattern' => '^[A-Za-z0-9\s\-]{4,10}$', // alphanumeric, hyphens, spaces, 4-10 characters
                                'title' => 'Only letters, numbers, spaces, and hyphens are allowed (4-10 characters)',
                                'value'=> $user -> user_postcode ?? '',
                                'required' => true]);?>
                        </div>
                        <div class="form-check mb-3">
                            <?= $this->Form->checkbox('save_address', ['value' => '1', 'class' => 'form-check-input']) ?>
                            <?= $this->Form->label('save_address', 'Save this address for faster checkout next time.', ['class' => 'form-check-label']) ?>
                        </div>
                        <?= $this->Form->control('shipping_cost', [
                            'type' => 'hidden',
                            'id' => 'shipping_cost_hidden',
                            'name' => 'shipping_cost',
                            'value'=> '0.00',
                        ]) ?>
                    </div>
                </div>
                <div class="btn-container text-end">
                    <?= $this->Form->button(
                        'Proceed to Payment',
                        ['class' => 'btn btn-lg proceed-btn' , 'id' => 'proceed-btn', 'disabled' => true]
                    ) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>

            <!-- Cart Items Table -->
            <div class="col cart-items-section">
                <h3 class="mb-4 text-center order-summary-header">Order Summary</h3>
                <div class="table-responsive">
                    <table class="cart-items-table" style="font-family: Comfortaa, sans-serif">
                        <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td>
                                    <span class="product-name"><?= h($item->product->name) ?></span>
                                </td>
                                <td>
                                    <span class="product-price"><?= $this->Number->currency($item->product->price) ?></span>
                                </td>
                                <td>
                                    <span class="product-quantity"><?= $this->Number->format($item->product_quantity) ?></span>
                                </td>
                                <td>
                                    <span class="product-subtotal"><?= $this->Number->currency($item->product->price * $item->product_quantity) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-end">Coupon:</td>
                            <td>
                                <div class="input-group">
                                    <select id="coupon-code" class="form-control">
                                        <option value="">Select a coupon</option>
                                        <?php foreach ($availableCoupons as $coupon): ?>
                                            <option value="<?= h($coupon->coupon_code) ?>"
                                                data-discount="<?= h($coupon->discount_amount) ?>"
                                                data-min-purchase="<?= h($coupon->min_purchase_amount) ?>"
                                                data-max-discount="<?= h($coupon->max_discount_amount) ?>"
                                                data-expiry="<?= $coupon->expiry_date ? h($coupon->expiry_date->format('Y-m-d H:i')) : 'No expiry' ?>"
                                                data-usage-limit="<?= $coupon->usage_limit ? h($coupon->usage_limit) : 'Unlimited' ?>"
                                                data-times-used="<?= h($coupon->times_used) ?>">
                                                <?= h($coupon->coupon_code) ?> (<?= h($coupon->discount_amount) ?>% off)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button" id="apply-coupon">Apply</button>
                                </div>
                                <div id="coupon-message" class="small text-muted mt-1"></div>
                                <div id="coupon-details" class="small mt-2" style="display: none;">
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <h6 class="card-title mb-2">Coupon Details:</h6>
                                            <ul class="list-unstyled mb-0">
                                                <li><strong>Discount:</strong> <span id="coupon-discount"></span></li>
                                                <li><strong>Min. Purchase:</strong> <span id="coupon-min-purchase"></span></li>
                                                <li><strong>Max. Discount:</strong> <span id="coupon-max-discount"></span></li>
                                                <li><strong>Expiry:</strong> <span id="coupon-expiry"></span></li>
                                                <li><strong>Usage:</strong> <span id="coupon-usage"></span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">Shipping Cost:</td>
                            <td class="shipping-cost" id="shipping-cost">$0.00</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                            <td><strong class="total-amount" data-base-total="<?= $totalAmount ?>"><?= $this->Number->currency($totalAmount)?></strong></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->Html->css('admin-table.css') ?>

<!-- JQuery-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!--JQuery to get shipping cost-->
<script>

    function checkFormFilled() {
        const name = $('#shipping_name').val().trim();
        const address = $('#shipping_address1').val().trim();
        const city = $('#shipping_city').val().trim();
        const state = $('#shipping_state').val().trim();
        const postcode = $('#shipping_postcode').val().trim();
        const shippingCost = parseFloat($('#shipping_cost_hidden').val());

        const isFormFilled =
            name !== '' &&
            address !== '' &&
            postcode !== '' &&
            city !== '' &&
            state !== '' &&
            !isNaN(shippingCost) &&
            shippingCost > 0;

        $('#proceed-btn').prop('disabled', !isFormFilled);
    }

    $('#shipping_name, #shipping_address1, #shipping_city, #shipping_state, #shipping_postcode').on('input', checkFormFilled); //check if the form is filled

    document.addEventListener('DOMContentLoaded', function(){
        fetch('<?= $this->Url->build('/docs/countries_states.json') ?>') //get the states database from the doc directory
            .then(response => response.json())
            .then(data => {
                const countryInput = document.getElementById('shipping_country');
                console.log("Country Saved:", "<?= h($user->user_country ?? '')?>");
                const stateInput = document.getElementById('shipping_state');

                // Populate state field
                function populateStates(){
                    const selectedCountry = countryInput.options[countryInput.selectedIndex].text.trim();
                    console.log("Selected Country", selectedCountry);

                    const country = data.find(c => c.name === selectedCountry);
                    const states = country ? country.states : [];
                    console.log("States:", states)

                    //Clear existing options
                    stateInput.innerHTML ='<option value="">-- Select State --</option>';

                    // Add new state options
                    states.forEach(state => {
                        const option = document.createElement('option');
                        option.value = state;
                        option.textContent = state;
                        stateInput.appendChild(option);
                    });

                    // Set the saved state after populating
                    const savedState = '<?= h($user->user_state ?? '') ?>';
                    if (savedState) {
                        stateInput.value = savedState;
                    }
                }

                countryInput.addEventListener('change', populateStates);

                // If the country field is autofilled, populate state field manually
                window.addEventListener('load', () => {
                    requestAnimationFrame(() => {
                        const savedCountry = countryInput.options[countryInput.selectedIndex].text.trim();
                        if (savedCountry) {
                            populateStates();
                        }
                    });
                });
                populateStates();
            })
            .catch(error => console.error("Error loading states:", error));
    })

    // Calculate shipping cost based on address
    function calculateShipping() {
        const toPostcode =  $('#shipping_postcode').val()?.trim();
        const selectedCountry = $('#shipping_country').val()?.trim();

        const isDomestic = selectedCountry === 'AU';
        const ajaxUrl = isDomestic
            ? '<?= $this->Url->build('/orders/get-domestic-postcode-details') ?>'
            : '<?= $this->Url->build('/orders/get-international-shipping-rate') ?>';

        $.ajax({
            url: ajaxUrl,
            type: 'GET',
            data: { to_postcode: toPostcode, shipping_country: selectedCountry },
            success: function (response) {
                const shippingCost = parseFloat(response.price); //get shipping cost
                console.log("shipping cost:", shippingCost);

                if(isNaN(shippingCost)){
                    $('#shipping-cost').text('$0.00');
                    $('#shipping_cost_hidden').val('0.00');
                    $('#proceed-btn').prop('disabled', true);

                    // Mark postcode field as invalid
                    $('#shipping_postcode').addClass('is-invalid');

                    if ($('#shipping_postcode').next('.invalid-feedback').length === 0) {
                        $('#shipping_postcode').after('<div class="invalid-feedback text-danger">Postcode is invalid</div>');
                    }
                }
                else {
                    // Remove invalid field
                    $('#shipping_postcode').removeClass('is-invalid');
                    $('#shipping_postcode').next('.invalid-feedback').remove();

                    $('#shipping-cost').text('$' + shippingCost.toFixed(2));
                    $('#shipping_cost_hidden').val(shippingCost);

                    // Get the cart total from the page
                    const cartTotalText = $('#cart-total').text().replace(/[^0-9.-]+/g, '');
                    const cartTotal = parseFloat(cartTotalText);

                    // Calculate final total
                    const baseCartTotal = parseFloat($('#cart-total').data('base-total')); // original total
                    const finalTotal = baseCartTotal + shippingCost;
                    $('#cart-total').text('$' + finalTotal.toFixed(2));
                    $('.total-amount').text('$' + finalTotal.toFixed(2));

                    // Enable button only if shipping cost is valid
                    checkFormFilled();
                }
            },
            error: function () {
                $('#shipping-cost').text('Error calculating shipping');
                $('#proceed-btn').prop('disabled', true);

                // Mark postcode field as invalid
                $('#shipping_postcode').addClass('is-invalid');

                if ($('#shipping_postcode').next('.invalid-feedback').length === 0) {
                    $('#shipping_postcode').after('<div class="invalid-feedback text-danger">Postcode is invalid</div>');
                }

            }
        });
    };


    //  if user enters postcode, calculate shipping
    $('#shipping_postcode, #shipping_country').on('change', calculateShipping);

    // When the page loads, trigger the change event if the postcode is autofilled
    window.addEventListener('load', () => {
        requestAnimationFrame(() => {
            const savedPostcode = $('#shipping_postcode').val().trim();
            const savedCountry = $('#shipping_country').val().trim();
            // console.log( "shipping country;", savedCountry);
            if (savedPostcode || savedCountry) {
                calculateShipping();
            }
        });
    });

    // Coupon handling
    let appliedCoupon = null;

    $('#apply-coupon').on('click', function() {
        const couponSelect = $('#coupon-code');
        const selectedOption = couponSelect.find('option:selected');
        const couponCode = selectedOption.val();

        if (!couponCode) {
            $('#coupon-message').text('Please select a coupon').addClass('text-danger').removeClass('text-success');
            return;
        }

        // Get the base total before any discounts
        const baseTotal = parseFloat($('#cart-total').data('base-total'));
        const minPurchase = parseFloat(selectedOption.data('min-purchase')) || 0;
        const maxDiscount = parseFloat(selectedOption.data('max-discount')) || 0;
        const discountPercentage = parseFloat(selectedOption.data('discount'));

        // Calculate potential discount
        let discount = (baseTotal * discountPercentage / 100);

        // Validate both minimum purchase and maximum discount
        let validationMessage = '';
        if (minPurchase > 0 && baseTotal < minPurchase) {
            validationMessage = `Minimum purchase amount of $${minPurchase} required`;
        }
        if (maxDiscount > 0 && discount > maxDiscount) {
            if (validationMessage) {
                validationMessage += ' and ';
            }
            validationMessage += `Maximum discount amount of $${maxDiscount} exceeded`;
        }

        if (validationMessage) {
            $('#coupon-message').text(validationMessage).addClass('text-danger').removeClass('text-success');
            return;
        }

        // Apply max discount limit if set
        if (maxDiscount > 0 && discount > maxDiscount) {
            discount = maxDiscount;
        }

        appliedCoupon = {
            code: couponCode,
            discount_percentage: discountPercentage,
            min_purchase: minPurchase,
            max_discount: maxDiscount
        };

        $('#coupon-message').text('Coupon applied successfully!').addClass('text-success').removeClass('text-danger');

        // Update total with discount
        const shippingCost = parseFloat($('#shipping_cost_hidden').val() || 0);
        const finalTotal = baseTotal - discount + shippingCost;

        // Add discount row if it doesn't exist
        if ($('#discount-row').length === 0) {
            $('.shipping-cost').closest('tr').before(
                `<tr id="discount-row">
                    <td colspan="3" class="text-end">Discount (${discountPercentage}%):</td>
                    <td class="text-danger">-$${discount.toFixed(2)}</td>
                </tr>`
            );
        } else {
            $('#discount-row td:last').text(`-$${discount.toFixed(2)}`);
        }

        // Update total
        $('#cart-total').text('$' + finalTotal.toFixed(2));
        $('.total-amount').text('$' + finalTotal.toFixed(2));
    });

    // Update coupon details when selection changes
    $('#coupon-code').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const couponDetails = $('#coupon-details');

        if (selectedOption.val()) {
            // Show coupon details
            couponDetails.show();

            // Update details
            $('#coupon-discount').text(selectedOption.data('discount') + '% off');

            const minPurchase = selectedOption.data('min-purchase');
            $('#coupon-min-purchase').text(minPurchase ? '$' + minPurchase : 'None');

            const maxDiscount = selectedOption.data('max-discount');
            $('#coupon-max-discount').text(maxDiscount ? '$' + maxDiscount : 'None');

            $('#coupon-expiry').text(selectedOption.data('expiry'));

            const usageLimit = selectedOption.data('usage-limit');
            const timesUsed = selectedOption.data('times-used');
            $('#coupon-usage').text(usageLimit === 'Unlimited' ?
                'Unlimited' :
                timesUsed + ' of ' + usageLimit + ' uses');

            // Validate both minimum purchase and maximum discount
            const baseTotal = parseFloat($('#cart-total').data('base-total'));
            const discountPercentage = parseFloat(selectedOption.data('discount'));
            let discount = (baseTotal * discountPercentage / 100);

            let validationMessage = '';
            if (minPurchase > 0 && baseTotal < minPurchase) {
                validationMessage = `Minimum purchase amount of $${minPurchase} required`;
            }
            if (maxDiscount > 0 && discount > maxDiscount) {
                if (validationMessage) {
                    validationMessage += ' and ';
                }
                validationMessage += `Maximum discount amount of $${maxDiscount} exceeded`;
            }

            if (validationMessage) {
                $('#apply-coupon').prop('disabled', true);
                $('#coupon-message').text(validationMessage).addClass('text-danger').removeClass('text-success');
            } else {
                $('#apply-coupon').prop('disabled', false);
                $('#coupon-message').text('').removeClass('text-danger text-success');
            }
        } else {
            // Hide coupon details
            couponDetails.hide();
            $('#apply-coupon').prop('disabled', false);
            $('#coupon-message').text('').removeClass('text-danger text-success');
        }

        // Reset coupon application
        if (appliedCoupon) {
            appliedCoupon = null;
            $('#coupon-message').text('').removeClass('text-success text-danger');
            $('#discount-row').remove();

            // Reset total without discount
            const baseTotal = parseFloat($('#cart-total').data('base-total'));
            const shippingCost = parseFloat($('#shipping_cost_hidden').val() || 0);
            const finalTotal = baseTotal + shippingCost;
            $('#cart-total').text('$' + finalTotal.toFixed(2));
            $('.total-amount').text('$' + finalTotal.toFixed(2));
        }
    });

    // Add coupon code and discount percentage to form data when submitting
    $('.shipping-form').on('submit', function(e) {
        if (appliedCoupon) {
            $('<input>').attr({
                type: 'hidden',
                name: 'coupon_code',
                value: appliedCoupon.code
            }).appendTo(this);
            $('<input>').attr({
                type: 'hidden',
                name: 'discount_amount',
                value: appliedCoupon.discount_percentage
            }).appendTo(this);
        }
    });

</script>

<style>
    /* ==========================================================================
       Header
       ========================================================================== */
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

    .page-header{
        font-family: "Gwendolyn", cursive;
        font-weight: 700;
        font-size: 80px;
    }

    /* ==========================================================================
       Cart Summary Cards
       ========================================================================== */
    .cart-summary-cards {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 30px;
    }

    .summary-card {
        flex: 1;
        max-width: 300px;
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
        Progress Bar
   ========================================================================== */
    .previous{
        color: var(--secondary-products-light);
        padding-right: 8px;
    }

    .current{
        font-weight: bold;
        color:var(--secondary-products-color);
        padding:8px;
    }

    .next{
        color: var(--primary-products-color);
        padding:8px;
    }


    /* ==========================================================================
       Cart Items Table
       ========================================================================== */
    .info-row{
        border-radius: 8px;
        background: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding:2px
    }

    .order-summary-header{
        font-family: 'Lora', serif;
    }
    .cart-items-section {
        background: white;
        padding: 20px;
        /*border-radius: 8px;*/
        /*box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);*/
    }

    .cart-items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .cart-items-table th {
        background-color: #f8f9fa;
        padding: 12px;
        text-align: left;
        font-weight: 500;
        color: var(--text-label-color);
    }

    .cart-items-table td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
    }

    .product-name {
        font-weight: 500;
        color: var(--primary-products-color);
    }

    .product-quantity {
        color: var(--product-text-color);
    }

    .product-price {
        color: var(--secondary-products-color);
    }

    .product-subtotal {
        font-weight: bold;
        color: var(--primary-products-color);
    }

    .total-amount {
        color: var(--primary-products-color);
        font-size: 1.2rem;
    }

    /* ==========================================================================
       Shipping Form
       ========================================================================== */
    .shipping-header{
        font-family: 'Lora', serif;
        padding-left: 20px;
    }

    .shipping-subheading{
        font-family: Comfortaa, sans-serif;
        color: white
    }
    .shipping-form-section {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        background: var(--secondary-products-light);
        padding: 20px;
        border-top-left-radius:8px;
        border-bottom-left-radius: 8px;
    }

    .shipping-form label {
        text-align: left;
        display: block;
    }

    .shipping-form {
        max-width: 600px;
        padding: 20px;
    }

    .form-control {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 10px;
        width: 100%;
    }

    .form-control:focus {
        border-color: var(--secondary-products-color);
        box-shadow: 0 0 0 0.2rem rgba(var(--secondary-products-color-rgb), 0.25);
    }

    input.form-control, select.form-control, textarea.form-control {
        border: 2px solid #dddddd ; /* Adds stronger styling */
        border-radius: 8px;
    }

    input.form-control:focus, select.form-control:focus, textarea.form-control:focus {
        border:3px solid var(--secondary-products-color) !important;
        outline: none;
        box-shadow: 0 0 10px rgb(139, 149, 132); /* Optional glow effect */

    }

    .proceed-btn {
        font-family: Comfortaa, sans-serif;
        position: relative;
        overflow: hidden;
        background-color: white;
        color: var(--secondary-products-color);
        border: 3px solid var(--secondary-products-color);
        margin-top: 10px;
        padding: 12px 24px;
        font-size: 1.1rem;
        transition: color 0.3s ease, border-color 0.3s ease;
        z-index: 1;
    }

    .proceed-btn::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background-color: var(--primary-products-color);
        z-index: -1;
        transition: left 0.3s ease;
    }

    .proceed-btn:hover::before {
        left: 0;
    }

    .proceed-btn:hover {
        color: white;
        border-color: var(--primary-products-color);
    }

    .proceed-btn:disabled {
        color: #ececec;
        border: 3px #C6C6C6FF;
        background-color: #C6C6C6FF;
        transform: translateY(-2px);
    }

    /* ==========================================================================
       Responsive Design
       ========================================================================== */
    @media (max-width: 768px) {
        .cart-summary-cards {
            flex-direction: column;
            align-items: center;
        }

        .summary-card {
            width: 100%;
            max-width: none;
            margin-bottom: 15px;
        }

        .cart-items-table {
            display: block;
            overflow-x: auto;
        }

        .shipping-form {
            padding: 0 15px;
        }

        .cart-items-section, .shipping-form-section {
            margin-bottom: 20px;
        }

        .shipping-form-section {
            padding-bottom:30px;
            border-top-left-radius:0px;
            border-bottom-left-radius: 0px;
        }

        .btn-container{
            text-align: center !important;
        }

    }

    .payment-btn {
        font-family: Comfortaa, sans-serif;
        background-color: var(--primary-products-color);
        color: white;
        border: none;
        padding: 12px 24px;
        font-size: 1.1rem;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .payment-btn:hover {
        background-color: var(--secondary-products-color);
    }

    #payment-element {
        margin: 20px 0;
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>
