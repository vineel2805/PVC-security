/**
 * =============================================
 * SHOPPING CART MANAGEMENT SYSTEM
 * =============================================
 * This script handles:
 * - Adding products to cart
 * - Storing cart in localStorage
 * - Displaying cart items
 * - Calculating totals
 * - Generating WhatsApp RFQ messages
 * =============================================
 */

// ==================== CART UTILITY FUNCTIONS ====================

/**
 * Get cart from localStorage
 * @returns {Array} Array of cart items
 */
function getCart() {
    const cart = localStorage.getItem('pvcCart');
    return cart ? JSON.parse(cart) : [];
}

/**
 * Save cart to localStorage
 * @param {Array} cart - Cart items array
 */
function saveCart(cart) {
    localStorage.setItem('pvcCart', JSON.stringify(cart));
    updateFloatingCartButton();

    // Immediately update header badge if present (same-tab update)
    try {
        const count = cart.reduce((total, item) => total + (item.quantity || 0), 0);
        const el = document.getElementById('pvc-cart-count');
        if (el) {
            el.textContent = count;
            el.style.display = count > 0 ? 'flex' : 'none';
        }
    } catch (e) {
        // ignore
    }

    // Dispatch a custom event so other scripts/tabs can respond
    try {
        window.dispatchEvent(new Event('pvc-cart-updated'));
    } catch (e) {
        // ignore
    }
}

/**
 * Add product to cart
 * @param {Object} product - Product object with name, model, price, image
 */
function addToCart(product) {
    const cart = getCart();

    // Check if product already exists in cart
    const existingItemIndex = cart.findIndex(item => item.model === product.model);

    if (existingItemIndex !== -1) {
        // Product exists, increment quantity
        cart[existingItemIndex].quantity += 1;
    } else {
        // New product, add to cart
        cart.push({
            name: product.name,
            model: product.model,
            price: product.price || 0,
            image: product.image || 'assets/img/products/network-products-update.png',
            quantity: 1
        });
    }

    saveCart(cart);
    showAddToCartNotification(product.name);
}

/**
 * Remove item from cart
 * @param {string} model - Product model number
 */
function removeFromCart(model) {
    let cart = getCart();
    cart = cart.filter(item => item.model !== model);
    saveCart(cart);

    // Reload cart display if on cart page
    if (window.location.pathname.includes('cart.php')) {
        loadCartPage();
    }
}

/**
 * Update item quantity in cart
 * @param {string} model - Product model number
 * @param {number} quantity - New quantity
 */
function updateCartItemQuantity(model, quantity) {
    const cart = getCart();
    const itemIndex = cart.findIndex(item => item.model === model);

    if (itemIndex !== -1) {
        if (quantity <= 0) {
            removeFromCart(model);
        } else {
            cart[itemIndex].quantity = quantity;
            saveCart(cart);

            // Update display if on cart page
            if (window.location.pathname.includes('cart.php')) {
                loadCartPage();
            }
        }
    }
}

/**
 * Calculate cart totals
 * @returns {Object} Object with subtotal, itemCount, and total
 */
function calculateCartTotals() {
    const cart = getCart();
    let subtotal = 0;
    let itemCount = 0;

    cart.forEach(item => {
        subtotal += (item.price * item.quantity);
        itemCount += item.quantity;
    });

    return {
        subtotal: subtotal,
        itemCount: itemCount,
        total: subtotal // For now, total equals subtotal (no shipping cost)
    };
}

/**
 * Format currency
 * @param {number} amount - Amount to format
 * @returns {string} Formatted currency string
 */
function formatCurrency(amount) {
    return '₹' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// ==================== FLOATING CART BUTTON ====================

/**
 * Update or create floating cart button
 */
function updateFloatingCartButton() {
    const cart = getCart();
    const itemCount = cart.reduce((total, item) => total + item.quantity, 0);

    // Remove existing button
    const existingBtn = document.getElementById('floatingCartBtn');
    if (existingBtn) {
        existingBtn.remove();
    }

    // Only show button if cart has items and not on cart page
    if (itemCount > 0 && !window.location.pathname.includes('cart.php')) {
        const floatingBtn = document.createElement('a');
        floatingBtn.id = 'floatingCartBtn';
        floatingBtn.href = 'cart.php';
        floatingBtn.className = 'floating-cart-btn';
        // Always show without parentheses (desktop and mobile)
        floatingBtn.innerHTML = `
            <i class="fa-solid fa-shopping-cart"></i>
            <span>View Cart <span class="cart-count-badge">${itemCount}</span></span>
        `;
        document.body.appendChild(floatingBtn);
    }
}

// Refresh floating button layout on viewport resize (if present)
window.addEventListener('resize', function () {
    if (document.getElementById('floatingCartBtn')) {
        updateFloatingCartButton();
    }
});

/**
 * Show notification when item is added to cart
 * @param {string} productName - Name of the product added
 */
function showAddToCartNotification(productName) {
    // Remove any existing notification
    const existingNotif = document.getElementById('cartNotification');
    if (existingNotif) {
        existingNotif.remove();
    }

    // Create notification
    const notification = document.createElement('div');
    notification.id = 'cartNotification';
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 30px;
        background: #25D366;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideInRight 0.4s ease;
        max-width: 300px;
    `;
    notification.innerHTML = `
        <strong><i class="fa-solid fa-check-circle"></i> Added to Cart!</strong>
        <p style="margin: 5px 0 0 0; font-size: 14px;">${productName}</p>
    `;

    document.body.appendChild(notification);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.4s ease';
        setTimeout(() => notification.remove(), 400);
    }, 3000);
}

// ==================== CART PAGE FUNCTIONS ====================
function loadCartPage() {
    const cart = getCart();
    const cartItemsContainer = document.getElementById('cartItemsContainer');
    const emptyCartMessage = document.getElementById('emptyCartMessage');
    const cartSummarySection = document.querySelector('.cart-summary-section');

    if (cart.length === 0) {
        // Show empty cart message
        if (cartSummarySection) cartSummarySection.style.display = 'none';
        if (emptyCartMessage) emptyCartMessage.style.display = 'block';
        return;
    }

    // Hide empty message, show cart
    if (cartSummarySection) cartSummarySection.style.display = 'block';
    if (emptyCartMessage) emptyCartMessage.style.display = 'none';

    // Display cart items
    cartItemsContainer.innerHTML = cart.map(item => `
        <div class="cart-item" data-model="${item.model}">
            <div class="cart-item-img">
                <img src="${item.image}" alt="${item.name}">
            </div>
            <div class="cart-item-details">
                <h4 class="cart-item-name">${item.name}</h4>
                <p class="cart-item-model">Model: ${item.model}</p>
                <div class="cart-item-controls">
                    <div class="quantity-control">
                        <button onclick="updateCartItemQuantity('${item.model}', ${item.quantity - 1})">
                            <i class="fa-solid fa-minus"></i>
                        </button>
                        <span>${item.quantity}</span>
                        <button onclick="updateCartItemQuantity('${item.model}', ${item.quantity + 1})">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>
                <p class="cart-item-price">${item.price > 0 ? formatCurrency(item.price * item.quantity) : 'Price on Request'}</p>
            </div>
            <button class="cart-item-remove" onclick="removeFromCart('${item.model}')" title="Remove item">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    `).join('');

    // Update totals
    updateCartTotals();
}

/**
 * Update cart totals display
 */
function updateCartTotals() {
    const totals = calculateCartTotals();

    const subtotalEl = document.getElementById('cartSubtotal');
    const itemCountEl = document.getElementById('cartItemCount');
    const totalEl = document.getElementById('cartTotal');
    const headerItemCountEl = document.getElementById('headerItemCount');

    if (subtotalEl) subtotalEl.textContent = formatCurrency(totals.subtotal);
    if (itemCountEl) itemCountEl.textContent = totals.itemCount;
    if (totalEl) totalEl.textContent = formatCurrency(totals.total);
    if (headerItemCountEl) headerItemCountEl.textContent = '(' + totals.itemCount + ')';
}

// ==================== SHIPPING METHOD SELECTION ====================

/**
 * Initialize shipping method selection
 */
function initializeShippingSelection() {
    const shippingCards = document.querySelectorAll('.shipping-card');

    shippingCards.forEach(card => {
        card.addEventListener('click', function () {
            // Remove selected class from all cards
            shippingCards.forEach(c => c.classList.remove('selected'));

            // Add selected class to clicked card
            this.classList.add('selected');
        });
    });
}

/**
 * Get selected shipping method
 * @returns {string} Selected shipping method
 */
function getSelectedShippingMethod() {
    const selectedCard = document.querySelector('.shipping-card.selected');
    return selectedCard ? selectedCard.dataset.method : 'Not Selected';
}

// ==================== WHATSAPP RFQ GENERATION ====================

/**
 * Generate and send WhatsApp RFQ
 */
// ==================== RFQ SUBMISSION WORKFLOW ====================

/**
 * Handle the click event on the Place Order button
 */
function handlePlaceOrderClick(event) {
    if (event) event.preventDefault();

    clearValidationErrors();

    const validation = validateContactForm();

    if (!validation.isValid) {
        showValidationErrors(validation.invalidFields);
        scrollToContactSection();
        if (validation.firstInvalidField) {
            validation.firstInvalidField.focus();
        }
        return;
    }

    const cart = getCart();

    if (cart.length === 0) {
        alert('Your cart is empty');
        return;
    }

    submitRFQ(cart);
}

/**
 * Validate the required contact fields
 * @returns {Object} Validation result
 */
function validateContactForm() {
    const fields = [
        { id: 'mobileNumber', name: 'Mobile Number' },
        { id: 'customerName', name: 'Name' },
        { id: 'cityName', name: 'City / Village' }
    ];

    let isValid = true;
    let firstInvalidField = null;
    const invalidFields = [];

    fields.forEach(field => {
        const element = document.getElementById(field.id);
        if (element && element.value.trim() === '') {
            isValid = false;
            invalidFields.push(element);
            if (!firstInvalidField) {
                firstInvalidField = element;
            }
        }
    });

    return { isValid, firstInvalidField, invalidFields };
}

/**
 * Clear existing validation errors and messages
 */
function clearValidationErrors() {
    const inputs = ['mobileNumber', 'customerName', 'cityName'];
    inputs.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.classList.remove('is-invalid');
        }
    });

    const existingMsg = document.getElementById('contactFormErrorMsg');
    if (existingMsg) {
        existingMsg.remove();
    }
}

/**
 * Show validation errors for missing fields
 * @param {Array} invalidFields - Array of invalid input elements
 */
function showValidationErrors(invalidFields) {
    invalidFields.forEach(element => {
        element.classList.add('is-invalid');
    });

    const contactFormSection = document.getElementById('contactFormMode');
    if (contactFormSection && !document.getElementById('contactFormErrorMsg')) {
        const errorMsg = document.createElement('div');
        errorMsg.id = 'contactFormErrorMsg';
        errorMsg.className = 'alert alert-danger mt-3 mb-0';
        errorMsg.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> Please complete your contact information before placing your order.';
        contactFormSection.appendChild(errorMsg);
    }
}

/**
 * Smoothly scroll to the Contact Information section
 */
function scrollToContactSection() {
    const contactSection = document.querySelector('.cart-form-section');
    if (contactSection) {
        contactSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/**
 * Submit the RFQ and open WhatsApp
 * @param {Array} cart - The cart items array
 */
function submitRFQ(cart) {
    const mobileNumber = document.getElementById('mobileNumber').value.trim();
    const customerName = document.getElementById('customerName').value.trim();
    const cityName = document.getElementById('cityName').value.trim();

    const encodedMessage = generateWhatsAppMessage(cart, customerName, cityName, mobileNumber);

    const WHATSAPP_NUMBER_1 = "9114456666";
    const WHATSAPP_NUMBER_2 = "918112456789";

    const whatsappURL1 = `https://wa.me/${WHATSAPP_NUMBER_1}?text=${encodedMessage}`;
    const whatsappURL2 = `https://wa.me/${WHATSAPP_NUMBER_2}?text=${encodedMessage}`;

    // Open first WhatsApp
    window.open(whatsappURL1, '_blank');

    // Open second WhatsApp after delay
    setTimeout(() => {
        window.open(whatsappURL2, '_blank');
    }, 1000);
}

/**
 * Generate the WhatsApp message exactly as the current implementation does
 * @param {Array} cart - Cart items
 * @param {string} customerName - Customer name
 * @param {string} cityName - City name
 * @param {string} mobileNumber - Mobile number
 * @returns {string} Encoded WhatsApp message
 */
function generateWhatsAppMessage(cart, customerName, cityName, mobileNumber) {
    let lastRFQ = localStorage.getItem('lastRFQNumber') || 0;
    lastRFQ = parseInt(lastRFQ) + 1;
    localStorage.setItem('lastRFQNumber', lastRFQ);
    const rfqNumber = String(lastRFQ).padStart(3, '0');

    const currentDate = new Date();
    const formattedDate =
        `${String(currentDate.getDate()).padStart(2, '0')}/` +
        `${String(currentDate.getMonth() + 1).padStart(2, '0')}/` +
        `${currentDate.getFullYear()}`;

    let productList = "";
    cart.forEach((item, index) => {
        productList += `${index + 1}. ${item.name} × ${item.quantity}\n`;
    });

    const message =
        `
──────────────────────
* RFQ Number: ${rfqNumber}
* Date: ${formattedDate}
* Name: ${customerName}
* City/Village: ${cityName}
* Registered Mobile Number: ${mobileNumber}
──────────────────────


${productList}

──────────────────────
Total Products : ${cart.length}`;

    return encodeURIComponent(message);
}

// ==================== INITIALIZATION ====================

/**
 * Initialize cart functionality on page load
 */
document.addEventListener('DOMContentLoaded', function () {
    // Removed: Clear cart on new session start. This was causing items to vanish 
    // when moving between product pages and the cart page.

    // Update floating cart button on all pages
    updateFloatingCartButton();

    // If on cart page, load cart
    if (window.location.pathname.includes('cart.php')) {
        loadCartPage();
        initializeShippingSelection();

        // Bind Place RFQ button
        const placeRFQBtn = document.getElementById('placeRFQBtn');
        if (placeRFQBtn) {
            placeRFQBtn.addEventListener('click', handlePlaceOrderClick);
        }
    }
});

// ==================== EXPORT FOR GLOBAL USE ====================

// Make functions available globally for onclick handlers
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateCartItemQuantity = updateCartItemQuantity;
window.handlePlaceOrderClick = handlePlaceOrderClick;
window.placeRFQOnWhatsApp = handlePlaceOrderClick;
