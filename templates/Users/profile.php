<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $userData
 */
?>
<div class="users profile content">
    <!-- Header Section -->
    <div class="header-image">
        <img src="https://images.unsplash.com/photo-1519671282429-b44660ead0a7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1380&h=200&q=80" alt="Profile Header">
    </div>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bolder">My Profile</h1>
            <p class="text-muted">View and manage your personal information</p>
        </div>

        <!-- Profile Summary Cards -->
        <div class="profile-summary-cards mb-5">
            <div class="summary-card">
                <i class="bi bi-person"></i>
                <h3>Full Name</h3>
                <p><?= h($userData->first_name . ' ' . $userData->last_name) ?></p>
            </div>
            <div class="summary-card">
                <i class="bi bi-envelope"></i>
                <h3>Email</h3>
                <p><?= h($userData->email) ?: 'N/A' ?></p>
            </div>
            <div class="summary-card">
                <i class="bi bi-telephone"></i>
                <h3>Phone</h3>
                <p><?= h($userData->phone) ?: 'N/A' ?></p>
            </div>
            <div class="summary-card">
                <i class="bi bi-geo-alt"></i>
                <h3>Address</h3>
                <p class="text-start">
                    <?= nl2br(h(trim("{$userData->user_address1}" .
                        ($userData->user_address2 ? "\n{$userData->user_address2}" : '') .
                        "\n{$userData->user_city}, " .
                        ($userData->user_state ? "{$userData->user_state} " : '') .
                        "{$userData->user_postcode}\n" .
                        "{$userData->user_country}"))) ?>
                </p>
            </div>
            <div class="summary-card">
                <i class="fas fa-cake-candles"></i>
                <h3>Birthday</h3>
                <p><?= $userData->birthday ? h($userData->birthday->format('F j')) : 'N/A' ?></p>
            </div>
            <div class="summary-card">
                <i class="bi bi-star"></i>
                <h3>Coupons</h3>
                <p><?= $this->Number->format($couponCount) ?></p>
            </div>
        </div>

        <!-- Profile Details Table -->
        <div class="table-responsive">
            <table id="ProfileTable" class="display responsive nowrap">
                <thead>
                <tr>
                    <th><i class="bi bi-info-circle me-2"></i>Field</th>
                    <th><i class="bi bi-card-text me-2"></i>Details</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><span class="profile-field">First Name</span></td>
                    <td><span class="profile-detail"><?= h($userData->first_name) ?: 'N/A' ?></span></td>
                </tr>
                <tr>
                    <td><span class="profile-field">Last Name</span></td>
                    <td><span class="profile-detail"><?= h($userData->last_name) ?: 'N/A' ?></span></td>
                </tr>
                <tr>
                    <td><span class="profile-field">Email</span></td>
                    <td><span class="profile-detail"><?= h($userData->email) ?: 'N/A' ?></span></td>
                </tr>
                <tr>
                    <td><span class="profile-field">Phone</span></td>
                    <td><span class="profile-detail"><?= h($userData->phone) ?: 'N/A' ?></span></td>
                </tr>
                <tr>
                    <td><span class="profile-field">Address</span></td>
                    <td><span class="profile-detail"><?= nl2br(h(trim("{$userData->user_address1}" .
                                ($userData->user_address2 ? "\n{$userData->user_address2}" : '') .
                                "\n{$userData->user_city}, " .
                                ($userData->user_state ? "{$userData->user_state} " : '') .
                                "{$userData->user_postcode}\n" .
                                "{$userData->user_country}"))) ?>
                        </span></td>
                </tr>
                <tr>
                    <td><span class="profile-field">Birthday</span></td>
                    <td><span class="profile-detail"><?= $userData->birthday ? h($userData->birthday->format('Y-m-d')) : 'N/A' ?></span></td>
                </tr>
                <tr>
                    <td><span class="profile-field">Coupons</span></td>
                    <td><span class="profile-detail"><?= $this->Number->format($couponCount) ?></span></td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-4">
            <?= $this->Html->link(
                '<i class="bi bi-house-door me-2"></i> Back to Home',
                '/',
                ['class' => 'btn btn-primary btn-large me-2', 'escape' => false]
            ) ?>
            <?= $this->Html->link(
                '<i class="bi bi-pencil-square me-2"></i> Edit Profile',
                ['controller' => 'Users', 'action' => 'profileEdit', $userData->id],
                ['class' => 'btn btn-secondary btn-large', 'escape' => false]
            ) ?>
        </div>
    </div>
</div>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#ProfileTable').DataTable({
            responsive: true,
            ordering: false,
            language: {
                search: "Search profile details:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                emptyTable: "No profile data available"
            }
        });
    });
</script>

<?= $this->Html->css('admin-table.css') ?>

<style>
    /* ==========================================================================
       Header Image
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
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* ==========================================================================
       Profile Summary Cards
       ========================================================================== */
    .profile-summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .summary-card {
        background: linear-gradient(145deg, #ffffff, #f8f9fa);
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .summary-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .summary-card i {
        font-size: 2.5rem;
        color: #6c5ce7;
        margin-bottom: 12px;
    }

    .summary-card h3 {
        font-size: 1.1rem;
        color: #2d3436;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .summary-card p {
        font-size: 1.3rem;
        font-weight: bold;
        color: #0984e3;
        margin: 0;
        word-break: break-all;
        overflow-wrap: break-word;
        white-space: normal;
    }

    /* ==========================================================================
       Table Enhancements
       ========================================================================== */
    .table-responsive {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-top: 20px;
    }

    #ProfileTable {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    #ProfileTable thead th {
        background: linear-gradient(145deg, #f8f9fa, #e9ecef);
        color: #2d3436;
        font-weight: 600;
        padding: 16px;
        text-align: left;
        border-bottom: 2px solid #dfe6e9;
    }

    #ProfileTable tbody tr {
        transition: background-color 0.2s ease;
    }

    #ProfileTable tbody tr:hover {
        background-color: #f1f3f5;
    }

    #ProfileTable tbody td {
        padding: 16px;
        border-bottom: 1px solid #dfe6e9;
        vertical-align: middle;
    }

    .profile-field {
        font-weight: bold;
        color: #6c5ce7;
    }

    .profile-detail {
        color: #2d3436;
    }

    /* ==========================================================================
       Buttons
       ========================================================================== */
    .btn-primary {
        background: linear-gradient(90deg, #6c5ce7, #0984e3);
        color: white !important;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #5a4cd6, #0774c3);
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .btn-secondary {
        background: linear-gradient(90deg, #636e72, #b2bec3);
        color: white !important;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
    }

    .btn-secondary:hover {
        background: linear-gradient(90deg, #535c60, #a0aeb3);
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    /* ==========================================================================
       DataTables Customization
       ========================================================================== */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 20px;
    }

    .dataTables_wrapper .dataTables_length select {
        padding: 6px;
        border-radius: 8px;
        border: 1px solid #dfe6e9;
        background: #ffffff;
    }

    .dataTables_wrapper .dataTables_filter input {
        padding: 8px;
        border-radius: 8px;
        border: 1px solid #dfe6e9;
        margin-left: 12px;
        background: #ffffff;
    }

    /* ==========================================================================
       Responsive Design
       ========================================================================== */
    @media (max-width: 768px) {
        .profile-summary-cards {
            grid-template-columns: 1fr;
        }

        .summary-card {
            margin-bottom: 15px;
        }

        .table-responsive {
            padding: 12px;
        }

        #ProfileTable thead th,
        #ProfileTable tbody td {
            padding: 12px;
        }

        .btn-primary, .btn-secondary {
            padding: 8px 16px;
            font-size: 0.9rem;
        }
    }
</style>
