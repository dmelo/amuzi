var search,
    searchSimilarList = [],
    incrementSimilarRunning = false;


function searchMusicCallbackCenter(v) {
    $('#' + v.objId).addClass('center');
}

function incrementSimilar() {
    if (true === incrementSimilarRunning) {
        setTimeout(1000, incrementSimilar);
    } else {
        if (searchSimilarList.length > 0) {
            var obj = searchSimilarList.shift(),
                artist = obj[0],
                musicTitle = obj[1],
                type = obj[2];

            incrementSimilarRunning = true;
            $.post('/api/searchsimilar', {
                q: artist + ' - ' + musicTitle,
                artist: artist,
                musicTitle: musicTitle,
                type: type,
                objIdList: search.ibb.getIdList()
            }, function(data) {
                loadSimilarMusic(data, 10);
                incrementSimilarRunning = false;
                incrementSimilar();
            }, 'json').error(function() {
                incrementSimilarRunning = false;
            });
        }
    }
}

function searchMusic(set, num, callback) {
    var m = set.shift(),
        uri,
        params;

    if (num > 0 && 'undefined' !== typeof m) {
        if ('type' in m && 'album' === m.type) {
            uri = '/api/searchalbum';
            params = {
               artist: m.artist,
               album: m.musicTitle
            };
        } else {
            uri = '/api/searchmusic',
            params = {
                artist: m.artist,
                musicTitle: m.musicTitle
            };
        }

        $.get(uri, params, function(v) {
            try {
                var start = new Date().getTime();
                if (null !== v && true === search.insert(v)) {
                    if ('function' === typeof callback) {
                        callback(v, set, num);
                    }
                    searchMusic(set, num - 1);
                } else {
                    searchMusic(set, num);
                }
                var end = new Date().getTime();
            } catch(e) {
                console.log(e.stack);
                console.log(e);
            }
        }, 'json');
    }
}



function loadSimilarMusic(data, num, callback) {
    $.bootstrapMessageOff();
    var total = 0;
    search.similarity = data[1];
    searchMusic(data[0], num, callback);
}

function searchSimilar(ele) {
    var type = 'undefined' === typeof ele.attr('albumid') ? 'track' : 'album';
    searchSimilarList.push([ele.attr('artist'), ele.attr('album' === type ? 'name' : 'musicTitle'), type]);
    incrementSimilar();
}

$(document).ready(function() {
    if (1 === $('#incboard-search').length) {
        search = new IncBoard();

        $('.music-large').live('click', function (e) {
            searchSimilar($(this).parent());
        });

        $('.incboard-cell').live('click', function (e) {
            searchSimilar($(this));
        });
        $(window).bind('resize', $.proxy(search.ibb, 'resize'));
    } else if (1 === $('#search').length) {
        search = new $.ResultSet();
    }

    $('form.search').ajaxForm({
        dataType: 'json',
        success: function (data) {
            loadSimilarMusic(data, 10);
        },
        error: function (data) {
            $.bootstrapMessageAuto('Error searching for music', 'error');
        },
        beforeSubmit: function() {
            $('#subtitle').subtitleInit();
            search.searchString = $('#q').val();
            $.bootstrapMessage('Loading...', 'info');
            search.clean();
            var obj = new Object();
            obj.artist = $('#artist').val();
            obj.musicTitle = $('#musicTitle').val();
            obj.type = $('#type').val();
            if ($.isSearchFormValid()) {
                searchMusic([obj], 1, searchMusicCallbackCenter);
            }
        }
    });

});
