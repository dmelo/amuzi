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
    };

    $.slideNext = function() {
        var slideWidth = $('body').width();
        $('.slide').each(function() {
            var offset = $(this).offset();
            offset.left -= slideWidth;
            $(this).animate(offset);
        });
    };

    $.slidePrev = function() {
        var slideWidth = $('body').width();
        $('.slide').each(function() {
            var offset = $(this).offset();
            offset.left += slideWidth;
            $(this).animate(offset);
        });
    };

})(jQuery);
