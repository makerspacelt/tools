(function($) {
    "use strict"; // Start of use strict

    //=========================== print button ==========================
    var sendPrintReq = function(event) {
        event.preventDefault();
        var $spinner = $('#print-spinner');
        $spinner.css('display', 'inline-block');
        var $toolCode = $('#tool-code').text();
        $.post('/print', {tool_code: $toolCode}, function(data) {
                if (!data.response) {
                    $('#error-modal-label').text("Data loading failed!");
                    $('#error-msg').text(data.error_msg);
                    $('#error-modal').modal('show');
                }
                $spinner.css('display', 'none');
            }, 'json');
    };

    $(document).on('click', '#print-btn', sendPrintReq);
    //========================== /print button ==========================

})(jQuery); // End of use strict