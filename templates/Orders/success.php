<?php
/**
 * Success page template for displaying after successful Stripe payment
 */
?>
<!-- Center only the success card, not the whole page -->
<div class="d-flex align-items-center justify-content-center" style="min-height: 60vh; background: transparent;">
    <div class="card shadow-sm w-100" style="max-width: 500px;">
        <div class="card-body text-center">
            <!-- Success Icon -->
            <div class="mb-4">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
            </div>
            
            <!-- Success Message -->
            <h2 class="card-title mb-4"><?= __('Payment Successful!') ?></h2>
            
            <!-- Order Details -->
            <div class="alert alert-success">
                <p class="mb-0"><?= __('Your order has been successfully processed.') ?></p>
                <p class="mb-0"><?= __('Order details have been sent to your email.') ?></p>
            </div>
            
            <!-- Action Buttons -->
            <div class="mt-4">
                <a href="<?= $this->Url->build(['controller' => 'Orders', 'action' => 'view', $order->order_id]) ?>" 
                   class="btn btn-primary me-2">
                    <?= __('View Order Details') ?>
                </a>
                <a href="<?= $this->Url->build(['controller' => 'Products', 'action' => 'page']) ?>" 
                   class="btn btn-outline-secondary">
                    <?= __('Continue Shopping') ?>
                </a>
            </div>
        </div>
    </div>
</div>
