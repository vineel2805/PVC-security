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

/**
 * Load and display cart page
 */
function loadCartPage() {
    const cart = getCart();
    const cartPageContainer = document.getElementById('cartPageContainer');
    const stickyBar = document.getElementById('rfqBottomBar');

    if (!cartPageContainer) return;

    if (cart.length === 0) {
        // Show empty cart message
        cartPageContainer.innerHTML = `
          <div id="emptyCartMessage" class="empty-cart-message">
            <i class="fa-solid fa-cart-shopping"></i>
            <h3>Your cart is empty</h3>
            <p>Add products to your cart to request a quotation</p>
            <a href="all-products.php" class="btn-continue-shopping">Browse Products</a>
          </div>
        `;
        if (stickyBar) stickyBar.style.display = 'none';

        // Also update totals to 0
        updateCartTotals();
        return;
    }

    // Show sticky bottom bar if items present
    if (stickyBar) stickyBar.style.display = 'block';

    const savedName = localStorage.getItem('pvcCustomerName') || '';
    const savedMobile = localStorage.getItem('pvcMobileNumber') || '';
    const savedCity = localStorage.getItem('pvcCityName') || 'Not set';
    const isFilled = savedName.trim() !== '' && savedMobile.trim() !== '' && savedCity.trim() !== 'Not set' && savedCity.trim() !== '';

    // Build the dynamic HTML
    let html = '';

    // 1. Delivery Location banner card
    html += `
      <!-- Delivery Location Banner -->
      <div class="delivery-banner rfq-card">
        <div class="delivery-left">
          <span class="delivery-icon">📍</span>
          <span>Deliver to: <strong id="deliveryLocationText">${savedCity}</strong></span>
        </div>
        <div class="delivery-right">
          <a href="#" id="deliveryLocationChangeBtn" class="delivery-change-link">Change</a>
        </div>
      </div>
    `;

    // 2. Products Section (Product list container wrapper)
    html += `
      <div class="products-section">
        <h3 class="section-subtitle">Products in your RFQ</h3>
        <div class="cart-items-container">
          <div class="cart-items-list" id="cartItemsContainer">
    `;

    cart.forEach(item => {
        const pricing = getProductPricing(item);
        const formattedOriginal = '₹' + pricing.original.toLocaleString('en-IN');
        const formattedDiscounted = '₹' + pricing.discounted.toLocaleString('en-IN');

        html += `
            <div class="cart-item rfq-card" data-model="${item.model}">
                <div class="cart-item-left">
                    <div class="cart-item-img-container">
                        <img src="${item.image}" alt="${item.name}" onerror="this.onerror=null; this.src='assets/img/products/network-products-update.png';">
                    </div>
                </div>
                <div class="cart-item-right">
                    <h4 class="cart-item-title">${item.name}</h4>
                    <p class="cart-item-model-str">Model: ${item.model}</p>
                    
                    <div class="cart-item-pricing-row">
                        <span class="cart-item-original-price">${formattedOriginal}</span>
                        <span class="cart-item-discounted-price">${formattedDiscounted}</span>
                        <span class="cart-item-discount-badge">${pricing.discount}% OFF</span>
                    </div>
                    
                    <div class="cart-item-controls-row">
                        <div class="quantity-control">
                            <button onclick="updateCartItemQuantity('${item.model}', ${item.quantity - 1})" class="qty-btn">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <span class="qty-val">${item.quantity}</span>
                            <button onclick="updateCartItemQuantity('${item.model}', ${item.quantity + 1})" class="qty-btn">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                        <button class="cart-item-delete-btn" onclick="removeFromCart('${item.model}')" title="Remove item">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    html += `
          </div>
          <!-- Add More Products Button -->
          <a href="all-products.php" class="btn-add-more-products">
            <i class="fa-solid fa-plus"></i> Add more products
          </a>
        </div>
      </div>
    `;

    // 3. Quote Summary Card
    const totals = calculateCartTotals();
    html += `
      <!-- Quote Summary Card -->
      <div class="quote-summary-card rfq-card">
        <h3 class="card-section-title">Quote Summary</h3>
        <div class="summary-row">
          <span>Total Items</span>
          <span id="summaryTotalItems">${totals.itemCount}</span>
        </div>
        <div class="summary-row">
          <span>Estimated Shipping <span class="shipping-info-icon" title="Shipping costs will be calculated during quotation confirmation">ⓘ</span></span>
          <span class="calc-later">Calculated later</span>
        </div>
        <hr class="summary-divider">
        <div class="summary-row total-row">
          <span>Total (Approx.)</span>
          <span id="summaryTotalApprox">-</span>
        </div>
      </div>
    `;

    // 4. Contact Information Card
    html += `
      <!-- Contact Information Card -->
      <div class="contact-info-card rfq-card">
        <div class="card-header-wrapper">
          <h3 class="card-section-title">Contact Information</h3>
          <button id="editContactBtn" class="btn-edit-contact" style="display: ${isFilled ? 'inline-flex' : 'none'};">
            ✏️ Edit
          </button>
        </div>
        
        <!-- Form Mode -->
        <div id="contactFormMode" style="display: ${isFilled ? 'none' : 'block'};">
          <div class="form-group">
            <label for="customerName">Name <span class="required">*</span></label>
            <input type="text" class="form-control" id="customerName" placeholder="Enter your full name" value="${savedName}">
          </div>
          <div class="form-group">
            <label for="mobileNumber">Registered Mobile Number <span class="required">*</span></label>
            <input type="tel" class="form-control" id="mobileNumber" placeholder="Enter your mobile number" value="${savedMobile}">
          </div>
          <div class="form-group">
            <label for="cityName">City / Delivery Address <span class="required">*</span></label>
            <input type="text" class="form-control" id="cityName" placeholder="Enter city or delivery address" value="${savedCity === 'Not set' ? '' : savedCity}">
          </div>
          <div class="form-actions text-right mt-3">
             <button id="saveContactBtn" class="btn-save-contact">Save Details</button>
          </div>
        </div>

        <!-- Summary Mode -->
        <div id="contactSummaryMode" class="contact-summary" style="display: ${isFilled ? 'block' : 'none'};">
          <div class="summary-list-item">
            <span class="summary-list-icon">👤</span>
            <span id="summaryName">${savedName}</span>
          </div>
          <div class="summary-list-item">
            <span class="summary-list-icon">📞</span>
            <span id="summaryMobile">${savedMobile}</span>
          </div>
          <div class="summary-list-item">
            <span class="summary-list-icon">📍</span>
            <span id="summaryCity">${savedCity}</span>
          </div>
        </div>
      </div>
    `;

    // 5. WhatsApp Notice Banner
    html += `
      <!-- WhatsApp Notice Banner -->
      <div class="whatsapp-notice-banner mt-3">
        <span class="checkmark-icon"><i class="fa-solid fa-circle-check"></i></span>
        <span>We will send your quotation details on WhatsApp</span>
      </div>
    `;

    // Render it in container
    cartPageContainer.innerHTML = html;

    // Bind event listeners to contact controls dynamically
    bindCartEvents();

    // Update totals
    updateCartTotals();
}
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
/**
 * Generate and send WhatsApp RFQ
 */
function placeRFQOnWhatsApp() {

    const mobileNumber = document.getElementById('mobileNumber').value.trim();
    const customerName = document.getElementById('customerName').value.trim();
    const cityName = document.getElementById('cityName').value.trim();

    if (!mobileNumber) {
        alert('Please enter your mobile number');
        return;
    }

    if (!customerName) {
        alert('Please enter your name');
        return;
    }

    if (!cityName) {
        alert('Please enter your city or village name');
        return;
    }

    const cart = getCart();

    if (cart.length === 0) {
        alert('Your cart is empty');
        return;
    }

    // ===============================
    // AUTO INCREMENT RFQ NUMBER
    // ===============================
    let lastRFQ = localStorage.getItem('lastRFQNumber') || 0;

    lastRFQ = parseInt(lastRFQ) + 1;

    localStorage.setItem('lastRFQNumber', lastRFQ);

    const rfqNumber = String(lastRFQ).padStart(3, '0');

    // ===============================
    // DATE
    // ===============================
    const currentDate = new Date();

    const formattedDate =
        `${String(currentDate.getDate()).padStart(2, '0')}/` +
        `${String(currentDate.getMonth() + 1).padStart(2, '0')}/` +
        `${currentDate.getFullYear()}`;

    // ===============================
    // PRODUCT LIST
    // ===============================
    let productList = "";

    cart.forEach((item, index) => {
        productList += `${index + 1}. ${item.name} × ${item.quantity}\n`;
    });

    // ===============================
    // WHATSAPP MESSAGE
    // ===============================
    let message =
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

    const encodedMessage = encodeURIComponent(message);

    // ===============================
    // WHATSAPP NUMBERS
    // ===============================
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

    // Optional clear cart after RFQ
    /*
    localStorage.removeItem('pvcCart');

    setTimeout(() => {
        window.location.href = 'products.php';
    }, 1000);
    */
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
            placeRFQBtn.addEventListener('click', placeRFQOnWhatsApp);
        }
    }
});

// ==================== EXPORT FOR GLOBAL USE ====================

// Make functions available globally for onclick handlers
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateCartItemQuantity = updateCartItemQuantity;
window.placeRFQOnWhatsApp = placeRFQOnWhatsApp;
