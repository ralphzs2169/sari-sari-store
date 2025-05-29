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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 text-success"><i class="fas fa-percent"></i> Profit Performance</h6>
                        <span id="profitPerformanceTotal" class="fw-bold text-success" style="font-size:1.1em"></span>
                    </div>
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

    });
</script>