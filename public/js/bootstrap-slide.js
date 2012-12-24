/**
 * bootstrap-slide.js
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
(function($, undefined) {
    $.slideLock = false;

    $.slideResize = function() {
        var slideWidth = $('body').width();
        $('.slide').width(slideWidth);

        $('.alert').css('left', (slideWidth - 600) / 2);

        var left = 0;
        $('.slide').each(function() {
            $(this).css('left', left);
            left += slideWidth;
        });

        $('.slide-prev').removeClass('active');
        $('.slide-next').addClass('active');
        $('#screen-search img').attr('src', '/img/search.png');
        $('#screen-music img').attr('src', '/img/music-dark.png');
        resizeEditPlaylist();


    };

    $.slideInit = function() {
        $.slideResize();

        $(window).bind('resize', $.slideResize);

        $('.slidegroup').append('<div class="slide-next slide-button active"></div>');
        $('.slidegroup').append('<div class="slide-prev slide-button"></div>');

        $(document).keyup(function (e) {
            if (false === $('input[type=text], textarea').is(':focus')) {
                var code = e.keyCode;
                switch (code) {
                    case 37:
                        $('.slide-prev').trigger('click');
                        break;
                    case 39:
                        $('.slide-next').trigger('click');
                        break;
                }
            }
        });

        $('#screen-search').click(function(e) {
            $('.slide-prev').trigger('click');
        });

        $('#screen-music').click(function(e) {
            $('.slide-next').trigger('click');
        });

        $('.slide-prev.active').live('click', function(e) {
            e.preventDefault();
            console.log('prev');
            $.slideMove(-1);
        });

        $('.slide-next.active').live('click', function(e) {
            e.preventDefault();
            console.log('next');
            $.slideMove(1);
        });

        $('#screen-search').html('<img src="/img/search.png"/>');
        $('#screen-music').html('<img src="/img/music-dark.png"/>');


    };

    $.slideMove = function(dir) {
        var slideWidth = $('body').width();
        if (false === $.slideLock) {
            $.slideLock = true;
            $('.slide').each(function() {
                var offset = $(this).offset();
                offset.left -= dir * slideWidth;
                $('#screen-search img').attr('src', '/img/search' + (1 === dir ? '-dark' : '') + '.png');
                $('#screen-music img').attr('src', '/img/music' + (1 === dir ? '' : '-dark') + '.png');

                $(this).animate(offset, function() {
                    $('.slide-' + (1 === dir ? 'next' : 'prev')).removeClass('active');
                    $('.slide-' + (1 === dir ? 'prev' : 'next')).addClass('active');
                    $.slideLock = false;
                });
            });
        }
    };

    $.slideGetCurrent = function() {
        return $('.slidegroup .active').hasClass('slide-prev') ?
            'search' : 'music';
    };
})(jQuery);
