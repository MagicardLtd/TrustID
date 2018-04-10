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
    var third = ($('#heroImg').height() *1/3)
    var twoThirds = ($('#heroImg').height() *2/3);
    $('#banner_content').css("padding-bottom", twoThirds+"px");
    $('#banner_curve').css("margin-bottom", third+"px");
    $('.image_container').css("bottom", "-"+third+"px");
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

  function updateURL(url, sub, cur) {
    var queryParameters = {}, queryString = url,
      re = /([^&=]+)=([^&]*)/g, m;
    while (m = re.exec(queryString)) {
      queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
    }
    queryParameters['cur'] = cur;
    queryParameters['sub'] = sub;
    console.log(queryParameters);
    console.log($.param(queryParameters));
    return queryParameters;
  }

  // function price_toggle() {
  function changeCurrency(flag) {
    var val = flag.val(),
      flagDefault = $('.edition_price').data('currency'),
      url = $('.trialDownload');
    if (val != flagDefault) {
      $('.edition_price').data('currency', val);
      flag.addClass('selected');
    }
    updatePrice();
  }

  function changeSubscription(sub) {
    var subDefault = $('.edition_price').data('subscription');
    if (sub != subDefault) {
      $('.edition_price').data('subscription', sub);
    }
    updatePrice();
  }

  function updatePrice() {
    var price = $('.edition_price'),
      url = $('.trialDownload'),
      // sub = $('.edition_price').data('subscription'),
      cur = $('.edition_price').data('currency');
      // tag = (sub == 'monthly') ? '<small> /monthly</small>' : '<small> /annually</small>';
    price.each(function() {
      // $(this).html($(this).data(sub+'-'+cur)+tag);
      $(this).html($(this).data('licence-'+cur));
    });
    url.each(function() {
      var newURL = paramReplace('cur', $(this).attr("href"), cur);
      // newURL = paramReplace('sub', newURL, sub);// var params = array();
      $(this).attr("href", newURL);
    });
  }

  function scrollToID(e) {
  	$('html, body').animate({
  		scrollTop: $($(this).attr('href')).offset().top
  	}, 1250, 'easeInOutExpo');
  }

  function paramReplace(name, string, value) {
    var re = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    delimeter = re.exec(string)[0].charAt(0),
    newString = string.replace(re, delimeter + name + "=" + value);
    return newString;
  }

  function tabbar() {
    $("a", '#theTabs ul li').on('click', function(e) {
      e.preventDefault();
    });
    $('#theTabs ul li').on('click', function() {
      $('#theTabs ul li.active').removeClass('active');
      $('#theTabContent .tab-pane').removeClass('active');
      $(this).addClass('active');
      var tabPane = $(this).data('tab-pane');
      // var href = $("a", this).attr('href');
      // console.log(href);
      $(tabPane).addClass('active');
    });
  }

  /*************************
  All function are called here either on page load or scroll
  *************************/
  $(document).ready(function() {
    // $('.edition_price').data({subscription:'monthly',  currency:'usd'});
    $('.edition_price').data({subscription:'licence',  currency:'usd'});
    page_banner();
    logo_swap();
    screensilder();
    popupgallery();
    tabbar();
    $('a[href*="#"]').on('click', function(e) {
      e.preventDefault();
      scrollToID();
    });
    $("ul>li,ol>li").wrapInner("<span></span>");
    $('.currency').change(function() { changeCurrency($(this)) });
    // $('#priceToggle').change(function() {
    //   if (this.checked) {
    //     changeSubscription('annualy');
    //   } else {
    //     changeSubscription('monthly');
    //   }
    // });
  });

  $(document).on('scroll', function() {
    logo_swap();
  });

  $(window).on('resize', function() {
    page_banner();
  });

})(jQuery);
