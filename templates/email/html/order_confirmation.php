<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order $order
 * @var \App\Model\Entity\User $user
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .order-details {
            margin-bottom: 30px;
        }
        .product-list {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .product-list th, .product-list td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .total {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
            <p>Thank you for your order!</p>
        </div>

        <div class="order-details">
            <h2>Order #<?= $order->order_id ?></h2>
            <p>Order Date: <?= $order->created->format('Y-m-d H:i:s') ?></p>
            
            <h3>Shipping Information</h3>
            <p>
                <?= h($user->first_name) ?> <?= h($user->last_name) ?><br>
                <?= h($order->shipping_address) ?><br>
                <?= h($order->shipping_city) ?>, <?= h($order->shipping_state) ?> <?= h($order->shipping_postcode) ?><br>
                <?= h($order->shipping_country) ?>
            </p>

            <h3>Order Items</h3>
            <table class="product-list">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order->order_products as $item): ?>
                    <tr>
                        <td><?= h($item->product->name) ?></td>
                        <td><?= h($item->quantity) ?></td>
                        <td>$<?= number_format($item->price, 2) ?></td>
                        <td>$<?= number_format($item->price * $item->quantity, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total">
                <p>Subtotal: $<?= number_format($order->total_amount - $order->shipping_cost, 2) ?></p>
                <p>Shipping: $<?= number_format($order->shipping_cost, 2) ?></p>
                <p>Total: $<?= number_format($order->total_amount, 2) ?></p>
            </div>
        </div>

        <p>If you have any questions about your order, please contact our customer service.</p>
    </div>
</body>
</html> 