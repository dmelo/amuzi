/**
 * Set of functions to manage the list of results.
 */

var resultSet = new ResultSet();


function ResultSet() {
    this.searchString = "";
    this.searchPage = 1;
}


/**
 * Transform an integer from 0 to 100 to a leading 0 number with up to two digits.
 *
 * @param num Number to be transformed.
 * @return Returns the two digit leading 0 number.
 */
ResultSet.prototype.twoDigit = function(num) {
    var str = '';
    if(num < 10) {
        str += '0';
    }

    return str + num;
}

/**
 * Put number of seconds into HH:MM:SS format when time is more than or equals to 3600 (one hour) or MM:SS, otherwise.
 *
 * @param time Time, in seconds.
 * @return Returns a string represening time in HH:MM:SS or MM:SS format.
 */
ResultSet.prototype.secondsToHMS = function(time) {
    var h = 0;
    var m = 0;
    var s = 0;

    h = Math.floor(time / 3600);
    time -= 3600 * h;
    m = Math.floor(time / 60);
    time -= 60 * m;
    s = time;

    var str = '';

    if(h > 0) {
        str = this.twoDigit(h);
    }

    str += this.twoDigit(m) + ':';
    str += this.twoDigit(s);

    return str;
}

/**
 * Cleans the table of results and let it ready to a search.
 */
ResultSet.prototype.cleanTable = function() {
    $('#result .music-large').remove();
    $('#more-results').css('opacity', '0.0');
}

ResultSet.prototype.getMusicLarge = function(v, objectType) {
    duration = this.secondsToHMS(v.duration);
    var aYoutube = '<a target="_blank" href="' + v.youtubeUrl + '" title="Youtube video" class="youtube-link"><img src="/img/youtube_icon.png"/></a>';
    var aDownload = '<a target="_blank" href="' + v.url + '"title="download ' + v.title + '" class="download"><img src="/img/download_icon.png"/></a>';
    var aPlay = '<a href="' + v.url + '" title="' + v.title + '" class="addplaylist"><img src="/img/play_icon.png"/></a>';
    return '<div class="music-large object-' + objectType + '" fid="' + v.fid + '" fcode="' + v.fcode + '"><div class="image"><img src="' + v.cover + '"/><div class="duration">' + duration + '</div></div><div class="title"><a href="' + v.url + '">' + v.title + '</a></div><div class="play">' + aYoutube + aDownload + aPlay + '</div>';
}

ResultSet.prototype.appendTable = function(v, objectType) {
    if($('[fid=' + v.fid + '][fcode=' + v.fcode + ']').length == 0)
        $('#result').append(this.getMusicLarge(v, objectType));
    $('#more-results').css('opacity', '1.0');
}

ResultSet.prototype.searchMore = function() {
    $.bootstrapMessage('Loading...', 'info');
    this.searchPage++;
    $.get('/api/search',{
        q: this.searchString,
        limit: 9,
        offset: 1 + (9 * (this.searchPage - 1))
    }, function (data) {
        $.bootstrapMessageOff();
        $.each(data, function(i, v) {
            resultSet.appendTable(v, 'track');
        });
    }, 'json').error(function(data) {
        $.bootstrapMessageAuto('An error occured', 'error');
    });
}

ResultSet.prototype.getSimilarTracks = function(artist, musicTitle) {
    $.get('/api/searchsimilar', {
        artist: artist,
        musicTitle: musicTitle
    }, function(data) {
        $.each(data, function(i, v) {
            resultSet.appendTable(v, 'music');
        });
    }, 'json');
}



$(document).ready(function() {
    $('.music-large').live({mouseenter: function() {
        $(this).find('a').css('color', 'white');
        $(this).find('.play').css('display', 'block');
    },mouseleave: function() {
        $(this).find('a').css('color', 'black');
        $(this).find('.play').css('display', 'none');
    }});

    // query youtube for videos and fill the result table.
    $('#search').ajaxForm({
        dataType: 'json',
        success: function (data) {
            $.bootstrapMessageOff();
            $.each(data, function(i, v) {
                resultSet.appendTable(v, 'track');
            });
        },
        beforeSubmit: function() {
            resultSet.cleanTable();
            resultSet.searchString = $('#q').val();
            console.log($('#q').val());
            resultSet.searchPage = 1;
            resultSet.getSimilarTracks($('#artist').val(), $('#musicTitle').val());
            $.bootstrapMessage('Loading...', 'info');
        }
    });

    $('#more-results').click(function(e) {
        resultSet.searchMore();
    });
});
