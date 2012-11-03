(function($, undefined) {


    var colors = [
        '#ff0000',
        '#00ff00',
        '#ff8000',
        '#d800ff',
        '#ffff00',
        '#00ffff',
        '#ff00ff',
        '#a0ff00',
        '#ff00a0',
    ];
    var subtitles = [];
    var counter = [];
    var colorLimit = 0;
    $.fn.extend({
        getRandomColor: function() {
            function c() {
                return Math.floor(Math.random()*256).toString(16)
            }
            return "#"+c()+c()+c();
        },
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
            colorLimit = 0;
        },
        subtitleAdd: function(subtitle) {
            if(subtitles.indexOf(subtitle) == -1) {
                subtitles.push(subtitle);
                if(typeof this.subtitleGetColor(subtitle) !== 'undefined') {
                    var e = $('<li artist="' + subtitle + '"><span class="subtitle-color">&nbsp;&nbsp;&nbsp;</span><span class="subtitle-label"> - ' + subtitle + '</span></li>');
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
            var i = subtitles.indexOf(subtitle);
            if (typeof colors[i] === 'undefined') {
                colors[i] = this.getRandomColor();
            }

            if (i > colorLimit && $('.subtitle').offset().top + $('.subtitle').height() + 26 < $(window).height() - 30) {
                colorLimit = i;
            }

            if (colorLimit >= i) {
                return colors[i];
            }
        },
        subtitleCounter: function() {
            return counter;
        }
    });
})(jQuery);
