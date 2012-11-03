/**
 * incboard-cell.js
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2012  Diogo Oliveira de Melo
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

IncBoardCell.prototype.setContent = function(v) {
    this.content = v;
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

IncBoardCell.prototype.setContent = function(content) {
    this.content = content;
}

IncBoardCell.prototype.getContent = function() {
    return this.content;
}

IncBoardCell.prototype.getHtml = function() {
    var v = this.content;
    var resultSet = new ResultSet();
    sty = 'width: ' + this.cellSizeX + 'px; height: ' + this.cellSizeY + 'px; ';
    img = '<div class="incboard-img"><img src="' + v.cover + '"/></div>';
    title = '<span class="title">' + v.artist + ' - ' + v.musicTitle + '</span>';
    duration = '<p>' + resultSet.secondsToHMS(v.duration) + '</p>';
    info = '<div class="incboard-info">' + title + duration + '</div>';
    control = '<div class="incboard-control play">' + resultSet.getControl(v) + '</div>';
    return $('<div id="' + v.artistMusicTitleId + '" artist="' + v.artist + '" musicTitle="' + v.musicTitle + '" class="incboard-cell" style="' + sty + 'top: ' + (this.row * this.cellSizeY) + 'px; left: ' + (this.col * this.cellSizeX) + 'px;">' + img + resultSet.getMusicLarge(v, 'music') + '</div>');
}

IncBoardCell.prototype.toString = function() {
    return '[ ' + this.content.artistMusicTitleId + ' (' + this.col + ',' + this.row + ')]';
}

IncBoardCell.prototype.draw = function() {
    var v = this.content;
    $('#' + v.artistMusicTitleId).remove();
    cellHtml = this.getHtml();
    $('#subtitle').subtitleAdd(v.artist);
    cellHtml.css('background-color', $('#subtitle').subtitleGetColor(v.artist));
    console.log('set bg-color to ' + $('#subtitle').subtitleGetColor(v.artist));
    $('#incboard-result #incboard').append(cellHtml);
}
