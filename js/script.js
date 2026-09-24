$(function() {
  $('a[href*="#"]:not([href="#"])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    }
  });
});


$(function () {
		
		var filterList = {
		
			init: function () {
			
				// MixItUp plugin
				// http://mixitup.io
				if ($('#portfoliolist .portfolio').length) {
					$('#portfoliolist').mixitup({
						targetSelector: '.portfolio',
						filterSelector: '.filter',
						effects: ['fade'],
						easing: 'snap',
						// call the hover effect
						onMixEnd: filterList.hoverEffect()
					});
				}
			
			},
			
			hoverEffect: function () {
			
				// Simple parallax effect
				$('#portfoliolist .portfolio').hover(
					function () {
						$(this).find('.label').stop().animate({bottom: 0}, 200, 'easeOutQuad');
						$(this).find('img').stop().animate({top: -30}, 500, 'easeOutQuad');				
					},
					function () {
						$(this).find('.label').stop().animate({bottom: -40}, 200, 'easeInQuad');
						$(this).find('img').stop().animate({top: 0}, 300, 'easeOutQuad');								
					}		
				);				
			
			}

		};
		
		// Run the show!
		filterList.init();
		
		
	});	
	
$(function() {
  try {
    if ($('.bxslider').length && typeof $.fn.bxSlider === 'function') {
      $('.bxslider').bxSlider({
        minSlides: 1,
        maxSlides: 3,
        slideWidth: 390,
        slideMargin: 10
      });
    }
  } catch (e) {
    if (window.console && console.warn) { console.warn('bxSlider init skipped:', e); }
  }
});


//animation effect(waypoint)
// Initialize Waypoint animations on DOM ready
// If Waypoints is missing/broken, show content immediately so text never stays invisible.
$(function() {
    function showAll() {
        $('.os-animation, .staggered-animation').addClass('animated');
    }

    if (typeof $.fn.waypoint !== 'function') {
        showAll();
        return;
    }

    function onScrollInit( items, trigger ) {
        items.each( function() {
            var osElement = $(this),
                osAnimationClass = osElement.attr('data-os-animation'),
                osAnimationDelay = osElement.attr('data-os-animation-delay');
              
                osElement.css({
                    '-webkit-animation-delay':  osAnimationDelay,
                    '-moz-animation-delay':     osAnimationDelay,
                    'animation-delay':          osAnimationDelay
                });

                var osTrigger = ( trigger ) ? trigger : osElement;

                try {
                osTrigger.waypoint(function() {
                    osElement.addClass('animated').addClass(osAnimationClass);
                    },{
                        triggerOnce: true,
                        offset: '90%'
                });
                } catch (e) {
                    osElement.addClass('animated').addClass(osAnimationClass);
                }
            });
    }

    onScrollInit( $('.os-animation') );
    onScrollInit( $('.staggered-animation'), $('.staggered-animation-container') );

    // Safety net: never leave content invisible. If a waypoint never fires
    // (e.g. plugin CDN issue), reveal everything after 4s.
    setTimeout(function() {
        $('.os-animation:not(.animated), .staggered-animation:not(.animated)').each(function() {
            var el = $(this);
            try {
                if (el.offset() && el.offset().top < ($(window).scrollTop() + $(window).height() + 200)) {
                    el.addClass('animated').addClass(el.attr('data-os-animation') || 'fadeIn');
                }
            } catch (e) {
                el.addClass('animated');
            }
        });
    }, 4000);
});

// Fade out preloader on DOM ready
$(function() {
    $(".se-pre-con").fadeOut("slow");
});

// Image fallback without inline handlers (CSP-safe replacement for
// onerror="this.src=..."). Binds error handling plus a check for images
// that already failed before binding.
$(function() {
    $('img[data-fallback]').each(function() {
        var img = this,
            $img = $(this);
        $img.on('error', function() {
            var fb = $img.attr('data-fallback');
            $img.removeAttr('data-fallback');
            if (fb && img.src !== fb) { img.src = fb; }
        });
        if (img.complete && typeof img.naturalWidth !== 'undefined' && img.naturalWidth === 0) {
            $img.trigger('error');
        }
    });
});

// CSRF token injection: static HTML forms can't embed a server-side token,
// so fetch it once per page view and attach it to every same-origin PHP form.
$(function() {
    try {
        if (!window.fetch) { return; }
        fetch('csrf-token.php', { credentials: 'same-origin' })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data || !data.token) { return; }
                $('form[action$=".php"]').each(function () {
                    var $f = $(this);
                    if ($f.find('input[name="csrf_token"]').length) { return; }
                    $('<input>').attr({ type: 'hidden', name: 'csrf_token', value: data.token }).appendTo($f);
                });
            })
            .catch(function () { /* forms will 403 with a reload hint; never break the page */ });
    } catch (e) { /* no-op */ }
});

// Accordion toggles + deep-link opener (moved from inline <script> blocks for CSP)
var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
      acc[i].addEventListener("click", function () {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.display === "block") {
          panel.style.display = "none";
        } else {
          panel.style.display = "block";
        }
      });
    }

document.addEventListener("DOMContentLoaded", function () {
      var hash = window.location.hash.substring(1);
      if (hash) {
        var targetAccordion = document.getElementById(hash);
        if (targetAccordion && targetAccordion.classList.contains("accordion")) {
          targetAccordion.classList.add("active");
          var panel = targetAccordion.nextElementSibling;
          panel.style.display = "block";
          targetAccordion.scrollIntoView({ behavior: "smooth", block: "center" });
        }
      }
    });
