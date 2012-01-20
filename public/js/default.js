/*global  jPlayerPlaylist: false, jQuery:false */

//(function($, undefined) {
//    'use strict';

    var myPlaylist;
    var jplayerCss;
    var jPlaylistTop = null;
    var repeat;
    var current;

    function callbackLogin(userId) {
        loadPlaylist();
        $('.loginRequired').fadeTo('slow', 1.0);
        $('body').append('<div class="invisible" id="userId">' + userId + '</div>');
    }


    // Soon to be deprecated.
    function savePlaylist(name) {
        name = name || 'default';
        $.post('/playlist/save', {
            playlist: myPlaylist.original,
            name: name
        }, function(data) {
        });
    }

    function addTrack(trackTitle, trackLink, trackCover, playlistName) {
        playlistName = playlistName || 'default';
        $.post('/playlist/addtrack', {
            playlist: playlistName,
            title: trackTitle,
            mp3: trackLink,
            cover: trackCover
        }, function(data) {
            $.bootstrapMessageAuto(data[0], data[1]);
            if(false == data[1])
                loadPlaylist(playlistName);
        }, 'json');
    }

    function rmTrack(url, playlistName) {
        playlistName = playlistName || 'default';
        $.post('/playlist/rmtrack', {
            playlist: playlistName,
            url: url
        }, function(data) {
            $.bootstrapMessageAuto(data[0], data[1]);
            if(false == data[1])
                loadPlaylist(playlistName);
        }, 'json');
    }

    function loadPlaylist(name) {
        name = name || '';
        myPlaylist.removeAll();
        $.post('/playlist/load', {
            name: name
        }, function(data) {
            if(data != null) {
                $.each(data[0], function(i, v) {
                    myPlaylist.add({title: v.title, mp3: v.mp3, free: true});
                });
                myPlaylist.name = data[1];
                setRepeatAndCurrent(parseInt(data[2]), parseInt(data[4]));
                setInterfaceShuffle(parseInt(data[3]));
                setTimeout(callbackShuffle, 1500);
            }
        }, 'json');
    }

    function setRepeatAndCurrent(repeat, current) {
        myPlaylist.loop = repeat;
        myPlaylist.newCurrent = current;
    }

    function setPlaylistRepeat(name, repeat) {
        name = name || 'default';

    }

    /**
     * When the playlist have more than 8 items it retracts on mouseleave and
     * restore on mouseover.
     */
    function retractablePlaylist() {
        $(jplayerCss).mouseover(function(e) {
            $('.jp-playlist').fadeIn();
        }).mouseleave(function(e) {
            if($('.jp-playlist li').length > 8)
                $('.jp-playlist').fadeOut();
        });
    }

    function callbackPlay(current) {
        $.post('/playlist/setcurrent', {
            name: myPlaylist.name,
            current: current
        }, function(data) {
            if(false == data[1])
                $.bootstrapMessageAuto(data[0], 'error');
        }, 'json');
    }

    function callbackShuffle() {
        myPlaylist.setCurrent(myPlaylist.newCurrent);
        setInterfaceRepeat(myPlaylist.loop);
    }

    function applyOverPlaylist() {
        if(!jPlaylistTop)
            jPlaylistTop = $('.jp-playlist').first().offset().top;
        maxHeight = $(window).height() - jPlaylistTop - 2;
        $('.jp-playlist').css('max-height', maxHeight);
    }

    // Repeat
    function setRepeat(repeat) {
        setInterfaceRepeat(repeat);
        $.post('/playlist/setrepeat', {
            name: myPlaylist.name,
            repeat: repeat
        }, function(data) {
            if(false == data[1])
                $.bootstrapMessageAuto(data[0], 'error');
        }, 'json');
    }

    function setInterfaceRepeat(repeat) {
        $('.jp-repeat-off').css('display', repeat ? 'block' : 'none');
        $('.jp-repeat').css('display', repeat ? 'none' : 'block');
    }


    function applyRepeatTriggers() {
        $('.jp-repeat').click(function(e) {
            setRepeat(true);
        });

        $('.jp-repeat-off').click(function(e) {
            setRepeat(false);
        });
    }

    // Shuffle
    function setShuffle(shuffle) {
        $.post('/playlist/setshuffle', {
            name: myPlaylist.name,
            shuffle: shuffle
        }, function(data) {
            if(false == data[1])
                $.bootstrapMessageAuto(data[0], 'error');
        }, 'json');
    }

    function setInterfaceShuffle(shuffle) {
        myPlaylist.shuffle(shuffle);
        $('.jp-shuffle-off').css('display', shuffle ? 'block' : 'none');
        $('.jp-shuffle').css('display', shuffle ? 'none' : 'block');
    }

    function applyShuffleTriggers() {
        $('.jp-shuffle').click(function(e) {
            setShuffle(true);
        });

        $('.jp-shuffle-off').click(function(e) {
            setShuffle(false);
        });
    }

    function applyPlaylistSettings() {
        $('.playlistsettings').ajaxForm({
            dataType: 'json',
            success: function (data) {
                alert(data);
            },
            beforeSubmit: function() {
                alert('kkkkk');
            }
        });
    }

    function isLoggedIn() {
        if($('#userId').length)
            return $('#userId').html();
        return false;
    }

    function playlistCallback() {
        // TODO: associate the right treatment to the form.
        $('#playlistsettings').ajaxForm({
            success: function(data) {
                $('#playlistsettings-result tbody').html(data);
            },
            error: function(data) {
                $.bootstrapMessageAuto(data, 'error');
            }
        });
    }

    $(document).ready(function() {
        // query youtube for videos and fill the result table.
        $('#search').ajaxForm({
            dataType: 'json',
            success: function (data) {
                $.bootstrapMessageOff();
                cleanTable();
                $.each(data, function(i, v) {
                    appendTable(v.pic, v.title, v.you2better, v.duration);
                });
            },
            beforeSubmit: function() {
                $.boostrapMessage('Loading...');
            }
        });

        // add track into the playlist.
        $('.addplaylist').live('click', function(e) {
            e.preventDefault();
            title = $(this).attr('title');
            mp3 = $(this).attr('href');
            pic = $(this).parent().parent().find('.image img').attr('src');
            alert(pic);
            myPlaylist.add({
                title: title,
                mp3: mp3,
                free: true
            });

            addTrack(title, mp3, pic, myPlaylist.name);
        });

        $('.jp-playlist-item-remove').live('click', function(e) {
            url = $(this).parent().parent().find('.jp-playlist-item-free').attr('href');
            rmTrack(url, myPlaylist.name);
        });

        // placeholder on the search input.
        $('#q').placeholder();
        // autocomplete the search input from last.fm.
        $('#q').autocomplete('/api/autocomplete', {
            dateType: 'json',
            parse: function(data) {
                data = $.parseJSON(data);
                return $.map(data, function(row) {
                    return {
                        data: row,
                        value: '<img src="' + row.pic + '"/> <span>' + row.name + '</span>',
                        result: row.name
                    };
                });

            },
            formatItem: function(row, i, n) {
                return '<img src="' + row.pic + '"/> <span>' + row.name + '</span>';
            }
        });

        // submit a query to youtube after change the value of the search input.
        $('#q').change(function() {
            $('#search').submit();
        });

        // start the jplayer.
        jplayerCss = "#jp_container_1";
        myPlaylist = new jPlayerPlaylist({
            jPlayer: "#jquery_jplayer_1",
            cssSelectorAncestor: jplayerCss
        }, [], {supplied: 'mp3', swfPath: "/obj/", free: true, callbackPlay: callbackPlay});

        $(jplayerCss + ' ul:last').sortable({
            update: function() {
                myPlaylist.scan();
                savePlaylist();
            }
        });

        retractablePlaylist();
        applyOverPlaylist();
        applyRepeatTriggers();
        applyShuffleTriggers();
        applyPlaylistSettings();
        $(window).resize(function(e) {
            applyOverPlaylist();
        });

        $('.loadModal').bootstrapLoadModal();

        // For some reason, i can't call loadPlaylist right the way, it must wait for some initialization stuff.
        setTimeout('loadPlaylist();', 1500);

        if(isLoggedIn())
            $('.loginRequired').fadeTo('slow', 1.0);
    });
//})(jQuery);
