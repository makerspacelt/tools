(function($) {
    "use strict"; // Start of use strict

    //=========================== print button ==========================
    var sendPrintReq = function(event) {
        event.preventDefault();
        var $spinner = $('#print-spinner');
        $spinner.css('display', 'inline-block');
        var $toolCode = $(document).find('#tool-code').val();
        $.post('/print', {tool_code: $toolCode}, function(data) {
                if (data.response) {
                    alert('Data loaded OK!');
                } else {
                    alert('Data loading failed!');
                }
            }, 'json');
        $spinner.css('display', 'none');
    };

    $(document).on('click', '#print-btn', sendPrintReq);
    //========================== /print button ==========================

})(jQuery); // End of use strict