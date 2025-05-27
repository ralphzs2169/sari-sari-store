<div id="dashboard" class="content-section active">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2><i class="fas fa-hand-wave text-warning"></i> Kumusta, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>!</h2>
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
                <a href="#" class="action-btn" onclick="showAddProductModal()">
                    <i class="fas fa-plus-circle"></i> Add New Product
                </a>
                <a href="#" class="action-btn" onclick="navigateToSection('sales')">
                    <i class="fas fa-cash-register"></i> New Sale Transaction
                </a>
                <a href="#" class="action-btn" onclick="navigateToSection('reports')">
                    <i class="fas fa-chart-bar"></i> Generate Report
                </a>
                <a href="#" class="action-btn" onclick="navigateToSection('products')">
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