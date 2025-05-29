<!-- POS Section -->

<div id="pos" class="content-section">
    <div class="row">
        <!-- Left Side - Product Grid and Filters -->
        <div class="col-md-8" style="padding-right: 20px;">
            <!-- Search and Filters (Fixed/Sticky) -->
            <div class="pos-search-filter-bar" id="posSearchFilterBar">
                <div class="row g-3 align-items-center mb-0">
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

            <!-- Product Grid (separate from search/filter) -->
            <div class="container mb-3" id="posProductGridCard" style="box-shadow:none;border:none;margin-top:0;">
                <div class="card-body p-2" style="flex:1 1 auto; min-height:0;">
                    <div class="row g-3" id="productGrid">
                        <?php foreach ($products as $product):
                            if ($product['quantity_in_stock'] > 0): ?>
                                <div class="col-md-3 product-item"
                                    data-category="<?= htmlspecialchars($product['category_id']) ?>"
                                    data-name="<?= htmlspecialchars(strtolower($product['name'])) ?>">
                                    <div class="card h-100">
                                        <div class="card-img-top text-center bg-light " style="height: 150px; display: flex; align-items: center; justify-content: center;">

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
            </div>
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
                    <!-- Cash Payment Button -->
                    <button class="btn btn-success w-100 mb-2" onclick="processPayment('Cash')">
                        <i class="fas fa-cash-register"></i> Cash Payment
                    </button>

                    <!-- GCash Payment Button -->
                    <button class="btn bg-primary w-100 mb-2 text-white" onclick="processPayment('GCash')">
                        <i class="fas fa-mobile-alt"></i> GCash Payment
                    </button>
                    <button class="btn btn-outline-danger w-100 " onclick="clearCart()">
                        <i class="fas fa-trash"></i> Clear Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>



<!-- GCash QR Modal -->
<div class="modal fade" id="gcashQRModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h5 class="modal-title">Scan to Pay (GCash)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img src="/sari-sari-store/assets/images/gcash-qr.jpg" alt="GCash QR Code" class="img-fluid mb-3" style="max-height: 300px;">
                <p class="text-muted">Please scan the QR code to complete payment.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button id="completeGcashPayment" class="btn btn-success">Payment Completed</button>
            </div>
        </div>
    </div>
</div>


<script src="/sari-sari-store/assets/js/payment-methods.js"></script>

<style>
    /* Receipt (Right) - Fixed Sidebar Style */
    #pos .col-lg-4 {
        position: fixed;
        right: 0;
        top: 56px;
        /* Match header height for flush alignment */
        height: calc(100vh - 56px);
        max-width: 400px;
        width: 400px;
        padding: 0;
        background: #fff;
        z-index: 1050;
        box-shadow: -2px 0 12px rgba(0, 0, 0, 0.07);
        border-left: 1.5px solid #e5e5e5;
        display: flex;
        flex-direction: column;
    }

    @media (max-width: 991.98px) {
        #pos .col-lg-4 {
            position: static;
            width: 100%;
            max-width: 100%;
            height: auto;
            top: unset;
            box-shadow: none;
            border-left: none;
        }
    }

    #pos .col-lg-8 {
        /* Remove margin-right, use padding-right to avoid collision with fixed receipt */
        padding-right: 420px;
        /* 400px receipt + 20px gap */
        position: relative;
        padding-top: 0;
        min-width: 0;
        box-sizing: border-box;
    }

    #pos .card.sticky-top {
        position: static !important;
        height: 100%;
        width: 100%;
        box-shadow: none;
        margin: 0;
        border-radius: 0;
        border: none;
        display: flex;
        flex-direction: column;
    }

    /* Make search and filters fixed at the top of the product section */
    #pos .card.mb-3 {
        position: sticky;
        top: 70px;
        /* Same as header height */
        z-index: 1040;
        margin-bottom: 0;
        border-bottom: 1.5px solid #e5e5e5;
        border-radius: 0;
    }

    #pos .card.mb-3 .card-body:first-child {
        padding-bottom: 0.5rem;
    }

    /* Remove gap above the search/filter section */
    #pos .col-lg-8>.card.mb-3 {
        margin-top: 0;
    }

    /* Ensure product grid starts right after the sticky filter */
    #pos .card.mb-3 .card-body.p-2 {
        margin-top: 0.5rem;
    }

    .pos-search-filter-bar {
        position: sticky;
        top: 56px;
        /* Bootstrap default navbar height is 56px, adjust if your header is taller */
        z-index: 1100;
        background: #fff;
        padding: 16px 16px 8px 16px;
        border-bottom: 1.5px solid #e5e5e5;
        margin-top: 0 !important;
    }

    /* Remove any margin above the search/filter bar */
    #pos .col-lg-8>.pos-search-filter-bar {
        margin-top: 0 !important;
    }

    /* Remove margin from product grid card so it sits right below the filter bar */
    #posProductGridCard {
        margin-top: 0 !important;
    }

    @media (max-width: 991.98px) {
        .pos-search-filter-bar {
            top: 0;
        }
    }

    /* Header z-index to match sticky search/filter bar */
    .navbar.navbar-expand-lg.navbar-dark.sticky-top {
        z-index: 1100;
    }

    @media (max-width: 991.98px) {
        #pos .col-lg-4 {
            position: static;
            width: 100%;
            max-width: 100%;
            height: auto;
            top: unset;
            box-shadow: none;
            border-left: none;
        }

        #pos .col-lg-8 {
            margin-right: 0;
        }

        #pos .card.mb-3 {
            top: 0;
        }

        #posProductGridCard {
            margin-top: 0 !important;
        }
    }

    /* Remove gap between POS section and header, make it stick like the sidebar */
    #pos.content-section {
        margin-top: 0 !important;
        padding-top: 0 !important;
        border-top: none !important;
    }

    #pos>.row {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
</style>