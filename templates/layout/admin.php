<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Admin Dashboard - Divine Vines</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= $this->Url->assetUrl('favicon.ico') ?>" />
    <!-- Bootstrap icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400&family=Comfortaa:wght@300&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Gwendolyn:wght@400;700&display=swap" rel="stylesheet">

    <!-- Core theme CSS (includes Bootstrap)-->
    <?= $this->Html->css('styles.css') ?>
    <?= $this->Html->css(['admin.css','admin-table.css', 'admin-view.css']) ?>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <h3>Admin Dashboard</h3>
                <!-- <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button> -->
            </div>
            <ul class="sidebar-menu">
                <li>
                    <?= $this->Html->link(
                        '<i class="fas fa-home"></i> Homepage',
                        '/',
                        ['escape' => false]
                    ) ?>
                </li>
                <li>
                    <?= $this->Html->link(
                        '<i class="fas fa-tachometer-alt"></i> Dashboard',
                        '/admin',
                        ['escape' => false]
                    ) ?>
                </li>
                <li>
                    <?= $this->Html->link(
                        '<i class="fas fa-chart-line"></i> Analytics',
                        '/admin/analytics',
                        ['escape' => false]
                    ) ?>
                </li>
                <li>
                    <button class="btn btn-toggle-admin align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#productsCollapse" aria-expanded="false">
                        <i class="bi bi-box-seam-fill"></i>  <span class="ms-0">Products</span> <i class="bi bi-plus ms-auto" style="font-size: 1.5rem;"></i>
                    </button>
                    <div class="collapse" id="productsCollapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li>
                                <?= $this->Html->link('Manage Products', '/products/index', ['class' => 'link-body-emphasis d-inline-flex text-decoration-none rounded']) ?>
                            </li>
                            <li>
                                <?= $this->Html->link('Manage Wine Styles', '/wine-styles/index', ['class' => 'link-body-emphasis d-inline-flex text-decoration-none rounded']) ?>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <?= $this->Html->link(
                        '<i class="fas fa-users"></i> Users',
                        '/users',
                        ['escape' => false]
                    ) ?>
                </li>
                <li>
                    <?= $this->Html->link(
                        '<i class="fas fa-envelope"></i> Enquiries',
                        '/contact-enquiries',
                        ['escape' => false]
                    ) ?>
                </li>
                <li>
                    <?= $this->Html->link(
                        '<i class="fas fa-shopping-cart"></i> Orders',
                        '/orders/admin-index',
                        ['escape' => false]
                    ) ?>
                </li>
                <li>
                    <?= $this->Html->link(
                        '<i class="fas fa-star"></i> Reviews',
                        '/reviews',
                        ['escape' => false]
                    ) ?>
                </li>
                <li>
                    <?= $this->Html->link(
                        '<i class="bi bi-newspaper"></i> Newsletters',
                        '/newsletters',
                        ['escape' => false]
                    ) ?>
                </li>
                <li>
                    <?= $this->Html->link(
                        '<i class="fas fa-ticket-alt"></i> Coupons',
                        '/coupons',
                        ['escape' => false]
                    ) ?>
                </li>
<!--                <li>-->
<!--                    --><?php //= $this->Html->link('Content Blocks', ['plugin' => 'ContentBlocks', 'controller' => 'ContentBlocks', 'action' => 'index']) ?>
<!---->
<!--                </li>-->
                <li>
                    <?= $this->Html->link(
                        '<i class="bi bi-box-seam-fill"></i> Content Blocks',
                        $this->Url->build('/content-blocks/content-blocks', ['fullBase' => true]),
                        ['escape' => false]
                    ) ?>
                </li>

                <li class="sidebar-footer">
                    <?= $this->Html->link(
                        '<i class="fas fa-sign-out-alt"></i> Logout',
                        $this->Url->build('/auth/logout', ['fullBase' => true]),
                        ['escape' => false]
                    ) ?>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">

            <div class="admin-content">
                <?= $this->fetch('content') ?>
            </div>
        </main>
    </div>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <?= $this->Html->script('admin.js') ?>
</body>
</html>
