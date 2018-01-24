(function ($) {
  "use strict";

  function logo_swap() {
    $("#main-header").each(function() {
      var src = $('#logo').attr('src');
      if ($(this).hasClass('et-fixed-header')) {
        src = src.replace("[white]", "[blue]");
        $("#logo").attr('src', src);
      } else {
        src = src.replace("[blue]", "[white]");
        $("#logo").attr('src', src);
      }
    });
  }

  function page_banner() {
    $("#banner_img").css('background-image', function () {
      var bg = ('url(' + $(this).data("image-src") + ')');
      return bg;
    });
    var imgH = $('#heroImg').height();
    $('#banner_curve').css("padding-bottom", imgH+"px");
  }

  function past_banner() {
    if($(document).scrollTop()>=$('#main-content').position().top){
      $('#main-header').toggleClass('past');
    }
  }

  function popupgallery() {
    $('.popup-gallery').magnificPopup({
      delegate: 'a.popup-img',
      type: 'image',
      tLoading: 'Loading image #%curr%...',
      mainClass: 'mfp-img-mobile',
      gallery: {
        enabled: true,
        navigateByImgClick: true,
        preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
      },
      image: {
        tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
        titleSrc: function(item) {
          return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';
        }
      }
    });

    $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
      disableOn: 700,
      type: 'iframe',
      mainClass: 'mfp-fade',
      removalDelay: 160,
      preloader: false,
      fixedContentPos: false
    });
  }

  function screensilder() {
      var slide = $('.slider-single');
      var slideTotal = slide.length - 1;
      var slideCurrent = -1;

      function slideInitial() {
          slide.addClass('proactivede');
          setTimeout(function() {
              slideRight();
          }, 500);
      }

      function slideRight() {
          if (slideCurrent < slideTotal) {
              slideCurrent++;
          } else {
              slideCurrent = 0;
          }

          if (slideCurrent > 0) {
              var preactiveSlide = slide.eq(slideCurrent - 1);
          } else {
              var preactiveSlide = slide.eq(slideTotal);
          }
          var activeSlide = slide.eq(slideCurrent);
          if (slideCurrent < slideTotal) {
              var proactiveSlide = slide.eq(slideCurrent + 1);
          } else {
              var proactiveSlide = slide.eq(0);

          }

          slide.each(function() {
              var thisSlide = $(this);
              if (thisSlide.hasClass('preactivede')) {
                  thisSlide.removeClass('preactivede preactive active proactive').addClass('proactivede');
              }
              if (thisSlide.hasClass('preactive')) {
                  thisSlide.removeClass('preactive active proactive proactivede').addClass('preactivede');
              }
          });
          preactiveSlide.removeClass('preactivede active proactive proactivede').addClass('preactive');
          activeSlide.removeClass('preactivede preactive proactive proactivede').addClass('active');
          proactiveSlide.removeClass('preactivede preactive active proactivede').addClass('proactive');
      }

      function slideLeft() {
          if (slideCurrent > 0) {
              slideCurrent--;
          } else {
              slideCurrent = slideTotal;
          }

          if (slideCurrent < slideTotal) {
              var proactiveSlide = slide.eq(slideCurrent + 1);
          } else {
              var proactiveSlide = slide.eq(0);
          }
          var activeSlide = slide.eq(slideCurrent);
          if (slideCurrent > 0) {
              var preactiveSlide = slide.eq(slideCurrent - 1);
          } else {
              var preactiveSlide = slide.eq(slideTotal);
          }
          slide.each(function() {
              var thisSlide = $(this);
              if (thisSlide.hasClass('proactivede')) {
                  thisSlide.removeClass('preactive active proactive proactivede').addClass('preactivede');
              }
              if (thisSlide.hasClass('proactive')) {
                  thisSlide.removeClass('preactivede preactive active proactive').addClass('proactivede');
              }
          });
          preactiveSlide.removeClass('preactivede active proactive proactivede').addClass('preactive');
          activeSlide.removeClass('preactivede preactive proactive proactivede').addClass('active');
          proactiveSlide.removeClass('preactivede preactive active proactivede').addClass('proactive');
      }

      var left = $('.slider-left');
      var right = $('.slider-right');
      left.on('click', function() {
          slideLeft();
      });
      right.on('click', function() {
          slideRight();
      });
      slideInitial();
  }

  // function price_toggle() {

    function changePrice(priceType, currency) {
      var price = $('.edition_price')
      if (priceType == 'buy') {
        price.each(function() {
          $(this).html($(this).data('buy-'+currency));
        });
      } else if (priceType == 'monthly'){
        price.each(function() {
          $(this).html($(this).data('monthly-'+currency));
        });
      }
    }
  // }


  /*************************
  All function are called here either on page load or scroll
  *************************/
  $(document).ready(function() {
    page_banner();
    logo_swap();
    screensilder();
    popupgallery();
    // price_toggle();
    changePrice('buy', 'usd');
    $('#priceToggle').change(function() {
      if (this.checked) {
        changePrice('monthly', 'usd');
      } else {
        changePrice('buy', 'usd');
      }
    });
  });

  $(document).on('scroll', function() {
    logo_swap();
  });

  $(window).on('resize', function() {
    page_banner();
  });

})(jQuery);
