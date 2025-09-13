<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\CartItem> $cartItems
 */
?>
<div class="cart-container cartItems index content">
    <?= $this->Html->link('<i class="bi bi-arrow-left"></i>  Back to Shopping',
        ['controller' => 'Products', 'action' => 'page'],
        ['class' => 'btn back-btn text-start w-auto', 'escape' => false]) ?>
    <div class="cart-page-title"><?= __('Your Shopping Cart') ?></div>
    <div class="alert alert-dismissible fade show" role="alert">
        <?= $this->Flash->render('shoppingCartFeedback');?>
        <?= $this->Flash->render('cartQuantityFeedback');?>

    </div>

    <?php if ($cartItems->isEmpty()): ?>
        <div class="empty-cart-message">
            <i class="bi bi-cart-x"></i>
            <h2>Your cart is empty</h2>
            <p>Looks like you haven't added any items to your cart yet.</p>
            <?= $this->Html->link('Continue Shopping', ['controller' => 'Products', 'action' => 'page'], ['class' => 'btn continue-shopping-btn']) ?>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="cart-table w-100">
                <thead>
                <tr style="margin-bottom: 10px; font-family: 'Comfortaa', sans-serif; font-size: 20px; color: #333333">
                    <th style="width: 150px;">Products</th>
                    <th style="width: 300px;"> </th>
                    <th style="width: 150px;">Quantity</th>
                    <th style="width: 150px;">Item Total</th>
                    <th class="actions"></th>
                </tr>
                </thead>
                <!-- Styled separator row -->
                <tr style="height: 0.5px; background-color: #b12704;">
                    <td colspan="5" style="padding: 0;"></td>
                </tr>

                <tbody>
                <?php
                $totalAmount = 0;
                $totalItems = 0;
                foreach ($cartItems as $cartItem):
                    $subtotal = $cartItem->product->price * $cartItem->product_quantity;
                    $totalAmount += $subtotal;
                    $totalItems += $cartItem->product_quantity;
                    ?>
                    <tr>
                        <td style="width: 150px;"><?= $cartItem->hasValue('product') ?
                                $this->Html->link(
                                    $this->Html->image('/img/' . $cartItem->product->image, [
                                        'alt' => $cartItem->product->name,
                                        'style' => 'width: 100px; height: 100px; object-fit: contain; object-position: center; padding: 10px; background: #f9f9f9; border-radius: 4px;'
                                    ]),
                                    ['controller' => 'Products', 'action' => 'page', $cartItem->product->id],
                                    ['escape' => false]
                                )
                                : '' ?>
                        </td>
                        <td style="width: 300px;">
                            <?= $cartItem->hasValue('product') ?
                                $this->Html->link(
                                    $cartItem->product->name,
                                    ['controller' => 'Products', 'action' => 'view', $cartItem->product->id],
                                    ['class' => 'cart-item-name']
                                )
                                : '' ?>
                        </td>
                        <td>
                            <div class="adjust-quantity">
                                <?= $this->Html->link('-', ['action' => 'decreaseQuantity', $cartItem->id], ['class' => 'btn btn-large',
                                    'title'=>'Decrease Quantity',
                                    'escape' => false,
                                    'disabled' => $cartItem->product_quantity <= 1 ? true : false,
                                    'onclick' => $cartItem->product_quantity <= 1 ? 'return false;' : null
                                ]); ?>
                                <?= $this->Form->create(null, [
                                    'url' => ['action' => 'updateQuantity', $cartItem->id],
                                    'class' => 'quantity-form',
                                    'id' => 'quantity-form-' . $cartItem->id
                                ]) ?>
                                <?= $this->Form->number('quantity', [
                                    'value' => $cartItem->product_quantity,
                                    'min' => 1,
                                    'max' => $cartItem->product->stock_quantity,
                                    'class' => 'quantity-input',
                                    'id' => 'cart_quantity_' . $cartItem->id,
                                    'data-cart-item-id' => $cartItem->id
                                ]) ?>
                                <?= $this->Form->end() ?>
                                <?= $this->Html->link('+',['controller' => 'CartItems',
                                    'action' => 'increaseQuantity', $cartItem->id],
                                    ['class' => 'btn btn-large',
                                        'escape' => false,
                                    ]) ?>
                            </div>
                        </td>

                        <td><?= $this->Number->currency($cartItem->product_quantity * $cartItem->product->price) ?></td>

                        <td class="actions">
                            <div class="action-buttons">
                                <?= $this->Form->postLink(
                                    '<i class="bi bi-trash"></i> Remove',
                                    ['action' => 'delete', $cartItem->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to remove {0} from cart?', $cartItem->product->name),
                                        'class' => 'btn btn-outline-danger btn-sm',
                                        'escape' => false
                                    ]
                                ) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tr style="height: 0.5px; background-color:#b12704;">
                    <td colspan="5" style="padding: 0;"></td>
                </tr>
                <tfoot>
                <tr>
                    <td colspan="2" class="text-right"><strong><?= __('Total:') ?></strong></td>
                    <td><strong><?= $this->Number->format($totalItems) ?> items</strong></td>
                    <td><strong><?= $this->Number->currency($totalAmount) ?></strong></td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>

        <div class="cart-actions mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex" style="gap: 20px;">
                    <!-- Grouped action buttons: Clear All Items and Continue Shopping -->
                    <?= $this->Form->postLink(
                        '<i class="bi bi-trash"></i> Clear All Items',
                        ['action' => 'clearAll'],
                        [
                            'confirm' => __('Are you sure you want to remove all items from your cart?'),
                            'class' => 'btn btn-outline-danger',
                            'escape' => false
                        ]
                    ) ?>
                    <?= $this->Html->link(
                        '<i class="bi bi-arrow-left"></i> Continue Shopping',
                        ['controller' => 'Products', 'action' => 'page'],
                        ['class' => 'btn btn-outline-secondary', 'escape' => false]
                    ) ?>
                </div>
                <?= $this->Html->link(
                    __('Checkout'),
                    ['controller' => 'Orders', 'action' => 'checkout'],
                    ['class' => 'btn checkout-btn btn-lg']
                ) ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .cart-container {
        max-width: 1380px;
        margin: 0 auto;
        margin-top: 50px;
        padding: 30px 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .cart-table {
        border-collapse: separate;
        border-spacing: 0 30px;
        width: 100%;
        font-family: 'Comfortaa', sans-serif;
    }
    .cart-page-title {
        font-size: 30px;
        font-weight: bold;
        font-family: 'Lora', serif;
        padding-top: 8px;
        margin-bottom: 20px;
        color: #333;
    }
    a.cart-item-name {
        text-decoration: none !important;
        color: #333;
        font-weight: 700;
        font-size: 16px;
    }
    .adjust-quantity {
        border: solid 1px #333333;
        background: transparent;
        border-radius: 3px;
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
    }
    .adjust-quantity .btn {
        padding: 4px 8px;
        font-size: 14px;
        color: #333;
        background: transparent;
        border: none;
        cursor: pointer;
    }
    .adjust-quantity .btn:hover {
        color: #b12704;
    }
    .adjust-quantity .btn:disabled {
        color: #ccc;
        cursor: not-allowed;
    }
    .quantity-input {
        width: 50px;
        height: 30px;
        text-align: center;
        border: none;
        background: transparent;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 16px;
        margin: 0 5px;
    }
    .quantity-input::-webkit-inner-spin-button,
    .quantity-input::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .action-buttons {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .btn-outline-danger {
        color: #b12704;
        border: 1px solid #b12704;
        background: transparent;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.3s ease;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-outline-danger:hover {
        background: #b12704;
        color: white;
        border-color: #b12704;
    }
    .btn-outline-danger i {
        margin-right: 5px;
    }
    .btn-outline-danger.btn-sm {
        padding: 6px 10px;
        font-size: 14px;
    }
    .cart-actions {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }
    .cart-actions .d-flex {
        gap: 20px;
    }
    .checkout-btn {
        font-family: 'Comfortaa', sans-serif;
        color: #b12704;
        border: 1px solid #b12704;
        background: transparent;
        padding: 12px 24px;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 300;
    }
    .checkout-btn:hover {
        background: #b12704;
        color: white;
        border-color: #b12704;
    }
    .empty-cart-message {
        text-align: center;
        padding: 50px 20px;
        background: #f9f9f9;
        border-radius: 8px;
        margin: 20px 0;
    }
    .empty-cart-message i {
        font-size: 48px;
        color: #b12704;
        margin-bottom: 20px;
    }
    .empty-cart-message h2 {
        font-family: 'Lora', serif;
        color: #333;
        margin-bottom: 10px;
    }
    .empty-cart-message p {
        color: #666;
        margin-bottom: 20px;
    }
    .continue-shopping-btn {
        background: #b12704;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s;
    }
    .continue-shopping-btn:hover {
        background: #8c1f03;
        color: white;
    }
    .back-btn {
        color: #666;
        background: transparent;
        border: none;
        padding: 8px 0;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        margin-bottom: 20px;
    }
    .back-btn:hover {
        color: #b12704;
    }
    .back-btn i {
        margin-right: 5px;
    }
    .alert {
        margin-bottom: 20px;
        border-radius: 4px;
    }
    .quantity-form {
        display: inline;
        margin: 0;
    }
    .btn-outline-secondary {
        color: #666;
        border: 1px solid #666;
        background: transparent;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.3s ease;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-outline-secondary:hover {
        background: #666;
        color: white;
        border-color: #666;
    }
    .btn-outline-secondary i {
        margin-right: 5px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle manual quantity input
        const quantityInputs = document.querySelectorAll('.quantity-input');
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                const form = this.closest('form');
                if (form) {
                    form.submit();
                }
            });
        });
    });
</script>
