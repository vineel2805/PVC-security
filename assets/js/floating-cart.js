/**
 * =============================================
 * FLOATING CART BUTTON
 * =============================================
 * Visual cart affordance and badge updates only.
 * =============================================
 */

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

function initializeFloatingCartButton() {
    updateFloatingCartButton();
}

function bindFloatingCartListeners() {
    window.addEventListener('resize', function () {
        if (document.getElementById('floatingCartBtn')) {
            updateFloatingCartButton();
        }
    });

    window.addEventListener('storage', updateFloatingCartButton);
    window.addEventListener('pvc-cart-updated', updateFloatingCartButton);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        initializeFloatingCartButton();
        bindFloatingCartListeners();
    });
} else {
    initializeFloatingCartButton();
    bindFloatingCartListeners();
}

window.updateFloatingCartButton = updateFloatingCartButton;