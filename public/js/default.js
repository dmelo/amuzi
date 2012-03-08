/*global  jPlayerPlaylist: false, jQuery:false */

//(function($, undefined) {
//    'use strict';

    var myPlaylist;
    var jplayerCss;
    var jPlaylistTop = null;
    var repeat;
    var current;
    var modalWrapper = "#load-modal-wrapper";
    var isRunCommand = false;

    function callbackLogin(userId) {
        loadPlaylist();
        $('.loginRequired').fadeTo('slow', 1.0);
        $('body').append('<div class="invisible" id="userId">' + userId + '</div>');
    }


    // Soon to be deprecated.
    function savePlaylist() {
        $.post('/playlist/save', {
            playlist: myPlaylist.original,
            name: myPlaylist.name
        }, function(data) {
        });
    }

    function addTrack(trackTitle, trackLink, trackCover) {
        $.post('/playlist/addtrack', {
            playlist: myPlaylist.name,
            title: trackTitle,
            mp3: trackLink,
            cover: trackCover
        }, function(data) {
            $.bootstrapMessageAuto(data[0], data[1] ? 'success': 'error');
            if(false == data[1])
                loadPlaylist(playlistName);
        }, 'json');
    }

    // TODO: take away the playlistName
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

    /**
     * Load the user's specific playlist or the default if an empty string is
     * especified.
     *
     * @param name Playlist's name, or an empty string to get the default
     * playlist.
     * @return void
     */
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
        }, 'json').complete(function() {
            if(isRunCommand)
                setTimeout('runCommands()', 1500);
        });
    }

    function rmPlaylist(name) {
        $.post('/playlist/remove', {
            name: name
        }, function(data) {
            $.bootstrapMessageAuto(data[0], data[1]);
        }, 'json');
    }

    function initAmuzi() {
        isRunCommand = true;
        loadPlaylist();
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
            },
            beforeSubmit: function() {
            }
        });
    }

    function isLoggedIn() {
        if($('#userId').length)
            return $('#userId').html();
        return false;
    }

    function playlistCallback() {
        $('#playlistsettings').ajaxForm({
            success: function(data) {
                $('#playlistsettings-result tbody').html(data);
            },
            error: function(data) {
                $.bootstrapMessageAuto(data, 'error');
            }
        });
    }

    function newPlaylistCallback() {
        $('form#newPlaylist').ajaxForm({
            dataType: 'json',
            success: function (data) {
                loadPlaylist($('input[name=name]').val());
                $.bootstrapMessageAuto(data[0], data[1]);
                $(modalWrapper).modal('hide');
            },
            error: function(data) {
                $.bootstrapMessageAuto('Error saving. Something went wrong', 'error');
                $('#load-modal-wrapper').modal('hide');
            }
        });
    }

    // Interpret and run commands
    // The separator for the commands is "&&::&&"
    function runCommands() {
        isRunCommand = false;
        url = $.url(window.location.href);
        commands = url.attr('fragment');

        if('string' == typeof(commands)) {
            commandArray = commands.split("&&::&&");
            for(var i = 0; i < commandArray.length; i++)
                runCommand(commandArray[i]);
        }
    }

    function runCommand(command) {
        if(2 == command.split("$$::$$").length) {
            var commandName = command.split("$$::$$")[0];
            var commandParams = command.split("$$::$$")[1].split(":::");

            // addTrack$$::$$title:::mp3:::pic
            if("addTrack" == commandName) {
                if(3 == commandParams.length) {
                    myPlaylist.add({title: commandParams[0], mp3: commandParams[1], free: true}, true);
                    addTrack(commandParams[0], commandParams[1], commandParams[2]);
                }
                else
                    $.bootstrapMessageAuto("Invalid parameters for addTrack", "error");
            }
            else {
                $.bootstrapMessageAuto("Command not found", "error");
            }
        }
    }


    $(document).ready(function() {
        // topbar menu
        $('.topbar').dropdown();
        // add track into the playlist.
        $('.addplaylist').live('click', function(e) {
            e.preventDefault();
            title = $(this).attr('title');
            mp3 = $(this).attr('href');
            pic = $(this).parent().parent().find('.image img').attr('src');
            myPlaylist.add({
                title: title,
                mp3: mp3,
                free: true
            });

            addTrack(title, mp3, pic);
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
        setTimeout('initAmuzi();', 1500);

        if(isLoggedIn())
            $('.loginRequired').fadeTo('slow', 1.0);

        // playlistsettings -> search
        $('#playlistsettings-result tbody tr').live('click', function(e) {
            loadPlaylist($(this).find('.name').html());
            $('#load-modal-wrapper').modal('hide');
        });

        $('#playlistsettings-result tbody tr .remove').live('click', function(e) {
            e.stopPropagation();
            if(confirm('Are you sure?')) {
                var name = $(this).parent().parent().find('.name').html();
                rmPlaylist(name);
                if(name == myPlaylist.name)
                    loadPlaylist('');
                $('#load-modal-wrapper').modal('hide');
            }
        });
    });
//})(jQuery);
