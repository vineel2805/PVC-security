/**
 * =============================================
 * CART PAGE RENDERING AND CHECKOUT
 * =============================================
 * Cart page display, form validation, and RFQ flow.
 * =============================================
 */

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

    if (!cartItemsContainer) {
        return;
    }

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

function isCartPage() {
    return window.location.pathname.includes('cart.php');
}

function bindCartPageEvents() {
    window.addEventListener('pvc-cart-updated', function () {
        if (isCartPage()) {
            loadCartPage();
        }
    });

    window.addEventListener('storage', function (event) {
        if (event.key === 'pvcCart' && isCartPage()) {
            loadCartPage();
        }
    });
}

function initializeCartPage() {
    if (!isCartPage()) {
        return;
    }

    loadCartPage();
    initializeShippingSelection();

    // Bind Place RFQ button
    const placeRFQBtn = document.getElementById('placeRFQBtn');
    if (placeRFQBtn) {
        placeRFQBtn.addEventListener('click', handlePlaceOrderClick);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        bindCartPageEvents();
        initializeCartPage();
    });
} else {
    bindCartPageEvents();
    initializeCartPage();
}

// ==================== EXPORT FOR GLOBAL USE ====================

window.loadCartPage = loadCartPage;
window.handlePlaceOrderClick = handlePlaceOrderClick;
window.placeRFQOnWhatsApp = handlePlaceOrderClick;