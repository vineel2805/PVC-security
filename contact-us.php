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
 <?php include 'includes/header.php'; ?>

  <!--=====HEADER END =======-->

  <section class="contact-hero-section">
    <div class="container">
      <div class="row align-items-center">
        <!-- Text Column -->
        <div class="col-lg-6 hero-text-col" data-aos="fade-right">
          <!-- Mobile Header (Title + Image side-by-side) -->
          <div class="d-flex justify-content-between align-items-center align-items-md-start">
            <div>
              <h1 class="hero-title">Contact the<br><span class="text-gold">PVC Security</span>  Team</h1>
            </div>
            <!-- Mobile CCTV Image -->
            <div class="mobile-hero-img d-lg-none ms-3">
              <img src="assets/img/contactus-hero.png" alt="PVC Security Camera" class="img-fluid">
            </div>
          </div>
          
          <p class="hero-desc">Whether you need installation, support, or service – our experts are ready to assist you.</p>
          
          <div class="trust-badges-row d-none d-lg-flex">
             <div class="trust-badge">
               <div class="tb-icon"><img src="assets/img/icons/fast.svg" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23D4AF37\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polygon points=\'13 2 3 14 12 14 11 22 21 10 12 10 13 2\'></polygon></svg>'" alt="Fast"></div>
               <div>
                 <strong>Fast Response</strong>
                 <span>Within 10 mins</span>
               </div>
             </div>
             <div class="trust-badge">
               <div class="tb-icon"><img src="assets/img/icons/expert.svg" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23D4AF37\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><path d=\'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2\'></path><circle cx=\'9\' cy=\'7\' r=\'4\'></circle><polyline points=\'16 11 18 13 22 9\'></polyline></svg>'" alt="Expert"></div>
               <div>
                 <strong>Expert Technicians</strong>
                 <span>Trained & Verified</span>
               </div>
             </div>
             <div class="trust-badge d-none d-md-flex">
               <div class="tb-icon"><img src="assets/img/icons/trust.svg" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23D4AF37\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><path d=\'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z\'></path></svg>'" alt="Trust"></div>
               <div>
                 <strong>Trusted by 1000+</strong>
                 <span>Homes & Businesses</span>
               </div>
             </div>
          </div>
        </div>
        
        <!-- Desktop CCTV Image -->
        <div class="col-lg-6 hero-img-col text-center d-none d-lg-block" data-aos="fade-left">
          <img src="assets/img/contactus-hero.png" alt="PVC Security Camera" class="hero-cctv-img img-fluid">
        </div>
      </div>
    </div>
  </section>

  <section class="contact-action-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7" data-aos="fade-up">
          <div class="pvc-complaint-card">
            
            <!-- Card Header -->
            <div class="pvc-complaint-header">
              <div class="pvc-complaint-icon-wrap">
                <i class="fa-solid fa-clipboard-list"></i>
              </div>
              <div class="pvc-complaint-header-text">
                <h2 class="pvc-complaint-title">Complaint Form</h2>
                <p class="pvc-complaint-subtitle">Share your issue and our support team will get back to you shortly.</p>
              </div>
            </div>

            <!-- Form Body -->
            <form id="pvc-complaint-form" novalidate>
              <div class="row g-3">
                <!-- 1. Full Name & 2. Mobile Number (One row on desktop) -->
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

                <!-- 3. Email Address (Optional) -->
                <!-- <div class="col-12">
                  <div class="pvc-form-group">
                    <label for="complaint-email">Email Address <span class="opt">(Optional)</span></label>
                    <input type="email" id="complaint-email" class="form-control pvc-input" placeholder="name@example.com">
                  </div>
                </div> -->

                <!-- 4. Complaint Type -->
                <div class="col-12">
                  <!-- <div class="pvc-form-group">
                    <label for="complaint-type">Complaint Type <span class="req">*</span></label>
                    <select id="complaint-type" class="form-select pvc-select" required>
                      <option value="" disabled selected>Select issue type...</option>
                      <option value="Installation Issue">Installation Issue</option>
                      <option value="Product Issue">Product Issue</option>
                      <option value="Service Request">Service Request</option>
                      <option value="Warranty Claim">Warranty Claim</option>
                      <option value="AMC Support">AMC Support</option>
                      <option value="Other">Other</option>
                    </select>
                    <div class="invalid-feedback" id="err-type">Please select a complaint type.</div>
                  </div>
                </div> -->

                <!-- 5. Installation Address -->
                <div class="col-12">
                  <div class="pvc-form-group">
                    <label for="complaint-address">Address <span class="req">*</span></label>
                    <input type="text" id="complaint-address" class="form-control pvc-input" placeholder="Street address, building, city" required>
                    <div class="invalid-feedback" id="err-address">Address is required.</div>
                  </div>
                </div>

                <!-- 6. Issue Description -->
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



                <!-- 7. Preferred Service Time -->
               <!-- <div class="col-12">
                  <div class="pvc-form-group">
                    <label for="complaint-time">Preferred Service Time</label>
                    <select id="complaint-time" class="form-select pvc-select">
                      <option value="Morning">Morning (9:00 AM - 12:00 PM)</option>
                      <option value="Afternoon">Afternoon (12:00 PM - 4:00 PM)</option>
                      <option value="Evening">Evening (4:00 PM - 8:00 PM)</option>
                    </select>
                  </div>
                </div> -->

                <!-- 8. Upload Photos / Videos (Optional) -->
                <!-- <div class="col-12">
                  <div class="pvc-form-group">
                    <label>Upload Photos / Videos <span class="opt">(Optional)</span></label>
                    <div class="pvc-upload-box" id="pvc-upload-box">
                      <i class="fa-solid fa-paperclip upload-icon"></i>
                      <p class="upload-text">Upload JPG, PNG or MP4</p>
                      <span class="upload-subtext">Maximum 10 MB</span>
                      <span class="file-name-display" id="file-name-display"></span>
                      <input type="file" id="complaint-file" class="d-none" accept="image/jpeg,image/png,video/mp4">
                    </div>
                  </div>
                </div> -->

                <!-- Submit Button & Security Note -->
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
    </div>
  </section>



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
      const uploadBox = document.getElementById('pvc-upload-box');
      const fileInput = document.getElementById('complaint-file');
      const fileNameDisplay = document.getElementById('file-name-display');

      // Character counter for Issue Description
      if (descInput && charCount) {
        descInput.addEventListener('input', function() {
          const len = descInput.value.length;
          charCount.textContent = `${len} / 500`;
        });
      }

      // Upload Box Click & File Display
      if (uploadBox && fileInput) {
        uploadBox.addEventListener('click', function() {
          fileInput.click();
        });

        fileInput.addEventListener('change', function() {
          if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            if (file.size > 10 * 1024 * 1024) {
              alert('File size exceeds 10 MB limit.');
              fileInput.value = '';
              fileNameDisplay.textContent = '';
              return;
            }
            fileNameDisplay.textContent = `Attached: ${file.name}`;
          } else {
            fileNameDisplay.textContent = '';
          }
        });
      }

      // Fields for validation
      const fields = [
        { id: 'complaint-fullname', errId: 'err-fullname' },
        { id: 'complaint-mobile', errId: 'err-mobile' },
        { id: 'complaint-type', errId: 'err-type' },
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
            const email = document.getElementById('complaint-email').value.trim() || 'N/A';
            const type = document.getElementById('complaint-type').value;
            const desc = document.getElementById('complaint-desc').value.trim();
            const address = document.getElementById('complaint-address').value.trim();
            const time = document.getElementById('complaint-time').value;
            const fileAttachment = fileInput && fileInput.files[0] ? fileInput.files[0].name : 'None';

            const token = generateComplaintToken();

            const message = `*PVC Security - Support Complaint*\n\n` +
                            `*Token:* ${token}\n` +
                            `*Full Name:* ${name}\n` +
                            `*Mobile:* ${mobile}\n` +
                            `*Email:* ${email}\n` +
                            `*Issue Type:* ${type}\n` +
                            `*Address:* ${address}\n` +
                            `*Preferred Time:* ${time}\n` +
                            `*Attachment:* ${fileAttachment}\n\n` +
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