/*global  jPlayerPlaylist: false, jQuery:false */

//(function($, undefined) {
//    'use strict';

    var myPlaylist;
    var jplayerCss;
    var jPlaylistTop = null;

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


    function message(text) {
        $('.message div').html(text);
        $('.message').css('opacity', '1');
        $('.message').css('filter', 'alpha(opacity=100)');
    }

    function messageOff() {
        $('.message').css('opacity', '0');
        $('.message').css('filter', 'alpha(opacity=0)');
    }

    function messageAuto(text) {
        message(text);
        setTimeout(messageOff, 5000);
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

    function addTrack(trackTitle, trackLink, playlistName) {
        playlistName = playlistName || 'default';
        $.post('/playlist/addtrack', {
            playlist: playlistName,
            title: trackTitle,
            mp3: trackLink
        }, function(data) {
            messageAuto(data[0], data[1]);
            if(false == data[1])
                loadPlaylist(playlistName);
        }, 'json');
    }

    function rmTrack(sort, playlistName) {
        playlistName = playlistName || 'default';
        $.post('/playlist/rmtrack', {
            playlist: playlistName,
            sort: sort
        }, function(data) {
            messageAuto(data[0], data[1]);
            if(false == data[1])
                loadPlaylist(playlistName);
        }, 'json');
    }

    function loadPlaylist(name) {
        name = name || 'default';
        myPlaylist.removeAll();
        $.post('/playlist/load', {
            name: name
        }, function(data) {
            if(data != null) {
                $.each(data[0], function(i, v) {
                    myPlaylist.add({title: v.title, mp3: v.mp3});
                });
            }
        }, 'json');
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

    function applyOverPlaylist() {
        if(!jPlaylistTop)
            jPlaylistTop = $('.jp-playlist').first().offset().top;
        maxHeight = $(window).height() - jPlaylistTop - 2;
        $('.jp-playlist').css('max-height', maxHeight);
    }

    $(document).ready(function() {
        // query youtube for videos and fill the result table.
        $('#search').ajaxForm({
            dataType: 'json',
            success: function (data) {
                messageOff();
                cleanTable();
                $.each(data, function(i, v) {
                    appendTable(v.pic, v.title, v.you2better, v.duration);
                });
            },
            beforeSubmit: function() {
                message('Loading...');
            }
        });

        // add track into the playlist.
        $('.addplaylist').live('click', function(e) {
            e.preventDefault();
            title = $(this).attr('title');
            mp3 = $(this).attr('href');
            myPlaylist.add({
                title: title,
                mp3: mp3
            });

            addTrack(title, mp3, 'default');
        });

        $('.jp-playlist-item-remove').live('click', function(e) {
            sort = $(this).parent().parent().attr('name');
            rmTrack(sort, 'default');
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
        }, [], {supplied: 'mp3', swfPath: "/obj/", free: true});

        $(jplayerCss + ' ul:last').sortable({
            update: function() {
                myPlaylist.scan();
                savePlaylist();
            }
        });

        retractablePlaylist();
        applyOverPlaylist();
        $(window).resize(function(e) {
            applyOverPlaylist();
        });

        // For some reason, i can't call loadPlaylist right the way, it must wait for some initialization stuff.
        setTimeout('loadPlaylist();', 1500);
    });
//})(jQuery);
