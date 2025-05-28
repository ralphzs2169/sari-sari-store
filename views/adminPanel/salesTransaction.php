<div id="sales" class="content-section">
    <div class="tab-content" id="salesTabContent">
        <!-- Sales Transactions Tab -->
        <div class="tab-pane fade show active" id="sales-pane" role="tabpanel" aria-labelledby="sales-tab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fas fa-receipt text-success"></i> Sales Transactions</h4>
            </div>

            <div class="table-responsive">
                <table id="salesTable" class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Admin</th>
                            <th>Date</th>
                            <th>Payment Method</th>
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $counter = 1;

                        foreach ($transactions as $transaction):
                            $customer = $transaction['customer_id'] ?? 'Walk-in';
                            $admin = $transaction['admin_id']; // You can JOIN to get admin name if needed
                            $date = date('Y-m-d H:i', strtotime($transaction['sale_date']));
                            $method = ucfirst($transaction['payment_method']);
                            $total = number_format($transaction['total_amount'], 2);
                        ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td><?= htmlspecialchars($customer) ?></td>
                                <td><?= htmlspecialchars($admin) ?></td>
                                <td><?= $date ?></td>
                                <td><span class="badge bg-info text-dark"><?= $method ?></span></td>
                                <td>₱<?= $total ?></td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-outline-info"
                                        onclick="viewTransactionDetails(<?= $transaction['sale_id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <!-- Optional Edit/Delete depending on your decision -->
                                    <!--
                                <button class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteTransaction(<?= $transaction['sale_id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                                -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>