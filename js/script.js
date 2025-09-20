// E-commerce website JavaScript functionality

// Document ready function
document.addEventListener('DOMContentLoaded', function() {
    // Initialize page
    init();
});

function init() {
    // Add event listeners
    setupCartButtons();
    setupQuantityUpdates();
    setupFormValidation();
}

// Cart functionality
function setupCartButtons() {
    // Add to cart buttons
    const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');
    addToCartBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const quantity = this.dataset.quantity || 1;
            
            addToCart(productId, quantity);
        });
    });
    
    // Remove from cart buttons
    const removeFromCartBtns = document.querySelectorAll('.remove-from-cart-btn');
    removeFromCartBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                removeFromCart(productId);
            }
        });
    });
}

function setupQuantityUpdates() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantity = parseInt(this.value);
            
            if (quantity > 0) {
                updateCartQuantity(productId, quantity);
            } else {
                this.value = 1;
            }
        });
    });
}

// Cart operations
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
            showMessage('Item added to cart!', 'success');
            updateCartCount(data.cart_count);
        } else {
            showMessage(data.message || 'Error adding item to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error adding item to cart', 'error');
    });
}

function removeFromCart(productId) {
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
            showMessage('Item removed from cart!', 'success');
            updateCartCount(data.cart_count);
            // Reload page to update cart display
            window.location.reload();
        } else {
            showMessage(data.message || 'Error removing item from cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error removing item from cart', 'error');
    });
}

function updateCartQuantity(productId, quantity) {
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
            updateCartCount(data.cart_count);
            // Update total if element exists
            const totalElement = document.querySelector('.cart-total-amount');
            if (totalElement && data.cart_total) {
                totalElement.textContent = '$' + data.cart_total.toFixed(2);
            }
        } else {
            showMessage(data.message || 'Error updating cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error updating cart', 'error');
    });
}

// Update cart count in navigation
function updateCartCount(count) {
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(element => {
        element.textContent = count;
    });
    
    // Update navigation cart link text
    const cartLink = document.querySelector('a[href="cart.php"]');
    if (cartLink) {
        cartLink.textContent = `Cart (${count})`;
    }
}

// Form validation
function setupFormValidation() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
}

function validateForm(form) {
    const requiredFields = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            showFieldError(field, 'This field is required');
            isValid = false;
        } else {
            clearFieldError(field);
        }
    });
    
    // Email validation
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        if (field.value && !isValidEmail(field.value)) {
            showFieldError(field, 'Please enter a valid email address');
            isValid = false;
        }
    });
    
    // Password confirmation
    const passwordField = form.querySelector('input[name="password"]');
    const confirmPasswordField = form.querySelector('input[name="confirm_password"]');
    
    if (passwordField && confirmPasswordField) {
        if (passwordField.value !== confirmPasswordField.value) {
            showFieldError(confirmPasswordField, 'Passwords do not match');
            isValid = false;
        }
    }
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.color = '#e74c3c';
    errorDiv.style.fontSize = '12px';
    errorDiv.style.marginTop = '5px';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
    field.style.borderColor = '#e74c3c';
}

function clearFieldError(field) {
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
    field.style.borderColor = '';
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Utility functions
function showMessage(message, type = 'info', duration = 3000) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.js-message');
    existingMessages.forEach(msg => msg.remove());
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `message message-${type} js-message`;
    messageDiv.textContent = message;
    messageDiv.style.position = 'fixed';
    messageDiv.style.top = '20px';
    messageDiv.style.right = '20px';
    messageDiv.style.zIndex = '1000';
    messageDiv.style.padding = '15px';
    messageDiv.style.borderRadius = '4px';
    messageDiv.style.maxWidth = '300px';
    
    if (type === 'success') {
        messageDiv.style.backgroundColor = '#d4edda';
        messageDiv.style.color = '#155724';
        messageDiv.style.border = '1px solid #c3e6cb';
    } else if (type === 'error') {
        messageDiv.style.backgroundColor = '#f8d7da';
        messageDiv.style.color = '#721c24';
        messageDiv.style.border = '1px solid #f5c6cb';
    }
    
    document.body.appendChild(messageDiv);
    
    // Auto-hide message after duration
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, duration);
}