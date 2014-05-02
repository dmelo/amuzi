function calculateMusicSquareSize() {
    var musicSquareWidth;
    for (var i = 1; (musicSquareWidth = $('html').width() / i) > 300; i++);


    $('.music-square:not(.incboard-cell), .music-square:not(.incboard-cell) .cover img').css('width', musicSquareWidth);
    $('.music-square:not(.incboard-cell), .music-square:not(.incboard-cell) .cover img').css('height', musicSquareWidth);
    $('.swiper-container').height($(window).height() - $('footer').height() - $('.navbar-fixed-top').height());
    $('.mozaic').width($(window).width());
}

$(document).ready(function() {
    var resultSet = new $.ResultSet();

    $(window).bind('resize', calculateMusicSquareSize);
    if ($('.mozaic').length > 0) {
        $.get(
            '/api/gettop', {},
            function(data) {
                data.forEach(function(item) {
                    console.log(item.cover);
                    $('.mozaic').append(resultSet.getMusicSquare(item));
                });

                calculateMusicSquareSize();
            }, 'json'
        );
    }
});
