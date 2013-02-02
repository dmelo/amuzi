/**
 * resultset.js
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
/**
 * Set of functions to manage the list of results.
 */


(function ($, undefined) {
    'use strict';

    $.ResultSet = function () {
        this.searchString = "";
        this.searchPage = 1;
    };

    var resultSet = new $.ResultSet();


    /**
     * Transform an integer from 0 to 100 to a leading 0 number with up to two digits.
     *
     * @param num Number to be transformed.
     * @return Returns the two digit leading 0 number.
     */
    $.ResultSet.prototype.twoDigit = function (num) {
        var str = '';
        if (num < 10) {
            str += '0';
        }

        return str + num;
    };

    /**
     * Put number of seconds into HH:MM:SS format when time is more than or equals to 3600 (one hour) or MM:SS, otherwise.
     *
     * @param time Time, in seconds.
     * @return Returns a string represening time in HH:MM:SS or MM:SS format.
     */
    $.ResultSet.prototype.secondsToHMS = function (time) {
        var h = 0,
            m = 0,
            s = 0,
            str = '';

        h = Math.floor(time / 3600);
        time -= 3600 * h;
        m = Math.floor(time / 60);
        time -= 60 * m;
        s = time;

        console.log("h: " + h + ". m: " + m + ". s: " + s);

        if (h > 0) {
            str = this.twoDigit(h) + ":";
        }

        str += this.twoDigit(m) + ':';
        str += this.twoDigit(s);

        return str;
    };

    /**
     * Cleans the table of results and let it ready to a search.
     */
    $.ResultSet.prototype.cleanTable = function () {
        $('#result .music-large').remove();
        $('#more-results').css('opacity', '0.0');
        $('#more-results').css('filter', 'alpha (opacity = 100)');
    };

    $.ResultSet.prototype.getControl = function (v) {
        var url = 'url' in v ? v.url : v.id;
        return '<a href="' + url + '" title="' + v.title + '" class="addplaylist"><img src="/img/play_icon.png"/></a>';
    };

    $.ResultSet.prototype.getDescription = function (v) {
        return '<div class="description"><div class="duration">' + this.secondsToHMS(v.duration) + '</div><div class="title"><a href="' + v.url + '">' + v.title + '</a></div></div>';
    };

    $.ResultSet.prototype.getAlbumDescription = function (v) {
        return '<div class="description"><div class="duration">' + this.secondsToHMS(v.duration) + '</div><div class="title">' + v.artist + ' - ' + v.name + '</div></div>';
    };

    $.ResultSet.prototype.getMusicLarge = function (v, objectType) {
        return '<div class="music-large object-' + objectType + '" fid="' + v.fid + '" fcode="' + v.fcode + '" trackId="' + v.id + '"><div class="image"><img src="' + v.cover + '"/><div class="duration">' + this.secondsToHMS(v.duration) + '</div></div><div class="title"><a href="' + v.url + '">' + v.title + '</a></div><div class="play">' + this.getControl(v) + '</div>';
    };

    $.ResultSet.prototype.getMusicSquare = function (v) {
        return '<div class="music-square" trackId="' + v.id + '" artist="' + v.artist + '" musicTitle="' + v.musicTitle + '"><div class="cover"><img src="' + v.cover + '" alt="cover"/></div><div class="overlay"></div>' + this.getDescription(v) + '<div class="play">' + this.getControl(v) + '</div>';
    };

    $.ResultSet.prototype.getAlbumSquare = function (v) {
        return '<div class="album-square music-square" albumid="' + v.id + '" artist="' + v.artist + '" name="' + v.name + '"><div class="cover"><img src="' + v.cover + '" alt="cover"/></div><div class="overlay"></div>' + this.getAlbumDescription(v) + '<div class="play">' + this.getControl(v) + '</div>';
    };

    $.ResultSet.prototype.appendTable = function (v, objectType) {
        if (0 === $('[trackId=' + v.id + ']').length) {
            $('#result').append(this.getMusicLarge(v, objectType));
        }
        $('#more-results').css('opacity', '1.0');
        $('#more-results').css('filter', 'alpha (opacity = 100)');
    };

    $.ResultSet.prototype.searchMore = function () {
        $.bootstrapMessage('Loading...', 'info');
        this.searchPage += 1;
        $.get('/api/search', {
            q: this.searchString,
            artist: $('#artist').val(),
            musicTitle: $('#musicTitle').val(),
            limit: 9,
            offset: 1 + (9 * (this.searchPage - 1))
        }, function (data) {
            $.bootstrapMessageOff();
            $.each(data, function (i, v) {
                resultSet.appendTable(v, 'track');
            });
        }, 'json').error(function (data) {
            $.bootstrapMessageAuto('An error occured', 'error');
        });
    };

    $.ResultSet.prototype.getSimilarTracks = function (artist, musicTitle) {
        $.get('/api/searchsimilar', {
            artist: artist,
            musicTitle: musicTitle
        }, function (data) {
            $.each(data, function (i, v) {
                resultSet.appendTable(v, 'music');
            });
        }, 'json');
    };

    $(document).ready(function () {
        $('.music-large').live({mouseenter: function () {
            $(this).find('a').css('color', 'white');
            $(this).find('.play').css('display', 'block');
        }, mouseleave: function () {
            $(this).find('a').css('color', 'black');
            $(this).find('.play').css('display', 'none');
        }});

        // query youtube for videos and fill the result table.
        $('#search').ajaxForm({
            dataType: 'json',
            success: function (data) {
                $.bootstrapMessageOff();
                $.each(data, function (i, v) {
                    resultSet.appendTable(v, 'track');
                });
            },
            error: function (data) {
                $.bootstrapMessageAuto('Error searching for music', 'error');
            },
            beforeSubmit: function () {
                resultSet.cleanTable();
                resultSet.searchString = $('#q').val();
                resultSet.searchPage = 1;
                $.bootstrapMessage('Loading...', 'info');
            }
        });

        $('#more-results').click(function (e) {
            resultSet.searchMore();
        });

        $('.music-square').live({mouseenter: function () {
            $(this).find('.description, .play').css('display', 'block');
            $(this).find('.overlay').css('display', 'none');
        }, mouseleave: function () {
            $(this).find('.description, .play').css('display', 'none');
            $(this).find('.overlay').css('display', 'block');
        }});

    });
}(jQuery, undefined));
