<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - PVC Global Security | Leading AIoT Surveillance</title>
    <meta name="description"
        content="Learn about PVC Security's mission to lead the future of AIoT through innovative surveillance technology and reliable security infrastructure in Andhra Pradesh & Telangana.">
    <link rel="shortcut icon" href="assets/img/logo/Untitled design-3.png" type="image/x-icon">

    <!-- Plugins -->
    <link rel="stylesheet" href="assets/css/plugins/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/plugins/fontawesome.css">
    <link rel="stylesheet" href="assets/css/plugins/aos.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/header_styles.css">

    <!-- FontAwesome 6 (Ensure loaded) -->
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
            color: var(--pvc-text-dark);
        }

        /* =========================================
       HERO SECTION
       ========================================= */
        .about-page-hero {
            background-color: var(--pvc-black);
            height: 60vh;
            min-height: 450px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            margin-top: 70px;
            /* Header offset */
            overflow: hidden;
        }

        .about-page-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(184, 134, 11, 0.1) 0%, rgba(0, 0, 0, 0) 60%);
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
       WHO WE ARE
       ========================================= */
        .who-we-are {
            padding: 40px 0 20px;
            background: var(--pvc-white);
        }

        .text-content h2 {
            font-size: 43px;
            font-weight: 700;
            color: var(--pvc-gold-dark);
            margin-bottom: 30px;
            letter-spacing: -0.5px;
            line-height: 1.25;
            text-transform: uppercase;
        }

        .text-content p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--pvc-text-gray);
            margin-bottom: 20px;
        }

        .gold-highlight {
            color: var(--pvc-gold);
            font-weight: 600;
        }

        /* =========================================
       WORKSPACE SECTION (IMAGES)
       ========================================= */
        .workspace-section {
            padding: 100px 0 60px;
            background: var(--pvc-off-white);
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-header h2 {
            font-size: 43px;
            font-weight: 700;
            color: var(--pvc-gold-dark);
            margin-bottom: 15px;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .section-header p {
            color: var(--pvc-text-gray);
            font-size: 1.1rem;
        }

        .shop-images-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        @media (max-width: 767px) {
            .shop-images-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 5px;
            }

            .shop-img-card {
                aspect-ratio: 1 / 1;
            }

            .shop-img-card img {
                height: 100%;
            }
        }

        .shop-img-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: var(--transition-smooth);
            position: relative;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .shop-img-card img {
            width: 100%;
            height: 350px;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
            display: block;
        }

        .shop-img-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(184, 134, 11, 0.15);
            border-color: rgba(184, 134, 11, 0.5);
        }

        .shop-img-card:hover img {
            transform: scale(1.05);
        }

        .img-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            padding: 30px 20px 20px;
            color: #fff;
            text-align: center;
            font-size: 0.95rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            opacity: 0;
            transform: translateY(10px);
            transition: var(--transition-smooth);
        }

        .shop-img-card:hover .img-caption {
            opacity: 1;
            transform: translateY(0);
        }


        /* =========================================
       MISSION & VISION
       ========================================= */
        .mission-vision-section {
            padding: 20px 0 100px;
            background: var(--pvc-white);
        }

        .mv-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }

        @media (max-width: 767px) {
            .mv-grid {
                grid-template-columns: 1fr 1fr;
                /* Explicitly keep 2-grid */
                gap: 15px;
            }

            .mv-card {
                padding: 15px 10px;
                aspect-ratio: 1 / 1;
                /* Force square proportions on mobile */
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .mv-card h3 {
                font-size: 0.9rem;
                margin-bottom: 5px;
                text-align: center;
            }

            .mv-card>p {
                font-size: 0.75rem;
                line-height: 1.2;
                text-align: center;
                display: -webkit-box;
                -webkit-line-clamp: 3;
                line-clamp: 3;
                /* Standard property */
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .mv-icon {
                width: 45px;
                height: 45px;
                font-size: 18px;
                margin-bottom: 10px;
            }
        }


        .mv-card {
            text-align: center;
            padding: 40px;
            background: #fff;
            border: 1px solid #f0f0f0;
            border-radius: 20px;
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
        }

        .mv-card:hover {
            z-index: 10;
            transform: translateY(-5px);
            border-color: var(--pvc-gold);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        }

        /* Hover Matter Popup style */
        .matter-popup {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #BF953F, #B38728, #AA771C);
            color: #fff !important;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform: scale(0.8) translateY(10px);
            z-index: 5;
            box-shadow: 0 15px 35px rgba(184, 134, 11, 0.4);
        }

        @media (max-width: 767px) {
            .mv-card:hover .matter-popup {
                opacity: 1;
                visibility: visible;
                transform: scale(1.05) translateY(0);
            }
        }

        .matter-popup p {
            color: #fff !important;
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 0;
            font-weight: 500;
        }

        @media (max-width: 767px) {
            .matter-popup {
                padding: 15px;
            }

            .matter-popup p {
                font-size: 0.75rem;
                line-height: 1.3;
            }
        }


        .mv-icon {
            width: 80px;
            height: 80px;
            background: rgba(184, 134, 11, 0.1);
            color: var(--pvc-gold);
            font-size: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            transition: var(--transition-smooth);
        }

        .mv-card:hover .mv-icon {
            background: var(--pvc-gold);
            color: #fff;
            transform: rotateY(180deg);
        }

        .mv-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--pvc-gold-dark);
            text-transform: uppercase;
        }

        /* =========================================
       WHY CHOOSE US GRID
       ========================================= */
        .why-us-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 60px;
        }

        .reason-card {
            flex: 0 0 calc(33.333% - 20px);
            text-align: center;
            padding: 20px;
            background: var(--pvc-off-white);
            border-radius: 12px;
            transition: var(--transition-smooth);
        }

        @media (max-width: 991px) {
            .reason-card {
                flex: 0 0 calc(50% - 20px);
            }
        }

        @media (max-width: 767px) {
            .reason-card {
                flex: 0 0 calc(33.333% - 10px);
                aspect-ratio: 1 / 1;
                /* Square proportions */
                padding: 5px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .reason-card h4 {
                font-size: 0.65rem;
                line-height: 1.1;
            }

            .reason-icon {
                font-size: 18px;
                margin-bottom: 5px;
            }
        }


        .reason-card:hover {
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transform: translateY(-5px);
        }

        .reason-icon {
            font-size: 30px;
            color: var(--pvc-gold);
            margin-bottom: 15px;
        }

        .reason-card h4 {
            font-size: 0.9rem;
            font-weight: 700;
            margin: 0;
            color: var(--pvc-text-dark);
        }

        /* =========================================
       SERVICE AREA & CTA
       ========================================= */
        .service-area-strip {
            background: var(--pvc-black);
            padding: 50px 0;
            text-align: center;
        }

        .service-area-strip h3 {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 300;
            margin: 0;
        }

        .service-area-strip span {
            color: var(--pvc-gold);
            font-weight: 700;
            display: block;
            font-size: 2rem;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .final-cta {
            background: var(--pvc-black);
            padding: 100px 0;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .final-cta h2 {
            font-size: 43px;
            font-weight: 700;
            color: var(--pvc-gold-dark);
            margin-bottom: 40px;
            text-transform: uppercase;
        }

        .gold-grad-text {
            background: linear-gradient(to right, #BF953F, #FCF6BA, #B38728, #FBF5B7, #AA771C);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-group-custom {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn-gold {
            padding: 18px 40px;
            background: linear-gradient(to right, #BF953F, #FCF6BA, #B38728, #FBF5B7, #AA771C);
            background-size: 200% auto;
            color: #000;
            font-weight: 800;
            text-transform: uppercase;
            border-radius: 50px;
            transition: all 0.4s ease;
            text-decoration: none;
            border: none;
            box-shadow: 0 10px 20px rgba(184, 134, 11, 0.3);
        }

        .btn-gold:hover {
            background-position: right center;
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(184, 134, 11, 0.4);
            color: #000;
            border: none;
        }

        .btn-outline-gold {
            padding: 18px 40px;
            background: transparent;
            color: var(--pvc-gold);
            font-weight: 800;
            text-transform: uppercase;
            border-radius: 50px;
            transition: var(--transition-smooth);
            text-decoration: none;
            border: 2px solid var(--pvc-gold);
        }

        .btn-outline-gold:hover {
            background: var(--pvc-gold);
            color: #000;
            box-shadow: 0 0 30px rgba(184, 134, 11, 0.3);
        }


        /* =========================================
       RESPONSIVE
       ========================================= */
        /* Responsive Headings */
        @media (max-width: 991px) {

            .text-content h2,
            .section-header h2,
            .final-cta h2 {
                font-size: 36px;
            }
        }

        @media (max-width: 767px) {

            .text-content h2,
            .section-header h2,
            .final-cta h2 {
                font-size: 28px;
            }
        }
    </style>

    <script src="assets/js/plugins/jquery-3-6-0.min.js"></script>
</head>

<body>

<?php include 'header.php'; ?>




    <!-- WORKSPACE SECTION -->
    <section class="workspace-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2>Inside PVC Security</h2>
                <p>A glimpse of our workspace and customer service environment</p>
            </div>

            <div class="shop-images-grid">
                <!-- Image 1 -->
                <div class="shop-img-card" data-aos="zoom-in" data-aos-delay="100">
                    <img src="assets/img/grouppic.jpg" alt="Office Display">
                    <div class="img-caption">PVC Security – Office & Display Area</div>
                </div>

                <!-- Image 2 -->
                <div class="shop-img-card" data-aos="zoom-in" data-aos-delay="200">
                    <!-- Placeholder using existing image if shop image not avail -->
                    <img src="assets/img/shop.jpeg" alt="Product Demo">
                    <div class="img-caption">Product Demonstration Section</div>
                </div>

                <!-- Image 3 -->
                <div class="shop-img-card" data-aos="zoom-in" data-aos-delay="300">
                    <img src="assets/img/shopin.jpeg" alt="Consultation Desk">
                    <div class="img-caption">Customer Support & Consultation Desk</div>
                </div>
            </div>
        </div>
    </section>
    <!-- WHO WE ARE -->
    <section class="who-we-are">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-content">
                    <div class="row">
                        <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                            <h2>Your Partner for a<br><span class="gold-grad-text">Safer Future</span></h2>
                        </div>
                        <div class="col-lg-6" data-aos="fade-left">
                            <p class="lead" style="font-weight: 500; color: #000;">PVC Security is a professional
                                security solutions provider offering CCTV cameras, surveillance systems, and complete
                                safety solutions for homes and businesses.</p>
                            <p>We are not just a seller; we are a dedicated service provider focused on <span
                                    class="gold-highlight">genuine quality</span>, professional installation, and
                                long-term customer support. Our goal is to make advanced security accessible, reliable,
                                and affordable for everyone in our region.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- MISSION & VISION -->
    <section class="mission-vision-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="mv-grid">
                        <!-- Mission -->
                        <div class="mv-card" data-aos="fade-up">
                            <div class="mv-icon">
                                <i class="fa-solid fa-bullseye"></i>
                            </div>
                            <h3>Our Mission</h3>
                            <p>To deliver reliable, high-quality, and affordable securit...</p>
                            <!-- MATTER POPUP -->
                            <div class="matter-popup">
                                <p>To deliver reliable, high-quality, and affordable security solutions that protect
                                    people, property, and assets effectively.</p>
                            </div>
                        </div>
                        <!-- Vision -->
                        <div class="mv-card" data-aos="fade-up" data-aos-delay="100">
                            <div class="mv-icon">
                                <i class="fa-solid fa-eye"></i>
                            </div>
                            <h3>Our Vision</h3>
                            <p>To become the most trusted and preferred security...</p>
                            <!-- MATTER POPUP -->
                            <div class="matter-popup">
                                <p>To become the most trusted and preferred security partner across Andhra Pradesh and
                                    Telangana for all safety needs.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TRUST ICONS -->
            <div class="why-us-grid">
                <div class="reason-card" data-aos="fade-up" data-aos-delay="100">
                    <i class="fa-solid fa-certificate reason-icon"></i>
                    <h4>Genuine Products</h4>
                </div>
                <div class="reason-card" data-aos="fade-up" data-aos-delay="200">
                    <i class="fa-solid fa-screwdriver-wrench reason-icon"></i>
                    <h4>Pro Installation</h4>
                </div>
                <div class="reason-card" data-aos="fade-up" data-aos-delay="300">
                    <i class="fa-solid fa-headset reason-icon"></i>
                    <h4>After-Sales Support</h4>
                </div>
                <div class="reason-card" data-aos="fade-up" data-aos-delay="400">
                    <i class="fa-solid fa-location-dot reason-icon"></i>
                    <h4>Trusted Local Partner</h4>
                </div>
                <div class="reason-card" data-aos="fade-up" data-aos-delay="500">
                    <i class="fa-solid fa-face-smile reason-icon"></i>
                    <h4>Customer Focused</h4>
                </div>
            </div>
        </div>
    </section>



    <!-- Scripts -->
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