/**
 * search-exec.js
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

(function ($, undefined) {
    var search,
        searchSimilarList = [],
        incrementSimilarRunning = false;

    /**
     * The search object have to implement the following methods:
     * .insert(v);
     * .clean();
     */

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
            uri = null,
            params;

        if (num > 0 && 'undefined' !== typeof m && null !== m) {
            if ('type' in m && 'album' === m.type) {
                uri = '/api/searchalbum';
                params = {
                   artist: m.artist,
                   album: m.musicTitle
                };
            } else if ('type' in m && 'track' === m.type) {
                uri = '/api/searchmusic';
                params = {
                    artist: m.artist,
                    musicTitle: m.musicTitle
                };
            }

            if (null !== uri) {
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
            } else {
                console.log('error: invalid parameters on searchMusic');
            }
        }
    }

    function searchMulti(q) {
        $.get('/api/autocomplete', {
            q: q
        }, function(data) {
            if (0 === data.length) {
                $.bootstrapMessageAuto('No results found', 'info');
            } else {
                console.log('results found');
                console.log(data);

                searchMusic(data, data.length);
            }
        }, 'json').error(function (data) {
            $.bootstrapMessageAuto(data[0], data[1]);
        });
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
            search = new $.IncBoard();

            $('.music-large').live('click', function (e) {
                searchSimilar($(this).parent());
            });

            $('.incboard-cell').live('click', function (e) {
                searchSimilar($(this));
            });
            $(window).bind('resize', $.proxy(search.ibb, 'resize'));
        } else if (1 === $('#search').length) {
            search = new $.ResultSet();
            $('#more-results').click(function (e) {
                // TODO: implement the search-more button.
                // resultSet.searchMore();
            });

            $('#result #close-results').live('click', function(e) {
                e.preventDefault();
                search.clean();
            });

        }

        $('.music-square, .album-square').live({mouseenter: function () {
            $(this).find('.description, .play').css('display', 'block');
            $(this).find('.overlay').css('display', 'none');
        }, mouseleave: function () {
            $(this).find('.description, .play').css('display', 'none');
            $(this).find('.overlay').css('display', 'block');
        }});


        $('form.search').ajaxForm({
            dataType: 'json',
            success: function (data) {
                if ('error' in data) {
                    console.log('error during searchsimilar: ' + data.error);
                } else {
                    /*
                    loadSimilarMusic(data, 10);
                    */
                }
            },
            error: function (data) {
                $.bootstrapMessageAuto('Error searching for music', 'error');
            },
            beforeSubmit: function() {
                $('#subtitle').subtitleInit();
                $.bootstrapMessage('Loading...', 'info');
                search.clean();
                var obj = new Object();
                obj.artist = $('#artist').val();
                obj.musicTitle = $('#musicTitle').val();
                obj.type = $('#type').val();
                console.log('artist: ' + obj.artist + '. musicTitle: ' + obj.musicTitle + '. type: ' + obj.type);
                if ($.isSearchFormValid()) {
                    searchMusic([obj], 1, searchMusicCallbackCenter);
                } else { // search in a way that many music can be retrieved.
                    searchMulti($('#q').val());
                }
            }
        });
    });

}(jQuery, undefined));
