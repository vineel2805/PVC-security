<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart - PVC Global</title>

  <!--=====FAB ICON=======-->
  <link rel="shortcut icon" href="assets/img/logo/Untitled design-3.png" type="image/x-icon">
  <!--===== CSS LINK =======-->
  <link rel="stylesheet" href="assets/css/plugins/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/plugins/fontawesome.css">
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/header_styles.css">
  <link rel="stylesheet" href="assets/css/pvc-header-footer.css">
  <link rel="stylesheet" href="assets/css/cart_styles.css">

  <!--=====  JS SCRIPT LINK =======-->
  <script src="assets/js/plugins/jquery-3-6-0.min.js"></script>
</head>

<body>

  <?php include 'header.php'; ?>


  <!-- Cart Page Content -->
  <section class="cart-page-section">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h1 class="cart-page-title">Your RFQ Cart</h1>
          <p class="cart-page-subtitle">Complete your details to request a quotation via WhatsApp</p>
        </div>
      </div>

      <!-- Two-column layout: Form (left) + Summary (right) -->
      <div class="cart-page-row mt-4">

        <!-- Left Side: User Details Form -->
        <div class="cart-form-col">
          <div class="cart-form-section">
            <h3 class="section-title"><i class="fa-solid fa-user-circle"></i> Contact Information</h3>

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
          </div>
        </div>

        <!-- Right Side: Cart Summary -->
        <div class="cart-summary-col">
          <div class="cart-summary-section">
            <h3 class="section-title"><i class="fa-solid fa-file-invoice"></i> Quote Summary</h3>

            <!-- Cart Items -->
            <div id="cartItemsContainer" class="cart-items-list">
              <!-- Cart items will be dynamically inserted here -->
            </div>

            <!-- Cart Summary Totals -->
            <div class="cart-totals">
              <div class="cart-total-row">
                <span>Items:</span>
                <span id="cartItemCount">0</span>
              </div>
              <div class="cart-shipping-note">
                <i class="fa-solid fa-info-circle"></i>
                <p>Shipping cost will be calculated and added to your final quotation</p>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="cart-actions">
              <button id="placeRFQBtn" class="btn-place-rfq" disabled>
                <i class="fa-brands fa-whatsapp"></i> Place RFQ on WhatsApp
              </button>
              <a href="all-products.php" class="btn-continue-shopping">
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

  <!--===== GLOBAL FOOTER COMPONENT =======-->
  <script src="assets/js/global_footer.js"></script>
  <!--===== END GLOBAL FOOTER =======-->
  <script>
    (function () {
      document.addEventListener('DOMContentLoaded', function () {
        var p = window.location.pathname;
        var page = p.split('/').pop() || 'index.php';
        if (!page) page = 'index.php';
        var q = '.pvc-main-nav a[href="' + page + '"]';
        var a = document.querySelector(q);
        if (a) a.classList.add('active');
        var m = document.querySelector('.clean-mobile-nav a[href="' + page + '"]');
        if (m) m.classList.add('active');
        var sb = document.querySelector('.nav-search-btn');
        var sw = document.querySelector('.nav-search-wrapper');
        if (sb && sw) {
          sb.addEventListener('click', function (e) { e.stopPropagation(); sw.classList.toggle('active'); });
          document.addEventListener('click', function () { sw.classList.remove('active'); });
        }
      });
    })();
  </script>

  <!-- Enable "Place RFQ on WhatsApp" button only when all contact fields are filled -->
  <script>
    (function () {
      document.addEventListener('DOMContentLoaded', function () {
        var mobileInput = document.getElementById('mobileNumber');
        var nameInput = document.getElementById('customerName');
        var cityInput = document.getElementById('cityName');
        var rfqBtn = document.getElementById('placeRFQBtn');

        if (!mobileInput || !nameInput || !cityInput || !rfqBtn) return;

        function checkFormFilled() {
          var filled =
            mobileInput.value.trim() !== '' &&
            nameInput.value.trim() !== '' &&
            cityInput.value.trim() !== '';

          rfqBtn.disabled = !filled;
          rfqBtn.classList.toggle('is-disabled', !filled);
        }

        [mobileInput, nameInput, cityInput].forEach(function (input) {
          input.addEventListener('input', checkFormFilled);
        });

        // Run once on load in case fields are pre-filled (e.g. browser autofill)
        checkFormFilled();
      });
    })();
  </script>

  <!-- Cart JavaScript -->
  <script src="assets/js/main.js"></script>
  <script src="assets/js/cart.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
</body>

</html>