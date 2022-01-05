(function ($) {
    "use strict"; // Start of use strict

    //=========================== print button ==========================
    var sendPrintReq = function (event) {
        event.preventDefault();
        let spinner = $('#print-spinner');
        spinner.css('display', 'inline-block');
        let url = $('#print-btn').attr('href');
        $.post(url, {}, function (data) {
            if (!data.response) {
                $('#error-modal-label').text("Printing failed!");
                $('#error-msg').text(data.error_msg);
                $('#error-modal').modal('show');
            }
            spinner.css('display', 'none');
        }, 'json');
    };

    $(document).on('click', '#print-btn', sendPrintReq);
    //========================== /print button ==========================

    $(function() {
        $(window).keypress(function(e) {
            if(!$('#search').isInViewport){
                $([document.documentElement, document.body]).animate({
                    scrollTop: $("#search").offset().top - 70
                }, 500);
            }
            $('#search').focus();
        });
     });

     $.fn.isInViewport = function() {
        var elementTop = $(this).offset().top;
        var elementBottom = elementTop + $(this).outerHeight();
    
        var viewportTop = $(window).scrollTop();
        var viewportBottom = viewportTop + $(window).height();
    
        return elementBottom > viewportTop && elementTop < viewportBottom;
    };

})(jQuery); // End of use strict
