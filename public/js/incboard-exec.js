var incBoard = new IncBoard();

function searchMusicCallbackCenter(v) {
    $('#' + v.objId).addClass('center');
}

function loadSimilarMusic(data, num, callback) {
    $.bootstrapMessageOff();
    var total = 0;
    incBoard.similarity = data[1];
    incBoard.searchMusic(data[0], num, callback);
}

function searchSimilar(ele) {
    var type = 'undefined' === typeof ele.attr('albumid') ? 'track' : 'album';
    incBoard.searchSimilarList.push([ele.attr('artist'), ele.attr('album' === type ? 'name' : 'musicTitle'), type]);
    incBoard.incrementSimilar();
}

$(document).ready(function() {
    if (1 === $('#incboard-search').length) {
        $('.music-large').live('click', function (e) {
            searchSimilar($(this).parent());
        });

        $('.incboard-cell').live('click', function (e) {
            searchSimilar($(this));
        });
    }

    $('#incboard-search').ajaxForm({
        dataType: 'json',
        success: function (data) {
            loadSimilarMusic(data, 10);
        },
        beforeSubmit: function() {
            $('#subtitle').subtitleInit();
            incBoard.searchString = $('#q').val();
            $.bootstrapMessage('Loading...', 'info');
            incBoard.clean();
            var obj = new Object();
            obj.artist = $('#artist').val();
            obj.musicTitle = $('#musicTitle').val();
            obj.type = $('#type').val();
            if ($.isSearchFormValid()) {
                incBoard.searchMusic([obj], 1, searchMusicCallbackCenter);
            }
        }
    });

    $(window).bind('resize', $.proxy(incBoard.ibb, 'resize'));
});
