/**
 * A thin layer to better control bootstrap-alerts.js
 *
 * The "messageCode" can assume one of the following values:
 *      "warning"
 *      "error"
 *      "success"
 *      "info"
 */


    var MESSAGE_WARNING = 'alert-warining';
    var MESSAGE_ERROR = 'alert-error';
    var MESSAGE_SUCCESS = 'alert-success';
    var MESSAGE_INFO = 'alert-info';


(function($) {
    $.bootstrapMessage = function(text, messageCode) {
        messageCode = typeof(messageCode) == 'undefined' ? MESSAGE_INFO : messageCode;
        $('div.alert').removeClass(MESSAGE_WARNING)
            .removeClass(MESSAGE_ERROR)
            .removeClass(MESSAGE_SUCCESS)
            .removeClass(MESSAGE_INFO);
        $('div.alert').addClass('alert-' + messageCode);

        $('div.alert p').html(text);
        $('div.alert').fadeTo('fast', 1.0);
    }

    $.bootstrapMessageLoading = function() {
        $.bootstrapMessage('<img src="img/loading.gif"/>', 'info');
    }

    $.bootstrapMessageOff = function() {
        $('div.alert').fadeTo('slow', 0.0);
    }

    $.bootstrapMessageAuto = function(text, messageCode) {
        $.bootstrapMessage(text, messageCode);
        setTimeout($.bootstrapMessageOff, 5000);
    }

    $('.close-lightly').live('click', function(e) {
        $(this).parent().fadeTo('slow', 0.0);
    });

    $('.close').live('click', function(e) {
        e.preventDefault();
    });

})(jQuery);
