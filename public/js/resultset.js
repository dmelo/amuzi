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

        var idList = [];

        /**
         * Transform an integer from 0 to 100 to a leading 0 number with up to two digits.
         *
         * @param num Number to be transformed.
         * @return Returns the two digit leading 0 number.
         */
        var twoDigit = function (num) {
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
        this.secondsToHMS = function (time) {
            var h = 0,
                m = 0,
                s = 0,
                str = '';

            h = Math.floor(time / 3600);
            time -= 3600 * h;
            m = Math.floor(time / 60);
            time -= 60 * m;
            s = time;

            if (h > 0) {
                str = twoDigit(h) + ":";
            }

            str += twoDigit(m) + ':';
            str += twoDigit(s);

            return str;
        };

        this.getControl = function (v) {
            var url = 'url' in v ? v.url : v.objId, ret = '',
                classes = ['play', 'addplaylist'],
                subtitles = ['Play it', 'Add to your collection'];
            

            for (var i = 0; i < classes.length; i++) {
                ret += '<div class="' + classes[i] + '"><a href="' + v.url + '" title="' + v.title + '"><img alt="' + subtitles[i] + '" src="/img/' + classes[i] + '_icon.png"/></a></div>';
            }
            
            return ret;
        };

        this.getDescription = function (v) {
            var duration = 'track' === v.type ? '<div class="duration">' + this.secondsToHMS(v.duration) + '</div>' : '',
                url = 'url' in v ? v.url : '#';
            if ('' == duration) {
                console.log(v);
            }

            return '<div class="description">' + duration + '<div class="title"><a href="' + url + '">' + v.title + '</a></div></div>';
        };

        this.getMusicLarge = function (v) {
            return '<div class="music-large object-' + v.type + '" fid="' + v.fid + '" fcode="' + v.fcode + '" ' + v.type + 'id="' + v.id + '"><div class="image"><img src="' + v.cover + '"/></div>' + this.getDescription(v) + '<span class="description-type">Type: ' + v.type + '</span>' + this.getControl(v);
        };

        this.insert = function (v) {
            if (0 === $('[trackId=' + v.id + ']').length) {
                console.log("ResultSet::insert");
                $('#result').append(this.getMusicLarge(v));
                this.idList.push(v.objId);
                console.log(this.idList);
                console.log(v);
            }
            $('#more-results').css('display', 'block');
            $('#result').css('display', 'block');

            return true;
        };

        this.clean = function() {
            this.idList = [];

            $('#result .music-large').remove();
            $('#result').css('display', 'none');
            $('#more-results').css('display', 'none');
        };

        this.getIdList = function() {
            return this.idList;
        }
    };

    var resultSet = new $.ResultSet();

    $.ResultSet.prototype.getAlbumDescription = function (v) {
        var duration = 'duration' in v ? '<div class="duration">' + this.secondsToHMS(v.duration) + '</div>' : '';
        return '<div class="description">' + duration + '<div class="title">' + v.artist + ' - ' + v.name + '</div></div>';
    };


    $.ResultSet.prototype.getMusicSquare = function (v) {
        return '<div class="music-square" trackId="' + v.id + '" artist="' + v.artist + '" musicTitle="' + v.musicTitle + '"><div class="cover"><img src="' + v.cover + '" alt="cover"/></div>' + this.getControl(v) + this.getDescription(v);
    };

    $.ResultSet.prototype.getAlbumSquare = function (v) {
        return '<div class="album-square music-square" albumid="' + v.id + '" artist="' + v.artist + '" name="' + v.name + '"><div class="cover"><div class="side"><img src="/img/album-side.png"/></div><img src="' + v.cover + '" alt="cover" class="cover-img" /></div><div class="overlay"></div>' + this.getAlbumDescription(v) + this.getControl(v) + '</div>';
    };
}(jQuery, undefined));
