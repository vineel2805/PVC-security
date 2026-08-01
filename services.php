<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Premium Security Services - PVC Global Security</title>
  <meta name="description"
    content="Expert CCTV installation, security planning, and maintenance services. PVC Security provides professional surveillance support across Andhra Pradesh and Telangana.">
    <?php include 'head.php'; ?>
  <!-- JS Plugins -->
  <script src="assets/js/plugins/jquery-3-6-0.min.js"></script>
  <!-- FontAwesome 6 (if not already fully loaded) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <style>
    :root {
      --pvc-black: #000000;
      --pvc-gold: #B8860B;
      --pvc-gold-light: #D4AF37;
      --pvc-white: #FFFFFF;
      --pvc-off-white: #F8F8F8;
      --pvc-text-dark: #1a1a1a;
      --pvc-text-gray: #444444;
      --pvc-gold-dark: #b8860b;
      --transition-smooth: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
      background-color: var(--pvc-white);
      font-family: 'Poppins', sans-serif;
      overflow-x: hidden;
    }

    /* =========================================
       HERO SECTION
       ========================================= */
    .services-hero {
      background-color: var(--pvc-black);
      height: 70vh;
      min-height: 500px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      position: relative;
      margin-top: 70px;
      /* Header offset */
      overflow: hidden;
    }

    .services-hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle at center, rgba(184, 134, 11, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
      pointer-events: none;
    }

    .hero-content {
      position: relative;
      z-index: 2;
      max-width: 800px;
      padding: 0 20px;
    }

    .hero-title {
      color: var(--pvc-white);
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 20px;
      letter-spacing: -1px;
    }

    .hero-subtitle {
      color: rgba(255, 255, 255, 0.8);
      font-size: 1.25rem;
      font-weight: 300;
      margin-bottom: 30px;
      letter-spacing: 0.5px;
    }

    .gold-divider {
      width: 80px;
      height: 4px;
      background: linear-gradient(90deg, var(--pvc-gold), var(--pvc-gold-light));
      margin: 0 auto;
      border-radius: 2px;
      animation: expandLine 1.5s ease-out forwards;
    }

    @keyframes expandLine {
      from {
        width: 0;
        opacity: 0;
      }

      to {
        width: 80px;
        opacity: 1;
      }
    }

    /* =========================================
       SERVICES GRID SECTION
       ========================================= */
    .services-grid-section {
      padding: 100px 0;
      background-color: var(--pvc-off-white);
    }

    .services-grid {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
    }

    .service-card {
      flex: 0 0 calc(25% - 30px);
      background: var(--pvc-white);
      border-radius: 20px;
      padding: 40px 30px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
      transition: var(--transition-smooth);
      position: relative;
      overflow: hidden;
      border: 1px solid transparent;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      height: auto;
      min-height: 100%;
    }

    .service-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 50px rgba(184, 134, 11, 0.15);
      border-color: rgba(184, 134, 11, 0.3);
    }


    .service-icon-wrapper {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: var(--pvc-off-white);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 25px;
      transition: var(--transition-smooth);
      font-size: 32px;
      color: var(--pvc-gold);
      border: 1px solid rgba(184, 134, 11, 0.15);
    }

    .service-card:hover .service-icon-wrapper {
      background: var(--pvc-gold);
      color: var(--pvc-white);
      transform: scale(1.1);
    }

    .service-card h3 {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--pvc-black);
      margin-bottom: 15px;
      text-transform: uppercase;
    }

    .service-card p {
      font-size: 0.95rem;
      color: var(--pvc-text-gray);
      line-height: 1.6;
      margin-bottom: 25px;
      flex-grow: 1;
    }

    .learn-more-btn {
      color: var(--pvc-gold);
      font-size: 0.9rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      position: relative;
      cursor: pointer;
      padding-bottom: 2px;
      transition: var(--transition-smooth);
    }

    .learn-more-btn::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 1px;
      background: var(--pvc-gold);
      transition: var(--transition-smooth);
    }

    .service-card:hover .learn-more-btn::after {
      width: 100%;
    }

    .shop-cat-title {
      font-size: 43px;
      font-weight: 700;
      color: var(--pvc-gold-dark);
      margin-bottom: 10px;
      text-transform: uppercase;
    }

    /* =========================================
       WHY CHOOSE US
       ========================================= */
    .why-us-section {
      background-color: var(--pvc-white);
      padding: 100px 0;
    }

    .section-header,
    .shop-cat-header {
      text-align: center;
      margin-bottom: 60px;
    }

    .section-header h2 {
      font-size: 43px;
      font-weight: 700;
      color: var(--pvc-gold-dark);
      margin-bottom: 20px;
      font-family: 'Poppins', sans-serif;
      letter-spacing: -0.5px;
      line-height: 1.25;
      text-transform: uppercase;
    }

    .trust-grid {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
      text-align: center;
    }

    .trust-item {
      flex: 0 0 calc(20% - 30px);
      padding: 20px;
    }

    @media (max-width: 991px) {
      .trust-item {
        flex: 0 0 calc(50% - 30px);
      }
    }

    .trust-item i {
      font-size: 40px;
      color: var(--pvc-gold);
      margin-bottom: 15px;
    }

    .trust-item h4 {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--pvc-text-dark);
    }

    /* =========================================
       PROCESS SECTION
       ========================================= */
    .process-section {
      padding: 100px 0;
      background-color: var(--pvc-off-white);
    }

    .process-timeline {
      display: flex;
      justify-content: space-between;
      position: relative;
      max-width: 1000px;
      margin: 60px auto 0;
    }

    /* Connector Line */
    .process-timeline::before {
      content: '';
      position: absolute;
      top: 25px;
      left: 50px;
      right: 50px;
      height: 2px;
      background: rgba(0, 0, 0, 0.1);
      z-index: 0;
    }

    .process-step {
      position: relative;
      z-index: 1;
      text-align: center;
      flex: 1;
    }

    .step-dot {
      width: 50px;
      height: 50px;
      background: var(--pvc-white);
      border: 2px solid var(--pvc-gold);
      border-radius: 50%;
      margin: 0 auto 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      color: var(--pvc-gold);
      transition: var(--transition-smooth);
    }

    .process-step:hover .step-dot {
      background: var(--pvc-gold);
      color: var(--pvc-white);
      box-shadow: 0 0 20px rgba(184, 134, 11, 0.4);
    }

    .process-step h4 {
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .process-step p {
      font-size: 0.9rem;
      color: #666;
      padding: 0 10px;
    }

    /* =========================================
       SERVICE AREA & CTA
       ========================================= */
    .service-area-section {
      background-color: var(--pvc-black);
      padding: 60px 0;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .service-area-text {
      color: var(--pvc-gold);
      font-size: 1.5rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .final-cta-section {
      background-color: var(--pvc-black);
      padding: 100px 0;
      text-align: center;
      color: var(--pvc-white);
    }

    .final-cta-content h2 {
      font-size: 43px;
      font-weight: 700;
      background: linear-gradient(to right, #BF953F, #FCF6BA, #B38728, #FBF5B7, #AA771C);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 40px;
    }

    .cta-buttons {
      display: flex;
      gap: 20px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn-premium {
      padding: 18px 40px;
      border: 2px solid var(--pvc-gold);
      background: transparent;
      color: var(--pvc-gold);
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      border-radius: 50px;
      transition: var(--transition-smooth);
      text-decoration: none;
      min-width: 200px;
    }

    .btn-premium:hover {
      background: var(--pvc-gold);
      color: var(--pvc-black);
      box-shadow: 0 0 30px rgba(184, 134, 11, 0.4);
    }

    .btn-premium.filled {
      background: linear-gradient(to right, #BF953F, #FCF6BA, #B38728, #FBF5B7, #AA771C);
      background-size: 200% auto;
      color: #000;
      border: none;
      box-shadow: 0 10px 20px rgba(184, 134, 11, 0.3);
    }

    .btn-premium.filled:hover {
      background-position: right center;
      transform: translateY(-3px);
      box-shadow: 0 15px 30px rgba(184, 134, 11, 0.4);
      color: #000;
    }

    /* =========================================
       RESPONSIVE
       ========================================= */
    @media (max-width: 991px) {

      .section-header h2,
      .final-cta-content h2,
      .shop-cat-title {
        font-size: 36px;
      }

      .services-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .hero-title {
        font-size: 2.5rem;
      }
    }

    @media (max-width: 767px) {

      .section-header h2,
      .final-cta-content h2,
      .shop-cat-title {
        font-size: 28px;
      }

      .services-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
      }

      .service-card {
        flex: 0 0 calc(50% - 15px);
        aspect-ratio: 1 / 1.1;
        padding: 15px 10px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
      }

      .service-card h3 {
        font-size: 0.85rem;
        margin-bottom: 5px;
        line-height: 1.2;
      }

      .service-card p {
        font-size: 0.75rem;
        margin-bottom: 0;
        line-height: 1.3;
        text-align: center;
      }

      .service-icon-wrapper {
        width: 50px;
        height: 50px;
        font-size: 22px;
        margin-bottom: 12px;
      }


      .hero-title {
        font-size: 2rem;
      }

      .process-timeline {
        flex-direction: column;
        gap: 40px;
        align-items: center;
      }

      .process-timeline::before {
        width: 2px;
        height: 100%;
        left: 50%;
        top: 0;
        transform: translateX(-50%);
      }

      .process-step {
        background: var(--pvc-off-white);
        /* cover the line behind text */
        padding: 10px 0;
        width: 100%;
      }

      .final-cta-content h2 {
        font-size: 2rem;
      }

      /* Optimizing Why Choose Us (Trust Grid) for 2-col mobile */
      .trust-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
      }

      .trust-item {
        padding: 15px;
      }

      .trust-item i {
        font-size: 30px;
        margin-bottom: 10px;
      }

      .trust-item h4 {
        font-size: 0.9rem;
      }
    }
  </style>
</head>

<body>

<?php include 'header.php'; ?>


  <!-- HERO SECTION -->
  <section class="shop-categories-section pt-5" style="background:#fff; margin-top:80px;">
    <div class="container">
      <div class="shop-cat-header" data-aos="fade-up">
        <h2 class="shop-cat-title">Professional Security Services</h2>
        <div class="eco-header-divider"></div>
        <p class="shop-cat-tagline">Complete CCTV & Surveillance Solutions by PVC Security</p>
      </div>
    </div>
  </section>

  <!-- SERVICES GRID -->
  <section class="services-grid-section">
    <div class="container">
      <div class="services-grid">
        <!-- Service 1 -->
        <div class="service-card" data-aos="fade-up" data-aos-delay="100">
          <div class="service-icon-wrapper">
            <i class="fa-solid fa-video"></i>
          </div>
          <h3>CCTV Installation</h3>
          <p>Professional installation of HD & IP camera systems for clear, uninterrupted surveillance coverage.</p>
        </div>

        <!-- Service 2 -->
        <div class="service-card" data-aos="fade-up" data-aos-delay="150">
          <div class="service-icon-wrapper">
            <i class="fa-solid fa-shield-halved"></i>
          </div>
          <h3>Security Planning</h3>
          <p>Strategic positioning and system design to eliminate blind spots and maximize security efficiency.</p>
        </div>

        <!-- Service 3 -->
        <div class="service-card" data-aos="fade-up" data-aos-delay="200">
          <div class="service-icon-wrapper">
            <i class="fa-solid fa-house-lock"></i>
          </div>
          <h3>Home Security</h3>
          <p>Smart security solutions protecting your family with 24/7 monitoring and mobile access control.</p>
        </div>

        <!-- Service 4 -->
        <div class="service-card" data-aos="fade-up" data-aos-delay="250">
          <div class="service-icon-wrapper">
            <i class="fa-solid fa-city"></i>
          </div>
          <h3>Office & Industrial</h3>
          <p>Robust surveillance for commercial spaces, factories, and warehouses ensuring asset protection.</p>
        </div>



        <!-- Service 5 -->
        <div class="service-card" data-aos="fade-up" data-aos-delay="300">
          <div class="service-icon-wrapper">
            <i class="fa-solid fa-headset"></i>
          </div>
          <h3>Maintenance</h3>
          <p>Regular system health checks, firmware updates, and comprehensive support to keep your security active.</p>
        </div>

        <!-- Service 6 -->
        <div class="service-card" data-aos="fade-up" data-aos-delay="350">
          <div class="service-icon-wrapper">
            <i class="fa-solid fa-cloud"></i>
          </div>
          <h3>Remote Access</h3>
          <p>Secure cloud backup and mobile integration for real-time alerts and remote viewing from anywhere.</p>
        </div>

        <!-- Service 7 -->
        <div class="service-card" data-aos="fade-up" data-aos-delay="400">
          <div class="service-icon-wrapper">
            <i class="fa-solid fa-screwdriver-wrench"></i>
          </div>
          <h3>AMC & Maintenance</h3>
          <p>Annual Maintenance Contracts to keep your security systems running at peak performance.</p>
        </div>

        <!-- Service 8 -->
        <div class="service-card" data-aos="fade-up" data-aos-delay="450">
          <div class="service-icon-wrapper">
            <i class="fa-solid fa-arrow-up-right-dots"></i>
          </div>
          <h3>System Upgrade</h3>
          <p>Modernize your existing analog systems to high-definition IP solutions with minimal disruption.</p>
        </div>
        <!-- Service 9 -->
        <div class="service-card" data-aos="fade-up" data-aos-delay="500">
          <div class="service-icon-wrapper">
            <i class="fa-solid fa-headset"></i>
          </div>
          <h3>Repair & Support</h3>
          <p>Quick response technical support and on-site repair services for all major security brands.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- WHY CHOOSE US -->
  <section class="why-us-section">
    <div class="container">
      <div class="section-header" data-aos="fade-up">
        <h2>Why Choose PVC Security?</h2>
        <div class="gold-divider"></div>
      </div>
      <div class="trust-grid">
        <div class="trust-item" data-aos="fade-up" data-aos-delay="100">
          <i class="fa-solid fa-user-gear"></i>
          <h4>Expert Technicians</h4>
        </div>
        <div class="trust-item" data-aos="fade-up" data-aos-delay="200">
          <i class="fa-solid fa-certificate"></i>
          <h4>Genuine Products</h4>
        </div>
        <div class="trust-item" data-aos="fade-up" data-aos-delay="300">
          <i class="fa-solid fa-comments"></i>
          <h4>After-Sales Support</h4>
        </div>
        <div class="trust-item" data-aos="fade-up" data-aos-delay="400">
          <i class="fa-solid fa-handshake"></i>
          <h4>Trusted Distributor</h4>
        </div>
        <div class="trust-item" data-aos="fade-up" data-aos-delay="500">
          <i class="fa-solid fa-bolt"></i>
          <h4>Quick Installation</h4>
        </div>
      </div>
    </div>
  </section>

  <!-- PROCESS SECTION -->
  <section class="process-section">
    <div class="container">
      <div class="section-header" data-aos="fade-up">
        <h2>Our Process</h2>
        <p>Simple steps to a secure environment</p>
      </div>

      <div class="process-timeline">
        <div class="process-step" data-aos="fade-up" data-aos-delay="100">
          <div class="step-dot">01</div>
          <h4>Requirement</h4>
          <p>We discuss your specific security needs and challenges.</p>
        </div>
        <div class="process-step" data-aos="fade-up" data-aos-delay="200">
          <div class="step-dot">02</div>
          <h4>Site Survey</h4>
          <p>Expert analysis of your premises to identify vulnerabilities.</p>
        </div>
        <div class="process-step" data-aos="fade-up" data-aos-delay="300">
          <div class="step-dot">03</div>
          <h4>Installation</h4>
          <p>Professional setup and configuration of your security system.</p>
        </div>
        <div class="process-step" data-aos="fade-up" data-aos-delay="400">
          <div class="step-dot">04</div>
          <h4>Support</h4>
          <p>Ongoing maintenance and technical assistance whenever needed.</p>
        </div>
      </div>
    </div>
  </section>



  <!-- SCRIPTS -->
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/plugins/aos.js"></script>
  <script src="assets/js/main.js"></script>

  <!--===== GLOBAL FOOTER COMPONENT =======-->
  <script src="assets/js/global_footer.js"></script>
  <!--===== END GLOBAL FOOTER =======-->
  <script>
    AOS.init({
      duration: 1000,
      once: true,
      offset: 100
    });
  </script>
</body>

</html>