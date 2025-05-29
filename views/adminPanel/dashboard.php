<div id="dashboard" class="content-section">
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
                <h3 class="stats-number text-primary"><?= htmlspecialchars($totalProductCount) ?></h3>
                <p class="stats-label">Total Products</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: linear-gradient(135deg, #27ae60, #229954);">
                    <i class="fas fa-peso-sign"></i>
                </div>
                <h3 class="stats-number text-success">₱<?= number_format($totalSales, 2) ?></h3>
                <p class="stats-label">Today's Sales</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: linear-gradient(135deg, #e67e22, #d35400);">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="stats-number text-warning"><?= number_format($todayTransactionCount) ?></h3>
                <p class="stats-label">Transactions Today</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="stats-number text-danger"><?= htmlspecialchars($totalLowStockCount) ?></h3>
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
                <a href="?section=sales" class="action-btn" data-section="sales">
                    <i class="fas fa-cash-register"></i> New Sale Transaction
                </a>
                <a href="?section=reports" class="action-btn" data-section="reports">
                    <i class="fas fa-chart-bar"></i> Generate Report
                </a>
                <a href="?section=products" class="action-btn" data-section="products">
                    <i class="fas fa-warehouse"></i> Check Inventory
                </a>
            </div>
        </div>
        <style>
            #activityTable {
                border-collapse: collapse !important;
                /* change from separate to collapse */
                border-spacing: 0;
                /* remove spacing */
            }

            #activityTable tbody tr {
                background: #ffffff;
                /* white background */
                border-radius: 0;
                /* no rounding */
                /* optionally add a subtle border between rows */
                border-bottom: 1px solid #dee2e6;
            }

            #activityTable tbody tr:last-child {
                border-bottom: none;
                /* no border on last row */
            }

            #activityTable tbody tr td {
                vertical-align: middle;
                padding: 8px 10px;
                /* reduce vertical padding to 8px, keep horizontal padding 10px */
            }

            .activity-time {
                font-size: 0.85rem;
                color: #6c757d;
            }

            #activityTable>thead {
                display: none;
            }
        </style>
        <!-- Recent Activity -->
        <div class="col-lg-8 mb-4">
            <div class="recent-activity">
                <h5 class="mb-3"><i class="fas fa-clock text-info"></i> Recent Activity</h5>
                <table id="activityTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        function timeAgo($datetime, $full = false)
                        {
                            // Philippine timezone
                            $timezone = new DateTimeZone('Asia/Manila');

                            $now = new DateTime('now', $timezone);
                            $ago = new DateTime($datetime, $timezone);

                            $diff = $now->diff($ago);

                            $weeks = floor($diff->d / 7);
                            $days = $diff->d % 7;

                            $string = [
                                'y' => $diff->y,
                                'm' => $diff->m,
                                'w' => $weeks,
                                'd' => $days,
                                'h' => $diff->h,
                                'i' => $diff->i,
                                's' => $diff->s,
                            ];

                            $units = [
                                'y' => 'year',
                                'm' => 'month',
                                'w' => 'week',
                                'd' => 'day',
                                'h' => 'hour',
                                'i' => 'minute',
                                's' => 'second',
                            ];

                            foreach ($string as $k => $v) {
                                if ($v) {
                                    $string[$k] = $v . ' ' . $units[$k] . ($v > 1 ? 's' : '');
                                } else {
                                    unset($string[$k]);
                                }
                            }

                            if (!$full) $string = array_slice($string, 0, 1); // only biggest unit

                            return $string ? implode(', ', $string) . ' ago' : 'just now';
                        }


                        ?>

                        <!-- inside your foreach loop -->
                        <?php
                        foreach ($activityLogs as $log) {
                            $createdAtAgo = timeAgo($log['created_at']);
                            $activityTitle = ucwords(str_replace('_', ' ', $log['activity_type']));
                            $description = htmlspecialchars($log['description']);
                            $shortDescription = mb_strlen($description) > 70 ? mb_substr($description, 0, 70) . "..." : $description;

                            echo "<tr class='activity-row' data-full-desc=\"{$description}\">";
                            echo "<td><strong>{$activityTitle}</strong><br><small class='text-muted activity-desc'>{$shortDescription}</small></td>";
                            echo "<td><span class='activity-time'>{$createdAtAgo}</span></td>";
                            echo "</tr>";
                        }

                        ?>

                    </tbody>
                </table>


            </div>
        </div>

    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const rows = document.querySelectorAll('.activity-row');

        rows.forEach(row => {
            row.addEventListener('click', () => {
                const descEl = row.querySelector('.activity-desc');
                const full = row.getAttribute('data-full-desc');

                if (descEl.textContent.length > 70 && descEl.textContent.endsWith("...")) {
                    descEl.textContent = full;
                } else {
                    descEl.textContent = full.length > 100 ? full.slice(0, 70) + "..." : full;
                }
            });
        });
    });
</script>