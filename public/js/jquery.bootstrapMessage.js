/**
 * jquery.bootstrapMessage.js
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2012  Diogo Oliveira de Melo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
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
        $('div.alert').css('display', 'block');
        $('div.alert').fadeTo('fast', 1.0);
    }

    $.bootstrapMessageLoading = function() {
        $.bootstrapMessage('<img src="/img/loading.gif"/>', 'info');
    }

    $.bootstrapMessageOff = function() {
        $('div.alert').fadeTo('slow', 0.0, function() {
            $('div.alert').css('display', 'none');
        });
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
        $.bootstrapMessageOff();
    });

})(jQuery);
