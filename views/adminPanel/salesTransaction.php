<div id="sales" class="content-section">
    <!-- Tabs for Sales Transactions, Sales Analytics, Top Products -->
    <ul class="nav nav-tabs mb-3" id="salesTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales-pane" type="button" role="tab">Sales Transactions</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics-pane" type="button" role="tab">Sales Analytics</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="top-products-tab" data-bs-toggle="tab" data-bs-target="#top-products-pane" type="button" role="tab">Top Products</button>
        </li>
    </ul>
    <div class="tab-content" id="salesTabContent">
        <!-- Sales Transactions Tab -->
        <div class="tab-pane fade show active" id="sales-pane" role="tabpanel" aria-labelledby="sales-tab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fas fa-receipt text-success"></i> Sales Transactions</h4>
                <div>
                    <label for="transactionStatusFilter" class="form-label me-2 mb-0">Filter by Status:</label>
                    <select id="transactionStatusFilter" class="form-select form-select-sm w-auto d-inline-block">
                        <option value="">All</option>
                        <option value="Active" selected>Active</option>
                        <option value="Void">Void</option>
                    </select>
                </div>
            </div>
            <table id="salesTable" class="table table-hover table-striped">
                <thead class="table-light">
                    <tr>
                        <th>No.</th>
                        <th>Cashier</th>
                        <th>Date</th>
                        <th>Payment Method</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    foreach ($transactions as $transaction):
                        $admin = $transaction['username'];
                        $date = date('F j, Y | g:i A', strtotime($transaction['sale_date']));
                        $method = ucfirst($transaction['payment_method']);
                        $total = number_format($transaction['total_amount'], 2);
                        $badgeClass = ($method === 'Cash') ? 'bg-success' : 'bg-primary';
                        $isVoided = (isset($transaction['status']) && strtolower($transaction['status']) === 'void');
                    ?>
                        <tr<?= $isVoided ? ' class="table-danger"' : '' ?>>
                            <td><?= $counter++ ?></td>
                            <td><?= htmlspecialchars($admin) ?></td>
                            <td><?= $date ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= $method ?></span></td>
                            <td>₱<?= $total ?></td>
                            <td>
                                <?php if ($isVoided): ?>
                                    <span class="badge bg-danger">Void</span>
                                    <span class="d-none">Void</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Active</span>
                                    <span class="d-none">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-info" onclick="viewTransactionDetails(<?= $transaction['sale_id'] ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if (!$isVoided): ?>
                                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteTransaction(<?= $transaction['sale_id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                            </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Sales Analytics Tab -->
        <div class="tab-pane fade" id="analytics-pane" role="tabpanel" aria-labelledby="analytics-tab">
            <div class="mt-4" id="sales-analytics-section">
                <div class="d-flex flex-wrap align-items-center mb-3 gap-2">
                    <h5 class="text-success mb-0 me-3"><i class="fas fa-chart-bar"></i> Sales Analytics</h5>
                    <label class="form-label mb-0 me-2">Date Range:</label>
                    <select id="salesChartRange" class="form-select form-select-sm w-auto me-2">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="this_week">This Week</option>
                        <option value="last_week">Last Week</option>
                        <option value="custom">Custom</option>
                    </select>
                    <input type="date" id="salesChartStart" class="form-control form-control-sm w-auto me-2 d-none">
                    <span id="toLabel" class="mx-1 d-none">to</span>
                    <input type="date" id="salesChartEnd" class="form-control form-control-sm w-auto me-2 d-none">
                    <label class="form-label mb-0 me-2">Payment Method:</label>
                    <select id="salesChartPayment" class="form-select form-select-sm w-auto">
                        <option value="all">All</option>
                        <option value="cash">Cash</option>
                        <option value="gcash">GCash</option>
                    </select>
                </div>
                <div class="card p-3 shadow-sm">
                    <canvas id="salesChart" height="300" style="min-height:300px;max-height:400px;width:100% !important;"></canvas>
                </div>
            </div>
        </div>
        <!-- Top Products Tab -->
        <div class="tab-pane fade" id="top-products-pane" role="tabpanel" aria-labelledby="top-products-tab">
            <div class="mt-4" id="topProductsSection">
                <div class="d-flex align-items-center mb-3 gap-2">
                    <h5 class="text-success mb-0 me-3"><i class="fas fa-chart-bar"></i> Top Products</h5>
                </div>
                <div class="card p-3 shadow-sm">
                    <canvas id="topProductsChart" height="350" style="min-height:350px;max-height:500px;width:100% !important;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="transactionDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionDetailsModalLabel">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="transaction-details-content">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom DataTables styles for your app */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        background: #fff;
        border: 1px solid #dee2e6;
        color: #198754;
        border-radius: 0.25rem;
        margin: 0 2px;
        padding: 0.25rem 0.75rem;
        transition: background 0.2s, color 0.2s;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #198754;
        color: #fff !important;
        border: 1px solid #198754;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.25rem;
        border: 1px solid #dee2e6;
        padding: 0.25rem 0.5rem;
        margin-left: 0.5em;
    }

    .dataTables_wrapper .dataTables_length select {
        border-radius: 0.25rem;
        border: 1px solid #dee2e6;
        padding: 0.25rem 0.5rem;
        margin-right: 0.5em;
    }

    .dataTables_wrapper .dataTables_info {
        color: #198754;
        margin-top: 0.5em;
    }

    .dataTables_wrapper .dataTables_filter label {
        color: #198754;
        font-weight: 500;
    }

    .dataTables_wrapper .dataTables_length label {
        color: #198754;
        font-weight: 500;
    }
</style>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/sari-sari-store/assets/datatables/js/datatables.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.jQuery && $('#salesTable').length) {
            var table;
            if (!$.fn.DataTable.isDataTable('#salesTable')) {
                table = $('#salesTable').DataTable({
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    language: {
                        search: '',
                        searchPlaceholder: 'Search transactions...'
                    },
                    order: [
                        [2, 'desc']
                    ]
                });
            } else {
                table = $('#salesTable').DataTable();
            }
            // Initial filter: show only 'Active' status
            table.column(5).search('Active', true, false).draw();
            // Filter dropdown event
            $('#transactionStatusFilter').on('change', function() {
                var val = $(this).val();
                table.column(5).search(val, true, false).draw();
            });
        }
    });
</script>

<script>
    // Group transactions by sale_id for easy lookup (each sale_id may have multiple items)
    const transactions = {};
    <?php foreach ($transactions as $row): ?>
        transactions['<?= $row['sale_id'] ?>'] = <?= json_encode([
                                                        'sale_id' => $row['sale_id'],
                                                        'username' => $row['username'],
                                                        'sale_date' => $row['sale_date'],
                                                        'total_amount' => $row['total_amount'],
                                                        'payment_method' => $row['payment_method'],
                                                        'status' => $row['status'],
                                                        // Wrap items in square brackets to make valid JSON
                                                        'items' => json_decode('[' . $row['items_json'] . ']')
                                                    ]) ?>;
    <?php endforeach; ?>


    function formatDateTime(dateString) {
        const date = new Date(dateString.replace(' ', 'T'));
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        const datePart = date.toLocaleDateString('en-US', options);
        let hours = date.getHours();
        const minutes = date.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        return `${datePart} | ${hours}:${minutes} ${ampm}`;
    }

    function viewTransactionDetails(saleId) {
        var transaction = transactions[saleId];
        if (!transaction || !transaction.items || transaction.items.length === 0) {
            document.getElementById('transaction-details-content').innerHTML = '<p>No details found.</p>';
            var modal = new bootstrap.Modal(document.getElementById('transactionDetailsModal'));
            modal.show();
            return;
        }

        var header = transaction; // ✅ Fix: this is the actual transaction object
        var items = transaction.items; // ✅ Fix: extract items array
        var totalQty = items.reduce((sum, item) => sum + parseInt(item.quantity), 0);

        var method = header.payment_method ? header.payment_method.toLowerCase() : '';
        var badgeClass = method === 'cash' ? 'bg-success' : (method === 'gcash' ? 'bg-primary' : 'bg-secondary');
        var methodLabel = method.charAt(0).toUpperCase() + method.slice(1);
        var isVoided = header.status && header.status.toLowerCase() === 'void';
        var voidBadge = isVoided ? '<span class="badge bg-danger">VOID</span>' : '';

        var html = `
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="border rounded p-3 h-100 bg-light">
                <div class="mb-2"><strong>Sale No.:</strong> ${header.sale_id} ${voidBadge}</div>
                <div class="mb-2"><strong>Admin:</strong> ${header.username}</div>
                <div><strong>Date:</strong> ${formatDateTime(header.sale_date)}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="border rounded p-3 h-100 bg-light">
                <div class="mb-2"><strong>Total Quantity:</strong> ${totalQty}</div>
                <div class="mb-2"><strong>Total Payment:</strong> ₱${parseFloat(header.total_amount).toFixed(2)}</div>
                <div><strong>Payment Method:</strong> <span class="badge ${badgeClass}">${methodLabel}</span></div>
            </div>
        </div>
    </div>
    <hr>
    <h5>Items:</h5>
    <div class="table-responsive" style="overflow-x:auto;">
    <table class="table table-bordered mb-0" style="table-layout:fixed;">
        <thead>
            <tr>
                <th style="width:70px;">Image</th>
                <th style="min-width:120px;max-width:200px;">Product</th>
                <th style="width:60px;">Qty</th>
                <th style="width:90px;">Price</th>
                <th style="width:100px;">Subtotal</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:270px; overflow-y:auto;">
    <table class="table table-bordered mb-0" style="table-layout:fixed;">
        <tbody>
    `;

        items.forEach(function(item) {
            html += `
            <tr>
                <td style="width:70px;"><img src="${item.image_path}" style="width:60px;height:60px;object-fit:cover;" alt="${item.product_name}"></td>
                <td style="word-break:break-word;white-space:normal;min-width:120px;max-width:200px;">${item.product_name}</td>
                <td style="width:60px;">${item.quantity}</td>
                <td style="width:90px;">₱${parseFloat(item.price).toFixed(2)}</td>
                <td style="width:100px;">₱${parseFloat(item.subtotal).toFixed(2)}</td>
            </tr>
        `;
        });

        html += `
        </tbody>
    </table>
    </div>
    </div>
    `;

        document.getElementById('transaction-details-content').innerHTML = html;
        var modal = new bootstrap.Modal(document.getElementById('transactionDetailsModal'));
        modal.show();
    }


    function confirmDeleteTransaction(saleId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Are you sure you want to void/cancel this transaction? This action cannot be reverted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, void/cancel it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit a form to POST to the controller
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/sari-sari-store/controllers/salesTransactionController.php?action=void';
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'sale_id';
                input.value = saleId;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // --- Top Products Chart (PHP to JS, no AJAX) ---
    <?php
    $topProductLabels = array_column($topProducts, 'name');
    $topProductValues = array_map('intval', array_column($topProducts, 'total_units_sold'));
    ?>
    const topProductLabels = <?= json_encode($topProductLabels) ?>;
    const topProductValues = <?= json_encode($topProductValues) ?>;
    let topProductsChart;

    // --- Chart.js Tab Handling ---
    function renderTopProductsChart(labels, data) {
        const canvas = document.getElementById('topProductsChart');
        if (!canvas) {
            console.error('Top Products canvas not found!');
            return;
        }
        const ctx = canvas.getContext('2d');
        if (topProductsChart) topProductsChart.destroy();
        topProductsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Units Sold',
                    data: data,
                    backgroundColor: 'rgba(25, 135, 84, 0.5)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 2,
                    borderRadius: 8, // slightly more rounded
                    maxBarThickness: 60 // wider bars
                }]
            },
            options: {
                indexAxis: 'y', // horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.parsed.x.toLocaleString()} units`
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            color: '#198754',
                            font: {
                                weight: 'bold'
                            }
                        }
                    },
                    y: {
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

    let salesChartInitialized = false;
    let topProductsChartInitialized = false;

    function renderSalesChartIfNeeded() {
        if (!salesChartInitialized) {
            // If you have a renderSalesChart() function, call it here
            if (typeof renderSalesChart === 'function') {
                renderSalesChart();
            }
            salesChartInitialized = true;
        }
    }

    function renderTopProductsChartIfNeeded() {
        if (!topProductsChartInitialized) {
            renderTopProductsChart(topProductLabels, topProductValues);
            topProductsChartInitialized = true;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Render sales chart if analytics tab is active by default
        if (document.getElementById('analytics-pane').classList.contains('show')) {
            renderSalesChartIfNeeded();
        }
        // Render top products chart if top products tab is active by default
        if (document.getElementById('top-products-pane').classList.contains('show')) {
            renderTopProductsChartIfNeeded();
        }
        // Tab shown event
        var salesTabs = document.getElementById('salesTabs');
        if (salesTabs) {
            salesTabs.addEventListener('shown.bs.tab', function(event) {
                if (event.target.getAttribute('data-bs-target') === '#analytics-pane') {
                    renderSalesChartIfNeeded();
                } else if (event.target.getAttribute('data-bs-target') === '#top-products-pane') {
                    renderTopProductsChartIfNeeded();
                }
            });
        }
    });
</script>