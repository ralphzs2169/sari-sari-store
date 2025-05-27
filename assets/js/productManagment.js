// Global variables to store data
let products = [{
        id: 1,
        name: 'Coca-Cola 330ml',
        category: 'Beverages',
        unit: 'Piece',
        price: 25.00,
        stock: 45,
        lowStock: 10
    },
    {
        id: 2,
        name: 'Lucky Me Beef',
        category: 'Instant Noodles',
        unit: 'Piece',
        price: 15.50,
        stock: 120,
        lowStock: 15
    },
    {
        id: 3,
        name: 'Pan de Sal',
        category: 'Bread',
        unit: 'Piece',
        price: 3.50,
        stock: 200,
        lowStock: 20
    },
    {
        id: 4,
        name: 'Maggi Noodles',
        category: 'Instant Noodles',
        unit: 'Piece',
        price: 12.00,
        stock: 5,
        lowStock: 10
    },
    {
        id: 5,
        name: 'Kopiko Coffee',
        category: 'Beverages',
        unit: 'Pack',
        price: 8.00,
        stock: 75,
        lowStock: 10
    }
];

let categories = [{
        id: 1,
        name: 'Beverages',
        description: 'Soft drinks, coffee, juice',
        productCount: 15
    },
    {
        id: 2,
        name: 'Instant Noodles',
        description: 'Cup noodles, packet noodles',
        productCount: 25
    },
    {
        id: 3,
        name: 'Bread',
        description: 'Pan de sal, loaf bread',
        productCount: 8
    }
];

let units = [{
        id: 1,
        name: 'Piece',
        abbreviation: 'pcs',
        description: 'Individual items'
    },
    {
        id: 2,
        name: 'Pack',
        abbreviation: 'pack',
        description: 'Packaged items'
    },
    {
        id: 3,
        name: 'Kilogram',
        abbreviation: 'kg',
        description: 'Weight measurement'
    }
];

let nextProductId = 6;
let nextCategoryId = 4;
let nextUnitId = 4;

$(document).ready(function() {
        $('#categoryTable').DataTable({
            paging: true,
            searching: true,
            pageLength: 5
        });
    });

// Set current date
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    document.getElementById('currentDate').textContent = today.toLocaleDateString('en-PH', options);
});

// Navigation functionality
function navigateToSection(sectionId) {
    // Hide all content sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });

    // Show target section
    document.getElementById(sectionId).classList.add('active');

    // Update sidebar navigation
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });

    document.querySelector(`[data-section="${sectionId}"]`).classList.add('active');
}


document.querySelectorAll('.sidebar-nav a').forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        const section = this.getAttribute('data-section');

        // Hide all sections
        document.querySelectorAll('.content-section').forEach(sec => sec.style.display = 'none');

        // Show selected section
        const target = document.getElementById(section);
        if (target) {
            target.style.display = 'block';

            // If entering the products section
            if (section === 'products') {
                // Wait a moment to ensure the section is visible
                setTimeout(() => {
                    // Deactivate all tabs and panes
                    document.querySelectorAll('#productTabs .nav-link').forEach(tab => tab.classList.remove('active'));
                    document.querySelectorAll('#productTabContent .tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    // Activate the default products tab
                    const defaultTab = document.querySelector('#productTabs .nav-link[data-bs-target="#products-pane"]');
                    const defaultPane = document.querySelector('#products-pane');
                    if (defaultTab && defaultPane) {
                        defaultTab.classList.add('active');
                        defaultPane.classList.add('show', 'active');
                    }
                }, 10); // Let the browser repaint first
            }
        }
    });
});




// Handle sidebar navigation clicks
document.querySelectorAll('[data-section]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const sectionId = this.getAttribute('data-section');
        navigateToSection(sectionId);
    });
});

// Product management functions
function showAddProductModal() {
    navigateToSection('products');
    setTimeout(() => {
        const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
        modal.show();
    }, 300);
}

function addProduct() {
    const form = document.getElementById('addProductForm');
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const newProduct = {
        id: nextProductId++,
        name: document.getElementById('productName').value,
        category: document.getElementById('productCategory').value,
        unit: document.getElementById('productUnit').value,
        price: parseFloat(document.getElementById('productPrice').value),
        stock: parseInt(document.getElementById('productStock').value),
        lowStock: parseInt(document.getElementById('productLowStock').value)
    };

    products.push(newProduct);
    refreshProductsTable();

    // Reset form and close modal
    form.reset();
    form.classList.remove('was-validated');
    bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();

    showAlert('Product added successfully!', 'success');
}

function editProduct(id) {
    const product = products.find(p => p.id === id);
    if (!product) return;

    document.getElementById('editProductId').value = product.id;
    document.getElementById('editProductName').value = product.name;
    document.getElementById('editProductCategory').value = product.category;
    document.getElementById('editProductUnit').value = product.unit;
    document.getElementById('editProductPrice').value = product.price;
    document.getElementById('editProductStock').value = product.stock;
    document.getElementById('editProductLowStock').value = product.lowStock;

    const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
    modal.show();
}

function updateProduct() {
    const form = document.getElementById('editProductForm');
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const id = parseInt(document.getElementById('editProductId').value);
    const productIndex = products.findIndex(p => p.id === id);

    if (productIndex !== -1) {
        products[productIndex] = {
            id: id,
            name: document.getElementById('editProductName').value,
            category: document.getElementById('editProductCategory').value,
            unit: document.getElementById('editProductUnit').value,
            price: parseFloat(document.getElementById('editProductPrice').value),
            stock: parseInt(document.getElementById('editProductStock').value),
            lowStock: parseInt(document.getElementById('editProductLowStock').value)
        };

        refreshProductsTable();
        bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
        showAlert('Product updated successfully!', 'success');
    }
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        products = products.filter(p => p.id !== id);
        refreshProductsTable();
        showAlert('Product deleted successfully!', 'success');
    }
}

function refreshProductsTable() {
    const tbody = document.getElementById('productsTableBody');
    tbody.innerHTML = '';

    products.forEach(product => {
        const statusBadge = product.stock <= product.lowStock ?
            '<span class="badge bg-danger badge-stock">Low Stock</span>' :
            '<span class="badge bg-success badge-stock">In Stock</span>';

        const row = `
            <tr>
                <td>${String(product.id).padStart(3, '0')}</td>
                <td>${product.name}</td>
                <td>${product.category}</td>
                <td>${product.unit}</td>
                <td>₱${product.price.toFixed(2)}</td>
                <td>${product.stock}</td>
                <td>${statusBadge}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="editProduct(${product.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteProduct(${product.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

// Category management functions
function addCategory() {
    const form = document.getElementById('addCategoryForm');
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const newCategory = {
        id: nextCategoryId++,
        name: document.getElementById('categoryName').value,
        description: document.getElementById('categoryDescription').value,
        productCount: 0
    };

    categories.push(newCategory);
    refreshCategoriesTable();

    // Reset form and close modal
    form.reset();
    form.classList.remove('was-validated');
    bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();

    showAlert('Category added successfully!', 'success');
}

function editCategory(id) {
    showAlert('Edit category functionality will be implemented soon!', 'info');
}

function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category?')) {
        categories = categories.filter(c => c.id !== id);
        refreshCategoriesTable();
        showAlert('Category deleted successfully!', 'success');
    }
}

function refreshCategoriesTable() {
    const tbody = document.getElementById('categoriesTableBody');
    tbody.innerHTML = '';

    categories.forEach(category => {
        const row = `
            <tr>
                <td>${category.id}</td>
                <td>${category.name}</td>
                <td>${category.description}</td>
                <td>${category.productCount}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="editCategory(${category.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteCategory(${category.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

// Unit management functions
function addUnit() {
    const form = document.getElementById('addUnitForm');
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const newUnit = {
        id: nextUnitId++,
        name: document.getElementById('unitName').value,
        abbreviation: document.getElementById('unitAbbreviation').value,
        description: document.getElementById('unitDescription').value
    };

    units.push(newUnit);
    refreshUnitsTable();

    // Reset form and close modal
    form.reset();
    form.classList.remove('was-validated');
    bootstrap.Modal.getInstance(document.getElementById('addUnitModal')).hide();

    showAlert('Unit added successfully!', 'success');
}


function editUnit(id) {
    showAlert('Edit unit functionality will be implemented soon!', 'info');
}

function editCategory(category) {
    document.getElementById('editCategoryId').value = category.category_id;
    document.getElementById('editCategoryName').value = category.name;
    document.getElementById('editCategoryDescription').value = category.description;

    let modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    modal.show();
}

function confirmDelete(deleteUrl, entityName) {
    Swal.fire({
        title: `Delete ${entityName}?`,
        text: `Are you sure you want to delete this ${entityName.toLowerCase()}? This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, delete ${entityName}`,
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = deleteUrl;
        }
    });
}


function refreshUnitsTable() {
    const tbody = document.getElementById('unitsTableBody');
    tbody.innerHTML = '';

    units.forEach(unit => {
        const row = `
            <tr>
                <td>${unit.id}</td>
                <td>${unit.name}</td>
                <td>${unit.abbreviation}</td>
                <td>${unit.description}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="editUnit(${unit.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="deleteUnit(${unit.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

// Utility functions
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    // Auto-dismiss after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}

function handleSignOut() {
    if (confirm('Are you sure you want to sign out?')) {
        showAlert('Signing out...', 'info');
        // Implement actual sign out logic here
        // window.location.href = 'login.php';
    }
}

// Add hover effects to stats cards
document.querySelectorAll('.stats-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.querySelector('.stats-number').classList.add('text-primary');
    });

    card.addEventListener('mouseleave', function() {
        this.querySelector('.stats-number').classList.remove('text-primary');
    });
});

// Initialize tooltips if any
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});