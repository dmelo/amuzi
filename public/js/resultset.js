/**
 * Set of functions to manage the list of results.
 */

    /**
     * Transform an integer from 0 to 100 to a leading 0 number with up to two digits.
     *
     * @param num Number to be transformed.
     * @return Returns the two digit leading 0 number.
     */
    function twoDigit(num) {
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
    function secondsToHMS(time) {
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
            str = twoDigit(h);
        }

        str += twoDigit(m) + ':';
        str += twoDigit(s);

        return str;
    }


    function cleanTable() {
        $('#result').html(' ');
    }

    function getMusicLarge(img, title, url, duration) {
        duration = secondsToHMS(duration);
        var aDownload = '<a target="_blank" href="' + url + '"title="download ' + title + '" class="download"><img src="/img/download_icon.png"/></a>';
        var aPlay = '<a href="' + url + '" title="' + title + '" class="addplaylist"><img src="/img/play_icon.png"/></a>';
        return '<div class="music-large"><div class="image"><img src="' + img + '"/><div class="duration">' + duration + '</div></div><div class="title"><a href="' + url + '">' + title + '</a></div><div class="play">' + aDownload + aPlay + '</div>';
    }

    function appendTable(img, title, url, duration) {
        $('#result').append(getMusicLarge(img, title, url, duration));
    }


    $(document).ready(function() {
        $('.music-large').live({mouseenter: function() {
            $(this).find('a').css('color', 'white');
            $(this).find('.play').css('display', 'block');
        },mouseleave: function() {
            $(this).find('a').css('color', 'black');
            $(this).find('.play').css('display', 'none');
        }});
    });
