/**
 * QUICK REFERENCE: Add to Cart Implementation
 * Copy and paste these code snippets into your product pages
 */

// ============================================================
// 1. ADD CART SCRIPTS TO YOUR PAGE (Before </body>)
// ============================================================

<!-- Cart Functionality -->
<link rel="stylesheet" href="assets/css/cart_styles.css">
<script src="assets/js/cart.js"></script>


// ============================================================
// 2. BASIC PRODUCT CARD WITH ADD TO CART BUTTON
// ============================================================

<div class="hik-product-card">
    <div class="hik-product-img">
        <img src="assets/img/products/product-image.png" alt="Product Name">
    </div>
    <div class="hik-product-info">
        <h3>Product Name</h3>
        <p>Product Description</p>
        <div class="hik-product-actions">
            <a href="#" class="hik-btn-main">View Details</a>
            <button class="hik-btn-outline" onclick="addToCart({
                name: 'Product Name', 
                model: 'MODEL-123', 
                price: 15999, 
                image: 'assets/img/products/product-image.png'
            }); return false;">
                <i class="fa-solid fa-cart-plus"></i>
            </button>
        </div>
    </div>
</div>


// ============================================================
// 3. SIMPLE BUTTON VERSION (No View Details)
// ============================================================

<button class="btn btn-primary" onclick="addToCart({
    name: 'Product Name', 
    model: 'MODEL-123', 
    price: 15999, 
    image: 'assets/img/products/product-image.png'
})">
    <i class="fa-solid fa-cart-plus"></i> Add to Cart
</button>


// ============================================================
// 4. PRODUCT WITH PRICE ON REQUEST (Set price: 0)
// ============================================================

<button class="hik-btn-outline" onclick="addToCart({
    name: 'Premium Product', 
    model: 'PREMIUM-001', 
    price: 0,  // Price on request
    image: 'assets/img/products/premium.png'
}); return false;">
    <i class="fa-solid fa-cart-plus"></i>
</button>


// ============================================================
// 5. MULTIPLE PRODUCTS IN A ROW
// ============================================================

<div class="row">
    <!-- Product 1 -->
    <div class="col-md-4">
        <div class="hik-product-card">
            <!-- Product content here -->
            <button onclick="addToCart({
                name: 'Product 1', 
                model: 'MOD-001', 
                price: 12999, 
                image: 'assets/img/products/prod1.png'
            })">Add to Cart</button>
        </div>
    </div>

    <!-- Product 2 -->
    <div class="col-md-4">
        <div class="hik-product-card">
            <!-- Product content here -->
            <button onclick="addToCart({
                name: 'Product 2', 
                model: 'MOD-002', 
                price: 15999, 
                image: 'assets/img/products/prod2.png'
            })">Add to Cart</button>
        </div>
    </div>

    <!-- Product 3 -->
    <div class="col-md-4">
        <div class="hik-product-card">
            <!-- Product content here -->
            <button onclick="addToCart({
                name: 'Product 3', 
                model: 'MOD-003', 
                price: 0, 
                image: 'assets/img/products/prod3.png'
            })">Add to Cart</button>
        </div>
    </div>
</div>


// ============================================================
// 6. STYLED FULL-WIDTH BUTTON
// ============================================================

<button style="
    width: 100%;
    padding: 12px;
    background: #d4af37;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
" onclick="addToCart({
    name: 'Product Name', 
    model: 'MODEL-123', 
    price: 15999, 
    image: 'assets/img/products/product.png'
})" onmouseover="this.style.background='#b8860b'" 
   onmouseout="this.style.background='#d4af37'">
    <i class="fa-solid fa-cart-plus"></i> Add to Cart
</button>


// ============================================================
// 7. JAVASCRIPT FUNCTION TO ADD TO CART PROGRAMMATICALLY
// ============================================================

// Call this function from anywhere in your code
function addProductToCart() {
    addToCart({
        name: 'Dynamic Product',
        model: 'DYN-001',
        price: 19999,
        image: 'assets/img/products/dynamic.png'
    });
}


// ============================================================
// 8. CUSTOMIZING WHATSAPP NUMBER
// ============================================================

// Edit this in assets/js/cart.js (around line 290)
const whatsappNumber = '919876543210'; // Change to your number

// Format: Country code + number (no spaces or symbols)
// Examples:
// India: 919876543210
// USA: 14155552671
// UK: 447123456789


// ============================================================
// 9. CLEAR CART AFTER RFQ (Optional)
// ============================================================

// In assets/js/cart.js, in placeRFQOnWhatsApp function
// Uncomment these lines to clear cart after placing RFQ:

localStorage.removeItem('pvcCart');
setTimeout(() => { 
    window.location.href = 'products.html'; 
}, 1000);


// ============================================================
// 10. CHECK IF PRODUCT IS IN CART
// ============================================================

// JavaScript function to check if product exists in cart
function isProductInCart(modelNumber) {
    const cart = JSON.parse(localStorage.getItem('pvcCart') || '[]');
    return cart.some(item => item.model === modelNumber);
}

// Usage:
if (isProductInCart('MODEL-123')) {
    console.log('Product already in cart');
}


// ============================================================
// 11. GET CART ITEM COUNT
// ============================================================

// JavaScript function to get total item count
function getCartItemCount() {
    const cart = JSON.parse(localStorage.getItem('pvcCart') || '[]');
    return cart.reduce((total, item) => total + item.quantity, 0);
}

// Usage:
const itemCount = getCartItemCount();
console.log(`Cart has ${itemCount} items`);


// ============================================================
// 12. PRODUCT DATA STRUCTURE
// ============================================================

/*
Each product in cart has this structure:
{
    name: "Product Name",        // Display name
    model: "MODEL-123",          // Unique identifier (required)
    price: 15999,                // Price in rupees (0 for price on request)
    image: "path/to/image.png",  // Product image path
    quantity: 1                  // Quantity (auto-managed)
}
*/


// ============================================================
// 13. LINK TO CART PAGE
// ============================================================

<!-- Simple link to cart -->
<a href="cart.html">View Cart</a>

<!-- Link with icon -->
<a href="cart.html">
    <i class="fa-solid fa-shopping-cart"></i> View Cart
</a>

<!-- Button style -->
<a href="cart.html" class="btn btn-primary">
    <i class="fa-solid fa-shopping-cart"></i> Checkout
</a>


// ============================================================
// 14. CUSTOM NOTIFICATION STYLING
// ============================================================

// The notification appears when item is added
// To customize, edit the showAddToCartNotification function in cart.js
// Default: Green notification, top-right, 3 seconds

// Example custom notification:
notification.style.cssText = `
    position: fixed;
    top: 20px;              // Position from top
    right: 20px;            // Position from right
    background: #25D366;    // Background color
    color: white;           // Text color
    padding: 15px 20px;     // Padding
    border-radius: 8px;     // Border radius
    z-index: 10000;         // Layer level
`;


// ============================================================
// 15. COMMON ISSUES & SOLUTIONS
// ============================================================

/*
ISSUE: Floating cart button not appearing
SOLUTION: Ensure cart.js and cart_styles.css are loaded

ISSUE: WhatsApp not opening
SOLUTION: Check WhatsApp number format (no spaces or symbols)

ISSUE: Cart not persisting
SOLUTION: Check if localStorage is enabled in browser

ISSUE: Styling conflicts
SOLUTION: Load cart_styles.css after other CSS files

ISSUE: Function not defined error
SOLUTION: Ensure cart.js is loaded before calling addToCart()
*/


// ============================================================
// 16. TESTING YOUR IMPLEMENTATION
// ============================================================

/*
1. Open browser console (F12)
2. Add a product to cart
3. Check localStorage:
   localStorage.getItem('pvcCart')
4. Should see JSON array with product data
5. Navigate to cart.html
6. Verify products display correctly
7. Test quantity controls
8. Test remove button
9. Fill form and test WhatsApp generation
*/

// Console command to view cart:
console.log(JSON.parse(localStorage.getItem('pvcCart') || '[]'));

// Console command to clear cart:
localStorage.removeItem('pvcCart');


// ============================================================
// END OF QUICK REFERENCE
// ============================================================
