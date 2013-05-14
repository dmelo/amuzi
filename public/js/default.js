/**
 * default.js
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2013  Diogo Oliveira de Melo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/*global  jPlayerPlaylist: false, jQuery:false */

(function ($, undefined) {
    'use strict';

    /* global window */
    /* global commands */
    var jplayerCss,
        jPlaylistTop = null,
        repeat,
        current,
        latestSearch,
        popup,
        loadingPlaylistMessage = null;

    window.myPlaylist = null;
    window.windowId = parseInt(Math.random() * 1000000);
    $.commands = new Commands();

    $.modalWrapper = "#load-modal-wrapper";
    // Soon to be deprecated.
    function savePlaylist() {
        $.post('/playlist/save', {
            playlist: window.myPlaylist.original,
            name: window.myPlaylist.name
        }, function (data) {
        });
    }

    /**
     * Scroll to the bottom of the playlist.
     */
    function playlistRollBottom() {
        $('.jp-playlist').scrollTop($('.jp-playlist').prop('scrollHeight'));
    }

    function setInterfaceShuffle(shuffle) {
        window.myPlaylist.shuffle(shuffle);
        $('.jp-shuffle-off').css('display', shuffle ? 'block' : 'none');
        $('.jp-shuffle').css('display', shuffle ? 'none' : 'block');
    }

    function setRepeatAndCurrent(repeat, current) {
        window.myPlaylist.loop = repeat;
        window.myPlaylist.newCurrent = current;
    }

    function setInterfaceRepeat(repeat) {
        $('.jp-repeat-off').css('display', repeat ? 'block' : 'none');
        $('.jp-repeat').css('display', repeat ? 'none' : 'block');
    }

    function unloadPlaylist() {
        window.myPlaylist.name = null;
        window.myPlaylist.removeAll();
    }

    $.loadNextPlaylist = function() {
        $.get('/user/nextplaylist', function(data) {
            if ('error' === data[1]) {
                $.bootstrapMessageAuto(data[0], data[1]);
            } else {
                $.loadPlaylist(data[0], {isAlbum: data[1]});
            }
        }, 'json').error(function(data) {
            $.bootstrapMessageAuto('Coundn\'t load next album/playlist', 'error');
        });
    };

    /**
     * Load the an specific playlist or the default if an empty string is
     * especified. If a number is given, load the playlist by it's id.
     *
     * @param name Playlist's name, or an empty string to get the default
     * playlist.
     * @return void
     */
    $.loadPlaylist = function (name, opt) {
        var isAlbum = 'undefined' !== typeof opt && 'isAlbum' in opt ? opt.isAlbum : false;
        name = name || null;
        window.myPlaylist.removeAll();
        window.myPlaylist.isAlbum = isAlbum;

        var options,
            uri = isAlbum ? '/album/load' : '/playlist/load',
            item = isAlbum ? 'album' : 'playlist';

        if (null === loadingPlaylistMessage) {
            loadingPlaylistMessage = 'Already loading ' + item + ', please wait.';
            if (typeof (name) === 'number' || (typeof (name) === 'string' && parseInt(name, 10) >= 0)) {
                // It's an ID
                if (typeof (name) === 'string') {
                    name = parseInt(name, 10);
                }
                options = { id: name };

            } else {
                // It's a name
                options = { name: name };
            }

            $.post(uri, options, function (data) {
                if (null !== data) {
                    $('.jp-title').css('display', 'block');
                    window.myPlaylist.name = data[1];
                    $.each(data[0], function (i, v) {
                        window.myPlaylist.add({title: v.title, flv: v.url, free: true, id: v.id, artist_music_title_id: v.artist_music_title_id}, false);
                    });
                    setRepeatAndCurrent(parseInt(data[2], 10), parseInt(data[4], 10));
                    setInterfaceShuffle(parseInt(data[3], 10));
                    applyOverPlaylist();

                    if (!isAlbum && 'number' === typeof options.id) {
                        $('.playlist-square').removeClass('current-playlist');
                        $('.playlist-square[playlistid=' + options.id + ']').addClass('current-playlist');
                    }

                    if ('undefined' !== typeof opt && 'playLast' in opt && opt.playLast) {
                        window.Playlist.setCurrent(-1);
                    }
                    window.myPlaylist.play();
                }
            }, 'json').complete(function () {
                loadingPlaylistMessage = null;
            }).error(function (e) {
                loadingPlaylistMessage = null;
                $.bootstrapMessageAuto('Error loading ' + item, 'error');
            });
        } else {
            $.bootstrapMessageAuto(loadingPlaylistMessage, 'info');
        }
    }


    function rmPlaylist(id, isPlaylist, callback) {
        var controller = isPlaylist ? 'playlist' : 'album';
        $.bootstrapMessageLoading();
        $.post('/' + controller + '/remove', {
            id: id
        }, function (data) {
            if ('success' === data[1] && 'function' === typeof callback) {
                callback(id, isPlaylist);
            }
            $.bootstrapMessageAuto(data[0], data[1]);
        }, 'json');
    }

    $.initAmuzi = function () {
        if (1 === $('#userId').length) {
            $.loadPlaylist();
        }
        setTimeout('$.commands.runProgram()', 1500);
    };

    function setPlaylistRepeat(name, repeat) {
        name = name || 'default';
    }

    function callbackPlay(current) {
        $.post('/playlist/setcurrent', {
            name: window.myPlaylist.name,
            current: current
        }, function (data) {
            if ('error' === data[1]) {
                $.bootstrapMessageAuto(data[0], data[1]);
            }
        }, 'json');
    }

    function applyOverPlaylist() {
        if ($('#jp_container_1').length > 0) {
            if (!jPlaylistTop) {
                jPlaylistTop = $('.jp-playlist').first().offset().top;
            }
            var maxHeight = $(window).height() - jPlaylistTop - 65;
            $('.jp-playlist').css('max-height', maxHeight);
        }
    }

    function applyOverResultDiv() {
        if ($('#result').length > 0) {
            var height = $(window).height() - $('#slide-search').offset().top - $('form#search').height() - parseInt($('form#search').css('margin-bottom'), 10) - $('#more-results').height() - $('.navbar').height() - 30;

            $('#result').height(height);
        }
    }

    // Repeat
    function setRepeat(repeat) {
        setInterfaceRepeat(repeat);
        $.post('/playlist/setrepeat', {
            name: window.myPlaylist.name,
            repeat: repeat
        }, function (data) {
            if ('error' === data[1]) {
                $.bootstrapMessageAuto(data[0], data[1]);
            }
        }, 'json');
    }

    function applyRepeatTriggers() {
        $('.jp-repeat').click(function (e) {
            setRepeat(true);
        });

        $('.jp-repeat-off').click(function (e) {
            setRepeat(false);
        });
    }

    // Shuffle
    function setShuffle(shuffle) {
        $.post('/playlist/setshuffle', {
            name: window.myPlaylist.name,
            shuffle: shuffle
        }, function (data) {
            if ('error' === data[1]) {
                $.bootstrapMessageAuto(data[0], data[1]);
            }
        }, 'json');
    }

    function applyShuffleTriggers() {
        $('.jp-shuffle').click(function (e) {
            setShuffle(true);
        });

        $('.jp-shuffle-off').click(function (e) {
            setShuffle(false);
        });
    }

    function applyPlaylistSettings() {
        $('.playlistsettings').ajaxForm({
            dataType: 'json',
            success: function (data) {
            },
            beforeSubmit: function () {
            }
        });
    }

    function isLoggedIn() {
        return $('#userId').length > 0 ? $('#userId').html() : false;
    }

    function playlistCallback() {
        $('#playlistsettings').ajaxForm({
            success: function (data) {
                $('#playlistsettings-result tbody').html(data);
            },
            error: function (data) {
                $.bootstrapMessageAuto(data, 'error');
            }
        });
    }

    function loadPlaylistSet() {
        $.get('/playlist/list', function (data) {
            $('.music-manager#playlists .stripe').html(data);
            $.resizeEditPlaylist();
            $('div[playlistid]').each(function(i, item) {
                $(item).popover({html:true, content: $(item).find('.playlist-info').html(), trigger: 'hover'});
            });
        }).error(function (data) {
            $.resizeEditPlaylist();
        });
    }

    function loadAlbumSet() {
        $.get('/album/list', function(data) {
            $('.music-manager#albums .stripe').html(data);
            $('div[albumid]').each(function(i, item) {
                $(item).popover({html:true, content: $(item).find('.album-info').html(), trigger: 'hover', placement: 'right', selector: '#slide-music-manager'});
            });
        });
    }

    $.rendered_newPlaylist = function () {
        $('form#newPlaylist').ajaxForm({
            dataType: 'json',
            success: function (data) {
                $.loadPlaylist($('input[name=name]').val());
                $.bootstrapMessageAuto(data[0], data[1]);
                $($.modalWrapper).modal('hide');
                loadPlaylistSet();
            },
            error: function (data) {
                $.bootstrapMessageAuto('Error saving. Something went wrong', 'error');
                $('#load-modal-wrapper').modal('hide');
            }
        });
    };

    function opacityFull(element) {
        element.addClass('opacity-full');
        element.removeClass('opacity-none');
    }

    function opacityNone(element) {
        element.addClass('opacity-none');
        element.removeClass('opacity-full');
    }

    function handleAutocompleteChoice(ui) {
        if (ui.item !== null && ui.item.value !== latestSearch) {
            $('#q').val(ui.item.value);
            $('#artist').val(ui.item.artist);
            $('#musicTitle').val(ui.item.musicTitle);
            $('#type').val(ui.item.type);
            $('form.search').submit();
            latestSearch = ui.item.value;
            if ('function' === typeof (window.tutorialCloseSearch)) {
                window.tutorialCloseSearch();
            }
        }
    }

    function preparePlaylistActions() {
        $('.jp-playlist ul li div').live('mouseover', function (e) {
            $(this).find('.jp-free-media').css('opacity', '1.0').css('-moz-opacity', '1.0').css('filter', 'alpha(opacity=100)');
        });

        $('.jp-playlist ul li div').live('mouseleave', function (e) {
            $(this).find('.jp-free-media').css('opacity', '0.0').css('-moz-opacity', '0.0').css('filter', 'alpha(opacity=0)');
        });
    }

    function prepareMusicTrackVote() {
        $('.vote').live('click', function (e) {
            e.preventDefault();
            $.get($(this).attr('href'), function (data) {
                $.bootstrapMessageAuto(data[0], data[1]);
                $($.modalWrapper).modal('hide');
            }, 'json').error(function (e) {
                $.bootstrapMessageAuto('Error registering vote', 'error');
                $($.modalWrapper).modal('hide');
            });
        });

    }

    function verifyView() {
        var viewPaths = ['/', '/index', '/index/', '/index/index', '/index/index/', '/index/incboard', '/index/incboard/'],
            pathname = window.location.pathname;

        if (-1 !== viewPaths.indexOf(pathname)) {
            $.get('/user/getview', function (data) {
                if ('incboard' === data && '/index/incboard' !== pathname && 'index/incboard/' !== pathname) {
                    window.location.pathname = '/index/incboard';
                } else if ('default' === data && '/' !== pathname) {
                    window.location.pathname = '/';
                }
            });
        }
    }

    $.callback_userSettings = function (data) {
        verifyView();
    };

    function refreshViewThumbnail() {
        var src = '';
        if ('default' === $('#view').val()) {
            src = '/img/thumb_classic.png';
        } else if ('incboard' === $('#view').val()) {
            src = '/img/thumb_incboard.png';
        }

        $('.side-view-thumb img').attr('src', src);
    }

    $.rendered_userSettings = function () {
        $('#view').parent().append('<div class="side-view-thumb"><img src=""/></div>');
        refreshViewThumbnail();
        $('#view').change(refreshViewThumbnail);
        $('#windowId').val(window.windowId);
    };

    function addElementAnimation(e) {
        var clone = e.clone();

        e.parent().append(clone);
        clone.animate({left: $(window).width()}, {
            duration: 1500,
            complete: function () {
                clone.remove();
            }
        });
    }

    function addToPlaylist(e) {
        var trackId = e.attr('trackId'),
            artist = e.attr('artist'),
            musicTitle = e.attr('musicTitle');

        if ($('.playlist-row[track_id=' + trackId + ']').length > 0) {
            if (!confirm('You already have this track on your playlist. Are you sure you want to insert it again?')) {
                return;
            }
        }

        if ('undefined' === typeof artist) {
            artist = e.parent().attr('artist');
            if ('undefined' === typeof artist) {
                artist = $('#artist').val();
            }
        }

        if ('undefined' === typeof musicTitle) {
            musicTitle = e.parent().attr('musicTitle');
            if ('undefined' === typeof musicTitle) {
                musicTitle = $('#musicTitle').val();
            }
        }

        $.addTrack(trackId, artist, musicTitle);

        addElementAnimation(e);
    }

    $.addTrack = function(trackId, artist, musicTitle, playNow) {
        var options;

        options = {
            id: trackId,
            playlist: window.myPlaylist.name,
            isAlbum: window.myPlaylist.isAlbum,
            artist: artist,
            musicTitle: musicTitle,
            windowId: window.windowId
        };

        $.bootstrapMessageLoading();
        $.post('/playlist/addtrack', options, function (data) {
            $.bootstrapMessageAuto(data[0], data[1]);
            if ('error' === data[1]) {
                $.loadPlaylist(window.myPlaylist.name);
            } else if ('success' === data[1]) {
                if (!window.myPlaylist.isAlbum) {
                    var v = data[2],
                        pOpt = {title: v.title, flv: v.url, free: true, id: v.id, trackId: v.trackId, artist_music_title_id: v.artistMusicTitleId, attrClass: "new", callback: playlistRollBottom}; // TODO: verify this.
                    window.myPlaylist.add(pOpt, playNow);
                }
            }
        }, 'json');
    }

    $.addAlbum = function(albumId) {
        $.bootstrapMessage('Adding album...', 'info');
        $.get('/album/add', {
            albumId: albumId,
            windowId: window.windowId
        }, function (data) {
            var ele = $('#slide-search div[albumid=' + albumId + ']');
            if (ele.length > 0) {
                addElementAnimation(ele);
            } else { // It's a share link.
                $('.slide-next').trigger('click');
            }
            loadAlbumSet();
            $.bootstrapMessageAuto('Album added', 'success');
        }, 'json');
    }

    function prepareVoteButton() {
    }

    function prepareNewTracks() {
        $('.jp-playlist .new').live('hover', function (e) {
            $(this).removeClass('new');
        });
    }

    $.resizeEditPlaylist = function () {
        if ($('.music-manager-content').length > 0) {
            $('#edit-playlist').css('height', $(window).height() - $('.stripe').first().height() - 170);
            $('.music-manager-content').height($(window).height() - $('.music-manager-content').offset().top - $('.footer').height());
        }
    };

    $.isSearchFormValid = function () {
        var artist = $('#artist').val(),
            musicTitle = $('#musicTitle').val();

        if (
            '' !== artist
                && '' !== musicTitle
                && $('#q').val() === artist + ' - ' + musicTitle
        ) {
            return true;
        }

        return false;
    };

    function prepareShareFacebook() {
        $('.share-facebook').live('click', function (e) {
            e.preventDefault();
            window.open($(this).attr('href'), 'Share on Facebook', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=700,height=433');
        });
    }

    function prepareShortcuts() {
        $(document).keyup(function (e) {
            if (false === $('input[type=text], textarea').is(':focus')) {
                var code = e.keyCode;
                switch (code) {
                case 32:
                    if ($('#jquery_jplayer_1').data("jPlayer").status.paused) {
                        window.myPlaylist.play();
                    } else {
                        window.myPlaylist.pause();
                    }
                    break;
                case 27: // Esc
                    $('.modal').modal('hide');
                }
            }
        });
    }

    function quoteAnimation() {
        if ($('.quote-set').length > 0) {
            var prev = $('.quote-set .quote-active'),
                cur = $('.quote-set .quote-active').next();
            if (0 == cur.length) {
                cur = $('.quote-set .quote').first();
            }
            $('.quote-set .quote').removeClass('quote-active');
            cur.addClass('quote-active');
            prev.fadeOut(function() {
                cur.fadeIn();
                setTimeout(quoteAnimation, 10000);
            });
        }
    }

    function checkBrowserCompatibility() {
        if (null === $.cookie('browser_compatibility')) {
            if ($('html').hasClass('msie')) {
                var id = $.bootstrapLoadModalDisplay("Upgrade to a better browser", "<div style='border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 75px; position: relative;'>    <div style='position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;'><a href='#' onclick='javascript:this.parentNode.parentNode.style.display='none'; return false;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-cornerx.jpg' style='border: none;' alt='Close this notice'/></a></div>    <div style='margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;'>      <div style='width: 75px; float: left;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg' alt='Warning!'/></div>      <div style='width: 275px; float: left; font-family: Arial, sans-serif;'>        <div style='font-size: 14px; font-weight: bold; margin-top: 12px;'>You are using an outdated browser</div>        <div style='font-size: 12px; margin-top: 6px; line-height: 12px;'>For a better experience using this site, please upgrade to a modern web browser.</div>      </div>      <div style='width: 75px; float: left;'><a href='http://www.firefox.com' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-firefox.jpg' style='border: none;' alt='Get Firefox 3.5'/></a></div>     <div style='float: left;'><a href='http://www.google.com/chrome' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-chrome.jpg' style='border: none;' alt='Get Google Chrome'/></a></div>    </div>  </div>");

                $(id).bind('hidden', function () {
                    $.cookie('browser_compatibility', 1);
                });
            }
        }
    }

    function removePlaylistSquareCallback(id, isPlaylist) {
        var attr = isPlaylist ? 'playlistid' : 'albumid';
        $('.playlist-square[' + attr + '=' + id + ']').remove();
        $.resizeEditPlaylist();
    }

    function ping() {
        $.get('/user/ping');
    }

    function startPing() {
        setInterval(ping, 30000);
    }

    $(document).ready(function () {
        var ac,
            message,
            st;

        verifyView();

        checkBrowserCompatibility();

        if ($('#email').length === 1) {
            $('#userEmail').html($('#email').html());
        }

        $('a#email').click(function (e) {
            e.preventDefault();
        });

        $('a.brand').click(function (e) {
            if ($('#userId').length > 0) {
                e.preventDefault();
                $('.slide-prev').trigger('click');
            }
        });

        // topbar menu
        $('.topbar').dropdown();

        $('.music-large, .music-square, .album-square').live('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (undefined !== $(this).attr('albumid')) {
                $.addAlbum($(this).attr('albumid'));
            } else {
                addToPlaylist($(this));
            }
        });

        $('.youtube-link, .download').live('click', function (e) {
            e.stopPropagation();
            window.myPlaylist.pause();
        });

        $('.youtube-link').live('click', function (e) {
            e.preventDefault();
            $.bootstrapLoadModalDisplay(
                'Youtube',
                '<iframe width="560" height="315" src="' + $(this).attr('href') + '" frameborder="0" allowfullscreen></iframe>',
                'big-modal'
            );
        });

        // placeholder on the search input.
        $('#q').placeholder();
        // autocomplete the search input from last.fm.
        $.ui.autocomplete.prototype._renderItem = function (ul, row) {
            var a = $('<li></li>')
                .data('item.autocomplete', row)
                .append('<a>' + row.label + '</a>')
                .appendTo(ul)
                .addClass(row.type);
            return a;
        };


        $.widget( "custom.catcomplete", $.ui.autocomplete, {
            _renderMenu: function( ul, items ) {
                var that = this,
                    currentCategory = "";
                $.each( items, function( index, item ) {
                    if ( item.category != currentCategory ) {
                        var t = 'track' === item.category ? 'Tracks' : 'Albums';
                        ul.append( "<li class='ui-autocomplete-category " + item.category + "'>" + t + "</li>" );
                        currentCategory = item.category;
                    }
                    that._renderItemData( ul, item );
                });
            }
        });

        ac = $('#q').catcomplete({
            source: function (request, response) {
                var start = new Date();
                $.get('/autocomplete.php', {
                    q: request.term,
                }, function (data) {
                    var end = new Date();
                    console.log("AUTOCOMPLETE: " + (end.getTime() - start.getTime()));
                    var count = 0;
                    var a =  $.map(data, function (row) {
                        return {
                            data: row,
                            label: '<div class="cover"><img src="' + ('' == row.cover ? '/img/album.png' : row.cover )+ '"/></div> <div class="description"><span>' + row.name + '</span></div>',
                            category: row.type,
                            value: row.name,
                            artist: row.artist,
                            musicTitle: row.musicTitle,
                            type: row.type
                        };
                    }, 'json');

                    response(a);
                }, 'json');
            },
            change: function (e, ui) {
                handleAutocompleteChoice(ui);
            },
            select: function (e, ui) {
                handleAutocompleteChoice(ui);
            },
            focus: function (e, ui) {
                $('#q').val(ui.item.value);
            },
            close: function (e, ui) {
            },
            open: function () {
                var left = $('.ui-autocomplete').position().left;
                $('.ui-autocomplete').css('left', (left + 15) + 'px');
            }
        });

        if ($('#status-message').length > 0) {
            message = $('#status-message p').html();
            st = $('#status-message span.status').html();

            if ($('#status-message').attr('noauto') === 'noauto') {
                $.bootstrapMessage(message, st);
            } else {
                $.bootstrapMessageAuto(message, st);
            }
        }

        // start the jplayer.
        jplayerCss = "#jp_container_1";
        window.myPlaylist = new jPlayerPlaylist({
            jPlayer: "#jquery_jplayer_1",
            cssSelectorAncestor: jplayerCss
        }, [], {supplied: 'flv', swfPath: "/obj/", free: true, callbackPlay: callbackPlay});

        $(jplayerCss + ' ul:last').sortable({
            update: function () {
                window.myPlaylist.scan();
                savePlaylist();
            }
        });

        applyOverPlaylist();
        applyRepeatTriggers();
        applyShuffleTriggers();
        applyPlaylistSettings();
        applyOverResultDiv();
        $(window).resize(function (e) {
            applyOverPlaylist();
            applyOverResultDiv();
        });

        //$('.loadModal').bootstrapLoadModal();
        $.bootstrapLoadModalInit();

        // For some reason, i can't call loadPlaylist right the way, it must wait for some initialization stuff.
        setTimeout($.initAmuzi, 1500);

        if (isLoggedIn()) {
            $('.loginRequired').fadeTo('slow', 1.0, function () {
                $(this).css('filter', 'alpha (opacity = 100)');
            });
        }

        $('#toc').tableOfContents(null, {startLevel: 2});

        preparePlaylistActions();
        prepareMusicTrackVote();
        prepareNewTracks();
        prepareShareFacebook();
        prepareShortcuts();

        $("#jquery_jplayer_1").bind($.jPlayer.event.ended + ".repeat", function () {
            $(this).jPlayer("play");
        });
        
        $.stuckCountDown = 20;

        $("#jquery_jplayer_1").bind($.jPlayer.event.timeupdate, function () {
            var progress = $('#jquery_jplayer_1').data("jPlayer").status.currentPercentAbsolute;
            if (progress >= 99 && progress != $.lastTrackProgress) {
                $.lastTrackProgress = progress;
                window.myPlaylist.next();
            } else if (progress == $.lastTrackProgress) {
                if ($.stuckCountDown-- <= 0) {
                    window.myPlaylist.next();
                    $.stuckCountDown = 20;
                } else {
                    console.log("stuck! countdown: " + $.stuckCountDown);
                }
            } else {
                $.stuckCountDown = 20;
            }
            $.lastTrackProgress = progress;
        });

        $('#jquery_jplayer_1').bind($.jPlayer.event.error, function () {
            console.log('MUSIC ERROR');
            window.myPlaylist.next();
        });

        $.resizeEditPlaylist();

        $.slideInit();

        $('.playlist-square[playlistid] .play').live('click', function (e) {
            e.preventDefault();
            $.loadPlaylist($(this).parent().attr('playlistid'));
        });

        $('.playlist-square[albumid] .play').live('click', function (e) {
            e.preventDefault();
            $.loadPlaylist($(this).parent().attr('albumid'), {isAlbum: true});
        });

        $('.playlist-square .remove').live('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure?')) {
                var name = $(this).parent().find('.name').html(),
                    p = $(this).parent(),
                    isPlaylist = undefined !== p.attr('playlistid'),
                    id = $(this).parent().attr(isPlaylist ? 'playlistid' : 'albumid');
                rmPlaylist(id, isPlaylist, removePlaylistSquareCallback);
                if (name === window.myPlaylist.name) {
                    $.loadPlaylist('');
                }
            }
        });

        $('.playlist-square').live({mouseenter: function () {
            $(this).find('.name').css('opacity', '1.0');
        }, mouseleave: function () {
            $(this).find('.name').css('opacity', '0.0');
        }});

        if ($('div.container.regular').length > 0) {
            $('body').css('overflow', 'auto');
        }
        loadPlaylistSet();
        loadAlbumSet();
        startPing();
        quoteAnimation();
    });
}(jQuery, undefined));
