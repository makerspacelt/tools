(function($) {
    "use strict"; // Start of use strict

    //=========================== print button ==========================
    var sendPrintReq = function(event) {
        event.preventDefault();
        $.post('/print', {tool_code: 'asadsdas'})
            .done(function(data) {
                alert('Data loaded: '+ data);
            });
    };

    $(document).on('click', '#print-btn', sendPrintReq);
    //========================== /print button ==========================

})(jQuery); // End of use strict