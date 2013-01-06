function calculateMusicSquareSize() {
    var musicSquareWidth;
    for (var i = 1; (musicSquareWidth = $('html').width() / i) > 300; i++);

    musicSquareWidth--;

    $('.music-square').css('width', musicSquareWidth);
    $('.music-square').css('height', musicSquareWidth);
}

$(document).ready(function() {
    var resultSet = new $.ResultSet();

    $(window).bind('resize', calculateMusicSquareSize);
    if ($('.mozaic').length > 0) {
        $.get('/api/gettop', {},
        function(data) {
            data.forEach(function(item) {
                console.log(item.cover);
                $('.mozaic').append(resultSet.getMusicSquare(item));
            });

            calculateMusicSquareSize();


        }, 'json');
    }
});
