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

})(jQuery); // End of use strict
