(function($, undefined) {

    $.slideInit = function() {
        var slideWidth = $('body').width();
        $('.slide').width(slideWidth);

        $('.alert').css('left', (slideWidth - 600) / 2);

        var left = 0;
        $('.slide').each(function() {
            $(this).css('left', left);
            left += slideWidth;
        });

        $('.slidegroup').append('<div class="slide-next slide-button active"></div>');
        $('.slidegroup').append('<div class="slide-prev slide-button"></div>');

        $(document).keyup(function (e) {
            var code = e.keyCode;
            console.log(code);
            switch (code) {
                case 37:
                    $('.slide-prev').trigger('click');
                    break;
                case 39:
                    $('.slide-next').trigger('click');
                    break;
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
            $('#screen-search img').attr('src', '/img/search.png');
            $('#screen-music img').attr('src', '/img/music-dark.png');
            $.slidePrev();
        });

        $('.slide-next.active').live('click', function(e) {
            e.preventDefault();
            $('#screen-search img').attr('src', '/img/search-dark.png');
            $('#screen-music img').attr('src', '/img/music.png');
            $.slideNext();
        });

        $('#screen-search').html('<img src="/img/search.png"/>');
        $('#screen-music').html('<img src="/img/music-dark.png"/>');

    };

    $.slideNext = function() {
        var slideWidth = $('body').width();
        $('.slide').each(function() {
            var offset = $(this).offset();
            offset.left -= slideWidth;
            $('.slide-next').removeClass('active');
            $('.slide-prev').addClass('active');
            $(this).animate(offset);
        });
    };

    $.slidePrev = function() {
        var slideWidth = $('body').width();
        $('.slide').each(function() {
            var offset = $(this).offset();
            offset.left += slideWidth;
            $('.slide-prev').removeClass('active');
            $('.slide-next').addClass('active');
            $(this).animate(offset);
        });
    };

})(jQuery);
