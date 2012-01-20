/**
 * A thin layer to better control bootstrap-alerts.js
 *
 * The "messageCode" can assume one of the following values:
 *      "warning"
 *      "error"
 *      "success"
 *      "info"
 */


    var MESSAGE_WARNING = 'warining';
    var MESSAGE_ERROR = 'error';
    var MESSAGE_SUCCESS = 'success';
    var MESSAGE_INFO = 'info';


(function($) {
    $.bootstrapMessage = function(text, messageCode) {
        messageCode = typeof(messageCode) == 'undefined' ? MESSAGE_INFO : messageCode;
        $('div.alert-message').removeClass(MESSAGE_WARNING)
            .removeClass(MESSAGE_ERROR)
            .removeClass(MESSAGE_SUCCESS)
            .removeClass(MESSAGE_INFO);
        $('div.alert-message').addClass(messageCode);

        $('div.alert-message p').html(text);
        $('div.alert-message').fadeTo('fast', 1.0);
    }

    $.bootstrapMessageOff = function() {
        $('div.alert-message').fadeTo('slow', 0.0);
    }

    $.bootstrapMessageAuto = function(text, messageCode) {
        $.bootstrapMessage(text, messageCode);
        setTimeout($.bootstrapMessageOff, 5000);
    }
})(jQuery);
