(function($) {
  "use strict"; // Start of use strict

  // Toggle the side navigation
  $("#sidebarToggle").click(function(e) {
    e.preventDefault();
    $("body").toggleClass("sidebar-toggled");
    $(".sidebar").toggleClass("toggled");
  });

  // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
  $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
    if ($window.width() > 768) {
      var e0 = e.originalEvent,
        delta = e0.wheelDelta || -e0.detail;
      this.scrollTop += (delta < 0 ? 1 : -1) * 30;
      e.preventDefault();
    }
  });

  // Scroll to top button appear
  $(document).scroll(function() {
    var scrollDistance = $(this).scrollTop();
    if (scrollDistance > 100) {
      $('.scroll-to-top').fadeIn();
    } else {
      $('.scroll-to-top').fadeOut();
    }
  });

  // Smooth scrolling using jQuery easing
  $(document).on('click', 'a.scroll-to-top', function(event) {
    var $anchor = $(this);
    $('html, body').stop().animate({
      scrollTop: ($($anchor.attr('href')).offset().top)
    }, 1000, 'easeInOutExpo');
    event.preventDefault();
  });

  //======================= dynamic tool params =======================
    var addFormGroup = function(event) {
        event.preventDefault();
        var $formGroup = $(this).closest('.form-group');
        var $formGroupClone = $formGroup.clone();
        $(this)
            .toggleClass('btn-success btn-add btn-danger btn-remove')
            .html('<i class="fas fa-minus"></i>');
        $formGroupClone.find('input').val('');
        var $n = $('div.tool_param_group').length;
        $formGroupClone.find('#tool_name').attr('name', 'tool_param['+$n+'][\'name\']');
        $formGroupClone.find('#tool_value').attr('name', 'tool_param['+$n+'][\'value\']');
        $formGroupClone.insertAfter($formGroup);
    };

    var removeFormGroup = function(event) {
        event.preventDefault();
        var $formGroup = $(this).closest('.form-group');
        $formGroup.remove();
    };

    $(document).on('click', '.btn-add', addFormGroup);
    $(document).on('click', '.btn-remove', removeFormGroup);
    //====================== /dynamic tool params =======================

})(jQuery); // End of use strict
