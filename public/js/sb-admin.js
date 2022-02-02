(function ($) {
    "use strict"; // Start of use strict

    // Toggle the side navigation
    $("#sidebarToggle").click(function (e) {
        e.preventDefault();
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");
    });

    // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
    $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function (e) {
        if ($window.width() > 768) {
            var e0 = e.originalEvent,
                delta = e0.wheelDelta || -e0.detail;
            this.scrollTop += (delta < 0 ? 1 : -1) * 30;
            e.preventDefault();
        }
    });

    // Scroll to top button appear
    $(document).scroll(function () {
        var scrollDistance = $(this).scrollTop();
        if (scrollDistance > 100) {
            $('.scroll-to-top').fadeIn();
        } else {
            $('.scroll-to-top').fadeOut();
        }
    });

    // Smooth scrolling using jQuery easing
    $(document).on('click', 'a.scroll-to-top', function (event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: ($($anchor.attr('href')).offset().top)
        }, 1000, 'easeInOutExpo');
        event.preventDefault();
    });

    $(document).ready(function () {

        $('#toolsDataTable').DataTable({
            "columnDefs": [
                {"orderable": false, "targets": [0, -1]}
            ],
            "order": [[1, "asc"]]
        });

        $('#tagsDataTable').DataTable({
            "columnDefs": [
                {"orderable": false, "targets": -1}
            ],
            "order": [[1, "desc"]]
        });

        // https://github.com/underovsky/jquery-tagsinput-revisited
        let tagsInput = $('.tagsinput#form_tags_tags')
        if (tagsInput.length) {
            tagsInput.tagsInput({
                placeholder: 'Tool tags',
                minChars: 3,
                delimiter: [',', ';', '|'],
                unique: true,
                removeWithBackspace: true,
                validationPattern: new RegExp('^[a-z\-]+$', 'i'),
                autocomplete: {
                    source: '/admin/tags/tags-autocomplete',
                    minLength: 2,
                    delay: 0
                }
            });
        }

    });

    //======================= dynamic tool params =======================
    var addParamGroup = function (event) {
        var list = jQuery('#tool_params_list');
        var counter = list.data('widget-counter') || list.children().length;
        var newWidget = list.attr('data-prototype');
        newWidget = newWidget.replace(/_newparameteritem_/g, counter);
        counter++;
        list.data('widget-counter', counter);
        var newElem = jQuery(list.attr('data-widget-tags')).html(newWidget);
        newElem.appendTo(list);
    };

    var removeParamGroup = function (event) {
        event.preventDefault();
        var $formGroup = $(this).closest('.tool_param_group');
        $formGroup.remove();
    };

    $(document).on('click', '.btn-add-param', addParamGroup);
    $(document).on('click', '.btn-remove-param', removeParamGroup);
    //====================== /dynamic tool params =======================

    //======================= dynamic tool logs =========================
    var addLog = function (event) {
        event.preventDefault();
        var list = jQuery('#tool_log_list');
        var counter = list.data('widget-counter') || list.children().length;
        var newWidget = list.attr('data-prototype');
        newWidget = newWidget.replace(/_newlogitem_/g, counter);
        counter++;
        list.data('widget-counter', counter);
        var newElem = jQuery(list.attr('data-widget-tags')).html(newWidget);
        newElem.appendTo(list);
    };

    var removeLog = function (event) {
        event.preventDefault();
        var $formGroup = $(this).closest('.tool_log_group');
        $formGroup.remove();
    };

    $(document).on('click', '.btn-add-log', addLog);
    $(document).on('click', '.btn-remove-log', removeLog);
    //======================= /dynamic tool logs ========================


})(jQuery); // End of use strict
