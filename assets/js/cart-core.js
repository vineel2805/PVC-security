/**
 * =============================================
 * SHOPPING CART CORE
 * =============================================
 * Reusable cart state and shared operations.
 * UI-specific behavior lives in floating-cart.js and cart-page.js.
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

    // Dispatch a custom event so header badges, floating cart, and cart page
    // listeners can react without the core module owning UI concerns.
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

// ==================== EXPORT FOR GLOBAL USE ====================

window.getCart = getCart;
window.saveCart = saveCart;
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateCartItemQuantity = updateCartItemQuantity;
window.calculateCartTotals = calculateCartTotals;
window.formatCurrency = formatCurrency;
window.generateWhatsAppMessage = generateWhatsAppMessage;