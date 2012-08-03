(function($, undefined) {
    $.fn.extend({
        subtitleInit: function(sTop, sLeft) {
            var ul = $('<ul class="subtitle"></ul>');
            ul.css('top', sTop);
            ul.css('left', sLeft);
            $(this).html(ul);
        },
        subtitleAdd: function(color, subtitle) {
            var e = $('<li><span class="subtitle-color">&nbsp;&nbsp;&nbsp;</span><span class="subtitle-label"> - ' + subtitle + '</span></li>');
            e.find('.subtitle-color').css('background-color', color);
            $(this).find('ul').append(e);
        }
    });
})(jQuery);
