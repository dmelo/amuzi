(function ($, undefined) {
    function randomColor() {
        var color = [],
            total = 0;

        color[0] = color[1] = color[2] = color[3] = parseInt(255 * Math.random());

        return color;
    }

    function getElement(color) {
        var v = new Object();
        v.objId = color[3];
        v.title = color[0] + " " + color[1] + " " + color[2];
        v.duration = 0;
        v.type = 'track';
        v.cover = '/svg.php?r=' + color[0] + '&g=' + color[1] + '&b=' + color[2];
        v.artist = 'red';

        if (color[1] > color[0]) {
            v.artist = 'green';
        }

        if (color[2] > color[1]) {
            v.artist = 'blue';
        }

        return v;
    }

    function getSimilarity(a, b) {
        var ret = Math.abs(a[0] - b[0]);
        // console.log("getSimilarity( " + a[0] + " , " + b[0] + " ) = " + ret);
        return ret;
    }

    function getSimilarityMatrix(colorSet) {
        var similarity = [];

        for (var i = 0; i < colorSet.length; i++) {
            var idI = colorSet[i][3];
            similarity[idI] = [];
            for (var j = 0; j < colorSet.length; j++) {
                idJ = colorSet[j][3];
                similarity[idI][idJ] = getSimilarity(colorSet[i], colorSet[j]);

                if (!(idJ in similarity)) {
                    similarity[idJ] = [];
                }
                similarity[idJ][idI] = getSimilarity(colorSet[i], colorSet[j]);
            }
        }

        return similarity;
    }

    function getURLParameter(name) {
          return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null
    }

    function loadUrl() {
        window.location.search = 'num=' + $.num + '&count=' + $.count + '&threashold=' + $.incBoard.stochasticLength;
    }

    function runTest() {
        $.count = parseInt(getURLParameter('count'));
        $.incBoard.stochasticLength = parseFloat(getURLParameter('threashold'));
        $.num = parseInt(getURLParameter('num'));

        if ($.count >= 100) {
            $.count = 0;
            $.incBoard.stochasticLength += 0.05;
        } else {
            $.count++;
        }

        if ($.incBoard.stochasticLength >= 1) {
            return;
        }

        $.incBoard.clean();
        var messageId = $.bootstrapMessageLoading(),
            colorSet = [];

        for (var i = 0; i < $.num; i++) {
            colorSet.push(randomColor());
        }

        $.incBoard.similarity = getSimilarityMatrix(colorSet);

        var begin = new Date();
        for (var i = 0; i < $.num; i++) {
            $.incBoard.insert(getElement(colorSet[i]));
        }
        var end = new Date();
        var quality = $.incBoard.quality(),
            time = end.getTime() - begin.getTime();

        $('#nro').html($.num);
        $('#time').html(time);
        $('#quality').html(quality);
        $('#threashold').html($.incBoard.stochasticLength);

        $('.incboard-cell .cover').css('padding', '0');

        $.bootstrapMessageOff(messageId);
        console.log('fim');
        $.get('/incboard_stats.php', {
            count: $.count,
            threadshold: $.incBoard.stochasticLength,
            num: $.num,
            time: time,
            quality: quality
        }, function (data) {
            if (getURLParameter('stop') != 1) {
                setTimeout(loadUrl, 1000);
            }
        });

    }

    $(document).ready(function() {
        $('html, body').css('background-color', '#ddf');
        $.incBoard = new $.IncBoard();
        $.count = 0;
        $.incBoard.stochasticLength = 0.0;
        runTest();
    });
}(jQuery, undefined));
