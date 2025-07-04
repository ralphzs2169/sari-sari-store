<div id="products" class="content-section">
    <!-- Tabs for Products, Categories, and Units -->
    <ul class="nav nav-tabs mb-3" id="productTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#products-pane">Products</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories-pane" type="button" role="tab">Categories</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="units-tab" data-bs-toggle="tab" data-bs-target="#units-pane" type="button" role="tab">Units</button>
        </li>
    </ul>

    <div class="tab-content" id="productTabContent">
        <!-- Products Tab -->
        <div class="tab-pane fade show active" id="products-pane" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fas fa-box text-primary"></i> Product Inventory</h4>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus"></i> Add New Product
                </button>
            </div>

            <div class="datatable-container">
                <table id="productTable" class="table table-hover datatable-custom">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Product Name</th>
                            <th width="15%">Category</th>
                            <th width="10%">Unit</th>
                            <th width="15%">Price per Unit</th>
                            <th width="10%">Stock</th>
                            <th width="10%">Status</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $counter = 1;
                        foreach ($products as $product):
                            // Now category_name and unit_name come directly from SQL JOIN, no need for lookup arrays
                            $categoryName = $product['category_name'] ?? 'Unknown';
                            $unitName = $product['unit_name'] ?? 'Unknown';
                        ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= htmlspecialchars($categoryName) ?></td>
                                <td><?= htmlspecialchars($unitName) ?></td>
                                <td>₱<?= number_format($product['selling_price'], 2) ?></td>
                                <td><?= htmlspecialchars($product['quantity_in_stock']) ?></td>
                                <td>
                                    <?php if ($product['quantity_in_stock'] > 5): ?>
                                        <span class="badge bg-success">In Stock</span>
                                    <?php elseif ($product['quantity_in_stock'] > 0): ?>
                                        <span class="badge bg-warning text-dark">Low Stock</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Out of Stock</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <button
                                        class="btn btn-sm btn-outline-primary btn-action"
                                        onclick='editProduct(<?= json_encode($product, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm btn-outline-danger btn-action"
                                        onclick="confirmDelete('/sari-sari-store/controllers/ProductController.php?action=delete&id=<?= $product['product_id'] ?>&name=<?= $product['name'] ?>', 'Product')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>


        <!-- Categories Tab -->
        <div class="tab-pane fade" id="categories-pane" role="tabpanel" aria-labelledby="categories-tab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fas fa-tags text-primary"></i> Product Categories</h4>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-plus"></i> Add New Category
                </button>
            </div>

            <div class="datatable-container">
                <table id="categoryTable" class="table table-hover datatable-custom">
                    <thead>
                        <tr>
                            <th width="10%">ID</th>
                            <th width="25%">Category Name</th>
                            <th width="35%">Description</th>
                            <th width="15%">Count</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTableBody">
                        <?php
                        $counter = 1; // Initialize counter
                        foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $counter++ ?></td> <!-- Display sequential number -->
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td><?= htmlspecialchars($category['description']) ?></td>
                                <td><?= htmlspecialchars($category['product_count'] ?? 0) ?></td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-outline-primary btn-action"
                                        onclick='editCategory(<?= json_encode($category, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-action"
                                        onclick="confirmDelete('/sari-sari-store/controllers/CategoryController.php?action=delete&id=<?= $category['category_id'] ?>', 'Category')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Units Tab -->
        <div class="tab-pane fade" id="units-pane" role="tabpanel" aria-labelledby="units-tab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fas fa-ruler text-primary"></i> Units of Measurement</h4>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                    <i class="fas fa-plus"></i> Add New Unit
                </button>
            </div>

            <div class="table-responsive">
                <table id="unitTable" class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Unit Name</th>
                            <th>Abbreviation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="unitsTableBody">
                        <?php
                        $unitCounter = 1; // Sequential ID (not actual DB ID)
                        foreach ($units as $unit): ?>
                            <tr>
                                <td><?= $unitCounter++ ?></td>
                                <td><?= htmlspecialchars($unit['name']) ?></td>
                                <td><?= htmlspecialchars($unit['abbreviation']) ?></td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-outline-primary btn-action"
                                        onclick='editUnit(<?= json_encode($unit, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm btn-outline-danger btn-action"
                                        onclick="confirmDelete('/sari-sari-store/controllers/UnitController.php?action=delete&id=<?= $unit['unit_id'] ?>', 'Unit')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>





    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-tags"></i> Add New Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="/sari-sari-store/controllers/CategoryController.php?action=create" method="post" class="container mt-3">

                        <input type="hidden" name="current_section" id="current_section" value="">


                        <?php if (isset($_SESSION['category_error'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['category_error'];
                                                            unset($_SESSION['category_error']); ?></div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="categoryName" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="categoryDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="categoryDescription" name="description" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Save</button>
                        <a href="categories.php" class="btn btn-secondary">Back</a>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <!-- Add Unit Modal -->
    <div class="modal fade" id="addUnitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-ruler"></i> Add New Unit</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="/sari-sari-store/controllers/UnitController.php?action=create" method="post" class="container mt-3">
                        <input type="hidden" name="current_section" id="current_section" value="">

                        <?php if (isset($_SESSION['unit_error'])): ?>
                            <div class="alert alert-danger">
                                <?= $_SESSION['unit_error'];
                                unset($_SESSION['unit_error']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="unitName" class="form-label">Unit Name</label>
                            <input type="text" class="form-control" id="unitName" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="unitAbbreviation" class="form-label">Abbreviation</label>
                            <input type="text" class="form-control" id="unitAbbreviation" name="abbreviation" required>
                        </div>

                        <button type="submit" class="btn btn-success">Save</button>
                        <a href="/sari-sari-store/views/adminPanel/index.php?section=units" class="btn btn-secondary">Back</a>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" action="/sari-sari-store/controllers/productController.php?action=update" method="post" class="container mt-3">

                        <input type="hidden" name="editProductId" id="editProductId">
                        <input type="hidden" name="current_section" value="categories">

                        <div class="row mb-3">
                            <div class="col-12">
                                <label>Product Name</label>
                                <input type="text" name="name" id="editProductName" class="form-control" required>
                            </div>
                        </div>

                        <!-- Image Display and Replace Section -->
                        <div class="row mb-3 align-items-center">
                            <!-- Image preview, hidden by default -->
                            <div class="col-md-6" id="imagePreviewContainer" style="display:none;">
                                <label>Current Product Image</label><br>
                                <img id="editProductImagePreview" src="" alt="Product Image"
                                    style="width: 200px; height: 150px; object-fit: contain; border: 1px solid #ccc; padding: 5px;">
                            </div>

                            <!-- File input -->
                            <div class="col-md-6" id="fileInputContainer">
                                <label for="replaceProductImage" id="fileInputLabel">Add Product Image</label>
                                <input class="form-control" type="file" id="replaceProductImage" name="product_image" accept="image/*" required>
                                <input type="hidden" name="current_image_path" id="currentImagePath" value="">
                            </div>
                        </div>



                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Category</label>
                                <select name="category_id" id="editProductCategory" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= htmlspecialchars($category['category_id']) ?>">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Unit</label>
                                <select name="unit_id" id="editProductUnit" class="form-select" required>
                                    <option value="">Select Unit</option>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?= htmlspecialchars($unit['unit_id']) ?>">
                                            <?= htmlspecialchars($unit['name']) ?> (<?= htmlspecialchars($unit['abbreviation']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Cost Price (₱)</label>
                                <input type="number" name="cost_price" id="editProductCostPrice" class="form-control" step="0.01" min="0" required>
                            </div>

                            <div class="col-md-6">
                                <label>Selling Price (₱)</label>
                                <input type="number" name="selling_price" id="editProductSellingPrice" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label>Quantity In Stock</label>
                                <input type="number" name="quantity_in_stock" id="editProductStock" class="form-control" min="0" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Update Product</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-tags"></i> Edit Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm" action="/sari-sari-store/controllers/CategoryController.php?action=update" method="post">
                        <input type="hidden" name="editCategoryId" id="editCategoryId">
                        <input type="hidden" name="current_section" value="categories">

                        <div class="mb-3">
                            <label for="editCategoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="editCategoryName" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="editCategoryDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editCategoryDescription" name="description" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Unit Modal -->
    <div class="modal fade" id="editUnitModal" tabindex="-1" aria-labelledby="editUnitLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-ruler"></i> Edit Unit</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editUnitForm" action="/sari-sari-store/controllers/UnitController.php?action=update" method="post">
                        <input type="hidden" name="editUnitId" id="editUnitId">
                        <input type="hidden" name="current_section" value="units">

                        <div class="mb-3">
                            <label for="editUnitName" class="form-label">Unit Name</label>
                            <input type="text" class="form-control" id="editUnitName" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="editUnitAbbreviation" class="form-label">Abbreviation</label>
                            <input type="text" class="form-control" id="editUnitAbbreviation" name="abbreviation" required>
                        </div>

                        <button type="submit" class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>