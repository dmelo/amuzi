(function($, undefined) {
    var colors = ['blue', 'green', 'red', 'yellow', 'gray'];
    var subtitles = [];
    $.fn.extend({
        subtitleInit: function(sTop, sLeft) {
            var ul = $('<ul class="subtitle"></ul>');
            if(typeof sTop !== 'undefined')
                ul.css('top', sTop);
            if(typeof sLeft !== 'undefined')
                ul.css('left', sLeft);
            ul.css('display', 'none');
            $(this).html(ul);
        },
        subtitleAdd: function(subtitle) {
            subtitles.push(subtitle);
            var e = $('<li><span class="subtitle-color">&nbsp;&nbsp;&nbsp;</span><span class="subtitle-label"> - ' + subtitle + '</span></li>');
            e.find('.subtitle-color').css('background-color', this.subtitleGetColor(subtitle));
            $(this).find('ul').append(e);
            $(this).find('ul').css('display', 'block');
        },
        subtitleGetColor: function(subtitle) {
            return colors[subtitles.indexOf(subtitle)];
        }

    });
})(jQuery);
