<?php
// --- PHP: Prepare summary card variables before HTML ---
$totalSales = 0;
$transactionCount = 0;
$avgTransaction = 0;
if (isset($transactions) && is_array($transactions) && count($transactions) > 0) {
    $totalSales = array_sum(array_column($transactions, 'total_amount'));
    $transactionCount = count($transactions);
    $avgTransaction = $transactionCount > 0 ? $totalSales / $transactionCount : 0;
}

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
<?php
// Sales Report Section - Enhanced Grid Layout
?>

<div id="reports" class="content-section">
    <div class="container-fluid">
        <h4 class="mb-3"><i class="fas fa-chart-line text-primary"></i> Sales Reports</h4>
        <!-- Summary Cards Row -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-success">
                    <div class="card-body text-center">
                        <h6 class="card-title text-success">Total Sales</h6>
                        <h4 class="fw-bold">₱<?= number_format($totalSales, 2) ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-primary">
                    <div class="card-body text-center">
                        <h6 class="card-title text-primary">Transactions</h6>
                        <h4 class="fw-bold"><?= number_format($transactionCount) ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-info">
                    <div class="card-body text-center">
                        <h6 class="card-title text-info">Avg. Transaction</h6>
                        <h4 class="fw-bold">₱<?= number_format($avgTransaction, 2) ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-warning">
                    <div class="card-body text-center">
                        <h6 class="card-title text-warning">Total Profit</h6>
                        <h4 class="fw-bold">₱<?= number_format($totalProfit, 2) ?></h4>
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
                <div class="card p-3 shadow-sm h-100 category-payment-card">
                    <h6 class="mb-3 text-primary"><i class="fas fa-chart-pie"></i> Sales by Category</h6>
                    <canvas id="salesByCategoryChart" height="260"></canvas>
                </div>
            </div>
            <div class="col-lg-3 mb-3">
                <div class="card p-3 shadow-sm h-100 category-payment-card">
                    <h6 class="mb-3 text-info"><i class="fas fa-wallet"></i> Payment Method Breakdown</h6>
                    <canvas id="paymentMethodChart" height="260"></canvas>
                </div>
            </div>
        </div>
        <style>
            /* Match background and spacing for category/payment cards, and align chart heights */
            .category-payment-card {
                background: linear-gradient(135deg, #f8fafc 0%, #f1f3f6 100%);
                min-height: 340px;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
            }

            #salesByCategoryChart,
            #paymentMethodChart,
            #salesTrendChart {
                min-height: 260px !important;
                max-height: 260px !important;
            }
        </style>
        <!-- Grid Row 3: Profit Performance (full width) -->
        <div class="row mb-4">
            <div class="col-12 mb-3">
                <div class="card p-3 shadow-sm h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 text-success"><i class="fas fa-percent"></i> Profit Performance</h6>
                        <span id="profitPerformanceTotal" class="fw-bold text-success" style="font-size:1.1em"></span>
                    </div>
                    <canvas id="profitMarginChart" height="220"></canvas>
                </div>
            </div>
        </div>
        <!-- Grid Row 2: Voided Transactions, Low Stock Alerts, Sales by Cashier -->
        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="card p-3 shadow-sm h-100">
                    <h6 class="mb-3 text-danger"><i class="fas fa-ban"></i> Voided Transactions</h6>
                    <div style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-sm mb-0" style="min-width:100%; table-layout:fixed;">
                            <thead class="table-light" style="position:sticky;top:0;z-index:2;background:#fff;">
                                <tr>
                                    <th style="width:60%;">Date</th>
                                    <th style="width:25%;">Cashier</th>
                                    <th style="width:15%;">View</th>
                                </tr>
                            </thead>
                            <tbody id="voidedTransactionsTbody">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card p-3 shadow-sm h-100">
                    <h6 class="mb-3 text-warning"><i class="fas fa-exclamation-triangle"></i> Low Stock Alerts</h6>
                    <div style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-sm mb-0" style="min-width:100%; table-layout:fixed;">
                            <thead class="table-light" style="position:sticky;top:0;z-index:2;background:#fff;">
                                <tr>
                                    <th style="width:85%;">Product Name</th>
                                    <th style="width:15%;">Qty</th>
                                </tr>
                            </thead>
                            <tbody id="lowStockTbody">
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card p-3 shadow-sm h-100">
                    <h6 class="mb-3 text-primary"><i class="fas fa-user-tie"></i> Sales by Cashier</h6>
                    <canvas id="salesByCashierChart" height="220"></canvas>
                </div>
            </div>
        </div>

        <!-- Download/Export Buttons -->
        <div class="mb-4">
            <button class="btn btn-outline-success me-2"><i class="fas fa-file-excel"></i> Export to Excel</button>
            <button class="btn btn-outline-danger me-2"><i class="fas fa-file-pdf"></i> Export to PDF</button>
            <button class="btn btn-outline-secondary"><i class="fas fa-print"></i> Print</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
<script>
    const salesTrendLabels = <?= json_encode($monthLabels) ?>;
    const salesTrendData = <?= json_encode(array_values($monthlySales)) ?>;

    document.addEventListener('DOMContentLoaded', async function() {

        // --- Sales Trend Over Time Chart ---
        const trendCanvas = document.getElementById('salesTrendChart');
        if (trendCanvas) {
            const ctx = trendCanvas.getContext('2d');
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

        // --- Sales by Category Chart ---
        const categoryCanvas = document.getElementById('salesByCategoryChart');
        if (categoryCanvas) {
            try {
                const response = await fetch('/sari-sari-store/controllers/salesTransactionController.php?action=sales_by_category');
                const data = await response.json();

                const labels = data.map(item => item.category);
                const sales = data.map(item => parseFloat(item.total_sales));
                const ctx = categoryCanvas.getContext('2d');

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Sales by Category',
                            data: sales,
                            backgroundColor: [
                                '#198754', '#0d6efd', '#ffc107', '#dc3545',
                                '#6610f2', '#20c997', '#fd7e14', '#6c757d'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#333',
                                    font: {
                                        weight: 'bold'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => `${ctx.label}: ₱${ctx.parsed.toLocaleString()}`
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error loading category sales data:', error);
            }
        }

        // --- Payment Method Breakdown Chart ---
        const paymentMethodCanvas = document.getElementById('paymentMethodChart');
        if (paymentMethodCanvas) {
            try {
                const response = await fetch('/sari-sari-store/controllers/salesTransactionController.php?action=payment_method_breakdown');
                const data = await response.json();

                const filtered = data.filter(item => item.payment_method && !isNaN(parseFloat(item.total_sales)));
                const labels = filtered.map(item => item.payment_method.charAt(0).toUpperCase() + item.payment_method.slice(1));
                const sales = filtered.map(item => parseFloat(item.total_sales));
                const ctx = paymentMethodCanvas.getContext('2d');

                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Payment Method Breakdown',
                            data: sales,
                            backgroundColor: [
                                '#198754', '#0d6efd'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#333',
                                    font: {
                                        weight: 'bold'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => `${ctx.label}: ₱${ctx.parsed.toLocaleString()}`
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error loading payment method data:', error);
            }
        }

        // --- Profit Performance Chart (Simple Bar, Customizable) ---
        const profitPerformanceCanvas = document.getElementById('profitMarginChart');
        if (profitPerformanceCanvas) {
            // Add filter controls if not already present
            let filterDiv = document.getElementById('profitPerformanceFilterContainer');
            if (!filterDiv) {
                filterDiv = document.createElement('div');
                filterDiv.id = 'profitPerformanceFilterContainer';
                filterDiv.className = 'mb-2';
                profitPerformanceCanvas.parentNode.insertBefore(filterDiv, profitPerformanceCanvas);
            }
            filterDiv.innerHTML = `
                <label class="form-label me-2">View Profit:</label>
                <select id="profitPerformanceRange" class="form-select form-select-sm w-auto d-inline-block me-2">
                    <option value="weekly" selected>This Week</option>
                    <option value="monthly">This Month</option>
                    <option value="custom">Custom Range</option>
                </select>
                <input type="date" id="profitStartDate" class="form-control form-control-sm d-inline-block me-2" style="width:auto;display:none;">
                <span id="profitToLabel" class="mx-1 d-none">to</span>
                <input type="date" id="profitEndDate" class="form-control form-control-sm d-inline-block" style="width:auto;display:none;">
            `;

            let profitPerformanceChart;
            async function renderProfitPerformanceChart(range = 'weekly', start = '', end = '') {
                let url = `/sari-sari-store/controllers/salesTransactionController.php?action=profit_performance&range=${range}`;
                if (range === 'custom' && start && end) {
                    url += `&start=${start}&end=${end}`;
                }
                const response = await fetch(url);
                const data = await response.json();
                const labels = data.map(item => item.label);
                const profits = data.map(item => parseFloat(item.profit));
                // Calculate total
                const total = profits.reduce((a, b) => a + b, 0);
                let totalLabel = '';
                if (range === 'weekly') totalLabel = `Weekly Total: ₱${total.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}`;
                else if (range === 'monthly') totalLabel = `Monthly Total: ₱${total.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}`;
                else totalLabel = `Total: ₱${total.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}`;
                document.getElementById('profitPerformanceTotal').textContent = totalLabel;
                if (profitPerformanceChart) profitPerformanceChart.destroy();
                const ctx = profitPerformanceCanvas.getContext('2d');
                profitPerformanceChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Profit (₱)',
                            data: profits,
                            backgroundColor: 'rgba(25, 135, 84, 0.7)',
                            borderColor: 'rgba(25, 135, 84, 1)',
                            borderWidth: 2,
                            maxBarThickness: 60
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            zoom: {
                                pan: {
                                    enabled: true,
                                    mode: 'x'
                                },
                                zoom: {
                                    wheel: {
                                        enabled: true
                                    },
                                    pinch: {
                                        enabled: true
                                    },
                                    mode: 'x'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => `₱${ctx.parsed.y ? ctx.parsed.y.toLocaleString() : 0}`
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Period'
                                },
                                ticks: {
                                    color: '#198754',
                                    font: {
                                        weight: 'bold'
                                    },
                                    autoSkip: false,
                                    maxRotation: 0,
                                    minRotation: 0
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Profit (₱)'
                                },
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
            // Initial render
            renderProfitPerformanceChart('weekly');
            const rangeSelect = document.getElementById('profitPerformanceRange');
            const startInput = document.getElementById('profitStartDate');
            const endInput = document.getElementById('profitEndDate');
            const toLabel = document.getElementById('profitToLabel');
            rangeSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    startInput.style.display = '';
                    endInput.style.display = '';
                    toLabel.classList.remove('d-none');
                } else {
                    startInput.style.display = 'none';
                    endInput.style.display = 'none';
                    toLabel.classList.add('d-none');
                    renderProfitPerformanceChart(this.value);
                }
            });
            startInput.addEventListener('change', function() {
                if (rangeSelect.value === 'custom' && startInput.value && endInput.value) {
                    renderProfitPerformanceChart('custom', startInput.value, endInput.value);
                }
            });
            endInput.addEventListener('change', function() {
                if (rangeSelect.value === 'custom' && startInput.value && endInput.value) {
                    renderProfitPerformanceChart('custom', startInput.value, endInput.value);
                }
            });
        }

        // --- Sales by Cashier Chart ---
        const cashierCanvas = document.getElementById('salesByCashierChart');
        if (cashierCanvas) {
            try {
                const response = await fetch('/sari-sari-store/controllers/salesTransactionController.php?action=sales_by_cashier');
                const data = await response.json();
                const labels = data.map(item => item.cashier);
                const sales = data.map(item => parseFloat(item.total_sales));
                const ctx = cashierCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Sales by Cashier',
                            data: sales,
                            backgroundColor: 'rgba(13, 110, 253, 0.7)',
                            borderColor: 'rgba(13, 110, 253, 1)',
                            borderWidth: 2,
                            maxBarThickness: 60
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => `₱${ctx.parsed.x ? ctx.parsed.x.toLocaleString() : ctx.parsed.y.toLocaleString()}`
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Sales (₱)'
                                },
                                ticks: {
                                    color: '#0d6efd',
                                    font: {
                                        weight: 'bold'
                                    }
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Cashier'
                                },
                                ticks: {
                                    color: '#0d6efd',
                                    font: {
                                        weight: 'bold'
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error loading cashier sales data:', error);
            }
        }

        // --- Low Stock Alerts Table Loader ---
        async function loadLowStockAlerts() {
            const tbody = document.getElementById('lowStockTbody');
            tbody.innerHTML = `<tr><td colspan='2' class='text-center text-muted'>Loading...</td></tr>`;
            try {
                const response = await fetch('/sari-sari-store/controllers/productController.php?action=low_stock');
                const data = await response.json();
                if (!data.length) {
                    tbody.innerHTML = `<tr><td colspan='2' class='text-center text-muted'>No low stock products</td></tr>`;
                    return;
                }
                tbody.innerHTML = data.map(prod =>
                    `<tr>
                        <td class='low-stock-product-name'>${prod.name}</td>
                        <td>${prod.quantity_in_stock}</td>
                    </tr>`
                ).join('');
            } catch (e) {
                tbody.innerHTML = `<tr><td colspan='2' class='text-center text-danger'>Error loading data</td></tr>`;
            }
        }
        loadLowStockAlerts();

        // --- Voided Transactions Table Loader ---
        async function loadVoidedTransactions() {
            const tbody = document.getElementById('voidedTransactionsTbody');
            tbody.innerHTML = `<tr><td colspan='3' class='text-center text-muted'>Loading...</td></tr>`;
            try {
                const response = await fetch('/sari-sari-store/controllers/salesTransactionController.php?action=voided_transactions');
                const data = await response.json();
                if (!data.length) {
                    tbody.innerHTML = `<tr><td colspan='3' class='text-center text-muted'>No voided transactions</td></tr>`;
                    return;
                }
                tbody.innerHTML = data.map(tran => {
                    const date = new Date(tran.sale_date);
                    const formatted = date.toLocaleString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });
                    // Format: May 2, 2023 | 3:43 PM
                    const dateStr = `${date.toLocaleString('en-US', { month: 'short' })} ${date.getDate()}, ${date.getFullYear()} | ${date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}`;
                    return `<tr>
                        <td>${dateStr}</td>
                        <td>${tran.cashier || '-'}</td>
                        <td class='text-center'>
                            <button class='btn btn-link p-0 view-voided-transaction' data-id='${tran.sale_id}' title='View'>
                                <i class='fas fa-eye text-primary'></i>
                            </button>
                        </td>
                    </tr>`;
                }).join('');
            } catch (e) {
                tbody.innerHTML = `<tr><td colspan='3' class='text-center text-danger'>Error loading data</td></tr>`;
            }
        }
        loadVoidedTransactions();
        // Delegate click for view buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.view-voided-transaction')) {
                const btn = e.target.closest('.view-voided-transaction');
                const saleId = btn.getAttribute('data-id');
                fetch(`/sari-sari-store/controllers/salesTransactionController.php?action=view_transaction&sale_id=${saleId}`)
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('viewTransactionModalBody').innerHTML = html;
                        const modal = new bootstrap.Modal(document.getElementById('viewTransactionModal'));
                        modal.show();
                    });
            }
        });

    });
</script>
<style>
    .low-stock-product-name {
        word-break: break-word;
        white-space: normal;
        max-width: 100%;
    }
</style>

<!-- View Transaction Modal (reuse from salesTransaction) -->
<div class="modal fade" id="viewTransactionModal" tabindex="-1" aria-labelledby="viewTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTransactionModalLabel">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewTransactionModalBody">
                <!-- Transaction details will be loaded here -->
            </div>
        </div>
    </div>
</div>