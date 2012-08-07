(function($, undefined) {
    var colors = ['green', 'red', 'yellow', 'gray', 'pink', 'black', '#0ff', '#f0f'];
    var subtitles = [];
    var counter = [];
    $.fn.extend({
        subtitleInit: function(sTop, sLeft) {
            var ul = $('<ul class="subtitle"></ul>');
            if(typeof sTop !== 'undefined')
                ul.css('top', sTop);
            if(typeof sLeft !== 'undefined')
                ul.css('left', sLeft);
            ul.css('display', 'none');
            $(this).html(ul);
            subtitles = [];
            counter = [];
        },
        subtitleAdd: function(subtitle) {
            if(subtitles.indexOf(subtitle) == -1) {
                subtitles.push(subtitle);
                if(typeof this.subtitleGetColor(subtitle) !== 'undefined') {
                    var e = $('<li><span class="subtitle-color">&nbsp;&nbsp;&nbsp;</span><span class="subtitle-label"> - ' + subtitle + '</span></li>');
                    e.find('.subtitle-color').css('background-color', this.subtitleGetColor(subtitle));
                    $(this).find('ul').append(e);
                    $(this).find('ul').css('display', 'block');
                }
            }

            if(typeof(counter[subtitles.indexOf(subtitle)]) == 'undefined')
                counter[subtitles.indexOf(subtitle)] = 1;
            else
                counter[subtitles.indexOf(subtitle)]++;

        },
        subtitleGetColor: function(subtitle) {
            return colors[subtitles.indexOf(subtitle)];
        },
        subtitleCounter: function() {
            return counter;
        }
    });
})(jQuery);
