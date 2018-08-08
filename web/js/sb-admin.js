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

    $(document).ready( function () {
        $('#toolsDataTable').DataTable();
        $('#tagsDataTable').DataTable();

        // https://github.com/underovsky/jquery-tagsinput-revisited
        $('.tagsinput#tool_tagsinput').tagsInput({
            placeholder: 'Tool tags',
            minChars: 3,
            delimiter: [',', ';', '|'],
            unique: true,
            removeWithBackspace: true,
            validationPattern: new RegExp('^[a-zA-Z]+$')
        });
    } );

  //======================= dynamic tool params =======================
    var addParamGroup = function(event) {
        event.preventDefault();
        var $formGroup = $(this).closest('.tool_param_group');
        var $formGroupClone = $formGroup.clone();
        $(this)
            .toggleClass('btn-success btn-add-param btn-danger btn-remove-param')
            .html('<i class="fas fa-minus"></i>');
        $formGroupClone.find('input').val('');
        var $n = $('div.tool_param_group').length;
        $formGroupClone.find('#tool_name').attr('name', 'tool_param['+$n+'][\'name\']');
        $formGroupClone.find('#tool_value').attr('name', 'tool_param['+$n+'][\'value\']');
        $formGroupClone.insertAfter($formGroup);
    };

    var removeParamGroup = function(event) {
        event.preventDefault();
        var $formGroup = $(this).closest('.tool_param_group');
        $formGroup.remove();
    };

    $(document).on('click', '.btn-add-param', addParamGroup);
    $(document).on('click', '.btn-remove-param', removeParamGroup);
    //====================== /dynamic tool params =======================

    //======================= dynamic tool logs =========================
    var addLog = function(event) {
        event.preventDefault();
        var $formGroup = $(this).closest('.tool_log');
        var $formGroupClone = $formGroup.clone();
        $(this)
            .toggleClass('btn-success btn-add-log btn-danger btn-remove-log')
            .html('<i class="fas fa-minus"></i>');
        $formGroupClone.find('textarea').val('');
        $formGroupClone.insertAfter($formGroup);
    };

    var removeLog = function(event) {
        event.preventDefault();
        var $formGroup = $(this).closest('.tool_log');
        $formGroup.remove();
    };

    $(document).on('click', '.btn-add-log', addLog);
    $(document).on('click', '.btn-remove-log', removeLog);
    //======================= /dynamic tool logs ========================

})(jQuery); // End of use strict
