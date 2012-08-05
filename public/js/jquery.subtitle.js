(function($, undefined) {
    var colors = ['blue', 'green', 'red', 'yellow', 'gray'];
    $.fn.extend({
        subtitleInit: function(sTop, sLeft) {
            var ul = $('<ul class="subtitle"></ul>');
            if(typeof sTop !== 'undefined')
                ul.css('top', sTop);
            if(typeof sLeft !== 'undefined')
                ul.css('left', sLeft);
            $(this).html(ul);
        },
        subtitleAdd: function(subtitle) {
            var e = $('<li><span class="subtitle-color">&nbsp;&nbsp;&nbsp;</span><span class="subtitle-label"> - ' + subtitle + '</span></li>');
            e.find('.subtitle-color').css('background-color', color);
            $(this).find('ul').append(e);
        },
        substitleGetColor: function(subtitle) {
        }

    });
})(jQuery);
