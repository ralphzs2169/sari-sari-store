<?php
// Sales Report Section - Enhanced Grid Layout
?>
<div class="container-fluid">
    <h4 class="mb-3"><i class="fas fa-chart-line text-primary"></i> Sales Reports</h4>
    <!-- Summary Cards Row -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <h6 class="card-title text-success">Total Sales</h6>
                    <h4 class="fw-bold">₱0.00</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center">
                    <h6 class="card-title text-primary">Transactions</h6>
                    <h4 class="fw-bold">0</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center">
                    <h6 class="card-title text-info">Avg. Transaction</h6>
                    <h4 class="fw-bold">₱0.00</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <h6 class="card-title text-warning">Best Day</h6>
                    <h4 class="fw-bold">-</h4>
                </div>
            </div>
        </div>
    </div>
    <!-- Grid Row 1: Sales Trend, Sales by Category, Payment Method Breakdown -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card p-3 shadow-sm h-100">
                <h6 class="mb-3 text-success"><i class="fas fa-chart-line"></i> Sales Trend Over Time</h6>
                <canvas id="salesTrendChart" height="260"></canvas>
            </div>
        </div>
        <div class="col-lg-3 mb-3">
            <div class="card p-3 shadow-sm h-100">
                <h6 class="mb-3 text-primary"><i class="fas fa-chart-pie"></i> Sales by Category</h6>
                <canvas id="salesByCategoryChart" height="180"></canvas>
            </div>
        </div>
        <div class="col-lg-3 mb-3">
            <div class="card p-3 shadow-sm h-100">
                <h6 class="mb-3 text-info"><i class="fas fa-wallet"></i> Payment Method Breakdown</h6>
                <canvas id="paymentMethodChart" height="180"></canvas>
            </div>
        </div>
    </div>
    <!-- Grid Row 2: Voided Transactions, Low Stock Alerts, Recent Activity -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="card p-3 shadow-sm h-100">
                <h6 class="mb-3 text-danger"><i class="fas fa-ban"></i> Voided Transactions</h6>
                <canvas id="voidedTransactionsChart" height="180"></canvas>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card p-3 shadow-sm h-100">
                <h6 class="mb-3 text-warning"><i class="fas fa-exclamation-triangle"></i> Low Stock Alerts</h6>
                <ul class="list-group list-group-flush" style="max-height: 160px; overflow-y: auto;">
                    <li class="list-group-item text-muted">No low stock products (sample)</li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card p-3 shadow-sm h-100">
                <h6 class="mb-3 text-secondary"><i class="fas fa-history"></i> Recent Activity</h6>
                <ul class="list-group list-group-flush" style="max-height: 160px; overflow-y: auto;">
                    <li class="list-group-item text-muted">No recent activity (sample)</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Grid Row 3: Profit Margin by Product, Sales by Cashier -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card p-3 shadow-sm h-100">
                <h6 class="mb-3 text-success"><i class="fas fa-percent"></i> Profit Margin by Product</h6>
                <canvas id="profitMarginChart" height="220"></canvas>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card p-3 shadow-sm h-100">
                <h6 class="mb-3 text-primary"><i class="fas fa-user-tie"></i> Sales by Cashier</h6>
                <canvas id="salesByCashierChart" height="220"></canvas>
            </div>
        </div>
    </div>
    <!-- Detailed Sales Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-semibold">
            <i class="fas fa-table"></i> Detailed Sales Table
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Cashier</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No data (sample layout)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Download/Export Buttons -->
    <div class="mb-4">
        <button class="btn btn-outline-success me-2"><i class="fas fa-file-excel"></i> Export to Excel</button>
        <button class="btn btn-outline-danger me-2"><i class="fas fa-file-pdf"></i> Export to PDF</button>
        <button class="btn btn-outline-secondary"><i class="fas fa-print"></i> Print</button>
    </div>
</div>

<?php
// --- PHP: Prepare monthly sales data for the last 12 months ---
$monthlySales = [];
$monthLabels = [];
$now = new DateTime('first day of this month');
for ($i = 11; $i >= 0; $i--) {
    $month = (clone $now)->modify("-$i months");
    $key = $month->format('Y-m');
    $monthLabels[] = $month->format('M Y');
    $monthlySales[$key] = 0;
}
foreach ($transactions as $t) {
    $monthKey = date('Y-m', strtotime($t['sale_date']));
    if (isset($monthlySales[$monthKey])) {
        $monthlySales[$monthKey] += floatval($t['total_amount']);
    }
}
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- Sales Trend Over Time Chart ---
    const salesTrendLabels = <?= json_encode($monthLabels) ?>;
    const salesTrendData = <?= json_encode(array_values($monthlySales)) ?>;

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('salesTrendChart')) {
            const ctx = document.getElementById('salesTrendChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: salesTrendLabels,
                    datasets: [{
                        label: 'Monthly Sales (₱)',
                        data: salesTrendData,
                        fill: true,
                        backgroundColor: 'rgba(25, 135, 84, 0.08)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 3,
                        pointBackgroundColor: 'rgba(25, 135, 84, 1)',
                        pointRadius: 5,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => `₱${ctx.parsed.y.toLocaleString()}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#198754',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        x: {
                            ticks: {
                                color: '#198754',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>