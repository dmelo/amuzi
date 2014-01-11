/**
 * jquery.subtitle.js
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2014  Diogo Oliveira de Melo
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
            var ul = $('<ul class="subtitle"><h4>Artists:</h4></ul>');
            if(typeof sTop !== 'undefined') {
                ul.css('top', sTop);
            }

            if(typeof sLeft !== 'undefined') {
                ul.css('left', sLeft);
            }

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

            if (subtitles.indexOf(subtitles) in counter) {
                counter[subtitles.indexOf(subtitle)]++;
            } else {
                counter[subtitles.indexOf(subtitle)] = 1;
            }
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
