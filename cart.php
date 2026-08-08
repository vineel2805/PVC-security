<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart - PVC Global</title>
  <?php include 'head.php'; ?>
  <!--=====FAB ICON=======-->
  <link rel="shortcut icon" href="assets/img/logo/Untitled design-3.png" type="image/x-icon">
  
  <link rel="stylesheet" href="assets/css/cart_styles.css">
  <!--=====  JS SCRIPT LINK =======-->
  <script src="assets/js/plugins/jquery-3-6-0.min.js"></script>
</head>

<body>

  <?php $currentPage = 'cart';  include 'header.php'; ?>
  <?php include 'includes/header.php'; ?>


  <!-- Cart Page Content -->
  <section class="cart-page-section" data-step="1">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="cart-header-wrapper">
            <h1 class="cart-page-title">Your RFQ Cart</h1>
            <a href="javascript:history.back()" class="cart-close-btn" aria-label="Close Cart" title="Exit Cart">
              <i class="fa-solid fa-xmark"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Step Indicator -->
      <div class="rfq-step-indicator" id="rfqStepIndicator">
        <div class="rfq-step active" data-step-num="1">
          <div class="rfq-step-circle">
            <span class="rfq-step-number">1</span>
            <span class="rfq-step-check"><i class="fa-solid fa-check"></i></span>
          </div>
          <span class="rfq-step-label">Products</span>
        </div>
        <div class="rfq-step-line">
          <div class="rfq-step-line-fill"></div>
        </div>
        <div class="rfq-step" data-step-num="2">
          <div class="rfq-step-circle">
            <span class="rfq-step-number">2</span>
            <span class="rfq-step-check"><i class="fa-solid fa-check"></i></span>
          </div>
          <span class="rfq-step-label">Contact Info</span>
        </div>
      </div>

      <!-- Main Cart Page Row: Side-by-side on Desktop, 2-Step on Mobile -->
      <div class="cart-page-row mt-4">
        
        <!-- LEFT COLUMN: Contact Information -->
        <div class="cart-form-col step-2-content">
          <div class="cart-form-section">

            <div class="section-title-wrapper">
              <h3 class="section-title"><i class="fa-solid fa-user-circle"></i> Contact Information</h3>
              <button id="editContactBtn" class="btn-edit-contact" style="display: none;">
                <i class="fa-solid fa-pen-to-square"></i> Edit
              </button>
            </div>

            <!-- Form Mode -->
            <div id="contactFormMode">
              <div class="form-row">
                <div class="form-group">
                  <label for="mobileNumber">Registered Mobile Number <span class="required">*</span></label>
                  <input type="tel" class="form-control" id="mobileNumber" placeholder="Enter your mobile number">
                </div>

                <div class="form-group">
                  <label for="customerName">Name <span class="required">*</span></label>
                  <input type="text" class="form-control" id="customerName" placeholder="Enter your full name">
                </div>
              </div>

              <div class="form-group">
                <label for="cityName">City / Village <span class="required">*</span></label>
                <input type="text" class="form-control" id="cityName" placeholder="Enter city or village name">
              </div>

              <div class="form-actions text-right mt-2">
                 <button id="saveContactBtn" class="btn-save-contact">Save Details</button>
              </div>
            </div>

            <!-- Summary Mode -->
            <div id="contactSummaryMode" class="contact-summary" style="display: none;">
              <div class="summary-item">
                <div class="summary-icon"><i class="fa-solid fa-phone"></i></div>
                <div class="summary-details">
                  <label>Mobile Number</label>
                  <p id="summaryMobile"></p>
                </div>
              </div>
              <div class="summary-item">
                <div class="summary-icon"><i class="fa-regular fa-user"></i></div>
                <div class="summary-details">
                  <label>Name</label>
                  <p id="summaryName"></p>
                </div>
              </div>
              <div class="summary-item">
                <div class="summary-icon"><i class="fa-solid fa-location-dot"></i></div>
                <div class="summary-details">
                  <label>City / Village</label>
                  <p id="summaryCity"></p>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- RIGHT COLUMN: Products in RFQ -->
        <div class="cart-summary-col step-1-content">
          <div class="cart-summary-section">
            <div class="cart-summary-header">
               <h3 class="section-title"><i class="fa-solid fa-shopping-cart"></i> Products in RFQ <span id="headerItemCount">(0 Items)</span></h3>
            </div>

            <!-- Scrollable Cart Items Area -->
            <div class="cart-items-scroll-area">
              <div id="cartItemsContainer" class="cart-items-list">
                <!-- Cart items will be dynamically inserted here -->
              </div>
            </div>

            <!-- Mobile-only Continue Shopping (Outlined Secondary Button) -->
            <div class="mobile-continue-shopping-wrapper">
              <a href="all-products.php" class="btn-continue-shopping-outline">
                <i class="fa-solid fa-arrow-left"></i> Continue Shopping
              </a>
            </div>
          </div>

          <!-- Empty Cart Message -->
          <div id="emptyCartMessage" class="empty-cart-message" style="display: none;">
            <i class="fa-solid fa-cart-shopping"></i>
            <h3>Your cart is empty</h3>
            <p>Add products to your cart to request a quotation</p>
            <a href="all-products.php" class="btn-continue-shopping">Browse Products</a>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- Sticky Bottom Action Bar -->
  <div class="cart-sticky-footer">
    <div class="container">
      <div class="sticky-footer-content">
        <div class="cart-shipping-note">
          <i class="fa-solid fa-circle-info"></i>
          <p>Shipping cost will be calculated and added to your final quotation</p>
        </div>

        <!-- Mobile Step 1 Continue Shopping -->
        <a href="all-products.php" class="btn-continue-shopping step-1-only sticky-continue-row">
          <i class="fa-solid fa-arrow-left"></i> Continue Shopping
        </a>

        <div class="sticky-footer-actions">
          <div class="sticky-totals">
            <span class="total-label">Total Products</span>
            <span id="cartItemCount" class="total-count">0 Items</span>
          </div>
          <div class="sticky-buttons">
            <!-- Desktop Continue Shopping (Option A Preferred Text Button) -->
            <a href="all-products.php" class="btn-continue-shopping-desktop">
              <i class="fa-solid fa-arrow-left"></i> Continue Shopping
            </a>

            <!-- Step 1: Next only (Mobile) -->
            <button id="btnNextStep" class="btn-next-step step-1-only">
              Next 
            </button>

            <!-- Step 2: Back + Place Order (Mobile) / Place Order (Desktop) -->
            <button id="btnBackStep" class="btn-back-step step-2-only">
              <i class="fa-solid fa-arrow-left"></i> Back
            </button>
            <button id="placeRFQBtn" class="btn-place-rfq step-2-only">
              <i class="fa-brands fa-whatsapp"></i>Place Order
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--===== GLOBAL FOOTER COMPONENT =======-->
  <script src="assets/js/global_footer.js"></script>
  <!--===== END GLOBAL FOOTER =======-->
  <!-- Cart JavaScript -->
  <script src="assets/js/main.js"></script>
  <script src="assets/js/cart-core.js"></script>
  <script src="assets/js/floating-cart.js"></script>
  <script src="assets/js/cart-page.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
</body>
</html>