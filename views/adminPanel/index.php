<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/categoryModel.php';
require_once __DIR__ . '/../../models/unitModel.php';
require_once __DIR__ . '/../../models/productModel.php';

$db = new Database();
$conn = $db->getConnection();

$categoryModel = new CategoryModel($conn);
$categories = $categoryModel->getAll();

$unitModel = new UnitModel($conn);
$units = $unitModel->getAll();

$productModel = new ProductModel($conn);
$products = $productModel->getAll();
$productCounts = $productModel->GetProductCounts();

$totalProductCount = $productCounts[0]['total_products'];
$totalLowStockCount = $productCounts[0]['low_stock_products'];

session_start();
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? $_SESSION['category_error'] ?? '';

unset($_SESSION['success'], $_SESSION['error'], $_SESSION['category_error']);


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JRJ Sari-Sari Store - Admin Dashboard</title>
    <link rel="stylesheet" href="/sari-sari-store/assets/css/styles.css">
    <link rel="stylesheet" href="/sari-sari-store/assets/css/popups.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/sari-sari-store/assets/bootstrap/css/bootstrap.min.css">
    <!-- JQuery CSS -->
    <link rel="stylesheet" href="/sari-sari-store/assets/jquery/css/jquery-ui.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="/sari-sari-store/assets/datatables/css/datatables.css">
    <link rel="stylesheet" href="/sari-sari-store/assets/datatables/css/datatables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>

    </style>
</head>

<body>
    <?php if (!empty($success) || !empty($error)): ?>
        <div id="notification-popup" class="<?= !empty($success) ? 'success' : 'error' ?>">
            <?= !empty($success) ? htmlspecialchars($success) : htmlspecialchars($error) ?>
        </div>

        <script>
            (function() {
                const popup = document.getElementById('notification-popup');
                if (!popup) return;

                // Slide in
                setTimeout(() => {
                    popup.classList.add('show');
                }, 100); // slight delay for transition

                // Slide out after 3 seconds
                setTimeout(() => {
                    popup.classList.remove('show');
                }, 3100);

                // Optional: click to dismiss immediately
                popup.addEventListener('click', () => {
                    popup.classList.remove('show');
                });
            })();
        </script>
    <?php endif; ?>


    <!-- Navigation Header -->

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-store"></i> JRJ Sari-Sari Store
            </a>

            <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
                <span class="navbar-text me-3">
                    <i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>!
                </span>
                <a class="btn btn-outline-light btn-sm" href="/sari-sari-store/controllers/adminController.php?action=logout">
                    <i class="fas fa-sign-out-alt"></i> Sign Out
                </a>

            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-lg-10 col-md-9">

                <!-- Dashboard Section -->
                <?php include 'dashboard.php'; ?>


                <!-- Products Section -->
                <?php include 'productInventory.php'; ?>

                <!-- Other sections -->
                <div id="sales" class="content-section">
                    <div class="table-container">
                        <h4><i class="fas fa-cash-register text-primary"></i> Sales Transactions</h4>
                        <p class="text-muted">Sales transactions management will be implemented here.</p>
                    </div>
                </div>

                <div id="reports" class="content-section">
                    <div class="table-container">
                        <h4><i class="fas fa-chart-line text-primary"></i> Sales Reports</h4>
                        <p class="text-muted">Sales reports and analytics will be displayed here.</p>
                    </div>
                </div>

                <div id="customers" class="content-section">
                    <div class="table-container">
                        <h4><i class="fas fa-users text-primary"></i> Customers</h4>
                        <p class="text-muted">Customer management will be implemented here.</p>
                    </div>
                </div>

                <div id="settings" class="content-section">
                    <div class="table-container">
                        <h4><i class="fas fa-cog text-primary"></i> Settings</h4>
                        <p class="text-muted">System settings and configuration options will be here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- JQuery Javascript -->
    <script src="/sari-sari-store/assets/jquery/js/jquery-3.7.1.js"></script>
    <script src="/sari-sari-store/assets/jquery/js/jquery-ui.min.js"></script>

    <!-- Bootstrap Javascript -->
    <script src="/sari-sari-store/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Datatables Javascript -->
    <script src="/sari-sari-store/assets/datatables/js/datatables.js"></script>
    <script src="/sari-sari-store/assets/datatables/js/datatables.min.js"></script>

    <script src="/sari-sari-store/assets/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="/sari-sari-store/assets/js/productManagment.js"></script>
    <script>
        $(document).ready(function() {
            // If the URL has ?section=..., update localStorage
            const urlSection = new URLSearchParams(window.location.search).get('section');
            if (urlSection) {
                localStorage.setItem('activeSection', urlSection);
            }

            // Load section from localStorage
            let savedSection = localStorage.getItem('activeSection') || 'dashboard';

            // Show/hide content
            function showSection(section) {
                $('.content-section').hide(); // hide all
                $('#' + section).show(); // show current

                $('.sidebar-nav .nav-link').removeClass('active');
                $(`.sidebar-nav .nav-link[data-section="${section}"]`).addClass('active');

                // Optional: update the browser URL (without reloading)
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('section', section);
                window.history.replaceState({}, '', newUrl);
            }

            // Initial load
            showSection(savedSection);

            // Sidebar click event
            $('.sidebar-nav .nav-link').on('click', function(e) {
                e.preventDefault(); // prevent default anchor behavior
                const section = $(this).data('section');
                localStorage.setItem('activeSection', section);
                showSection(section);
            });

            // Include section in form submissions
            $('form').on('submit', function() {
                const section = localStorage.getItem('activeSection') || 'dashboard';
                $('#current_section').val(section);
            });
        });
    </script>

</body>

</html>