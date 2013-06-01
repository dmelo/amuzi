/**
 * jquery.bootstrapMessage.js
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2013  Diogo Oliveira de Melo
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
 * Insert the following html into the page:
 *
 * <div class="alert"><a class="close" href="#">x</a><p></p></div>
 *
 * You might want to use some CSS to make it invisible at first (when no
 * message is being displayed).
 *
 * div.alert {
 *      opacity: 0;
 *      filter: alpha (opacity = 0);
 * }
 *
 * The "type" can assume one of the following values:
 *      "warning"
 *      "error"
 *      "success"
 *      "info"
 *
 *
 */

var MESSAGE_WARNING = 'alert-warining',
    MESSAGE_ERROR = 'alert-error',
    MESSAGE_SUCCESS = 'alert-success',
    MESSAGE_INFO = 'alert-info',
    DEFAULT_TIMEOUT = 5000;


(function($) {
    var MessageEntry = function(text, type, timeout) {
        var text,
            type,
            timeout;

        this.text = text;
        this.type = type;
        this.timeout = timeout;

        this.show = function() {
            console.log('show: ' + this.text + ", " + this.type + ", " + this.timeout);
            this.type = typeof(this.type) == 'undefined' ? MESSAGE_INFO : this.type;
            $('div.alert').removeClass(MESSAGE_WARNING)
                .removeClass(MESSAGE_ERROR)
                .removeClass(MESSAGE_SUCCESS)
                .removeClass(MESSAGE_INFO);
            $('div.alert').addClass('alert-' + type);

            $('div.alert p').html(this.text);
            $('div.alert').css('display', 'block');
            $('div.alert').fadeTo('fast', 1.0);
        };

        this.hide = function() {
            console.log('hide: ' + this.text + ", " + this.type + ", " + this.timeout);
            $('div.alert').fadeTo('slow', 0.0, function() {
                $('div.alert').css('display', 'none');
            });
        };
    }

    var messageQueue = [],
        currentMessage = null;

    window.messageQueue = messageQueue;
    window.currentMessage = currentMessage;

    $.bootstrapMessageRun = function() {
        // See if it's time to have the next message and switch it.
        var now = new Date().getTime();

        if (
            messageQueue.length > 0 &&
            (null === currentMessage
            || (0 === currentMessage.timeout && now - currentMessage.start >= DEFAULT_TIMEOUT)
            || (0 !== currentMessage.timeout && now - currentMessage.start >= currentMessage.timeout))
        ) { // Replace the message by a newer one.
            console.log(messageQueue);
            currentMessage = messageQueue.shift();
            currentMessage.start = now;
            currentMessage.show();
        } else if (0 === messageQueue.length && null !== currentMessage
            && 0 !== currentMessage.timeout
            && now - currentMessage.start >= currentMessage.timeout
        ) { // Hide an outlived message.
            currentMessage.hide();
            currentMessage = null;
        }
    }

    $.bootstrapMessage = function(text, type, timeout) {
        if (undefined === typeof timeout) {
            timeout = 0;
        }
        messageQueue.push(new MessageEntry(text, type, 0));
    }

    $.bootstrapMessageLoading = function() {
        $.bootstrapMessage('<img src="/img/loading.gif"/>', 'info');
    }

    $.bootstrapMessageOff = function() {
        $('div.alert').fadeTo('slow', 0.0, function() {
            $('div.alert').css('display', 'none');
        });
    }

    $.bootstrapMessageAuto = function(text, type) {
        messageQueue.push(new MessageEntry(text, type, DEFAULT_TIMEOUT));
    }

    $('.close-lightly').live('click', function(e) {
        $(this).parent().fadeTo('slow', 0.0);
    });

    $('.close').live('click', function(e) {
        e.preventDefault();
        $.bootstrapMessageOff();
    });

    $(document).ready(function() {
        setInterval($.bootstrapMessageRun, 1000);
    });
})(jQuery);
