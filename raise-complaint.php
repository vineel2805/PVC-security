<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Raise a Complaint - PVC Security Solutions | Support</title>
  <meta name="description"
    content="Facing an issue with your installation, product or service? File a complaint with PVC Security Solutions and our support team will get back to you shortly.">
   <?php include 'head.php'; ?>
   <link rel="stylesheet" href="assets/css/contact_pvc.css?v=<?php echo filemtime(__DIR__ . '/assets/css/contact_pvc.css'); ?>">
  <script src="assets/js/plugins/jquery-3-6-0.min.js"></script>
</head>

<body>

 <?php include 'header.php'; ?>
 <?php include 'includes/header.php'; ?>

  <!--=====HEADER END =======-->

  <!--===== COMPLAINT HERO (dark premium page header) =======-->
  <section class="cu-hero cu-hero--compact">
    <div class="cu-hero-glow"></div>
    <div class="container">
      <div class="cu-hero-inner" data-aos="fade-up">
        <nav class="cu-breadcrumb" aria-label="breadcrumb">
          <a href="index.php">Home</a>
          <i class="fa-solid fa-chevron-right"></i>
          <a href="contact-us.php">Contact Us</a>
          <i class="fa-solid fa-chevron-right"></i>
          <span>Raise a Complaint</span>
        </nav>
        <span class="cu-eyebrow">Support</span>
        <h1 class="cu-hero-title">Raise a <span class="text-gold">Complaint</span></h1>
        <p class="cu-hero-desc">Tell us what went wrong. Our support team typically responds within 10 minutes on WhatsApp.</p>
      </div>
    </div>
  </section>

  <!--===== COMPLAINT FORM =======-->
  <section class="cu-form-section">
    <div class="container">
      <div class="cu-form-col" data-aos="fade-up">
        <div class="pvc-complaint-card">

          <!-- Card Header -->
          <div class="pvc-complaint-header premium-header-section">
            <div class="premium-icon-box">
              <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <h2 class="pvc-complaint-title premium-title">Complaint Form</h2>
            <div class="premium-divider"></div>
            <div class="pvc-complaint-header-text">
              <p class="pvc-complaint-subtitle">Share your issue and our support team will get back to you shortly.</p>
            </div>
          </div>

          <!-- Form Body -->
          <form id="pvc-complaint-form" novalidate>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="pvc-form-group">
                  <label for="complaint-fullname">Full Name <span class="req">*</span></label>
                  <input type="text" id="complaint-fullname" class="form-control pvc-input" placeholder="Enter your full name" required>
                  <div class="invalid-feedback" id="err-fullname">Full name is required.</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="pvc-form-group">
                  <label for="complaint-mobile">Mobile Number <span class="req">*</span></label>
                  <input type="tel" id="complaint-mobile" class="form-control pvc-input" placeholder="Enter 10-digit mobile number" required>
                  <div class="invalid-feedback" id="err-mobile">Valid mobile number is required.</div>
                </div>
              </div>

              <div class="col-12">
                <div class="pvc-form-group">
                  <label for="complaint-address">Address <span class="req">*</span></label>
                  <input type="text" id="complaint-address" class="form-control pvc-input" placeholder="Street address, building, city" required>
                  <div class="invalid-feedback" id="err-address">Address is required.</div>
                </div>
              </div>

              <div class="col-12">
                <div class="pvc-form-group">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="complaint-desc" class="mb-0">Complaint Reason <span class="req">*</span></label>
                    <span class="char-count" id="desc-char-count">0 / 500</span>
                  </div>
                  <textarea id="complaint-desc" class="form-control pvc-textarea" rows="3" maxlength="500" placeholder="Describe your issue..." required></textarea>
                  <div class="invalid-feedback" id="err-desc">Complaint reason is required.</div>
                </div>
              </div>

              <div class="col-12 mt-4">
                <button type="submit" id="btnSubmitComplaint" class="pvc-btn-gold-submit">
                  Submit Complaint
                </button>
                <div class="pvc-secure-note">
                  <i class="fa-solid fa-lock"></i>
                  <span>Your information is secure and will only be used to resolve your complaint.</span>
                </div>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </section>

    <?php include 'global_footer.php'; ?>


        <script src="assets/js/plugins/bootstrap.min.js"></script>
        <script src="assets/js/plugins/aos.js"></script>
        <script src="assets/js/plugins/fontawesome.js"></script>
        <script src="assets/js/main.js"></script>

        <!--===== GLOBAL FOOTER COMPONENT =======-->
        <script src="assets/js/global_footer.js"></script>
        <!--===== END GLOBAL FOOTER =======-->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const complaintForm = document.getElementById('pvc-complaint-form');
      const descInput = document.getElementById('complaint-desc');
      const charCount = document.getElementById('desc-char-count');

      // Character counter for Issue Description
      if (descInput && charCount) {
        descInput.addEventListener('input', function() {
          const len = descInput.value.length;
          charCount.textContent = `${len} / 500`;
        });
      }

      // Fields for validation
      const fields = [
        { id: 'complaint-fullname', errId: 'err-fullname' },
        { id: 'complaint-mobile', errId: 'err-mobile' },
        { id: 'complaint-desc', errId: 'err-desc' },
        { id: 'complaint-address', errId: 'err-address' }
      ];

      fields.forEach(field => {
        const el = document.getElementById(field.id);
        if (el) {
          el.addEventListener('input', function() {
            if (el.value.trim() !== '') {
              el.classList.remove('is-invalid');
              const errEl = document.getElementById(field.errId);
              if (errEl) errEl.style.display = 'none';
            }
          });
          el.addEventListener('change', function() {
            if (el.value.trim() !== '') {
              el.classList.remove('is-invalid');
              const errEl = document.getElementById(field.errId);
              if (errEl) errEl.style.display = 'none';
            }
          });
        }
      });

      function generateComplaintToken() {
        const date = new Date();
        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        const xxx = String(Math.floor(Math.random() * 900) + 100);
        return `PVC-${yyyy}${mm}${dd}-${xxx}`;
      }

      if (complaintForm) {
        complaintForm.addEventListener('submit', function(e) {
          e.preventDefault();
          let formIsValid = true;

          fields.forEach(field => {
            const el = document.getElementById(field.id);
            const errEl = document.getElementById(field.errId);
            if (!el.value.trim()) {
              formIsValid = false;
              el.classList.add('is-invalid');
              if (errEl) errEl.style.display = 'block';
            } else {
              el.classList.remove('is-invalid');
              if (errEl) errEl.style.display = 'none';
            }
          });

          if (formIsValid) {
            const name = document.getElementById('complaint-fullname').value.trim();
            const mobile = document.getElementById('complaint-mobile').value.trim();
            const desc = document.getElementById('complaint-desc').value.trim();
            const address = document.getElementById('complaint-address').value.trim();

            const token = generateComplaintToken();

            const message = `*PVC Security solutions - Support Complaint*\n\n` +
                            `*Token:* ${token}\n` +
                            `*Full Name:* ${name}\n` +
                            `*Mobile:* ${mobile}\n` +
                            `*Address:* ${address}\n\n` +
                            `*Issue Description:*\n${desc}`;

            const encodedText = encodeURIComponent(message);
            const whatsappNumber = '919114456666';
            const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodedText}`;

            window.open(whatsappUrl, '_blank');
          }
        });
      }
    });
  </script>
  <script src="assets/js/global_search.js"></script>
</body>

</html>
