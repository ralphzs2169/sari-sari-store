<?php
session_start();


// Check if admin is logged in
if (!isset($_SESSION['admin_username'])) {
    http_response_code(401);
    header("Location: login.php");
}

$adminUsername = $_SESSION['admin_username'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JRJ Sari-Sari Store - Admin Dashboard</title>
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

</head>

<body>
    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-store"></i> JRJ Sari-Sari Store
            </a>

            <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
                <span class="navbar-text me-3">
                    <i class="fas fa-user-circle"></i> Welcome, <?= htmlspecialchars($adminUsername) ?>!
                </span>
                <a class="btn btn-outline-light btn-sm" href="../controllers/adminController.php?action=logout">
                    <i class="fas fa-sign-out-alt"></i> Sign Out
                </a>

            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3">
                <div class="sidebar-nav">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#dashboard">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#products">
                                <i class="fas fa-box"></i> Product Inventory
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#sales">
                                <i class="fas fa-cash-register"></i> Sales Transactions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#reports">
                                <i class="fas fa-chart-line"></i> Sales Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#customers">
                                <i class="fas fa-users"></i> Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#settings">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10 col-md-9">
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2><i class="fas fa-hand-wave text-warning"></i> Kumusta, <?= htmlspecialchars($adminUsername) ?>!</h2>
                            <p class="mb-0">Welcome back to your Sari-Sari Store management system. Here's what's happening in your store today.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h5 class="text-muted mb-0">Today's Date</h5>
                            <h6 id="currentDate" class="text-primary"></h6>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                                <i class="fas fa-box"></i>
                            </div>
                            <h3 class="stats-number text-primary">248</h3>
                            <p class="stats-label">Total Products</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon" style="background: linear-gradient(135deg, #27ae60, #229954);">
                                <i class="fas fa-peso-sign"></i>
                            </div>
                            <h3 class="stats-number text-success">₱15,450</h3>
                            <p class="stats-label">Today's Sales</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon" style="background: linear-gradient(135deg, #e67e22, #d35400);">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3 class="stats-number text-warning">127</h3>
                            <p class="stats-label">Transactions Today</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-icon" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h3 class="stats-number text-danger">12</h3>
                            <p class="stats-label">Low Stock Items</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Quick Actions -->
                    <div class="col-lg-4 mb-4">
                        <div class="quick-actions">
                            <h5 class="mb-3"><i class="fas fa-bolt text-warning"></i> Quick Actions</h5>
                            <a href="#" class="action-btn">
                                <i class="fas fa-plus-circle"></i> Add New Product
                            </a>
                            <a href="#" class="action-btn">
                                <i class="fas fa-cash-register"></i> New Sale Transaction
                            </a>
                            <a href="#" class="action-btn">
                                <i class="fas fa-chart-bar"></i> Generate Report
                            </a>
                            <a href="#" class="action-btn">
                                <i class="fas fa-warehouse"></i> Check Inventory
                            </a>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="col-lg-8 mb-4">
                        <div class="recent-activity">
                            <h5 class="mb-3"><i class="fas fa-clock text-info"></i> Recent Activity</h5>
                            <div class="activity-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>New sale transaction completed</strong>
                                        <br><small class="text-muted">Customer purchased Coca-Cola, Lucky Me, and Pan de Sal</small>
                                    </div>
                                    <span class="activity-time">2 min ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>Low stock alert</strong>
                                        <br><small class="text-muted">Maggi Noodles - Only 5 pieces remaining</small>
                                    </div>
                                    <span class="activity-time">15 min ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>New product added</strong>
                                        <br><small class="text-muted">Kopiko Coffee - 50 pieces added to inventory</small>
                                    </div>
                                    <span class="activity-time">1 hr ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>Daily sales report generated</strong>
                                        <br><small class="text-muted">Yesterday's total: ₱18,250</small>
                                    </div>
                                    <span class="activity-time">3 hrs ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        // Set current date
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('currentDate').textContent = today.toLocaleDateString('en-PH', options);
        });

        // Handle navigation clicks
        $('.nav-link').on('click', function(e) {
            e.preventDefault();
            $('.nav-link').removeClass('active');
            $(this).addClass('active');

            const target = $(this).attr('href').substring(1);
            console.log('Navigating to:', target);
            // Here you would implement actual navigation logic
        });

        // Handle quick action clicks
        $('.action-btn').on('click', function(e) {
            e.preventDefault();
            const action = $(this).text().trim();
            alert('Navigating to: ' + action);
            // Implement actual navigation logic here
        });

        // Handle sign out
        function handleSignOut() {
            if (confirm('Are you sure you want to sign out?')) {
                alert('Signing out...');
                // Implement actual sign out logic here
                // window.location.href = 'login.php';
            }
        }

        // Add hover effects to stats cards
        $('.stats-card').hover(
            function() {
                $(this).find('.stats-number').addClass('text-primary');
            },
            function() {
                $(this).find('.stats-number').removeClass('text-primary');
            }
        );

        // Simulate real-time updates (optional)
        setInterval(function() {
            // You can add logic here to update stats in real-time
            // For example, fetch new data via AJAX
        }, 30000); // Update every 30 seconds
    </script>
</body>

</html>