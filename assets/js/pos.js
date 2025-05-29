// Initialize cart array
let cart = [];
let total = 0;

// Product filtering functions
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('productSearch');
    searchInput.addEventListener('input', filterProducts);

    // Category filter
    const categoryFilter = document.getElementById('categoryFilter');
    categoryFilter.addEventListener('change', filterProducts);

    // Payment amount input
    const paymentInput = document.getElementById('paymentAmount');
    paymentInput.addEventListener('input', calculateChange);
});

function filterProducts() {
    const searchTerm = document.getElementById('productSearch').value.toLowerCase();
    const selectedCategory = document.getElementById('categoryFilter').value;
    const products = document.querySelectorAll('.product-item');

    products.forEach(product => {
        const productName = product.getAttribute('data-name');
        const productCategory = product.getAttribute('data-category');
        const matchesSearch = productName.includes(searchTerm);
        const matchesCategory = selectedCategory === '' || productCategory === selectedCategory;

        if (matchesSearch && matchesCategory) {
            product.style.display = '';
        } else {
            product.style.display = 'none';
        }
    });
}

function clearFilters() {
    document.getElementById('productSearch').value = '';
    document.getElementById('categoryFilter').value = '';
    filterProducts();
}

// Cart management functions
function addToCart(product) {
    const existingItem = cart.find(item => item.id === product.id);

    if (existingItem) {
        if (existingItem.quantity >= product.stock) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock Limit Reached',
                text: 'Not enough stock available!'
            });
            return;
        }
        existingItem.quantity++;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: parseFloat(product.price), // Ensure price is a number
            unit: product.unit,
            quantity: 1,
            stock: product.stock
        });
    }
    updateCart();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

function updateQuantity(index, change) {
    const item = cart[index];
    const newQuantity = item.quantity + change;
    if (newQuantity > 0 && newQuantity <= item.stock) {
        item.quantity = newQuantity;
        updateCart();
    } else if (newQuantity > item.stock) {
        Swal.fire({
            icon: 'warning',
            title: 'Stock Limit Reached',
            text: 'Not enough stock available!'
        });
    }
}

function updateCart() {
    const cartContainer = document.getElementById('cartItems');
    cartContainer.innerHTML = '';
    total = 0;
    let totalItems = 0;
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        totalItems += item.quantity;
        cartContainer.innerHTML += `
            <div class="cart-item p-2 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="mb-0">${item.name}</h6>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">${item.unit}</small>
                        <br>
                        <span>₱${item.price.toFixed(2)} × </span>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" onclick="updateQuantity(${index}, -1)">-</button>
                            <input type="number" min="1" max="${item.stock}" value="${item.quantity}" class="form-control form-control-sm d-inline-block pos-qty-input" style="width:50px;" data-index="${index}">
                            <button class="btn btn-outline-secondary" onclick="updateQuantity(${index}, 1)">+</button>
                        </div>
                    </div>
                    <strong>₱${itemTotal.toFixed(2)}</strong>
                </div>
            </div>
        `;
    });
    document.getElementById('subtotal').textContent = `₱${total.toFixed(2)}`;
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('total').textContent = `₱${total.toFixed(2)}`;
    calculateChange();
}

// Event delegation for quantity input
const cartItemsDiv = document.getElementById('cartItems');
cartItemsDiv.addEventListener('input', function(e) {
    if (e.target.classList.contains('pos-qty-input')) {
        const index = parseInt(e.target.dataset.index);
        let val = parseInt(e.target.value);
        if (isNaN(val) || val < 1) val = 1;
        if (val > cart[index].stock) val = cart[index].stock;
        cart[index].quantity = val;
        updateCart();
    }
});

function calculateChange() {
    const paymentAmount = parseFloat(document.getElementById('paymentAmount').value) || 0;
    const change = paymentAmount - total;
    document.getElementById('changeAmount').value = change >= 0 ? `${change.toFixed(2)}` : '';
}

function clearCart() {
    Swal.fire({
        title: 'Clear Cart?',
        text: 'Are you sure you want to clear the cart?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, clear it!'
    }).then((result) => {
        if (result.isConfirmed) {
            cart = [];
            updateCart();
            document.getElementById('paymentAmount').value = '';
            document.getElementById('changeAmount').value = '';
        }
    });
}

function processPayment() {
    const paymentAmount = parseFloat(document.getElementById('paymentAmount').value) || 0;
    if (cart.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Empty Cart',
            text: 'Please add items to the cart before proceeding.'
        });
        return;
    }
    if (paymentAmount < total) {
        Swal.fire({
            icon: 'error',
            title: 'Insufficient Payment',
            text: 'The payment amount is less than the total.'
        });
        return;
    }

    // Create a hidden form and submit to backend 
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/sari-sari-store/controllers/salesTransactionController.php?action=create';

    // Add total_amount
    const totalInput = document.createElement('input');
    totalInput.type = 'hidden';
    totalInput.name = 'total_amount';
    totalInput.value = total;
    form.appendChild(totalInput);

    // Add payment_method (default to cash)
    const paymentMethodInput = document.createElement('input');
    paymentMethodInput.type = 'hidden';
    paymentMethodInput.name = 'payment_method';
    paymentMethodInput.value = 'cash';
    form.appendChild(paymentMethodInput);

    // Add payment_amount (for reference)
    const paymentAmountInput = document.createElement('input');
    paymentAmountInput.type = 'hidden';
    paymentAmountInput.name = 'payment_amount';
    paymentAmountInput.value = paymentAmount;
    form.appendChild(paymentAmountInput);

    // Add cart items as JSON
    const cartInput = document.createElement('input');
    cartInput.type = 'hidden';
    cartInput.name = 'cart';
    cartInput.value = JSON.stringify(cart);
    form.appendChild(cartInput);

    document.body.appendChild(form);
    form.submit();
}
