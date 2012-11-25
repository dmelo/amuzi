/**
 * incboard-board.js
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

"use strict";


function IncBoardBoard() {
    this.rows = 7;
    this.cols = 14;
    this.init();
}

IncBoardBoard.prototype.init = function () {
    this.clean();
    this.animateCells();
};

IncBoardBoard.prototype.clean = function () {
    var cell = new IncBoardCell();
    this.listByAMTId = [];
    this.listByPos = [];
    this.size = 0;
    this.drawList = [];

    this.resize();
    console.log("COLS: " + this.cols);
    console.log("ROWS: " + this.rows);

    var table = $('<div id="incboard"></div>');
    this.l = [];
    $('#incboard-result').html(table);
};

IncBoardBoard.prototype.getCols = function () {
    return this.cols;
};

IncBoardBoard.prototype.getRows = function () {
    return this.rows;
};

IncBoardBoard.prototype.insert = function (music, pos) {
    var ret,
        cell = new IncBoardCell(),
        intPos;

    if ('object' === typeof pos && 'object' === typeof music && -1 === this.listByAMTId.indexOf(music.artistMusicTitleId)) {
        music.artistMusicTitleId = parseInt(music.artistMusicTitleId);
        intPos = this.posToInt(pos);
        cell.setContent(music);
        cell.setPos(pos);

        this.listByAMTId[music.artistMusicTitleId] = cell;

        if (!(intPos in this.listByPos)) {
            this.listByPos[intPos] = [];
        }

        this.listByPos[intPos][music.artistMusicTitleId] = cell;

        ret = true;
        this.size++;
        if (this.drawList.indexOf(music.artistMusicTitleId) === -1) {
            this.drawList.push(music.artistMusicTitleId);
        }
    } else {
        throw new Error('Invalid parameter given');
        ret = false;
    }

    this.fsckReport();

    return ret;
};

/**
 * Safe way to set IncBoardCell position.
 */
IncBoardBoard.prototype.setPos = function(artistMusicTitleId, pos) {
    var ret,
        cell,
        oldPos,
        intPos;

    if ('undefined' !== typeof artistMusicTitleId && 'object' === typeof pos && (artistMusicTitleId in this.listByAMTId)) {
        cell = this.listByAMTId[artistMusicTitleId];
        oldPos = this.posToInt(cell.getPos());
        intPos = this.posToInt(pos);

        delete this.listByPos[oldPos][artistMusicTitleId];

        if (!(intPos in this.listByPos)) {
            this.listByPos[intPos] = [];
        }

        this.listByPos[intPos][artistMusicTitleId] = cell;
        cell.setPos(pos);

        if (this.drawList.indexOf(artistMusicTitleId) === -1) {
            this.drawList.push(artistMusicTitleId);
        }

        ret = true;
    } else {
        var errMsg = "";

        if ('undefined' === typeof artistMusicTitleId) {
            errMsg += 'artistMusicTitleId is undefined. ';
        }

        if ('object' !== typeof pos) {
            errMsg += 'pos is not the type object. ';
        }

        if (!(artistMusicTitleId in this.listByAMTId)) {
            errMsg += 'artistMusicTitleId is not in listByAMTId. ';
        }

        throw new Error('Invalid parameter given: ' + errMsg);
        ret = false;
    }

    return ret;
};

/**
 * Safely remove an element from the board.
 */
IncBoardBoard.prototype.remove = function(artistMusicTitleId) {
    console.log("removing " + artistMusicTitleId);
    var pos = this.listByAMTId[artistMusicTitleId].getPos();

    // cell.remove()
    this.listByAMTId[artistMusicTitleId].remove();

    // size
    this.size--;

    // listByAMTId
    delete this.listByAMTId[artistMusicTitleId];

    // listByPos
    delete this.listByPos[this.posToInt(pos)][artistMusicTitleId];

    // drawList
    if (-1 !== this.drawList.indexOf(artistMusicTitleId)) {
        delete this.drawList[this.drawList.indexOf(artistMusicTitleId)];
    }
};

/**
 * Remove elements that are out of the border.
 */
IncBoardBoard.prototype.removeOutOfBorder = function() {
    var self = this;

    this.listByAMTId.forEach(function (cell) {
        var pos = cell.getPos();
        if (pos[0] < 0 || pos[0] >= self.cols || pos[1] < 0 || pos[1] >= self.rows) {
            self.remove(cell.getContent().artistMusicTitleId);
        }
    });
};

IncBoardBoard.prototype.resize = function() {
    var cell = new IncBoardCell();
    this.cols = parseInt( ( $(window).width() - 250 ) / cell.cellSizeX );
    this.rows = parseInt( ( $(window).height() - $('form.search').height() - $('.navbar').height() - $('.footer').height() - $('.alert').height() - $('.alert').offset().top ) / cell.cellSizeY );
    this.removeOutOfBorder();
    this.centralizeItems();
    this.flushDraw();
};

/**
 * Shifts the elements in order to keep then at the center.
 */
IncBoardBoard.prototype.centralizeItems = function() {
    var minX = 1000,
        maxX = 0,
        minY = 1000,
        maxY = 0,
        self = this;
    this.listByAMTId.forEach(function (cell) {
        if (cell.row < minY) {
            minY = cell.row;
        }

        if (cell.row > maxY) {
            maxY = cell.row;
        }

        if (cell.col < minX) {
            minX = cell.col;
        }

        if (cell.col > maxX) {
            maxX = cell.col;
        }
    });

    var shiftX = parseInt(((this.cols - maxX - 1) - minX) / 2);
    var shiftY = parseInt(((this.rows - maxY - 1) - minY) / 2);

    if (0 !== shiftX || 0 !== shiftY) {
        console.log("Applying shift (" + shiftX + ", " + shiftY + ")");
        this.listByAMTId.forEach(function (cell) {
            var pos = cell.getPos();
            pos[0] += shiftX;
            pos[1] += shiftY;
            self.setPos(cell.getContent().artistMusicTitleId, pos);
        });
    } else {
        console.log("shift: (" + shiftX + ", " + shiftY + ")");
    }
};

IncBoardBoard.prototype.flushDraw = function() {
    var self = this;

    this.drawList.forEach(function(id) {
        self.listByAMTId[id].draw();
    });

    this.drawList = [];
};

IncBoardBoard.prototype.getByPos = function(pos) {
    var pos = this.posToInt(pos),
        list = [];

    if (pos in this.listByPos) {
        this.listByPos[pos].forEach(function (cell) {
            list.push(cell.getContent());
        });
    }

    return list;
};

IncBoardBoard.prototype.getByAMTId = function (artistMusicTitleId) {
    return this.listByAMTId[artistMusicTitleId];
};

IncBoardBoard.prototype.getAllMusic = function () {
    var list = [];

    this.listByAMTId.forEach(function (item, id) {
        list[id] = item.getContent();
    });

    return list;
};

IncBoardBoard.prototype.focusArtist = function (artist) {
    $.each($('.incboard-cell'), function (i, e) {
        if($(this).attr('artist') === artist)
            $(this).addClass('focus');
        else
            $(this).removeClass('focus');
    });
};

IncBoardBoard.prototype.getPos = function (artistMusicTitleId) {
    if (artistMusicTitleId in this.listByAMTId) {
        return this.listByAMTId[artistMusicTitleId].getPos();
    } else {
        return false;
    }
};

/**
 * Returns the number of elements currently on position (col, row) if any,
 * false otherwise.
 */
IncBoardBoard.prototype.isPosOccupied = function (pos) {
    var total = 0;

    this.getByPos(pos).forEach(function(item) {
        total++;
    });

    return total > 0 ? total : false;
};

IncBoardBoard.prototype.animateCells = function () {
    var self = this;
    $('.incboard-cell').live('mouseover', function (e) {
        $('.incboard-img').css('display', 'block');
        $('.incboard-cell').find('.inevidence').removeClass('inevidence');
        $(this).find('.object-music').addClass('inevidence');
        $(this).find('.incboard.img').css('display', 'none');

        self.focusArtist($(this).attr('artist'));
    });

    $('.incboard-cell').live('mouseleave', function (e) {
        $('.incboard-img').css('display', 'block');
        $('.incboard-cell').find('.inevidence').removeClass('inevidence');
        $('.incboard-cell').removeClass('focus');
    });

    $('#subtitle li').live('hover', function (e) {
        self.focusArtist($(this).attr('artist'));
        $('html').css('cursor', 'pointer');
    });

    $('#subtitle li').live('mouseleave', function (e) {
        $('.incboard-cell').removeClass('focus');
        $('html').css('cursor', 'default');
    });
};

IncBoardBoard.prototype.posToInt = function (pos) {
    return (pos[1] * 10000) + pos[0];
};

IncBoardBoard.prototype.intToPos = function (num) {
    return [num % 10000, Math.floor(num / 10000)];
};

IncBoardBoard.prototype.getSize = function () {
    return this.size;
};

IncBoardBoard.prototype.getIdsList = function() {
    var list = [];

    this.listByAMTId.forEach(function (item, id) {
        list.push(id);
    });

    return list;
};

IncBoardBoard.prototype.fsckReport = function() {
    try {
        this.fsck();
    } catch (err) {
        $.post('/api/reporterror', {
            origin: 'incBoardBoard',
            err: err,
            obj: this
        });
    }
}

IncBoardBoard.prototype.fsck = function () {
    var counter = [],
        self = this;

    this.listByAMTId.forEach(function (item, id) {
        counter[id] = 1;
    });

    var conflictedCells = 0;
    this.listByPos.forEach(function (posList, pos) {
        var count = 0;
        posList.forEach(function (item, id) {
            count++;
        });

        if (count > 1) {
            conflictedCells++;
            if (conflictedCells >= 2) {
                var str = "There is " + conflictedCells + " conflicted cells. There is " + count + " elements on pos " + pos + ": ";
                posList.forEach(function (item, id) {
                    str += ", " + id;
                });
                console.log(str);
            }
        }

        posList.forEach(function (item, id) {
            if (id !== item.getContent().artistMusicTitleId) {
                throw new Error("artistMusicTitleId on listByPos index doesn't match the content id: " + id + ". contentid: " + item.getContent().artistMusicTitleId);
            } else {
                counter[id]--;
                if (counter[id] !== 0) {
                    throw new Error("artistMusicTitleId " + id + " happens on listByPos more than once (" + pos + ")");
                }
            }
        });

    });

    this.listByAMTId.forEach(function (cell, id) {
        if (cell.getContent().artistMusicTitleId !== id) {
            throw new Error("artistMusicTitleId on listByPos index doesn't match the content id: " + id + ". contentid: " + cell.getContent().artistMusicTitleId);
        }

        var intPos = self.posToInt(cell.getPos());
        if (!(intPos in self.listByPos)) {
            console.log("merda 1");
            console.log(index);
            console.log(self.listByPos[intPos]);
            throw new Error("merda 1");
        }

        if (!(cell.getContent().artistMusicTitleId in self.listByPos[self.posToInt(cell.getPos())])) {
            console.log("merda 2");
            console.log(cell);
            console.log(self);
            throw new Error("merda 2");
        }
    });


    return true;
};
