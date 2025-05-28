<?php
session_start();

// Check if admin_id is set in the session. If not, redirect to login page.
if (!isset($_SESSION['admin_id'])) {
    header('Location: /sari-sari-store/views/login.php');
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/categoryModel.php';
require_once __DIR__ . '/../../models/unitModel.php';
require_once __DIR__ . '/../../models/productModel.php';
require_once __DIR__ . '/../../models/activityLogModel.php';
require_once __DIR__ . '/../../models/salesTransactionModel.php';

$db = new Database();
$conn = $db->getConnection();

$categoryModel = new CategoryModel($conn);
$categories = $categoryModel->getAll();

$unitModel = new UnitModel($conn);
$units = $unitModel->getAll();

$activityLogModel = new ActivityLogModel($conn);
$activityLogs = $activityLogModel->getAll();

$productModel = new ProductModel($conn, $categoryModel, $unitModel);
$products = $productModel->getAll();
$productCounts = $productModel->GetProductCounts();

$salesModel = new SalesTransactionModel();
$transactions = $salesModel->getAll();

$totalProductCount = $productCounts[0]['total_products'];
$totalLowStockCount = $productCounts[0]['low_stock_products'];

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
        /* Content section visibility rules */
        .content-section {
            display: none;
            width: 100%;
            padding: 20px;
        }

        /* Make sure the container takes full width */
        .col-lg-10.col-md-9 {
            position: relative;
        }
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


                <?php include 'pos.php'; ?>
                <!-- Products Section -->
                <?php include 'salesTransaction.php'; ?>
                <?php include 'productInventory.php'; ?>


                <!-- POS Section -->

                <!-- Sales Section -->
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
    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Add New Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" action="/sari-sari-store/controllers/productController.php?action=create" method="post" class="container mt-3">

                        <div class="row mb-3">
                            <!-- Product Name - full width -->
                            <div class="col-12">
                                <label>Product Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="productImage" class="form-label">Product Image</label>
                            <input class="form-control" type="file" id="productImage" name="product_image" accept="image/*">
                        </div>

                        <div class="row mb-3">
                            <!-- Category -->
                            <div class="col-md-6">
                                <label>Category</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= htmlspecialchars($category['category_id']) ?>">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Unit -->
                            <div class="col-md-6">
                                <label>Unit</label>
                                <select name="unit_id" class="form-select" required>
                                    <option value="">Select Unit</option>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?= htmlspecialchars($unit['unit_id']) ?>">
                                            <?= htmlspecialchars($unit['name']) ?> (<?= htmlspecialchars($unit['abbreviation']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Cost Price -->
                            <div class="col-md-6">
                                <label>Cost Price (₱)</label>
                                <input type="number" name="cost_price" class="form-control" step="0.01" min="0" required>
                            </div>
                            <!-- Selling Price -->
                            <div class="col-md-6">
                                <label>Selling Price (₱)</label>
                                <input type="number" name="selling_price" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Initial Stock - full width -->
                            <div class="col-12">
                                <label>Initial Stock</label>
                                <input type="number" name="quantity_in_stock" class="form-control" min="0" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Save Product</button>
                        <a href="index.php" class="btn btn-secondary">Back</a>
                    </form>
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
    <script src="/sari-sari-store/assets/js/pos.js"></script>
    <script>
        // Initialize DataTables
        $(document).ready(function() {
            $('#activityTable').DataTable({
                paging: true,
                searching: true,
                info: false,
                lengthChange: false,
                pageLength: 5,
                ordering: false
            });
        }); // Global navigation function
        window.navigateToSection = function(sectionId) {
            console.log('Navigating to section:', sectionId);

            // Debug: List all content sections
            console.log('All content sections:');
            $('.content-section').each(function() {
                console.log('Found section:', this.id);
            });

            // First, hide all sections with !important to override any CSS
            $('.content-section').css('cssText', 'display: none !important');

            // Then get and show the target section
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                console.log('Found target section:', sectionId);
                // Use !important to override any other CSS
                $(targetSection).css('cssText', 'display: block !important');

                // Debug: Verify the element's display state
                const computedStyle = window.getComputedStyle(targetSection);
                console.log('Target section computed style:', {
                    'display': computedStyle.display,
                    'visibility': computedStyle.visibility,
                    'opacity': computedStyle.opacity,
                    'position': computedStyle.position,
                    'zIndex': computedStyle.zIndex,
                    'overflow': computedStyle.overflow
                });

                // Debug: Check parent elements' visibility
                let parent = targetSection.parentElement;
                while (parent) {
                    const pStyle = window.getComputedStyle(parent);
                    console.log('Parent element:', parent.tagName, {
                        'display': pStyle.display,
                        'visibility': pStyle.visibility,
                        'opacity': pStyle.opacity,
                        'position': pStyle.position,
                        'overflow': pStyle.overflow
                    });
                    parent = parent.parentElement;
                }
            } else {
                console.error('Target section not found:', sectionId);
                // List all elements with id matching sectionId
                const possibleElements = document.querySelectorAll(`#${sectionId}`);
                console.log(`Found ${possibleElements.length} elements with id '${sectionId}':`);
                possibleElements.forEach(el => console.log(el));
            }

            // Update sidebar navigation state
            $('.sidebar-nav .nav-link').removeClass('active');
            $(`.sidebar-nav .nav-link[data-section="${sectionId}"]`).addClass('active');

            // Save state and update URL
            localStorage.setItem('activeSection', sectionId);
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.set('section', sectionId);
            window.history.replaceState({}, '', newUrl);
        };

        // Initialize navigation on page load
        $(document).ready(function() {
            const urlSection = new URLSearchParams(window.location.search).get('section');
            const savedSection = urlSection || localStorage.getItem('activeSection') || 'dashboard';

            console.log('Initial section:', savedSection);

            // Set up navigation click handlers
            $('.sidebar-nav .nav-link').on('click', function(e) {
                e.preventDefault();
                const section = $(this).data('section');
                console.log('Navigation clicked:', section);
                navigateToSection(section);
            });

            // Do initial navigation
            navigateToSection(savedSection);
        });
    </script>

</body>

</html>