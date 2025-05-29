<div id="sales" class="content-section">
    <div class="tab-content" id="salesTabContent">
        <!-- Sales Transactions Tab -->
        <div class="tab-pane fade show active" id="sales-pane" role="tabpanel" aria-labelledby="sales-tab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fas fa-receipt text-success"></i> Sales Transactions</h4>
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
                        $badgeClass = ($method === 'Cash') ? 'bg-success ' : 'bg-primary';
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
                                <?php else: ?>
                                    <span class="badge bg-success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button
                                    class="btn btn-sm btn-outline-info"
                                    onclick="viewTransactionDetails(<?= $transaction['sale_id'] ?>)">
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
            <!-- Sales Analytics Chart Section -->
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
                    <canvas id="salesChart" height="90"></canvas>
                </div>
            </div>

            <!-- Top Products Chart Section -->
            <div class="mt-4" id="topProductsSection">
                <div class="d-flex align-items-center mb-3 gap-2">
                    <h5 class="text-success mb-0 me-3"><i class="fas fa-chart-bar"></i> Top Products</h5>
                </div>
                <div class="card p-3 shadow-sm">
                    <canvas id="topProductsChart" height="150"></canvas>
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
</script>