$.fn.dmplaylist = function(options) {
    var defaults = {
    };

    var opts = $.extend(defaults, options);
    var play = $(this);

    $(this).html('<div id="playlist"><ol></ol></div><audio controls="true" autoplay="true"></audio>');

    $('.playlist-item').live('click', function(e) {
        e.preventDefault();
        $.fn.dmplaylist.play($(this));
    });

    $('.playlist-remove').live('click', function(e) {
        e.preventDefault();
        $.fn.dmplaylist.removeFromPlaylist($(this));
    });

    play.find('audio').bind('play', function() {
    });

    setInterval('$.fn.dmplaylist.checkEnded()', 500);
};

$.fn.dmplaylist.addToPlaylist = function(src, title) {
    $(this).find('#playlist ol').append('<li><a class="playlist-item" href="' + src + '">' + title + '</a> <a href="#" class="playlist-remove">X</a></li>');
    var a = document.getElementsByTagName('audio')[0];
    if(a.ended || a.paused)
        $(this).find('a.playlist-item').last().trigger('click');
}

$.fn.dmplaylist.play = function(aElement) {
    var href = aElement.attr('href');

    aElement.mb_miniPlayer();
}

$.fn.dmplaylist.playNext = function() {
    next = $('.playlist-item.active').parent().next().find('a');
    if(!next)
        next = $('.playlist-item').first();
    $.fn.dmplaylist.play(next);
}

$.fn.dmplaylist.checkEnded = function() {
    if(document.getElementsByTagName('audio')[0].ended)
        $.fn.dmplaylist.playNext();
}

$.fn.dmplaylist.removeFromPlaylist = function(aElement) {
    aElement.parent().remove();
}
