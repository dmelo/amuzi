/**
 * incboard-cell.js
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

"use strict";

function IncBoardCell() {
    var cellSizeX = 56,
        cellSizeY = 44,
        content = null,
        row = null,
        col = null,
        self = this;

    getHtml = function() {
        var v = self.getContent(),
            resultSet = new $.ResultSet();

        console.log(v);
        ret = ('album' === v.type) ? $(resultSet.getAlbumSquare(v)) : $(resultSet.getMusicSquare(v));

        ret.addClass('music-square incboard-cell incboard-col-' + self.col + ' incboard-row-' + self.row);
        ret.find('.cover').addClass('incboard-img');
        ret.attr('data-content', v.title + ('duration' in v ? ' (' + resultSet.secondsToHMS(v.duration) + ')' : ''));
        ret.attr('data-trigger', 'hover');
        ret.attr('id', v.type + v.objId);
        ret.find('.description').remove();
        ret.popover({placement: 'top'});

        delete resultSet;

        return ret;
    };

    this.draw = function() {
        var v = self.content,
            e = $('#' + v.type + v.objId);

        // If the element already exists then the only attribute that can chage is the position.
        if (e.length !== 0) {
            console.log
            e.removeClass();
            e.addClass('incboard-cell incboard-row-' + self.row + ' incboard-col-' + self.col);
            if ('album' === v.type) {
                e.addClass('album-square');
            } else {
                e.addClass('music-square');
            }
        } else {
            e = getHtml();
            $('#subtitle').subtitleAdd(v.artist);
            e.css('background-color', $('#subtitle').subtitleGetColor(v.artist));
            $('#incboard-result #incboard').append(e);
        }
    };

    /**
     * Set position of the cell.
     * Can be used as setPos(2, 3) or setPos([2, 3]).
     */
    this.setPos = function(pos) {
        if ('object' === typeof pos) {
            self.col = pos[0];
            self.row = pos[1];
        } else {
            throw new Error('First argument of IncBoardCell.setPos not an object.');
        }
    };

    this.getPos = function() {
        return [self.col, self.row];
    };

    this.setContent = function(v) {
        self.content = v;
    };

    this.getContent = function() {
        return self.content;
    };

    this.toString = function() {
        return '[ ' + self.content.objId + self.content.type + ' (' + self.col + ',' + self.row + ')]';
    };

    /**
     * Remove itself from the HTML.
     */
    this.remove = function() {
        $('#' + self.content.type + self.content.objId).remove();
    };
}
