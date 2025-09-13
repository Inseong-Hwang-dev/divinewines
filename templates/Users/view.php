<?= $this->Html->css('styles', ['block' => true]) ?>

<div class="user-details-container">
    <div class="user-details-grid">
        <?= $this->Html->tag('button', '<i class="bi bi-arrow-left"></i>  Back',
            ['type' => 'button',
                'onclick' => '{window.history.back();}',
                'class' => 'btn back-btn text-start w-auto']) ?>

        <div class="user-info-section">
            <h1 class="user-name"><?= h($user->first_name . ' ' . $user->last_name) ?></h1>
            <div class="user-email"><?= h($user->email) ?></div>

            <div class="user-meta">
                <div class="meta-item">
                    <span class="meta-label">Phone:</span>
                    <span class="meta-value"><?= h($user->phone) ?: 'Not provided' ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">User Type:</span>
                    <span class="meta-value"><?= h($user->user_type) ?: 'Regular' ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Loyalty Points:</span>
                    <span class="meta-value"><?= $user->loyalty_points === null ? 'Not set' : $this->Number->format($user->loyalty_points) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Address:</span>
                    <span class="meta-value"><?= h($user->address) ?: 'Not provided' ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Birthday:</span>
                    <span class="meta-value">
                        <?php
                        if ($user->birthday) {
                            $date = new \DateTime($user->birthday);
                            $months = [
                                '1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April',
                                '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August',
                                '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
                            ];
                            echo $date->format('d') . ' ' . $months[$date->format('n')];
                        } else {
                            echo 'Not provided';
                        }
                        ?>
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Member Since:</span>
                    <span class="meta-value">
                        <?php
                        if ($user->created) {
                            $date = new \DateTime($user->created);
                            echo $date->format('F j, Y');
                        } else {
                            echo 'Unknown';
                        }
                        ?>
                    </span>
                </div>
            </div>

            <div class="user-actions">
                <?= $this->Html->link(__('Edit User'), ['action' => 'edit', $user->id], ['class' => 'user-action-btn edit-btn']) ?>
            </div>

            <?php if ($this->Identity->isLoggedIn() && $this->Identity->get('user_type') === 'Admin'): ?>
                <div class="admin-actions">
                    <h3 class="admin-section-title">Administrative Actions</h3>
                    <?php if (!$user->archived): ?>
                        <?= $this->Form->postLink(__('Archive User'),
                            ['action' => 'archive', $user->id],
                            ['confirm' => __('Are you sure you want to archive this user?'), 'class' => 'admin-action-btn archive-btn'])
                        ?>
                    <?php else: ?>
                        <?= $this->Form->postLink(__('Restore User'),
                            ['action' => 'restore', $user->id],
                            ['confirm' => __('Are you sure you want to restore this user?'), 'class' => 'admin-action-btn restore-btn'])
                        ?>
                    <?php endif; ?>

                    <?= $this->Html->link(__('View User Orders'),
                        ['controller' => 'Orders', 'action' => 'userOrders', $user->id],
                        ['class' => 'admin-action-btn orders-btn'])
                    ?>


                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .user-details-container {
        max-width: 1200px;
        margin: 50px auto;
        padding: 0 20px;
    }

    .user-details-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 40px;
    }

    .user-info-section {
        padding: 20px;
    }

    .user-name {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 32px;
        color: #333;
        margin-bottom: 10px;
    }

    .user-email {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 20px;
        color: #666;
        margin-bottom: 30px;
    }

    .user-meta {
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
    }

    .meta-value {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        color: #333;
        font-size: 16px;
    }

    .user-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 30px;
    }

    .user-action-btn {
        padding: 10px 20px;
        background-color: #f9f9f9;
        color: #333;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        text-align: center;
    }

    .user-action-btn:hover {
        background-color: #fff;
        border-color: #2c3e50;
        color: #2c3e50;
    }

    .edit-btn:hover {
        border-color: #3498db;
        color: #3498db;
    }

    .admin-actions {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .admin-section-title {
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        font-size: 20px;
        color: #333;
        margin-bottom: 20px;
    }

    .admin-action-btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #f9f9f9;
        color: #333;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Comfortaa', sans-serif;
        font-weight: 300;
        margin-bottom: 10px;
        margin-right: 10px;
    }

    .admin-action-btn:hover {
        background-color: #fff;
        border-color: #2c3e50;
        color: #2c3e50;
    }

    .restore-btn:hover {
        border-color: #2ecc71;
        color: #2ecc71;
    }

    .orders-btn:hover {
        border-color: #f39c12;
        color: #f39c12;
    }



    @media (max-width: 768px) {

        .user-details-container {
            margin: 20px auto;
        }

        .user-actions {
            flex-direction: column;
        }

        .user-action-btn {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any necessary JavaScript functions here
        // For example, form validation, dynamic content loading, etc.
    });
</script>
