
// Auto-scroll brands carousel when section comes into view
document.addEventListener('DOMContentLoaded', function () {
const brandSection = document.getElementById('brand-partners');
const brandCarousel = $('#brandCarousel');

// Initialize carousel with autoplay
brandCarousel.owlCarousel({
loop: true,
margin: 30,
nav: false,
dots: false,
autoplay: true,
autoplayTimeout: 1500,
autoplaySpeed: 700,
autoplayHoverPause: true,
responsive: {
0: { items: 2 },
600: { items: 3 },
1000: { items: 5 }
}
});

// Observe when section enters viewport
const observer = new IntersectionObserver((entries) => {
entries.forEach(entry => {
if (entry.isIntersecting) {
    brandCarousel.trigger('play.owl.autoplay');
}
});
}, { threshold: 0.3 });

if (brandSection) {
observer.observe(brandSection);
}
});

document.addEventListener('DOMContentLoaded', function () {
    $('.feedback-carousel').owlCarousel({
    loop: true,
    margin: 30,
    nav: false,
    dots: true,
    autoplay: true,
    autoplayTimeout: 3000,
    autoplaySpeed: 1000,
    smartSpeed: 800,
    responsive: {
        0: {
        items: 1
        },
        768: {
        items: 2
        },
        1000: {
        items: 3
        }
    }
    });
});
      
/* Quick Links Auto-Scroll + Manual Swipe (Mobile Only) */

    (function () {
      const container = document.querySelector('#quick-links .container');
      const row = document.querySelector('.quick-links-row');
      if (!container || !row) return;

      let scrollInterval = null;
      let resumeTimeout = null;

      function checkLoop() {
        const limit = row.scrollWidth / 2;
        if (container.scrollLeft >= limit) {
          container.scrollLeft = 0;
        }
      }

      function startScrolling() {
        if (scrollInterval) return;
        scrollInterval = setInterval(() => {
          container.scrollLeft += 1;
          checkLoop();
        }, 25);
      }

      function stopScrolling() {
        if (scrollInterval) {
          clearInterval(scrollInterval);
          scrollInterval = null;
        }
      }

      function onTouchStart() {
        stopScrolling();
        if (resumeTimeout) {
          clearTimeout(resumeTimeout);
          resumeTimeout = null;
        }
      }

      function onTouchEnd() {
        if (resumeTimeout) clearTimeout(resumeTimeout);
        resumeTimeout = setTimeout(() => {
          startScrolling();
        }, 2000);
      }

      function initMobileAutoScroll() {
        if (scrollInterval) return;

        // Force duplicates to display on mobile for seamless loop
        row.querySelectorAll('.quick-link-duplicate').forEach(el => {
          el.style.setProperty('display', 'flex', 'important');
        });

        container.scrollLeft = 0;

        container.addEventListener('scroll', checkLoop, { passive: true });
        container.addEventListener('touchstart', onTouchStart, { passive: true });
        container.addEventListener('touchend', onTouchEnd, { passive: true });
        container.addEventListener('touchcancel', onTouchEnd, { passive: true });

        startScrolling();
      }

      function cleanupMobileAutoScroll() {
        stopScrolling();
        if (resumeTimeout) {
          clearTimeout(resumeTimeout);
          resumeTimeout = null;
        }

        // Restore duplicate items display back to desktop CSS default (none)
        row.querySelectorAll('.quick-link-duplicate').forEach(el => {
          el.style.removeProperty('display');
        });

        container.removeEventListener('scroll', checkLoop);
        container.removeEventListener('touchstart', onTouchStart);
        container.removeEventListener('touchend', onTouchEnd);
        container.removeEventListener('touchcancel', onTouchEnd);

        container.scrollLeft = 0;
      }

      const mobileQuery = window.matchMedia("(max-width: 768px)");

      function handleResize() {
        if (mobileQuery.matches) {
          initMobileAutoScroll();
        } else {
          cleanupMobileAutoScroll();
        }
      }

      if (mobileQuery.addEventListener) {
        mobileQuery.addEventListener("change", handleResize);
      } else if (mobileQuery.addListener) {
        mobileQuery.addListener(handleResize);
      }
      handleResize();
    })();

    
    AOS.init({
      duration: 1000,
      once: true
    });

    // Premium Icon Tilt Interaction
    const interactiveElements = document.querySelectorAll('.service-icon, .progres-section-area .check, .site-logo img, .social-links a, .service-boxarea .icons, .contact-boxarea .img1, .product-img-box');
    interactiveElements.forEach(el => {
      el.addEventListener('mousemove', e => {
        const rect = el.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        let tiltAmount = 6;
        if (el.classList.contains('product-img-box')) tiltAmount = 15; // More pronounced for the hardware box

        const rotateX = (y - centerY) / tiltAmount;
        const rotateY = (centerX - x) / tiltAmount;
        el.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
        el.style.transition = 'transform 0.1s ease-out';
      });
      el.addEventListener('mouseleave', () => {
        el.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
        el.style.transition = 'transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
      });
    });

    $('.hero-slider-desktop').owlCarousel({
      items: 1,
      loop: true,
      autoplay: true,
      autoplayTimeout: 1500,
      autoplayHoverPause: false,
      nav: false,
      dots: true
    });

    $('.hero-slider-mobile').owlCarousel({
      items: 1,
      loop: true,
      autoplay: true,
      autoplayTimeout: 1500,
      autoplayHoverPause: false,
      nav: false,
      dots: true
    });
