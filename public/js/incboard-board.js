/**
 * incboard-board.js
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


function IncBoardBoard() {
    this.rows = 7;
    this.cols = 14;
    this.cellSizeX = 56;
    this.cellSizeY = 44;
    this.log = new Log();

    this.init();
}

IncBoardBoard.prototype.init = function () {
    this.clean();
    this.animateCells();
};

IncBoardBoard.prototype.clean = function () {
    this.listByObjId = [];
    this.listByPos = [];
    this.size = 0;
    this.drawList = [];

    this.resize();
    this.log.debug("COLS: " + this.cols);
    this.log.debug("ROWS: " + this.rows);

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

IncBoardBoard.prototype.insert = function (obj, pos) {
    var ret,
        cell = new IncBoardCell(),
        intPos = this.posToInt(pos);

    console.log(obj);
    console.log(pos);

    if ('object' === typeof pos && 'object' === typeof obj && -1 === this.listByObjId.indexOf(obj.objId)) {
        cell.setContent(obj);
        cell.setPos(pos);

        // Fill listByObjId
        this.listByObjId[obj.objId] = cell;

        if (!(intPos in this.listByPos)) {
            this.listByPos[intPos] = [];
        }

        // Fill listByPos
        this.listByPos[intPos][obj.objId] = cell;

        ret = true;
        this.size++;
        if (this.drawList.indexOf(obj.objId) === -1) {
            this.drawList.push(obj.objId);
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
IncBoardBoard.prototype.setPos = function(objId, pos) {
    var ret,
        cell,
        oldPos,
        intPos;

    if ('undefined' !== typeof objId && 'object' === typeof pos && (objId in this.listByObjId)) {
        cell = this.listByObjId[objId];
        oldPos = this.posToInt(cell.getPos());
        intPos = this.posToInt(pos);

        delete this.listByPos[oldPos][objId];

        if (!(intPos in this.listByPos)) {
            this.listByPos[intPos] = [];
        }

        this.listByPos[intPos][objId] = cell;
        cell.setPos(pos);

        if (this.drawList.indexOf(objId) === -1) {
            this.drawList.push(objId);
        }

        ret = true;
    } else {
        var errMsg = "";

        if ('undefined' === typeof objId) {
            errMsg += 'objId is undefined. ';
        }

        if ('object' !== typeof pos) {
            errMsg += 'pos is not the type object. ';
        }

        if (!(objId in this.listByObjId)) {
            errMsg += 'objId is not in listByObjId. ';
        }

        throw new Error('Invalid parameter given: ' + errMsg);
        ret = false;
    }

    return ret;
};

/**
 * Safely remove an element from the board.
 */
IncBoardBoard.prototype.remove = function(objId) {
    this.log.debug("removing " + objId);
    var pos = this.listByObjId[objId].getPos();

    // cell.remove()
    this.listByObjId[objId].remove();

    // size
    this.size--;

    // listByObjId
    delete this.listByObjId[objId];

    // listByPos
    delete this.listByPos[this.posToInt(pos)][objId];

    // drawList
    if (-1 !== this.drawList.indexOf(objId)) {
        delete this.drawList[this.drawList.indexOf(objId)];
    }
};

/**
 * Remove elements that are out of the border.
 */
IncBoardBoard.prototype.removeOutOfBorder = function() {
    var self = this;

    for (var id in this.listByObjId) {
        var cell = this.listByObjId[id],
            pos = cell.getPos();

        if (pos[0] < 0 || pos[0] >= self.cols || pos[1] < 0 || pos[1] >= self.rows) {
            self.remove(cell.getContent().objId);
        }
    }
};

IncBoardBoard.prototype.resize = function() {
    if ($('form.search').length > 0) {
        var cell = new IncBoardCell();
        this.cols = parseInt( ( $(window).width() - 296 ) / this.cellSizeX );
        this.rows = parseInt( ( $(window).height() - $('form.search').height() - $('form.search').offset().top - $('.footer').height() ) / this.cellSizeY );
        this.removeOutOfBorder();
        this.centralizeItems();
        this.flushDraw();
    }
};

/**
 * Shifts the elements in order to keep then at the center.
 */
IncBoardBoard.prototype.centralizeItems = function() {
    var  self = this,
         isEmpty = true;

    this.minX = this.minY = 1000;
    this.maxX = this.maxY = 0;

    for (var id in this.listByObjId) {
        var cell = this.listByObjId[id];

        isEmpty = false;
        if (cell.row < self.minY) {
            self.minY = cell.row;
        }

        if (cell.row > self.maxY) {
            self.maxY = cell.row;
        }

        if (cell.col < self.minX) {
            self.minX = cell.col;
        }

        if (cell.col > self.maxX) {
            self.maxX = cell.col;
        }
    }

    if (!isEmpty) {
        var shiftX = parseInt(((this.cols - this.maxX - 1) - this.minX) / 2);
        var shiftY = parseInt(((this.rows - this.maxY - 1) - this.minY) / 2);

        if (0 !== shiftX || 0 !== shiftY) {
            self.log.debug("Applying shift (" + shiftX + ", " + shiftY + ")");
            for (var id in self.listByObjId) {
                var cell = self.listByObjId[id],
                    pos = cell.getPos();

                pos[0] += shiftX;
                pos[1] += shiftY;
                self.setPos(cell.getContent().objId, pos);
            }
        }
    }
};

IncBoardBoard.prototype.flushDraw = function() {
    var self = this,
        newCellSizeX,
        newCellSizeY,
        sheet,
        realHeight,
        realWidth,
        factorX,
        factorY,
        factor,
        marginFactor,
        newTop,
        newLeft;

    for (var key in this.drawList) {
        var id = this.drawList[key];
        self.listByObjId[id].draw();
    }

    realHeight = this.maxY - (this.minY - 1) + 1;
    realWidth = this.maxX - (this.minX - 1) + 1;
    factorY = this.rows / realHeight;
    factorX = this.cols / realWidth;
    // self.log.debug('incboard: minX(' + this.minX + ') maxX(' + this.maxX + ') minY(' + this.minY + ') maxY(' + this.maxY + ') cols: ' + this.cols + ", rows: " + this.rows);
    factor = Math.min(factorX, factorY);

    if (factor * this.cellSizeX > 120 || factor * this.cellSizeY > 90 || factor < 0) {
        newCellSizeX = 120;
        newCellSizeY = 90;
        factor =  120 / this.cellSizeX;
    } else {
        newCellSizeX = factor * this.cellSizeX;
        newCellSizeY = factor * this.cellSizeY;
    }



    newTop = (1 - factor) * this.cellSizeY * this.rows * 0.5;
    newLeft = ((1 - factor) * this.cellSizeX * this.cols * 0.5);

    $('#incboard').css('width', (newCellSizeX * this.cols) + 'px');
    $('#incboard').css('top', newTop + 'px');
    $('#incboard').css('left', newLeft + 'px');


    $.cssRule('.incboard-cell', 'width', newCellSizeX + 'px');
    $.cssRule('.incboard-cell', 'height', newCellSizeY + 'px');
    $.cssRule('.incboard-cell.album-square .cover img', 'width', newCellSizeY + 'px !important');
    $.cssRule('.incboard-cell.album-square .side', 'width', (newCellSizeX - newCellSizeY) + 'px');
    $.cssRule('.incboard-cell.album-square .side', 'height', '100%');


    for (var i = 0; i < this.rows; i++) {
        $.cssRule('.incboard-row-' + i, 'top', (i * newCellSizeY) + "px");
    }

    for (var i = 0; i < this.cols; i++) {
        $.cssRule('.incboard-col-' + i, 'left', (i * newCellSizeX) + "px");
    }

    this.drawList = [];
};

IncBoardBoard.prototype.getByPos = function(pos) {
    var pos = this.posToInt(pos),
        list = [];

    if (pos in this.listByPos) {
        for (var id in this.listByPos[pos]) {
            var cell = this.listByPos[pos][id];
            list.push(cell.getContent());
        }
    }

    return list;
};

IncBoardBoard.prototype.getByObjId = function (objId) {
    return this.listByObjId[objId];
};

IncBoardBoard.prototype.getAllMusic = function () {
    var list = [];

    for (var id in this.listByObjId) {
        var item = this.listByObjId[id];
        list[id] = item.getContent();
    }

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

IncBoardBoard.prototype.getPos = function (objId) {
    if (objId in this.listByObjId) {
        return this.listByObjId[objId].getPos();
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

    for (var id in this.getByPos(pos)) {
        total++;
    }

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

IncBoardBoard.prototype.getIdList = function() {
    var list = [];

    for (var id in this.listByObjId) {
        list.push(id);
    }

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
        self = this,
        ret = true;

    console.log('fsck begin');
    for (var id in this.listByObjId) {
        counter[id] = 1;
    }

    try {
        var conflictedCells = 0;
        console.log(this.listByPos);
        for (var pos in this.listByPos) {
            var posList = this.listByPos[pos];
            var count = 0;
            for (var id in posList) {
                count++;
            }

            if (count > 1) {
                conflictedCells++;
                if (conflictedCells >= 2 || count > 2) {
                    var str = "There is " + conflictedCells + " conflicted cells. There is " + count + " elements on pos " + pos + ": ";
                    for (id in posList) {
                        str += ', ' + id;
                    }
                    self.log.debug(str);
                    ret = false;
                }
            }

            for (var id in posList) {
                if (id  !== posList[id].getContent.objId) {
                    throw new Error("objId on listByPos index doesn't match the content id: " + id + ". contentid: " + item.getContent().objId);
                } else {
                    counter[id]--;
                    if (counter[id] !== 0) {
                        throw new Error("objId " + id + " happens on listByPos more than once (" + pos + ")");
                    }
                }
            }
        }

        for (var id in this.listByObjId) {
            var cell = listByObjId[id];
            if (cell.getContent().objId !== id) {
                throw new Error("objId on listByPos index doesn't match the content id: " + id + ". contentid: " + cell.getContent().objId);
            }

            var intPos = self.posToInt(cell.getPos());
            if (!(intPos in self.listByPos)) {
                self.log.debug(index);
                self.log.debug(self.listByPos[intPos]);
                throw new Error("merda 1");
            }

            if (!(cell.getContent().objId in self.listByPos[self.posToInt(cell.getPos())])) {
                self.log.debug(cell);
                self.log.debug(self);
                throw new Error("merda 2");
            }
        }
    } catch (e) {
        console.log('===> ');
        console.log(e);
    }
    console.log("fsck end");

    return ret;
};
