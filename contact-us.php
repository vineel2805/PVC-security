<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - PVC Global Security | Expert Technical Support</title>
  <meta name="description"
    content="Reach out to PVC Security for expert surveillance guidance, sales inquiries, and technical support in AP & Telangana. Secure your future with our advanced AIoT solutions.">
   <?php include 'head.php'; ?>
   <link rel="stylesheet" href="assets/css/contact_pvc.css">
  <script src="assets/js/plugins/jquery-3-6-0.min.js"></script>
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
              <button type="submit" id="registerComplaintBtn" class="btn-register-complaint">
                <i class="fab fa-whatsapp"></i> Register Complaint
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