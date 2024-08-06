'use strict';


//----------------------------------------------------/
// TheSaaS
//----------------------------------------------------/
//
+function($, window){

  var thesaas = {
    name:       'TheSaaS',
    version:    '1.2.0',
  };

  thesaas.defaults = {
    googleApiKey: null,
    googleAnalyticsId: null,
    smoothScroll: true,
  }

  // Breakpoint values
  //
  thesaas.breakpoint = {
    xs: 576,
    sm: 768,
    md: 992,
    lg: 1200
  };


  // Config application
  //
  thesaas.config = function(options) {
    //$.extend(true, thesaas.defaults, options);

    // Rteurn config value
    if ( typeof options === 'string' ) {
      return thesaas.defaults[options];
    }


    // Save configs
    $.extend(true, thesaas.defaults, options);


    // Make necessary changes
    //
    if ( !thesaas.defaults.smoothScroll ) {
      SmoothScroll.destroy();
    }



    // Google map
    // 
    if ( $('[data-provide~="map"]').length && window["google.maps.Map"] === undefined ) {
      $.getScript("https://maps.googleapis.com/maps/api/js?key="+ thesaas.defaults.googleApiKey +"&callback=thesaas.map");
    }


    // Google Analytics
    //
    if ( thesaas.defaults.googleAnalyticsId ) {
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', thesaas.defaults.googleAnalyticsId, 'auto');
      ga('send', 'pageview');
    }

  }



  // Initialize the application
  //
  thesaas.init = function() {


    thesaas.topbar();
    thesaas.parallax();
    thesaas.carousel();
    thesaas.scrolling();
    thesaas.counter();
    thesaas.aos();
    thesaas.typed();
    thesaas.contact();
    thesaas.constellation();


    $(document).on('click', '[data-provide~="lightbox"]', lity);


    // Video-wrapper
    $(document).on('click', '.video-wrapper .btn', function(){
      var wrapper = $(this).closest('.video-wrapper');
      wrapper.addClass('reveal');

      if ( wrapper.find('video').length )
        wrapper.find('video').get(0).play();

      if ( wrapper.find('iframe').length ) {
        var iframe = wrapper.find('iframe');
        var src = iframe.attr('src');

        if ( src.indexOf('?') > 0 )
          iframe.get(0).src += "&autoplay=1";
        else
          iframe.get(0).src += "?autoplay=1";
      }
    });



    // Upload
    //
    $(document).on('click', '.file-browser', function() {
      var browser = $(this);
      var file = browser.closest('.file-group').find('[type="file"]');
      if ( browser.hasClass('form-control') ) {
        setTimeout(function(){
          file.trigger('click');
        },300);
      }
      else {
        //console.log( $browser.closest('.file-group').find('[type="file"]').length );
        file.trigger('click');
      }
    });

    // Event to change file name after file selection
    $(document).on('change', '.file-group [type="file"]', function(){
      var input = $(this);
      var filename = input.val().split('\\').pop();
      input.closest('.file-group').find('.file-value').val(filename).text(filename).focus();
    });



    // FadeOUt header
    $(window).on('scroll', function() {
      var st = $(this).scrollTop()-200;
      $('.header.fadeout').css('opacity', (1-st/ window.innerHeight ) );
    });


  };




  //----------------------------------------------------/
  // Parallax
  //----------------------------------------------------/
  thesaas.parallax = function() {

    var is_safari = (navigator.userAgent.indexOf('Safari') != -1) && (navigator.userAgent.indexOf('Chrome') == -1);
    if (navigator.userAgent.match(/(iPod|iPhone|iPad)/) || is_safari) {
      return;
    }

    $('[data-parallax]').each(function() {
      var parallax = $(this);
      var img = parallax.data('parallax');
      var speed = 0.3;

      if ( $(this).hasClass('header') ) {
        speed = 0.6;
      }

      parallax.parallax({
        imageSrc: img,
        speed: speed,
        bleed: 50
      });

    });
    
  }



  //----------------------------------------------------/
  // Google map
  //----------------------------------------------------/
  thesaas.map = function() {

    $('[data-provide~="map"]').each(function() {

      var setting = {
        lat: '',
        lng: '',
        zoom: 13,
        markerLat: '',
        markerLng: '',
        markerIcon: '',
        style: ''
      }

      setting = $.extend(setting, thesaas.getDataOptions($(this)));

      var map = new google.maps.Map( $(this)[0], {
        center: {
          lat: Number(setting.lat),
          lng: Number(setting.lng)
        },
        zoom: Number(setting.zoom)
      });

      var marker = new google.maps.Marker({
        position: {
          lat: Number(setting.markerLat),
          lng: Number(setting.markerLng)
        },
        map: map,
        animation: google.maps.Animation.DROP,
        icon: setting.markerIcon
      });

      var infowindow = new google.maps.InfoWindow({
        content: $(this).dataAttr('info', '')
      });

      marker.addListener('click', function() {
        infowindow.open(map, marker);
      });

      switch (setting.style) {
        case 'light':
          map.set('styles', [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]);
          break;

        case 'dark':
          map.set('styles', [{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}])
          break;

        default:
          if ( Array.isArray(setting.style) ) {
            map.set('styles', setting.style);
          }
      }

    });

  }




  //----------------------------------------------------/
  // Carousel
  //----------------------------------------------------/
  thesaas.carousel = function() {

    $('.swiper-container').each(function(){
      var options = {
        autoplay: 3000,
        speed: 1000,
        loop: true,
        breakpoints: {
          // when window width is <= 640px
          480: {
            slidesPerView: 1
          }
        }
      };

      var swiper = $(this);

      if ( swiper.find('.swiper-button-next').length ) {
        options.nextButton = '.swiper-button-next';
      }

      if ( swiper.find('.swiper-button-prev').length ) {
        options.prevButton = '.swiper-button-prev';
      }

      if ( swiper.find('.swiper-pagination').length ) {
        options.pagination = '.swiper-pagination';
        options.paginationClickable = true;
      }

      options = $.extend( options, thesaas.getDataOptions(swiper));

      new Swiper ( swiper, options );
    });

  }




  //----------------------------------------------------/
  // Smooth scroll to a target element
  //----------------------------------------------------/
  thesaas.scrolling = function() {

    var topbar_height = 60;
    var html_body = $('html, body');

    // Back to top
    $(document).on( 'click', '.scroll-top', function() {
      html_body.animate({scrollTop : 0}, 600);
      $(this).blur();
      return false;
    });

    // Smoothscroll to anchor
    $(document).on('click', '[data-scrollto]', function(){
      var id = '#' + $(this).data('scrollto');
      if ( $(id).length > 0 ) {
        var offset = 0;
        if ( $('.topbar.topbar-sticky').length ) {
          offset = topbar_height;
        }
        html_body.animate({scrollTop: $(id).offset().top - offset}, 1000);
      }
      return false;
    });

    // Smoothscroll to anchor in page load
    var hash = location.hash.replace('#','');
    if (hash != '' && $("#"+hash).length > 0) {
      html_body.animate({scrollTop: $("#"+hash).offset().top - topbar_height}, 1000);
    }

  }





  //----------------------------------------------------/
  // jQuery CountTo and Count Down
  //----------------------------------------------------/
  thesaas.counter = function() {

    // CountTo
    var waypoints = $('[data-provide~="counter"]:not(.counted)').waypoint({
      handler: function(direction) {
        $(this.element).countTo().addClass('counted');
      },
      offset: '100%'
    });


    // Count Down
    $('[data-countdown]').each(function() {
      var format = '%D day%!D %H:%M:%S';
      var countdown = $(this);
      if ( countdown.hasDataAttr('format') )
        format = countdown.data('format');

      countdown.countdown( countdown.data('countdown'), function(event) {
        countdown.html(event.strftime( format ));
      } )

    });

  }




  //----------------------------------------------------/
  // Animate on scroll
  //----------------------------------------------------/
  thesaas.aos = function() {
    AOS.init({
      offset: 220,
      duration: 1500,
      disable: 'mobile'
    });
  }



  //----------------------------------------------------/
  // Topbar functionality
  //----------------------------------------------------/
  thesaas.topbar = function() {

    var body = $('body');
    $(window).on('scroll', function() {
      if ($(document).scrollTop() > 10) {
        body.addClass('body-scrolled');
      }
      else {
        body.removeClass('body-scrolled');
      }
    });


    // Topbar toggler
    // 
    $(document).on('click', '.topbar-toggler', function(){
      //body.toggleClass('topbar-reveal').prepend('<div class="topbar-backdrop"></div>');
      body.toggleClass('topbar-reveal');
      $('.topbar').prepend('<div class="topbar-backdrop"></div>');
    });

    $(document).on('click', '.topbar-backdrop', function(){
      body.toggleClass('topbar-reveal');
      $(this).remove();
    });


    // Dropdown for small screens
    //
    $(document).on('click', '.topbar-reveal .topbar-nav .nav-item', function(){
      var item = $(this);
      item.closest('.topbar-nav').find('.nav-submenu').slideUp();
      item.children('.nav-submenu').slideToggle();
    });

    // Close nav if a scrollto link clicked
    //
    $(document).on('click', '.topbar-reveal .topbar-nav .nav-link', function(){
      if ( $(this).hasDataAttr('scrollto') ) {
        body.removeClass('topbar-reveal');
        $('.topbar-backdrop').remove();
      }
    });

  }



  //----------------------------------------------------/
  // Typed
  //----------------------------------------------------/
  thesaas.typed = function() {

    $('[data-type]').each(function(){
      var el = $(this);
      var strings = el.data('type').split(',');
      var options = {
        strings: strings,
        typeSpeed: 50,
        backDelay: 800,
        loop: true
      }

      options = $.extend( options, thesaas.getDataOptions(el) );

      el.typed(options);
    });

  }



  //----------------------------------------------------/
  // Contact form
  //----------------------------------------------------/
  thesaas.contact = function() {

    $(document).on('click', '#contact-send', function(){
      
      var name = $("#contact-name").val();
      var email = $("#contact-email").val();
      var message = $("#contact-message").val();
      var dataString = 'name=' + name + '&email=' + email + '&message=' + message;
      var validEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      var error = $('#contact-error');

      if (email.length < 1) {
        error.html('Please enter your email address.').fadeIn(500);
        return;
      }

      if (!validEmail.test(email)) {
        error.html('Please enter a valid email address.').fadeIn(500);
        return;
      }

      $.ajax({
        type: "POST",
        url: "assets/php/sendmail.php",
        data: dataString,
        success: function () {
          error.fadeOut(400);
          $('#contact-success').fadeIn(1000);
          $("#contact-name, #contact-email, #contact-message").val('');
        },
        error: function() {
          error.html('There is a problem in our email service. Please try again later.').fadeIn(500);
        }
      });
    });
  }




  //----------------------------------------------------/
  // Constellation
  //----------------------------------------------------/
  thesaas.constellation = function() {
    if($(window).width() < 700){
      $('.constellation').constellation({
        distance: 40,
        star: {
          color: 'rgba(255, 255, 255, .8)',
          width: 1
        },
        line: {
          color: 'rgba(255, 255, 255, .8)',
          width: 0.2
        }
      });
    }
    else{
      $('.constellation').constellation({
        star: {
          color: 'rgba(255, 255, 255, .8)',
          width: 1
        },
        line: {
          color: 'rgba(255, 255, 255, .8)',
          width: 0.2
        }
      });
    }
  }





  //----------------------------------------------------/
  // MOBILE CHECK
  //----------------------------------------------------/
  var isMobile = {
    Android: function () {
      return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function () {
      return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function () {
      return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function () {
      return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function () {
      return navigator.userAgent.match(/IEMobile/i);
    },
    any: function () {
      return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
  };



  // Convert data-attributes options to Javascript object
  //
  thesaas.getDataOptions = function(el, castList) {
    var options = {};

    $.each( $(el).data(), function(key, value){

      key = thesaas.dataToOption(key);

      // Escape data-provide
      if ( key == 'provide' ) {
        return;
      }

      if ( castList != undefined ) {
        var type = castList[key];
        switch (type) {
          case 'bool':
            value = Boolean(value);
            break;

          case 'num':
            value = Number(value);
            break;

          case 'array':
            value = value.split(',');
            break;

          default:

        }
      }

      options[key] = value;
    });

    return options;
  }


  // Convert fooBarBaz to foo-bar-baz
  //
  thesaas.optionToData = function(name) {
    return name.replace(/([A-Z])/g, "-$1").toLowerCase();
  }


  // Convert foo-bar-baz to fooBarBaz
  //
  thesaas.dataToOption = function(name) {
    return name.replace(/-([a-z])/g, function(x){return x[1].toUpperCase();});
  }



  window.thesaas = thesaas;
}(jQuery, window);




$(function() {
  thesaas.init();
});



// Check if an element has a specific data attribute
//
jQuery.fn.hasDataAttr = function(name) {
  return $(this)[0].hasAttribute('data-'+ name);
};



// Get data attribute. If element doesn't have the attribute, return default value
//
jQuery.fn.dataAttr = function(name, def) {
  return $(this)[0].getAttribute('data-'+ name) || def;
};



// Instance search
//
//$.expr[':'] -> $.expr.pseudos
jQuery.expr[':'].search = function(a, i, m) {
  return $(a).html().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};
