<!-- POS Section -->

<div id="pos" class="content-section">
    <div class="row">
        <!-- Left Side - Product Grid and Filters -->
        <div class="col-lg-8">
            <!-- Search and Filters -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="productSearch" placeholder="Search products...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="categoryFilter">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['category_id']) ?>">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                <i class="fas fa-sync"></i> Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="row g-3" id="productGrid">
                <?php foreach ($products as $product):
                    if ($product['quantity_in_stock'] > 0): ?>
                        <div class="col-md-3 product-item"
                            data-category="<?= htmlspecialchars($product['category_id']) ?>"
                            data-name="<?= htmlspecialchars(strtolower($product['name'])) ?>">
                            <div class="card h-100">
                                <div class="card-img-top text-center bg-light p-2" style="height: 150px; display: flex; align-items: center; justify-content: center;">

                                    <?php if (!empty($product['image_path'])): ?>

                                        <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                    <?php else: ?>
                                        <i class="fas fa-box fa-3x text-secondary"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title mb-1"><?= htmlspecialchars($product['name']) ?></h6>
                                    <p class="card-text mb-1">
                                        <small class="text-muted"><?= htmlspecialchars($product['unit_name']) ?></small>
                                    </p>
                                    <p class="card-text text-primary mb-2">₱<?= number_format($product['selling_price'], 2) ?></p>
                                    <button class="btn btn-sm btn-primary w-100"
                                        onclick="addToCart(<?= htmlspecialchars(json_encode([
                                                                'id' => $product['product_id'],
                                                                'name' => $product['name'],
                                                                'price' => $product['selling_price'],
                                                                'unit' => $product['unit_name'],
                                                                'stock' => $product['quantity_in_stock']
                                                            ])) ?>)">
                                        Add to Cart
                                    </button>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <small class="text-success">
                                        <i class="fas fa-check-circle"></i>
                                        In Stock: <?= $product['quantity_in_stock'] ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right Side - Receipt -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 70px; z-index: 1020;">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-cart"></i> Current Transaction
                    </h5>
                </div>
                <div class="card-body p-0">
                    <!-- Cart Items -->
                    <div class="cart-items" id="cartItems" style="max-height: 60vh; overflow-y: auto; margin-bottom: 0;">
                        <!-- Cart items will be dynamically added here -->
                    </div>

                    <!-- Cart Summary -->
                    <div class="p-3 border-top">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">₱0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Items:</span>
                            <span id="totalItems">0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h5>Total:</h5>
                            <h5 class="text-primary" id="total">₱0.00</h5>
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="p-3 border-top">
                        <div class="mb-3">
                            <label class="form-label">Payment Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="paymentAmount" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Change</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="text" class="form-control" id="changeAmount" readonly>
                            </div>
                        </div>
                        <button class="btn btn-success w-100 mb-2" onclick="processPayment()">
                            <i class="fas fa-cash-register"></i> Process Payment
                        </button>
                        <button class="btn btn-outline-danger w-100" onclick="clearCart()">
                            <i class="fas fa-trash"></i> Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>