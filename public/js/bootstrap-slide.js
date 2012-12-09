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
            if (false === $('form.search input[type=text]').is(':focus')) {
                var code = e.keyCode;
                switch (code) {
                    case 37:
                        $('.slide-prev').trigger('click');
                        break;
                    case 39:
                        $('.slide-next').trigger('click');
                        break;
                    case 32:

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
})(jQuery);
