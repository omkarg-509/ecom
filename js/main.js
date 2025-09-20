// Simple JavaScript for the e-commerce website

// Add to cart functionality
function addToCart(productId, quantity = 1) {
    fetch('cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart counter in navigation
            updateCartCounter();
            showMessage('Product added to cart!', 'success');
        } else {
            showMessage('Error adding product to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error adding product to cart', 'error');
    });
}

// Update cart item quantity
function updateQuantity(productId, quantity) {
    if (quantity <= 0) {
        removeFromCart(productId);
        return;
    }
    
    fetch('cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update&product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Refresh to update totals
        } else {
            showMessage('Error updating quantity', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error updating quantity', 'error');
    });
}

// Remove item from cart
function removeFromCart(productId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        fetch('cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove&product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Refresh to update cart
            } else {
                showMessage('Error removing item from cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error removing item from cart', 'error');
        });
    }
}

// Update cart counter in navigation
function updateCartCounter() {
    const cartLink = document.querySelector('a[href="cart.php"]');
    if (cartLink) {
        fetch('cart.php?action=count')
        .then(response => response.json())
        .then(data => {
            cartLink.textContent = `Cart (${data.count})`;
        });
    }
}

// Show success/error messages
function showMessage(message, type) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    // Insert at the top of main content
    const main = document.querySelector('main');
    main.insertBefore(alert, main.firstChild);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        alert.remove();
    }, 3000);
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#e74c3c';
            isValid = false;
        } else {
            input.style.borderColor = '#ddd';
        }
    });
    
    return isValid;
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for quantity controls
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantity = parseInt(this.value);
            updateQuantity(productId, quantity);
        });
    });
    
    // Add event listeners for remove buttons
    const removeButtons = document.querySelectorAll('.remove-btn');
    removeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            removeFromCart(productId);
        });
    });
    
    // Form validation on submit
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (this.dataset.validate === 'true') {
                if (!validateForm(this.id)) {
                    e.preventDefault();
                    showMessage('Please fill in all required fields', 'error');
                }
            }
        });
    });
});