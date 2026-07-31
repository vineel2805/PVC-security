<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - PVC Global Security | Expert Technical Support</title>
  <meta name="description"
    content="Reach out to PVC Security for expert surveillance guidance, sales inquiries, and technical support in AP & Telangana. Secure your future with our advanced AIoT solutions.">
  <link rel="shortcut icon" href="assets/img/logo/Untitled design-3.png" type="image/x-icon">
  <link rel="stylesheet" href="assets/css/plugins/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/plugins/fontawesome.css">
  <link rel="stylesheet" href="assets/css/plugins/aos.css">
  <link rel="stylesheet" href="assets/css/main.css">
  <style>
    :root {
      --pvc-gold-dark: #b8860b;
    }
  </style>
  <link rel="stylesheet" href="assets/css/header_styles.css">
  <script src="assets/js/plugins/jquery-3-6-0.min.js"></script>
  <style>
    .contact-hero {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/img/hero-contact.jpg');
      background-size: cover;
      background-position: center;
      height: 80vh;
      margin-top: 70px !important;
      display: flex;
      align-items: center;
      justify-content: flex-start;
      padding: 0;
      text-align: left;
      color: #fff;
    }

    .contact-hero h1 {
      font-size: 60px;
      font-weight: 600;
      letter-spacing: -2px;
      margin-bottom: 20px;
    }

    .contact-hero p {
      font-size: 24px;
      font-weight: 500;
    }

    /* Premium Action Cards */
    .contact-action-card {
      background: #fff;
      border: 1px solid rgba(0, 0, 0, 0.08);
      /* Subtle border */
      border-radius: 20px;
      padding: 40px 30px;
      text-align: center;
      transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
      display: block;
      text-decoration: none;
      height: 100%;
      position: relative;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    }

    .contact-action-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
      border-color: var(--pvc-gold-mid);
    }

    .contact-action-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, var(--pvc-gold-dark), var(--pvc-gold-mid));
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .contact-action-card:hover::before {
      opacity: 1;
    }

    .card-icon-box {
      width: 70px;
      height: 70px;
      background: #f4f4f4;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 25px;
      font-size: 28px;
      color: var(--pvc-gold-dark);
      transition: all 0.4s ease;
    }

    .card-icon-box.black-bg {
      background: #000;
      color: var(--pvc-gold-mid);
    }

    .contact-action-card:hover .card-icon-box {
      transform: scale(1.1) rotate(5deg);
    }

    .contact-action-card h3 {
      font-size: 20px;
      font-weight: 700;
      color: #000;
      margin-bottom: 8px;
    }

    .contact-action-card p {
      font-size: 15px;
      color: #555;
      margin-bottom: 15px;
      line-height: 1.5;
    }

    .contact-action-card .micro-text {
      font-size: 13px;
      color: #999;
      display: block;
      font-weight: 500;
      letter-spacing: 0.5px;
    }

    .whatsapp-card:hover {
      background: linear-gradient(135deg, #fff, #f0fff4);
    }

    /* Color Variations */
    .card-green {
      border-bottom: 4px solid #25D366;
    }

    .card-green .card-icon-box {
      background: #25D366;
      color: #fff;
      box-shadow: 0 10px 20px rgba(37, 211, 102, 0.2);
    }

    .card-gold {
      border-bottom: 4px solid #D4AF37;
    }

    .card-gold .card-icon-box {
      background: #D4AF37;
      color: #fff;
      box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
    }

    .card-red {
      border-bottom: 4px solid #E91E63;
    }

    .card-red .card-icon-box {
      background: #E91E63;
      color: #fff;
      box-shadow: 0 10px 20px rgba(233, 30, 99, 0.2);
    }

    .card-black {
      border-bottom: 4px solid #333;
    }

    .card-black .card-icon-box {
      background: #333;
      color: #fff;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    /* Feature Pills in Form Section */
    .icon-square-gold {
      width: 50px;
      height: 50px;
      background: #D4AF37;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 20px;
      box-shadow: 0 8px 20px rgba(212, 175, 55, 0.2);
      flex-shrink: 0;
    }

    .feature-pill-card {
      background: #fff;
      padding: 15px;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
      border: 1px solid #f9f9f9;
    }

    /* Trust Strip Below Cards */
    .trust-strip {
      display: flex;
      justify-content: center;
      gap: 40px;
      margin-top: 60px;
      flex-wrap: wrap;
      border-top: 1px solid rgba(0, 0, 0, 0.05);
      padding-top: 40px;
    }

    .trust-item {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
      font-weight: 600;
      color: #333;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .trust-item i {
      color: var(--pvc-gold-dark);
      font-size: 16px;
    }

    /* Form Section */
    .contact-form-section {
      background: #fcfcfc;
      padding: 80px 0;
    }

    /* Headings Standardisation */
    .shop-cat-title,
    .section-heading-gold {
      font-size: 43px !important;
      font-weight: 700 !important;
      color: var(--pvc-gold-dark) !important;
      text-transform: uppercase !important;
      letter-spacing: -0.5px;
    }

    @media (max-width: 991px) {

      .shop-cat-title,
      .section-heading-gold {
        font-size: 36px !important;
      }
    }

    @media (max-width: 767px) {

      .shop-cat-title,
      .section-heading-gold {
        font-size: 28px !important;
      }
    }

    .form-container {
      background: #fff;
      padding: 60px 80px;
      border-radius: 30px;
      box-shadow: 0 10px 60px rgba(0, 0, 0, 0.03);
      max-width: 900px;
      margin: 0 auto;
      text-align: left;
    }

    .section-header,
    .shop-cat-header {
      text-align: center;
      margin-bottom: 50px;
    }

    .form-container h2 {
      font-size: 28px;
      font-weight: 700;
      color: #222;
      margin-bottom: 5px;
    }

    .form-container p {
      font-size: 14px;
      color: #666;
      margin-bottom: 40px;
    }

    .form-control {
      background: #F8F9FA;
      border: none;
      padding: 15px 20px;
      border-radius: 8px;
      font-size: 14px;
      color: #333;
      margin-bottom: 0;
    }

    .form-control::placeholder {
      color: #999;
    }

    .form-control:focus {
      background: #fff;
      box-shadow: 0 0 0 2px var(--pvc-gold-mid);
    }
    .btn-submit {
      background: #25D366;
      background-size: 200% auto;
      color: var(--pvc-white) !important;
      border: none;
      padding: 15px 0;
      border-radius: 50px;
      font-size: 16px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      width: 100%;
      cursor: pointer;
      transition: all 0.4s ease;
      margin-top: 20px;
      box-shadow:0 4px 15px rgba(37, 211, 102, 0.3);
    }

    .btn-submit:hover {
      background-position: right center;
      transform: translateY(-3px);
      box-shadow: 0 15px 30px rgba(184, 134, 11, 0.4);
      color: #000;
    }

    /* Responsive Adjustments */
    @media (max-width: 991px) {
      .contact-hero {
        height: 60vh;
      }

      .contact-hero h1 {
        font-size: 40px;
      }

      .trust-strip {
        gap: 20px 30px;
        justify-content: center;
      }
    }

    @media (max-width: 767px) {
      .contact-hero {
        text-align: center;
        justify-content: center;
        height: 50vh;
      }

      .mobile-sticky-cta {
        display: flex;
        animation: slideUp 0.5s ease-out forwards;
      }

      .trust-strip {
        flex-direction: column;
        align-items: center;
        gap: 15px;
      }

      .contact-action-card {
        padding: 20px 10px;
        min-height: 220px;
        /* Uniform height */
      }

      .card-icon-box {
        width: 50px;
        height: 50px;
        font-size: 20px;
        margin-bottom: 15px;
      }

      .contact-action-card h3 {
        font-size: 16px;
      }

      .contact-action-card p {
        font-size: 13px;
      }

      .form-container {
        padding: 30px;
      }
    }

    @keyframes slideUp {
      from {
        transform: translateY(100px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .gold-gradient-text {
      background: linear-gradient(to right, #BF953F, #FCF6BA, #B38728, #FBF5B7, #AA771C);
      background-size: 200% auto;
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      color: #B38728;
      display: inline-block;
    }

    /* === SUCCESS POPUP STYLES === */
    .success-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.85);
      z-index: 99999;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(8px);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .success-overlay.active {
      display: flex !important;
      opacity: 1;
    }

    .success-card {
      background: white;
      padding: 50px 40px;
      border-radius: 30px;
      text-align: center;
      width: 90%;
      max-width: 400px;
      transform: scale(0.8);
      opacity: 0;
      transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .success-overlay.active .success-card {
      transform: scale(1);
      opacity: 1;
    }

    .checkmark-circle {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, #D4AF37, #AA771C);
      border-radius: 50%;
      margin: 0 auto 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.7);
      animation: pulseGold 2s infinite;
    }

    @keyframes pulseGold {
      0% {
        box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.7);
      }

      70% {
        box-shadow: 0 0 0 20px rgba(212, 175, 55, 0);
      }

      100% {
        box-shadow: 0 0 0 0 rgba(212, 175, 55, 0);
      }
    }

    .checkmark-icon {
      font-size: 40px;
      color: white;
      opacity: 0;
      transform: scale(0);
    }

    .success-overlay.active .checkmark-icon {
      animation: popCheck 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.3s forwards;
    }

    @keyframes popCheck {
      from {
        opacity: 0;
        transform: scale(0) rotate(-45deg);
      }

      to {
        opacity: 1;
        transform: scale(1) rotate(0);
      }
    }

    .btn-success-close {
      background: linear-gradient(to right, #BF953F, #FCF6BA, #B38728, #FBF5B7, #AA771C);
      color: #000;
      border: none;
      padding: 15px 50px;
      border-radius: 50px;
      font-weight: 800;
      font-size: 16px;
      margin-top: 30px;
      cursor: pointer;
      width: 100%;
      transition: all 0.3s;
      box-shadow: 0 10px 20px rgba(184, 134, 11, 0.3);
    }

    .btn-success-close:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 30px rgba(184, 134, 11, 0.4);
    }

    /* Complaint Section & Form Styles */
    .complaint-section {
      background: #fafafa;
      padding: 80px 0;
    }
    
    .complaint-form-container {
      background: #fff;
      padding: 60px 80px;
      border-radius: 30px;
      box-shadow: 0 10px 60px rgba(0, 0, 0, 0.03);
      max-width: 750px;
      margin: 0 auto;
      text-align: left;
    }
    
    .complaint-form-group {
      margin-bottom: 25px;
      position: relative;
    }
    
    .complaint-form-group label {
      font-size: 14px;
      font-weight: 700;
      color: #222;
      margin-bottom: 8px;
      display: block;
    }
    
    .complaint-form-group label span {
      color: #E91E63;
    }
    
    .complaint-form-group .form-control.is-invalid {
      border: 1px solid #E91E63 !important;
      background-color: #FFF5F7 !important;
      box-shadow: 0 0 0 2px rgba(233, 30, 99, 0.15) !important;
    }
    
    .complaint-form-group .invalid-feedback {
      display: none;
      color: #E91E63;
      font-size: 12px;
      font-weight: 600;
      margin-top: 6px;
    }
    
    /* Touch friendly styling for textarea */
    textarea.form-control {
      min-height: 120px;
      resize: vertical;
    }
    
    @media (max-width: 767px) {
      .complaint-form-container {
        padding: 30px;
        border-radius: 30px;
      }
    }
  </style>
</head>

<body>

 <?php include 'header.php'; ?>


  <!--=====HEADER END =======-->





  <section class="sp1" style="background: #fafafa;">
    <div class="container">
      <div class="row text-center mb-5">
        <div class="col-12" data-aos="fade-up">
          <h2 class="section-heading-gold">Connect with PVC Security Instantly</h2>
          <p
            style="font-size: 14px; color: #555; text-transform: uppercase; font-weight: 700; letter-spacing: 2px; margin-bottom: 10px;">
            CCTV • SURVEILLANCE • INSTALLATION • SUPPORT
          </p>
          <p style="color: var(--pvc-gold-dark); font-weight: 700; font-size: 16px;">Serving Andhra Pradesh & Telangana
          </p>
        </div>
      </div>

      <div class="row g-3 g-md-4">
        <!-- Card 1: WhatsApp (Green) -->
        <div class="col-6 col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
          <a href="https://wa.me/919114456666" target="_blank" class="contact-action-card whatsapp-card card-green">
            <div class="card-icon-box">
              <i class="fa-brands fa-whatsapp"></i>
            </div>
            <h3>Chat on WhatsApp</h3>
            <p>Instant help & quotation</p>
            <span class="micro-text" style="color: #25D366; font-weight: 700;">Click to Chat Now <i
                class="fa-solid fa-arrow-right"></i></span>
          </a>
        </div>

        <!-- Card 2: Call Now (Gold) -->
        <div class="col-6 col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
          <a href="tel:+919114456666" class="contact-action-card card-gold">
            <div class="card-icon-box">
              <i class="fa-solid fa-phone-volume"></i>
            </div>
            <h3>Call PVC Security</h3>
            <p style="font-weight: 700; font-size: 18px; color: #555;">+91 91144 56666</p>
            <span class="micro-text" style="color: #D4AF37; font-weight: 700;">Tap to Call Now <i
                class="fa-solid fa-phone"></i></span>
          </a>
        </div>

        <!-- Card 3: Register Complaint -->
        <div class="col-6 col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
          <a href="javascript:void(0);" id="register-complaint-card" class="contact-action-card card-red">
            <div class="card-icon-box">
              <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <h3>Register Complaint</h3>
            <p>Report CCTV, DVR,<br>NVR or Camera Issues</p>
            <span class="micro-text" style="color: #E91E63; font-weight: 700;">Click to Register →</span>
          </a>
        </div>

        <!-- Card 4: Email (Black) -->
        <div class="col-6 col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
          <a href="mailto:pvcsecurity@gmail.com" class="contact-action-card card-black">
            <div class="card-icon-box">
              <i class="fa-solid fa-envelope"></i>
            </div>
            <h3>Email Us</h3>
            <p>pvcsecurity@gmail.com</p>
            <span class="micro-text" style="color: #333; font-weight: 700;">Click to Send Email <i
                class="fa-solid fa-envelope"></i></span>
          </a>
        </div>
      </div>

    </div>
  </section>
  <!--===== COMPLAINT FORM SECTION =======-->
  <section id="complaint-section" class="complaint-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12">
          <div class="complaint-form-container" data-aos="fade-up">
            
            <div class="text-center mb-4">
              <h2 class="section-heading-gold" style="font-size: 28px !important; margin-bottom: 10px;">Register Your Complaint</h2>
              <p style="color: #666; font-size: 15px; font-weight: 500; margin-bottom: 30px; line-height: 1.6;">
                Facing an issue with your CCTV or security system?<br>Fill out the form below
              </p>
            </div>
            
            <form id="complaint-form" novalidate>
              <!-- Customer Name -->
              <div class="complaint-form-group">
                <label for="complaint-name">Customer Name <span>*</span></label>
                <input type="text" id="complaint-name" class="form-control" placeholder="Enter your full name" required>
                <div class="invalid-feedback" id="complaint-name-error">Customer name is required.</div>
              </div>
              
              <!-- Mobile Number -->
              <div class="complaint-form-group">
                <label for="complaint-mobile">Mobile Number <span>*</span></label>
                <input type="tel" id="complaint-mobile" class="form-control" placeholder="Enter your mobile number" required>
                <div class="invalid-feedback" id="complaint-mobile-error">Mobile number is required.</div>
              </div>
              
              <!-- Address -->
              <div class="complaint-form-group">
                <label for="complaint-address">Address <span>*</span></label>
                <input type="text" id="complaint-address" class="form-control" placeholder="Enter your address" required>
                <div class="invalid-feedback" id="complaint-address-error">Address is required.</div>
              </div>
              
              <!-- Complaint Reason -->
              <div class="complaint-form-group">
                <label for="complaint-reason">Complaint Reason <span>*</span></label>
                <textarea id="complaint-reason" class="form-control" placeholder="Describe your issue..." required></textarea>
                <div class="invalid-feedback" id="complaint-reason-error">Complaint description is required.</div>
              </div>
              
              <!-- Submit Button -->
              <button type="submit" class="btn-submit" id="complaint-submit-btn">
                <i class="fa-brands fa-whatsapp" style="margin-right: 10px; font-size: 18px;"></i>
                Register Complaint via WhatsApp
              </button>
            </form>
            
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--===== END COMPLAINT FORM SECTION =======-->



        <script src="assets/js/plugins/bootstrap.min.js"></script>
        <script src="assets/js/plugins/aos.js"></script>
        <script src="assets/js/plugins/fontawesome.js"></script>
        <script src="assets/js/main.js"></script>

        <!--===== GLOBAL FOOTER COMPONENT =======-->
        <script src="assets/js/global_footer.js"></script>
        <!--===== END GLOBAL FOOTER =======-->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const complaintCard = document.getElementById('register-complaint-card');
      const complaintSection = document.getElementById('complaint-section');
      const complaintForm = document.getElementById('complaint-form');
      
      // Smooth Scrolling Behaviour
      if (complaintCard && complaintSection) {
        complaintCard.addEventListener('click', function(e) {
          e.preventDefault();
          complaintSection.scrollIntoView({ behavior: 'smooth' });
        });
      }
      
      // Form Input Validation Setup
      const fields = [
        { id: 'complaint-name', errorId: 'complaint-name-error', name: 'Customer name' },
        { id: 'complaint-mobile', errorId: 'complaint-mobile-error', name: 'Mobile number' },
        { id: 'complaint-address', errorId: 'complaint-address-error', name: 'Address' },
        { id: 'complaint-reason', errorId: 'complaint-reason-error', name: 'Complaint description' }
      ];
      
      fields.forEach(field => {
        const inputEl = document.getElementById(field.id);
        if (inputEl) {
          inputEl.addEventListener('input', function() {
            if (inputEl.value.trim() !== '') {
              inputEl.classList.remove('is-invalid');
              const errorEl = document.getElementById(field.errorId);
              if (errorEl) errorEl.style.display = 'none';
            }
          });
        }
      });
      
      // Helper function to generate unique YYYYMMDD-XXX token
      function generateComplaintToken() {
        const date = new Date();
        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        const xxx = String(Math.floor(Math.random() * 900) + 100);
        return `PVC-${yyyy}${mm}${dd}-${xxx}`;
      }
      
      // Submit Handler
      if (complaintForm) {
        complaintForm.addEventListener('submit', function(e) {
          e.preventDefault();
          let formIsValid = true;
          
          fields.forEach(field => {
            const inputEl = document.getElementById(field.id);
            const errorEl = document.getElementById(field.errorId);
            
            if (!inputEl.value.trim()) {
              formIsValid = false;
              inputEl.classList.add('is-invalid');
              if (errorEl) {
                errorEl.textContent = `${field.name} is required.`;
                errorEl.style.display = 'block';
              }
            } else {
              inputEl.classList.remove('is-invalid');
              if (errorEl) errorEl.style.display = 'none';
            }
          });
          
          if (formIsValid) {
            const name = document.getElementById('complaint-name').value.trim();
            const mobile = document.getElementById('complaint-mobile').value.trim();
            const address = document.getElementById('complaint-address').value.trim();
            const reason = document.getElementById('complaint-reason').value.trim();
            
            const token = generateComplaintToken();
            
            // Build the WhatsApp message matching required layout
            const message = `*PVC Security - Complaint Registration*\n\n` +
                            `Complaint Token: ${token}\n\n` +
                            `Customer Name: ${name}\n` +
                            `Mobile: ${mobile}\n` +
                            `Address: ${address}\n\n` +
                            `Complaint Reason:\n${reason}`;
            
            const encodedText = encodeURIComponent(message);
            const whatsappNumber = '919114456666';
            const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodedText}`;
            
            window.open(whatsappUrl, '_blank');
          }
        });
      }
    });
  </script>
</body>

</html>