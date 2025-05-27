    <div id="products" class="content-section" style="display:none;">
        <!-- Tabs for Products, Categories, and Units -->
        <ul class="nav nav-tabs" id="productTabs" role="tablist">
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
            <div class="tab-pane fade show active" id="products-pane" role="tabpanel" aria-labelledby="products-tab">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><i class="fas fa-box text-primary"></i> Product Inventory</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus"></i> Add New Product
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <tr>
                                <td>001</td>
                                <td>Coca-Cola 330ml</td>
                                <td>Beverages</td>
                                <td>Piece</td>
                                <td>₱25.00</td>
                                <td>45</td>
                                <td><span class="badge bg-success badge-stock">In Stock</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="editProduct(1)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteProduct(1)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>002</td>
                                <td>Lucky Me Beef</td>
                                <td>Instant Noodles</td>
                                <td>Piece</td>
                                <td>₱15.50</td>
                                <td>120</td>
                                <td><span class="badge bg-success badge-stock">In Stock</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="editProduct(2)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteProduct(2)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>003</td>
                                <td>Pan de Sal</td>
                                <td>Bread</td>
                                <td>Piece</td>
                                <td>₱3.50</td>
                                <td>200</td>
                                <td><span class="badge bg-success badge-stock">In Stock</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="editProduct(3)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteProduct(3)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>004</td>
                                <td>Maggi Noodles</td>
                                <td>Instant Noodles</td>
                                <td>Piece</td>
                                <td>₱12.00</td>
                                <td>5</td>
                                <td><span class="badge bg-danger badge-stock">Low Stock</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="editProduct(4)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteProduct(4)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>005</td>
                                <td>Kopiko Coffee</td>
                                <td>Beverages</td>
                                <td>Pack</td>
                                <td>₱8.00</td>
                                <td>75</td>
                                <td><span class="badge bg-success badge-stock">In Stock</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="editProduct(5)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteProduct(5)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
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

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Description</th>
                                <th>Product Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?= htmlspecialchars($category['category_id']) ?></td>
                                    <td><?= htmlspecialchars($category['name']) ?></td>
                                    <td><?= htmlspecialchars($category['description']) ?></td>
                                    <td><?= htmlspecialchars($category['product_count'] ?? 0) ?></td> <!-- Assuming your query provides product_count -->
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary btn-action" onclick="editCategory(<?= htmlspecialchars($category['category_id']) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteCategory(<?= htmlspecialchars($category['category_id']) ?>)">
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
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Unit Name</th>
                                <th>Abbreviation</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="unitsTableBody">
                            <tr>
                                <td>1</td>
                                <td>Piece</td>
                                <td>pcs</td>
                                <td>Individual items</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="editUnit(1)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteUnit(1)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Pack</td>
                                <td>pack</td>
                                <td>Packaged items</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="editUnit(2)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteUnit(2)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Kilogram</td>
                                <td>kg</td>
                                <td>Weight measurement</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="editUnit(3)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteUnit(3)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Add New Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="../controllers/productController.php?action=create" method="post" class="container mt-3">

                        <div class="mb-3">
                            <label>Product Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                <!-- Dynamically generate options from DB -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Unit</label>
                            <select name="unit_id" class="form-select" required>
                                <option value="">Select Unit</option>
                                <!-- Dynamically generate options from DB -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Cost Price (₱)</label>
                            <input type="number" name="cost_price" class="form-control" step="0.01" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label>Selling Price (₱)</label>
                            <input type="number" name="selling_price" class="form-control" step="0.01" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label>Initial Stock</label>
                            <input type="number" name="quantity_in_stock" class="form-control" min="0" required>
                        </div>

                        <button type="submit" class="btn btn-success">Save Product</button>
                        <a href="index.php" class="btn btn-secondary">Back</a>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addProduct()">Add Product</button>
                </div>
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
                    <form action="/sari-sari-store/controllers/CategoryController.php?action=create" method="post" class="container mt-5">
                        <h2>Add Category</h2>

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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addCategory()">Add Category</button>
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
                    <form id="addUnitForm">
                        <div class="mb-3">
                            <label for="unitName" class="form-label">Unit Name</label>
                            <input type="text" class="form-control" id="unitName" required>
                        </div>
                        <div class="mb-3">
                            <label for="unitAbbreviation" class="form-label">Abbreviation</label>
                            <input type="text" class="form-control" id="unitAbbreviation" required>
                        </div>
                        <div class="mb-3">
                            <label for="unitDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="unitDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addUnit()">Add Unit</button>
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
                    <form id="editProductForm">
                        <input type="hidden" id="editProductId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductName" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="editProductName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductCategory" class="form-label">Category</label>
                                    <select class="form-select" id="editProductCategory" required>
                                        <option value="">Select Category</option>
                                        <option value="Beverages">Beverages</option>
                                        <option value="Instant Noodles">Instant Noodles</option>
                                        <option value="Bread">Bread</option>
                                        <option value="Snacks">Snacks</option>
                                        <option value="Condiments">Condiments</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductUnit" class="form-label">Unit</label>
                                    <select class="form-select" id="editProductUnit" required>
                                        <option value="">Select Unit</option>
                                        <option value="Piece">Piece</option>
                                        <option value="Pack">Pack</option>
                                        <option value="Kilogram">Kilogram</option>
                                        <option value="Liter">Liter</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductPrice" class="form-label">Price (₱)</label>
                                    <input type="number" class="form-control" id="editProductPrice" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductStock" class="form-label">Current Stock</label>
                                    <input type="number" class="form-control" id="editProductStock" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductLowStock" class="form-label">Low Stock Alert Level</label>
                                    <input type="number" class="form-control" id="editProductLowStock" min="1" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editProductDescription" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="editProductDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateProduct()">Update Product</button>
                </div>
            </div>
        </div>
    </div>