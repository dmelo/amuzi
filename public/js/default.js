var myPlayList;

function setAudio(src, title) {
    $('#play').html('<p>Title: ' + title + '</p><audio controls preload><source src="' + src + '"></source></audio>');
}

function cleanTable() {
    thead = '<thead><tr class="topic"><th>Cover</th><th>Title</th><th>Options</th></tr></thead>';
    tbody = '<tbody></tbody>';
    $('#result').html('<table>' + thead + tbody + '</table>');
}

function appendTable(img, title, url) {
    img = '<img class="cover" src="' + img + '" alt="' + title + '"/>';
    options = '<a href="' + url + '" class="addplaylist" title="' + title + '"><img alt="playlist" src="/img/playlist.gif"/></a>';
    options += '<a href="' + url + '" class="download" title="' + title + '" target="_blank"><img alt="download" src="/img/download.gif"/></a>';
    tr = '<tr><td>' + img + '</td><td><p>' + title + '</p></td><td>' + options + '</td><td>' + '</tr>';
    $('#result table tbody').append(tr);
}

function message(text) {
    $('.message div').html(text);
    $('.message').css('opacity', '1');
}

function messageOff() {
    $('.message').css('opacity', '0');
}



$(document).ready(function() {
    $('#search').ajaxForm({
        dataType: 'json', 
        success: function (data) {
            messageOff();
            cleanTable();
            for(i = 0; i < data.length; i++)
                appendTable(data[i].pic, data[i].title, data[i].you2better);
        },
        beforeSubmit: function() {
            message('Loading...');
        }
    });

    $('.audio').live('click', function(e) {
        e.preventDefault();
        setAudio($(this).attr('href'), $(this).attr('title'));
    });

    $('.addplaylist').live('click', function(e) {
        e.preventDefault();
        //$('#play').dmplaylist.addToPlaylist($(this).attr('href'), $(this).attr('title'));
        myPlayList[myPlayList.length] = {name: $(this).attr('title'), mp3: $(this).attr('href')};
        displayPlayList();
    });

    $('#play').draggable();

    $('#q').autocomplete('/api/autocomplete', {
        dateType: 'json',
        parse: function(data) {
            data = $.parseJSON(data);
            return $.map(data, function(row) {
                return {
                    data: row,
                    value: '<img src="' + row.pic + '"/> <span>' + row.name + '</span>',
                    result: row.name
                }
            });

        },
        formatItem: function(row, i, n) {
            return '<img src="' + row.pic + '"/> <span>' + row.name + '</span>';
        }
    });

    $('#q').change(function() {
        $('#search').submit();
    });


    var playItem = 0;



    myPlayList = [

        {name:"Tempered Song",mp3:"http://www.jplayer.org/audio/mp3/Miaow-01-Tempered-song.mp3"},

        {name:"Hidden",mp3:"http://www.jplayer.org/audio/mp3/Miaow-02-Hidden.mp3"},

        {name:"Lentement",mp3:"http://www.jplayer.org/audio/mp3/Miaow-03-Lentement.mp3"},

        {name:"Lismore",mp3:"http://www.jplayer.org/audio/mp3/Miaow-04-Lismore.mp3",ogg:"http://www.jplayer.org/audio/ogg/Miaow-04-Lismore.ogg"},

        {name:"The Separation",mp3:"http://www.jplayer.org/audio/mp3/Miaow-05-The-separation.mp3",ogg:"http://www.jplayer.org/audio/ogg/Miaow-05-The-separation.ogg"},

        {name:"Beside Me",mp3:"http://www.jplayer.org/audio/mp3/Miaow-06-Beside-me.mp3",ogg:"http://www.jplayer.org/audio/ogg/Miaow-06-Beside-me.ogg"},

        {name:"Bubble",mp3:"http://www.jplayer.org/audio/mp3/Miaow-07-Bubble.mp3",ogg:"http://www.jplayer.org/audio/ogg/Miaow-07-Bubble.ogg"},

        {name:"Stirring of a Fool",mp3:"http://www.jplayer.org/audio/mp3/Miaow-08-Stirring-of-a-fool.mp3",ogg:"http://www.jplayer.org/audio/ogg/Miaow-08-Stirring-of-a-fool.ogg"},

        {name:"Partir",mp3:"http://www.jplayer.org/audio/mp3/Miaow-09-Partir.mp3",ogg:"http://www.jplayer.org/audio/ogg/Miaow-09-Partir.ogg"},

        {name:"Thin Ice",mp3:"http://www.jplayer.org/audio/mp3/Miaow-10-Thin-ice.mp3",ogg:"http://www.jplayer.org/audio/ogg/Miaow-10-Thin-ice.ogg"},

        {name:"Coldplay",mp3:"http://you2better.localhost/api/fyMhvkC3A84/Coldplay%20-%20Every%20Teardrop%20Is%20A%20Waterfall.mp3"}

    ];





    $("#jquery_jplayer").jPlayer({

        ready: function() {
            displayPlayList();
            playListInit(true); // Parameter is a boolean for autoplay.
        },

        swfPath: "/obj/",

        oggSupport: false

    })

    .jPlayerId("play", "player_play")

    .jPlayerId("pause", "player_pause")

    .jPlayerId("stop", "player_stop")

    .jPlayerId("loadBar", "player_progress_load_bar")

    .jPlayerId("playBar", "player_progress_play_bar")

    .jPlayerId("volumeMin", "player_volume_min")

    .jPlayerId("volumeMax", "player_volume_max")

    .jPlayerId("volumeBar", "player_volume_bar")

    .jPlayerId("volumeBarValue", "player_volume_bar_value")

    .onProgressChange( function(loadPercent, playedPercentRelative, playedPercentAbsolute, playedTime, totalTime) {

        var myPlayedTime = new Date(playedTime);

        var ptMin = (myPlayedTime.getUTCMinutes() < 10) ? "0" + myPlayedTime.getUTCMinutes() : myPlayedTime.getUTCMinutes();

        var ptSec = (myPlayedTime.getUTCSeconds() < 10) ? "0" + myPlayedTime.getUTCSeconds() : myPlayedTime.getUTCSeconds();

        $("#play_time").text(ptMin+":"+ptSec);



        var myTotalTime = new Date(totalTime);

        var ttMin = (myTotalTime.getUTCMinutes() < 10) ? "0" + myTotalTime.getUTCMinutes() : myTotalTime.getUTCMinutes();

        var ttSec = (myTotalTime.getUTCSeconds() < 10) ? "0" + myTotalTime.getUTCSeconds() : myTotalTime.getUTCSeconds();

        $("#total_time").text(ttMin+":"+ttSec);

    })

    .onSoundComplete( function() {

        playListNext();

    });



    $("#ctrl_prev").click( function() {

        playListPrev();

        return false;

    });



    $("#ctrl_next").click( function() {

        playListNext();

        return false;

    });



    function displayPlayList() {
        $("#playlist_list ul").html(' ');
        for (i=0; i < myPlayList.length; i++) {
            $("#playlist_list ul").append("<li id='playlist_item_"+i+"'>"+ myPlayList[i].name +"</li>");
            $("#playlist_item_"+i).data( "index", i ).hover(
                function() {
                    if (playItem != $(this).data("index")) {
                        $(this).addClass("playlist_hover");
                    }
                },
                function() {
                    $(this).removeClass("playlist_hover");
                }
            ).click( function() {
                var index = $(this).data("index");
                if (playItem != index) {
                    playListChange( index );
                }
            });
        }
    }



    function playListInit(autoplay) {

        if(autoplay) {

            playListChange( playItem );

        } else {

            playListConfig( playItem );

        }

    }



    function playListConfig( index ) {

        $("#playlist_item_"+playItem).removeClass("playlist_current");

        $("#playlist_item_"+index).addClass("playlist_current");

        playItem = index;

        $("#jquery_jplayer").setFile(myPlayList[playItem].mp3);

    }



    function playListChange( index ) {

        playListConfig( index );

        $("#jquery_jplayer").play();

    }



    function playListNext() {

        var index = (playItem+1 < myPlayList.length) ? playItem+1 : 0;

        playListChange( index );

    }



    function playListPrev() {

        var index = (playItem-1 >= 0) ? playItem-1 : myPlayList.length-1;

        playListChange( index );

    }

});










