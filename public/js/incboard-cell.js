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

function IncBoardCell() {
    this.cellSizeX = 56;
    this.cellSizeY = 44;
    this.content = null;
    this.row = null;
    this.col = null;
}

/**
 * Set position of the cell.
 * Can be used as setPos(2, 3) or setPos([2, 3]).
 */
IncBoardCell.prototype.setPos = function(pos) {
    if ('object' === typeof pos) {
        this.col = pos[0];
        this.row = pos[1];
    } else {
        throw new Error('First argument of IncBoardCell.setPos not an object.');
    }
}

IncBoardCell.prototype.getPos = function() {
    return [this.col, this.row];
}

IncBoardCell.prototype.setContent = function(v) {
    this.content = v;
}

IncBoardCell.prototype.getContent = function() {
    return this.content;
}

IncBoardCell.prototype.getInnerHtml = function() {
    var v = this.content;
    var resultSet = new $.ResultSet();
    return img + resultSet.getMusicLarge(v, 'music');
}

IncBoardCell.prototype.getHtml = function() {
    var v = this.content,
        resultSet = new $.ResultSet(),
        ret = ('trackList' in v) ? $(resultSet.getAlbumSquare(v)) : $(resultSet.getMusicSquare(v));

    ret.addClass('incboard-cell incboard-col-' + this.col + ' incboard-row-' + this.row);
    ret.find('.cover').addClass('incboard-img');
    ret.attr('data-content', v.title + ('duration' in v ? ' (' + resultSet.secondsToHMS(v.duration) + ')' : ''));
    ret.attr('data-trigger', 'hover');
    ret.attr('id', 'artistMusicTitleId' in v ? v.artistMusicTitleId : v.id); // in case it is an album, get the album id.
    ret.find('.description').remove();
    ret.popover({placement: 'top'});
    return ret;
}

IncBoardCell.prototype.toString = function() {
    return '[ ' + this.content.artistMusicTitleId + ' (' + this.col + ',' + this.row + ')]';
}

IncBoardCell.prototype.draw = function() {
    var v = this.content;
    var e = $('#' + v.artistMusicTitleId);
    var sty = 'width: ' + this.cellSizeX + 'px; height: ' + this.cellSizeY + 'px; ';

    // If the element already exists then the only attribute that can chage is the position.
    if (e.length !== 0) {
        e.removeClass();
        e.addClass('music-square incboard-cell incboard-row-' + this.row + ' incboard-col-' + this.col);
    } else {
        e = this.getHtml();
        $('#subtitle').subtitleAdd(v.artist);
        e.css('background-color', $('#subtitle').subtitleGetColor(v.artist));
        $('#incboard-result #incboard').append(e);
    }
}

/**
 * Remove itself from the HTML.
 */
IncBoardCell.prototype.remove = function() {
    $('#' + this.content.artistMusicTitleId).remove();
}
