; (function ($) {

  $(document).ready(function () {

    //========== CLEAN MOBILE MENU (FRESH LOGIC) ============= //
    // Toggle Menu Open/Close - Support both old and new toggle classes
    $(".clean-mobile-toggle, .mobile-menu-toggle").on("click", function () {
      $(".clean-mobile-toggle, .mobile-menu-toggle").toggleClass("active");
      $(".clean-mobile-overlay").toggleClass("active");
      $(".clean-mobile-sidebar").toggleClass("active");
    });

    // Toggle Menu Close
    $(".clean-close-btn, .clean-mobile-overlay").on("click", function () {
      $(".clean-mobile-toggle, .mobile-menu-toggle").removeClass("active");
      $(".clean-mobile-overlay").removeClass("active");
      $(".clean-mobile-sidebar").removeClass("active");
    });
    //========== END CLEAN MOBILE MENU ============= //

    //========== HEADER ACTIVE STRATS ============= //
    if ($("#header").length > 0) {
      $(window).on("scroll", function (event) {
        var scroll = $(window).scrollTop();
        if (scroll < 1) {
          $(".header-area").removeClass("sticky");
        } else {
          $(".header-area").addClass("sticky");
        }
      });
    }
    //========== HEADER ACTIVE ENDS ============= //

    //========== SIDEBAR/SEARCH AREA ============= //

    //========== RANGE AREA ============= //
    (function () {
      var parent = document.querySelector("#rangeSlider");
      if (!parent) return;

      var rangeS = parent.querySelectorAll("input[type=range]"),
        numberS = parent.querySelectorAll("input[type=number]");

      rangeS.forEach(function (el) {
        el.oninput = function () {
          var slide1 = parseFloat(rangeS[0].value),
            slide2 = parseFloat(rangeS[1].value);

          if (slide1 > slide2) {
            [slide1, slide2] = [slide2, slide1];
          }

          numberS[0].value = slide1;
          numberS[1].value = slide2;
        };
      });

      numberS.forEach(function (el) {
        el.oninput = function () {
          var number1 = parseFloat(numberS[0].value),
            number2 = parseFloat(numberS[1].value);

          if (number1 > number2) {
            var tmp = number1;
            numberS[0].value = number2;
            numberS[1].value = tmp;
          }

          rangeS[0].value = number1;
          rangeS[1].value = number2;
        };
      });
    })();

    //minus
    var buttonPlus = $(".qty-btn-plus");
    var buttonMinus = $(".qty-btn-minus");

    var incrementPlus = buttonPlus.click(function () {
      var $n = $(this)
        .parent(".qty-container")
        .find(".input-qty");
      $n.val(Number($n.val()) + 1);
    });

    var incrementMinus = buttonMinus.click(function () {
      var $n = $(this)
        .parent(".qty-container")
        .find(".input-qty");
      var amount = Number($n.val());
      if (amount > 0) {
        $n.val(amount - 1);
      }
    });
    //========== PROGRESS ACTIVE ENDS ============= //

    //========== PRICING AREA ============= //
    $("#ce-toggle").click(function (event) {
      $(".plan-toggle-wrap").toggleClass("active");
    });

    $("#ce-toggle").change(function () {
      if ($(this).is(":checked")) {
        $(".tab-content #yearly").hide();
        $(".tab-content #monthly").show();
      } else {
        $(".tab-content #yearly").show();
        $(".tab-content #monthly").hide();
      }
    });

    //========== PAGE PROGRESS STARTS ============= //
    var progressPath = document.querySelector(".progress-wrap path");
    if (progressPath) {
      var pathLength = progressPath.getTotalLength();
      progressPath.style.transition = progressPath.style.WebkitTransition =
        "none";
      progressPath.style.strokeDasharray = pathLength + " " + pathLength;
      progressPath.style.strokeDashoffset = pathLength;
      progressPath.getBoundingClientRect();
      progressPath.style.transition = progressPath.style.WebkitTransition =
        "stroke-dashoffset 10ms linear";
      var updateProgress = function () {
        var scroll = $(window).scrollTop();
        var height = $(document).height() - $(window).height();
        var progress = pathLength - (scroll * pathLength) / height;
        progressPath.style.strokeDashoffset = progress;
      };
      updateProgress();
      $(window).scroll(updateProgress);
      var offset = 50;
      var duration = 550;
      jQuery(window).on("scroll", function () {
        if (jQuery(this).scrollTop() > offset) {
          jQuery(".progress-wrap").addClass("active-progress");
        } else {
          jQuery(".progress-wrap").removeClass("active-progress");
        }
      });
      jQuery(".progress-wrap").on("click", function (event) {
        event.preventDefault();
        jQuery("html, body").animate({ scrollTop: 0 }, duration);
        return false;
      });
    }
    //========== PAGE PROGRESS STARTS ============= //


    //========== VIDEO POPUP STARTS ============= //
    if ($(".popup-youtube").length > 0) {
      if ($.fn.magnificPopup) {
        $(".popup-youtube").magnificPopup({
          type: "iframe",
        });
      }
    }
    //========== NICE SELECT ============= //
    if ($.fn.niceSelect) {
      $('select').niceSelect();
    }
    //========== AOS ANIMATION ============= //
    if (typeof AOS !== 'undefined') {
      AOS.init;
      AOS.init({ disable: 'mobile' });
    }

  });
  //========== COUNTER UP============= //
  const ucounter = $('.counter');
  if (ucounter.length > 0) {
    ucounter.countUp();
  };

  //========== TESTIMONIAL AREA ============= //
  // testimonial //
  $('.project-slider-area').owlCarousel({
    loop: true,
    margin: 30,
    nav: false,
    dots: true,
    items: 9,
    autoplay: true,
    smartSpeed: 2000,
    autoplayTimeout: 3000,
    responsiveClass: true,
    responsive: {
      0: {
        items: 1,
        nav: false,
      },
      600: {
        items: 2,
      },
      1000: {
        items: 3,
      }
    }
  });

  // BRAND //
  $('.testimonial-slider').owlCarousel({
    loop: true,
    margin: 30,
    nav: false,
    dots: false,
    items: 9,
    autoplay: true,
    smartSpeed: 2000,
    autoplayTimeout: 3000,
    responsiveClass: true,
    responsive: {
      0: {
        items: 2,
        nav: false,
      },
      600: {
        items: 4,
      },
      1000: {
        items: 5,
      }
    }
  });

  // TESTIMONIAL //
  $('.testimonial-slider-area').owlCarousel({
    loop: true,
    margin: 30,
    nav: false,
    dots: true,
    items: 9,
    autoplay: true,
    smartSpeed: 2000,
    autoplayTimeout: 3000,
    responsiveClass: true,
    responsive: {
      0: {
        items: 1,
        nav: false,
      },
      600: {
        items: 1,
      },
      1000: {
        items: 1,
      }
    }
  });

  // HEADER //
  $('.carousel-area').owlCarousel({
    loop: true,
    margin: 0,
    nav: false,
    dots: true,
    mouseDrag: false,
    items: 1,
    autoplay: true,
    navText: ["<i class='fa-solid fa-angle-up'></i>", "<i class='fa-solid fa-angle-down'></i>"],
    vertical: true,
    animateOut: 'fadeOut',
    animateIn: 'fadeIn',
    active: true,
    smartSpeed: 800,
    autoplayTimeout: 5000,
    autoplayHoverPause: false,
    responsiveClass: true,
    responsive: {
      0: {
        items: 1,
        nav: false,
      },
      600: {
        items: 1,
        nav: false,
      },
      1000: {
        items: 1,
        nav: false,
      }
    }
  });


  // SERVICE TESTIMONIAL //
  $('.service-carousel-area').owlCarousel({
    loop: true,
    margin: 30,
    nav: true,
    dots: true,
    dotsEach: true,
    slideBy: 1,
    mouseDrag: true,
    touchDrag: true,
    pullDrag: true,
    items: 10,
    navText: ["<i class='fa-solid fa-chevron-left'></i>", "<i class='fa-solid fa-chevron-right'></i>"],
    autoplay: false,
    smartSpeed: 1000,
    autoplayHoverPause: true,
    responsiveClass: true,
    responsive: {
      0: {
        items: 1,
        nav: true,
      },
      600: {
        items: 2,
      },
      1000: {
        items: 3,
      }
    }
  });

  // TESTIMONIAL //
  $('.testimonial-author-slider').owlCarousel({
    loop: true,
    margin: 30,
    nav: true,
    dots: false,
    items: 10,
    navText: ["<i class='fa-solid fa-arrow-left'></i>", "<i class='fa-solid fa-arrow-right'></i>"],
    autoplay: true,
    smartSpeed: 2000,
    autoplayTimeout: 3000,
    responsiveClass: true,
    responsive: {
      0: {
        items: 1,
        nav: true,
      },
      600: {
        items: 2,
      },
      1000: {
        items: 2,
      }
    }
  });

  // BLOG TESTIMONIAL //
  $('.blog-slider-area').owlCarousel({
    loop: true,
    margin: 30,
    nav: true,
    dots: false,
    items: 10,
    navText: ["<i class='fa-solid fa-arrow-left'></i>", "<i class='fa-solid fa-arrow-right'></i>"],
    autoplay: true,
    smartSpeed: 2000,
    autoplayTimeout: 3000,
    responsiveClass: true,
    responsive: {
      0: {
        items: 1,
        nav: true,
      },
      600: {
        items: 2,
      },
      1000: {
        items: 2,
      }
    }
  });

  //========== GSAP AREA ============= //
  $(window).on("load", function () {

    if ($('.text-anime-style-1').length) {
      let staggerAmount = 0.05,
        translateXValue = 0,
        delayValue = 0.5,
        animatedTextElements = document.querySelectorAll('.text-anime-style-1');

      animatedTextElements.forEach((element) => {
        let animationSplitText = new SplitText(element, { type: "chars, words" });
        gsap.from(animationSplitText.words, {
          duration: 1,
          delay: delayValue,
          x: 20,
          autoAlpha: 0,
          stagger: staggerAmount,
          scrollTrigger: { trigger: element, start: "top 85%" },
        });
      });
    }

    if ($('.text-anime-style-2').length) {
      let staggerAmount = 0.05,
        translateXValue = 20,
        delayValue = 0.5,
        easeType = "power2.out",
        animatedTextElements = document.querySelectorAll('.text-anime-style-2');

      animatedTextElements.forEach((element) => {
        let animationSplitText = new SplitText(element, { type: "chars, words" });
        gsap.from(animationSplitText.chars, {
          duration: 1,
          delay: delayValue,
          x: translateXValue,
          autoAlpha: 0,
          stagger: staggerAmount,
          ease: easeType,
          scrollTrigger: { trigger: element, start: "top 85%" },
        });
      });
    }

    if ($('.text-anime-style-3').length) {
      let animatedTextElements = document.querySelectorAll('.text-anime-style-3');

      animatedTextElements.forEach((element) => {
        //Reset if needed
        if (element.animation) {
          element.animation.progress(1).kill();
          element.split.revert();
        }

        element.split = new SplitText(element, {
          type: "lines,words,chars",
          linesClass: "split-line",
        });
        gsap.set(element, { perspective: 400 });

        gsap.set(element.split.chars, {
          opacity: 0,
          x: "50",
        });

        element.animation = gsap.to(element.split.chars, {
          scrollTrigger: { trigger: element, start: "top 90%" },
          x: "0",
          y: "0",
          rotateX: "0",
          opacity: 1,
          duration: 1,
          ease: Back.easeOut,
          stagger: 0.02,
        });
      });
    }


    if ($('.reveal').length) { gsap.registerPlugin(ScrollTrigger); let revealContainers = document.querySelectorAll(".reveal"); revealContainers.forEach((container) => { let image = container.querySelector("img"); let tl = gsap.timeline({ scrollTrigger: { trigger: container, toggleActions: "play none none none" } }); tl.set(container, { autoAlpha: 1 }); tl.from(container, 1.5, { xPercent: -100, ease: Power2.out }); tl.from(image, 1.5, { xPercent: 100, scale: 1.3, delay: -1.5, ease: Power2.out }); }); }

  });
  //========== GSAP AREA ============= //

  //========== PROGRESSBAR AREA ============= //
  {
    function animateElements() {
      $('.progressbar').each(function () {
        var elementPos = $(this).offset().top;
        var topOfWindow = $(window).scrollTop();
        var percent = $(this).find('.circle').attr('data-percent');
        var percentage = parseInt(percent, 10) / parseInt(100, 10);
        var animate = $(this).data('animate');
        if (elementPos < topOfWindow + $(window).height() - 10 && !animate) {
          $(this).data('animate', true);
          $(this).find('.circle').circleProgress({
            startAngle: -Math.PI / 2,
            value: percent / 100,
            size: 120,
            thickness: 10,
            emptyFill: "#E8E7E6",
            fill: {
              color: '#FA8100'
            }
          }).on('circle-animation-progress', function (event, progress, stepValue) {
            $(this).find('div').text((stepValue * 100).toFixed() + "%");
          }).stop();
        }
      });
    }

    animateElements();
    $(window).scroll(animateElements);
  };

  //========== PARALLAX AREA ============= //


})(jQuery);

//========== PRELOADER (SAFE REGISTRATION WITH FALLBACK) ============= //
$(window).on("load", function () {
  setTimeout(function () {
    $(".preloader").fadeOut(400, function () {
      $(this).addClass("preloader-hidden");
    });
  }, 200);
});

// FORCE fade out after 3.5 seconds if 'load' event hangs
setTimeout(function () {
  if ($(".preloader").is(":visible")) {
    $(".preloader").fadeOut(400, function () {
      $(this).addClass("preloader-hidden");
    });
  }
}, 3500);
