<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart - PVC Global</title>
  <?php include 'head.php'; ?>
  <!--=====FAB ICON=======-->
  <link rel="shortcut icon" href="assets/img/logo/Untitled design-3.png" type="image/x-icon">
  

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
        </div>
      </div>

      <!-- Two-column layout: Form (left) + Summary (right) -->
      <div class="cart-page-row mt-4">

        <!-- Left Side: User Details Form -->
        <div class="cart-form-col">
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

        <!-- Right Side: Cart Summary -->
        <div class="cart-summary-col">
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
          <i class="fa-solid fa-info-circle"></i>
          <p>Shipping cost will be calculated and added to your final quotation</p>
        </div>
        <div class="sticky-footer-actions">
          <div class="sticky-totals">
            <span class="total-label">Total Products</span>
            <span id="cartItemCount" class="total-count">0 Items</span>
          </div>
          <div class="sticky-buttons">
            <button id="placeRFQBtn" class="btn-place-rfq" disabled>
              <i class="fa-brands fa-whatsapp"></i> Place Order
            </button>
            <a href="all-products.php" class="btn-continue-shopping">
              <i class="fa-solid fa-arrow-left"></i> Continue Shopping
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

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
        
        var editBtn = document.getElementById('editContactBtn');
        var saveBtn = document.getElementById('saveContactBtn');
        var formMode = document.getElementById('contactFormMode');
        var summaryMode = document.getElementById('contactSummaryMode');
        
        var summaryMobile = document.getElementById('summaryMobile');
        var summaryName = document.getElementById('summaryName');
        var summaryCity = document.getElementById('summaryCity');

        function checkFormFilled() {
          var filled =
            mobileInput.value.trim() !== '' &&
            nameInput.value.trim() !== '' &&
            cityInput.value.trim() !== '';

          if(rfqBtn) {
              rfqBtn.disabled = !filled;
              rfqBtn.classList.toggle('is-disabled', !filled);
          }
          return filled;
        }

        function toggleContactMode(isEdit) {
            if(isEdit) {
                formMode.style.display = 'block';
                summaryMode.style.display = 'none';
                editBtn.style.display = 'none';
            } else {
                if(checkFormFilled()) {
                    summaryMobile.textContent = mobileInput.value.trim();
                    summaryName.textContent = nameInput.value.trim();
                    summaryCity.textContent = cityInput.value.trim();
                    
                    formMode.style.display = 'none';
                    summaryMode.style.display = 'flex';
                    editBtn.style.display = 'inline-flex';
                }
            }
        }

        if (mobileInput && nameInput && cityInput) {
            [mobileInput, nameInput, cityInput].forEach(function (input) {
              input.addEventListener('input', checkFormFilled);
            });
            
            if(saveBtn) {
                saveBtn.addEventListener('click', function() {
                    if(checkFormFilled()) {
                        toggleContactMode(false);
                    } else {
                        alert("Please fill all required fields.");
                    }
                });
            }
            
            if(editBtn) {
                editBtn.addEventListener('click', function() {
                    toggleContactMode(true);
                });
            }

            // Run once on load
            checkFormFilled();
            
            // Auto switch to summary if pre-filled via browser (timeout to allow autofill)
            setTimeout(function() {
                if(checkFormFilled()) {
                    toggleContactMode(false);
                }
            }, 500);
        }
      });
    })();
  </script>

  <!-- Cart JavaScript -->
  <script src="assets/js/main.js"></script>
  <script src="assets/js/cart.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
</body>

</html>