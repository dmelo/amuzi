/**
 * default.js
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2014  Diogo Oliveira de Melo
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
        loadingPlaylistMessage = null,
        globalResponse;

    window.myPlaylist = null;
    window.windowId = parseInt(Math.random() * 1000000);
    $.isLoggedIn = function() {
        return $('#email').length === 1;
    };

    $.commands = new Commands();

    $.modalWrapper = "#load-modal-wrapper";
    // Soon to be deprecated.
    function savePlaylist() {
        $.post('/playlist/save', {
            playlist: window.myPlaylist.original,
            name: window.myPlaylist.name
        }, function (data) {
        }).error(function (data) {
            $.bootstrapMessageAuto($.i18n._('Error saving playlist.'), 'error');
        });
    }

    /**
     * Scroll to the bottom of the playlist.
     */
    function playlistRollBottom() {
        $('.jp-playlist').scrollTop($('.jp-playlist').prop('scrollHeight'));
    }

    function setInterfaceShuffle(shuffle, playNow) {
        playNow = 'undefined' == typeof(playNow) ? true : playNow;
        shuffle = parseInt(shuffle, 10);
        console.log("setInterfaceShuffle " + shuffle + ". playNow: " + playNow);
        window.myPlaylist.shuffle(shuffle, playNow);
        $('.jp-shuffle-off').css('display', shuffle ? 'block' : 'none');
        $('.jp-shuffle').css('display', shuffle ? 'none' : 'block');

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
                $.loadPlaylist(parseInt(data[0], 10), {isAlbum: data[1]});
            }
        }, 'json').error(function(data) {
            $.bootstrapMessageAuto($.i18n._('Couldn\'t load next album/playlist'), 'error');
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
    $.loadPlaylist = function (id, optArg) {
        var baseOpt = {
            isAlbum: false,
            forcePlaylist: false,
            playNow: false
        },  opt = $.extend(baseOpt, optArg),
            uri = opt.isAlbum ? '/album/load' : '/playlist/load',
            item = opt.isAlbum ? 'album' : 'playlist',
            options;


        window.myPlaylist.removeAll();
        window.myPlaylist.type = opt.isAlbum ? 'album' : 'playlist';

        if (null === loadingPlaylistMessage) {
            loadingPlaylistMessage = 'Already loading ' + item + ', please wait.';
            if (typeof (id) === 'number' || typeof (id) === 'undefined') {
                options = { id: id, forcePlaylist: opt.forcePlaylist};
            } else {
                throw "First argument must be a number.";
            }

            $.post(uri, options, function (data) {
                if (null !== data) {
                    $('.jp-title').css('display', 'block');
                    // $("#jquery_jplayer_1").data("jPlayer").status.paused = true;
                    window.myPlaylist.pause();
                    window.myPlaylist.id = data.id;
                    window.myPlaylist.name = data.name;
                    window.myPlaylist.type = data.type;
                    setInterfaceShuffle(data.shuffle, opt.playNow);
                    $('#jquery_jplayer_1').data('jPlayer')._loop(1 === parseInt(data.repeat, 10));

                    $.each(data.trackList, function (i, v) {
                        if ('url' in v) {
                            var obj = {title: v.title, free: true, id: v.id, artist_music_title_id: v.artist_music_title_id};
                            obj.m4a = v.url;
                            window.myPlaylist.add(obj, false);
                        }
                    });
                    applyOverPlaylist();

                    if (!opt.isAlbum && 'number' === typeof options.id) {
                        $('.playlist-square').removeClass('current-playlist');
                        $('.playlist-square[playlistid=' + options.id + ']').addClass('current-playlist');
                    }

                    var index = 0;
                    if ('undefined' !== typeof opt && 'playLast' in opt && opt.playLast) {
                        index = -1;;
                    } else {
                        index = parseInt(data.currentTrack, 10);
                    }
                   setTimeout(function() { 
                        window.myPlaylist.setCurrent(index);
                        console.log('setCurrent ' + index);
                        if (opt.playNow) {
                            window.myPlaylist.play();
                        } else {
                            window.myPlaylist.pause();
                        }
                   }, 1000);
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
    };

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
        }, 'json').error(function (e) {
            $.bootstrapMessageAuto($.i18n._('Error removing ' + controller), 'error');
        });
    }

    $.initAmuzi = function () {
        if (1 === $('#userId').length) {
            $.loadPlaylist();
        }
        setTimeout("$.commands.runProgram()", 1500);
    };

    function setPlaylistRepeat(name, repeat) {
        name = name || 'default';
    }

    function callbackPlay(current) {
        console.log('callbackPlay');
        if ('playlist' === window.myPlaylist.type) {
            $.post('/playlist/setcurrent', {
                name: window.myPlaylist.name,
                current: current
            }, function (data) {
                if ('error' === data[1]) {
                    $.bootstrapMessageAuto(data[0], data[1]);
                }
            }, 'json').error(function (e) {
                // TODO: fix the setcurrent.
            });
        } else { // album
        }
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
        var uri = window.myPlaylist.type == 'album' ? '/album' : '/playlist';
        uri += '/setrepeat';

        if (isLoggedIn()) {
            window.myPlaylist.loopLock = true;
            $.post(uri, {
                id: window.myPlaylist.id,
                repeat: repeat
            }, function (data) {
                if ('error' === data[1]) {
                    $.bootstrapMessageAuto(data[0], data[1]);
                }
                window.myPlaylist.loopLock = false;
            }, 'json').error(function (e) {
                $.bootstrapMessageAuto($.i18n._('Error saving settings'), 'error');
                window.myPlaylist.loopLock = false;
            });
        }
    }

    function applyRepeatTriggers() {
        $('.jp-repeat').click(function (e) {
            setRepeat(1);
        });

        $('.jp-repeat-off').click(function (e) {
            setRepeat(0);
        });
    }

    // Shuffle
    function setShuffle(shuffle) {
        var uri = window.myPlaylist.type === 'album' ? '/album' : '/playlist';

        uri += '/setshuffle';

        if (isLoggedIn()) {
            window.myPlaylist.shuffledLock = true;
            $.post(uri, {
                id: window.myPlaylist.id,
                shuffle: shuffle
            }, function (data) {
                if ('error' === data[1]) {
                    $.bootstrapMessageAuto(data[0], data[1]);
                }
                window.myPlaylist.shuffledLock = false;
            }, 'json').error(function (e) {
                $.bootstrapMessageAuto($.i18n._('Error saving settings'), 'error');
                window.myPlaylist.shuffledLock = false;
            });
        }
    }

    function applyShuffleTriggers() {
        $('.jp-shuffle').click(function (e) {
            setShuffle(1);
        });

        $('.jp-shuffle-off').click(function (e) {
            setShuffle(0);
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

    window.isLoggedIn = isLoggedIn;

    function isMainPage() {
        return (isLoggedIn() && ['/', '/index/incboard', '/index/incboard/'].indexOf(window.location.pathname) != -1);
    }
    $.isMainPage = isMainPage;

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
                $(item).popover({html:true, content: $(item).find('.playlist-info').html(), trigger: 'mouseover'});
            });
        }).error(function (data) {
            $.resizeEditPlaylist();
        });
    }

    function loadAlbumSet() {
        $.get('/album/list', function(data) {
            $('.music-manager#albums .stripe').html(data);
            $('div[albumid]').each(function(i, item) {
                $(item).popover({html:true, content: $(item).find('.album-info').html(), trigger: 'mouseover', placement: 'right', selector: '#slide-music-manager'});
            });
        }).error(function (data) {
            $.bootstrapMessageAuto(
                $.i18n._('Error loading your album list, please try again later'), 'error'
            );
        });
    }

    $.rendered_newPlaylist = function () {
        $('form#newPlaylist').ajaxForm({
            dataType: 'json',
            success: function (data) {
                $.loadPlaylist(parseInt(data[2], 10));
                $.bootstrapMessageAuto(data[0], data[1]);
                $($.modalWrapper).modal('hide');
                loadPlaylistSet();
            },
            error: function (data) {
                $.bootstrapMessageAuto(
                    $.i18n._('Error saving. Something went wrong.'), 'error'
                );
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

    function handleOfflineAutocompleteChoice(e, ui) {
        window.location.pathname = '/' + ui.item.type + '/' + ui.item.artist
            + ('album' === ui.item.type ? '/' + ui.item.musicTitle : '');
    }

    function handleAutocompleteChoice(e, ui) {
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
        $('body').delegate('.jp-playlist ul li div', 'mouseover', function (e) {
            $(this).find('.jp-free-media').css('opacity', '1.0').css('-moz-opacity', '1.0').css('filter', 'alpha(opacity=100)');
        });

        $('body').delegate('.jp-playlist ul li div', 'mouseleave', function (e) {
            $(this).find('.jp-free-media').css('opacity', '0.0').css('-moz-opacity', '0.0').css('filter', 'alpha(opacity=0)');
        });
    }

    function prepareMusicTrackVote() {
        $('body').delegate('.vote', 'click', function (e) {
            e.preventDefault();
            $.get($(this).attr('href'), function (data) {
                $.bootstrapMessageAuto(data[0], data[1]);
                $($.modalWrapper).modal('hide');
            }, 'json').error(function (e) {
                $.bootstrapMessageAuto(
                    $.i18n._('Error registering vote'), 'error'
                );
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
            }).error(function (e) {
                $.bootstrapMessageAuto('Error detecting your preferences, please try reloading', 'error');
            });
        }
    }

    $.callback_userSettings = function (data) {
        // verifyView();
        window.location.pathname = window.location.pathname;
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
        clone.animate({left: "100%"}, {
            duration: 1500,
            complete: function () {
                clone.remove();
            }
        });
    }

    function addToPlaylist(e, playNow, callback) {
        var trackId = e.attr('trackId'),
            artist = e.attr('artist'),
            musicTitle = e.attr('musicTitle'),
            isMozaic = e.hasClass('music-square') && !e.hasClass('incboard-cell');

        if ($('.playlist-row[track_id=' + trackId + ']').length > 0) {
            if (!confirm($.i18n._('You already have this track on your playlist. Are you sure you want to insert it again?'))) {
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

        $.addTrack(trackId, artist, musicTitle, playNow, isMozaic);

        addElementAnimation(e);
    }

    $.addTrack = function(trackId, artist, musicTitle, playNow, isMozaic) {
        var options;

        console.log("ADDTRACK");
        console.log(isMozaic);

        options = {
            id: trackId,
            playlist: window.myPlaylist.name,
            isAlbum: window.myPlaylist.type === 'album',
            artist: artist,
            musicTitle: musicTitle,
            windowId: window.windowId,
            isMozaic: isMozaic,
            searchId: isMozaic ? 0 : window.searchId,
            rfDepth: window.rfDepth,
        };

        $.bootstrapMessageLoading();
        $.post('/playlist/addtrack', options, function (data) {
            $.bootstrapMessageAuto(data[0], data[1]);
            if ('error' === data[1]) {
                $.loadPlaylist();
            } else if ('success' === data[1]) {
                if ('playlist' == window.myPlaylist.type) {
                    var v = data[2],
                        pOpt = {title: v.title, free: true, id: v.id, trackId: v.trackId, artist_music_title_id: v.artistMusicTitleId, attrClass: "new", callback: playlistRollBottom}; // TODO: verify this.
                        pOpt.m4a = v.url;
                    window.myPlaylist.add(pOpt, playNow);
                } else if ('album' === window.myPlaylist.type && playNow) {
                    $.loadPlaylist(undefined, {playLast: true, forcePlaylist: true, playNow: playNow});
                    setInterfaceShuffle(0, playNow);
                }
            }
        }, 'json').error(function (e) {
            $.bootstrapMessageAuto(
                $.i18n._('Error adding track. Please, try again.'), 'error'
            );
        });
    };

    $.addAlbum = function(albumId, playNow) {
        var messageId = $.bootstrapMessage($.i18n._('Adding album...'), 'info');
        $.get('/album/add', {
            albumId: albumId,
            windowId: window.windowId,
            searchId: window.searchId,
            rfDepth: window.rfDepth,
        }, function (data) {
            var ele = $('#slide-search div[albumid=' + albumId + ']');
            if (ele.length > 0) {
                addElementAnimation(ele);
            } else { // It's a share link.
                $('.slide-next').trigger('click');
            }
            loadAlbumSet();
            $.bootstrapMessageOff(messageId);
            $.bootstrapMessageAuto('Album added', 'success');
            if (playNow) {
                $.loadPlaylist(parseInt(albumId, 10), { isAlbum: true, playNow: playNow });
            }
        }, 'json').error(function (e) {
            $.bootstrapMessageAuto(
                $.i18n._('Error adding your album. Please, try again.'), 'error'
            );
        });
    };

    function prepareVoteButton() {
    }

    function prepareNewTracks() {
        $('body').delegate('.jp-playlist .new', 'mouseover', function (e) {
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

        if ('' !== artist && '' !== musicTitle && $('#q').val() === artist + ' - ' + musicTitle) {
            return true;
        }

        return false;
    };

    function prepareShareFacebook() {
        $('body').delegate('.share-facebook', 'click', function (e) {
            e.preventDefault();
            window.open($(this).attr('href'), $.i18n._('Share on Facebook'), 'toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=700,height=433');
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
            if (0 === cur.length) {
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
        $.get('/index/ping', {
            windowId: window.windowId
        }, function(data) {
            if (0 == data) {
                window.location.pathname = '/';
            }
        }, 'json');
    }

    function startPing() {
        setInterval(ping, 30000);
    }

    function showIntroVideo() {
        $.bootstrapLoadModalDisplay('Introdução', '<iframe width="720" height="540" src="http://www.youtube.com/embed/UGl-sSa5ibI?autoplay=1" frameborder="0" allowfullscreen></iframe>', 'modal-wide');
    }

    function openAutocomplete() {
        $('.ui-autocomplete').addClass(0 === $('#userId').length ? 'ui-autocomplete-logout' : 'ui-autocomplete-login');
    }

    function nothing() {
    }

    var callbackAutocomplete = function(data) {
        var end = new Date(),
            count = 0,
            a = null !== data ? $.map(data, function (row) {
            return {
                data: row,
                label: '<div class="cover"><img src="' + ('' === row.cover ? '/img/album.png' : row.cover )+ '"/></div> <div class="description"><span>' + row.name + '</span></div>',
                category: row.type,
                value: row.name,
                artist: row.artist,
                musicTitle: row.musicTitle,
                type: row.type
            };
        }, 'json') : [];

        globalResponse(a);
    };

    function acError(e) {
        $.bootstrapMessageAuto(
            $.i18n._('Error loading suggestions. Please, try reloading your browser.'),
            'error'
        );
    }

    function addElement(ele, playNow) {
        if (ele.hasClass('incboard-cell')) {
            ele.trigger('searchsimilar');
        }

        if (undefined !== ele.attr('albumid')) {
            $.addAlbum(ele.attr('albumid'), playNow);
        } else {
            addToPlaylist(ele, playNow);
        }
    }

    function swiperButtons() {
        var next = window.swiper.swipeNext,
            prev = window.swiper.swipePrev;

        window.swiper.lock = false;

        window.swiper.swipeNext = function() {
            next();
            $('#screen-search').addClass('dark').removeClass('light');
            $('#screen-music').addClass('light').removeClass('dark');
        };

        window.swiper.swipePrev = function() {
            prev();
            $('#screen-music').addClass('dark').removeClass('light');
            $('#screen-search').addClass('light').removeClass('dark');

        };

        window.swiper.addCallback('SlideChangeStart', function() {
            window.swiper.lock = true;
        });
            
        window.swiper.addCallback('SlideChangeEnd', function() {
            window.swiper.lock = false;
        });


        $('#screen-music').click(function(e) {
            window.swiper.swipeNext();
        });

        $('#screen-search').click(function(e) {
            window.swiper.swipePrev();
        });

    }

    $(document).ready(function () {
        var ac,
            message,
            st;

        if (isLoggedIn()) {
            verifyView();
            /*
            if ($('.search-inner').length > 0) {
                $('.search-loggedoff').remove();
            }
            */
        }

        // For debugging purposes only.
        window.throwMany = function(n) {
            if (n > 0) {
                setTimeout("window.throwMany(" + (n - 1) + ")", 50);
                $.get('/album/info/id/' + n);
            }
        };


        checkBrowserCompatibility();

        if ($.isLoggedIn()) {
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

        $('body').delegate('.music-large .addplaylist, .music-square .addplaylist, .album-square .addplaylist', 'click', function (e) {
            e.preventDefault();
            addElement($(this).parent(), false);
        });

        $('body').delegate('.music-large .play, .music-square .play, .album-square .play, .similarity-list .object-playlist .play, .music-square .description', 'click', function (e) {
            e.preventDefault();
            $('.modal .close').trigger('click');
            addElement($(this).parent(), true);
        });

        $('body').delegate('.music-large, .incboard-cell', 'click', function(e) {
            e.preventDefault();
            var ele = $(e.target.parentNode.parentNode);
            if (!ele.hasClass('play')  && !ele.hasClass('addplaylist')) {
                addElement($(this), true);
            }
        });

        $('body').delegate('.object-playlist .cover', 'click', function(e) {
            if ($.isLoggedIn()) {
                e.preventDefault();
                $(this).parent().find('.play').trigger('click');
            } else {
                $.bootstrapMessageLoading();
            }
        });

        $('body').delegate('.youtube-link, .download', 'click', function (e) {
            e.stopPropagation();
            window.myPlaylist.pause();
        });

        $('body').delegate('.youtube-link', 'click', function (e) {
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
                        var t = item.category.charAt(0).toUpperCase() + item.category.slice(1) + 's';
                        ul.append( "<li class='ui-autocomplete-category " + item.category + "'>" + t + "</li>" );
                        currentCategory = item.category;
                    }
                    that._renderItemData( ul, item );
                });
            }
        });

        var acOption = {
            source: function (request, response) {
                globalResponse = response;
                $.get('/autocomplete.php', {
                    q: request.term,
                }, callbackAutocomplete, 'json').error(acError);
            }, messages: {
                noResults: '',
                results: function() {}
            }, change: handleAutocompleteChoice,
            select: handleAutocompleteChoice,
            focus: nothing,
            close: nothing,
            open: openAutocomplete
        };

        if ($('#userId').length > 0) {
            $('#q').catcomplete(acOption);
        } else {
            acOption.source = function (request, response) {
                globalResponse = response;
                $.get('/autocomplete.php', {
                    logout: true,
                    q: request.term,
                }, callbackAutocomplete, 'json').error(acError);
            };

            acOption.select = handleOfflineAutocompleteChoice;
            acOption.change = handleOfflineAutocompleteChoice;

            $('#q').catcomplete(acOption);
        }

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
            cssSelectorAncestor: jplayerCss,
            repeat: function(e) {
                console.log(e);
            }
        }, [], {
            supplied: 'm4a',
            swfPath: "/obj/",
            free: true,
            consoleAlerts: true,
            errorAlerts: true,
            warningHint: true,
            callbackPlay: callbackPlay,
        });

        $(jplayerCss + ' ul:last').sortable({
            update: function () {
                window.myPlaylist.scan();
                if ('playlist' === window.myPlaylist.type) {
                    savePlaylist();
                }
            }
        });
        window.myPlaylist.shuffledLock = false;
        window.myPlaylist.loopLock = false;

        applyOverPlaylist();
        applyRepeatTriggers();
        applyShuffleTriggers();
        applyPlaylistSettings();
        applyOverResultDiv();
        $(window).resize(function (e) {
            applyOverPlaylist();
            applyOverResultDiv();
            $.resizeEditPlaylist();
        });

        //$('.loadModal').bootstrapLoadModal();
        $.bootstrapLoadModalInit();

        // For some reason, i can't call loadPlaylist right the way, it must
        // wait for some initialization stuff.
        setTimeout($.initAmuzi, 1500);

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
                    $.stuckCountDown = 20;
                    console.log("REFRESHING COUNTDOWN: " + $.stuckCountDown);
                    window.myPlaylist.next();
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
            if (myPlaylist.original.length > 1) {
                window.myPlaylist.next();
            }
        });

        $.resizeEditPlaylist();

        $('body').delegate('.music-manager .playlist-square .play', 'click', function (e) {
            e.preventDefault();
            var type = $(this).parent().attr('albumid') ? 'albumid' : 'playlistid';
            $.loadPlaylist(parseInt($(this).parent().attr(type), 10), {
                isAlbum: 'albumid' === type,
                playNow: true
            });
        });

        $('body').delegate('.music-manager .playlist-square .remove', 'click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure?')) {
                var name = $(this).parent().find('.name').html(),
                    p = $(this).parent(),
                    isPlaylist = (undefined !== p.attr('playlistid')),
                    id = $(this).parent().attr(isPlaylist ? 'playlistid' : 'albumid');
                rmPlaylist(id, isPlaylist, removePlaylistSquareCallback);
                if (name === window.myPlaylist.name) {
                    $.loadPlaylist();
                }
            }
        });


        if ($('div.container.regular').length > 0) {
            $('body').css('overflow', 'auto');
        }

        if (isMainPage()) {
            loadPlaylistSet();
            loadAlbumSet();
            if (isLoggedIn()) {
                startPing();
            }
        } else if ($('#jp_container_1.lonely').length > 0) {
            if (1 === $('#load-playlist').length) {
                $.loadPlaylist(parseInt($('#load-playlist').html(), 10), {isAlbum: $('#load-playlist').attr('isAlbum')});
            }

            $('body').delegate('#jp_container_1.lonely', 'mouseover', function() {
                $('#jp_container_1 .jp-playlist').css('display', 'block');
            });

            $('body').delegate('#jp_container_1.lonely', 'mouseleave', function() {
                $('#jp_container_1 .jp-playlist').css('display', 'none');
            });

        }
        quoteAnimation();
        $('.intro-video a').click(function(e) {
            e.preventDefault();
            showIntroVideo();
        });

        $('.intro-video a img').popover({
            'trigger': 'mouseover'
        });

        $(document).delegate('.item-square', 'mouseover', function(e) {
            $(this).find('.name').css('opacity', '1.0');
            if ($(this).parent().parent().hasClass('music-manager')) {
                $(this).find('.remove').css('display', 'block');
            }
            $(this).find('.info').css('display', 'block');
        });

        $(document).delegate('.item-square', 'mouseleave', function(e) {
            $(this).find('.name').css('opacity', '0.0');
            $(this).find('.remove').css('display', 'none');
            $(this).find('.info').css('display', 'none');
        });

        $('#facebook-connect').click(function(e) {
            $.bootstrapMessage(
                $.i18n._('Make sure your browser is not blocking Facebook\'s POP-UP!'),
                'warning'
            );
        });

        // Settings titles of menu items
        $('.navbar .nav li a').each(function(index) {
            $(this).attr('title', $(this).html());
        });

        $('.swiper-container').height($(window).height() - $('.navbar').height() - $('footer').height());
        $('.mozaic').width($(window).width());

        window.swiper = $('.swiper-container').swiper({
            mode: 'horizontal',
            loop: false,
        });


        if ('undefined' !== typeof window.swiper.enableKeyboardControl) {
            window.swiper.enableKeyboardControl();
            swiperButtons();
        }

    });
}(jQuery, undefined));
