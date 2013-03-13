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

function IncBoardBoard() {
    var rows = 7,
        cols = 14,
        cellSizeX = 56,
        cellSizeY = 44,
        listByObjId = [],
        listByPos = [],
        size = 0,
        drawList = [],
        log = new Log(),
        self = this;


    /**
     * Safely remove an element from the board.
     */
    remove = function(objId) {
        log.debug("removing " + objId);
        var pos = this.listByObjId[objId].getPos(),
            key;

        // cell.remove()
        this.listByObjId[objId].remove();

        // size
        this.size--;

        // listByObjId
        delete this.listByObjId[objId];

        // listByPos
        delete this.listByPos[this.posToInt(pos)][objId];

        // drawList
        if (-1 !== (key = this.drawList.indexOf(objId))) {
            delete this.drawList[key];
        }
    };


    /**
     * Remove elements that are out of the border.
     */
    removeOutOfBorder = function() {
        for (var id in listByObjId) {
            var cell = listByObjId[id],
                pos = cell.getPos();

            if (pos[0] < 0 || pos[0] >= cols || pos[1] < 0 || pos[1] >= rows) {
                self.remove(cell.getContent().objId);
            }
        }
    };

    /**
     * Safe way to set IncBoardCell position.
     */
    this.setPos = function(objId, pos) {
        var ret,
            cell,
            oldPos,
            intPos;

        if ('undefined' !== typeof objId && 'object' === typeof pos && (objId in listByObjId)) {
            cell = listByObjId[objId];
            oldPos = posToInt(cell.getPos());
            intPos = posToInt(pos);

            delete listByPos[oldPos][objId];

            if (!(intPos in listByPos)) {
                listByPos[intPos] = [];
            }

            listByPos[intPos][objId] = cell;
            cell.setPos(pos);

            if (drawList.indexOf(objId) === -1) {
                drawList.push(objId);
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

            if (!(objId in listByObjId)) {
                errMsg += 'objId is not in listByObjId. ';
            }

            throw new Error('Invalid parameter given: ' + errMsg);
            ret = false;
        }

        return ret;
    };



    /**
     * Shifts the elements in order to keep then at the center.
     */
    centralizeItems = function() {
        var isEmpty = true,
            minX = 1000,
            minY = 1000,
            maxX = 0,
            maxY = 0;

        for (var id in listByObjId) {
            var cell = listByObjId[id];

            isEmpty = false;
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
        }

        if (!isEmpty) {
            var shiftX = parseInt(((cols - maxX - 1) - minX) / 2);
            var shiftY = parseInt(((rows - maxY - 1) - minY) / 2);

            if (0 !== shiftX || 0 !== shiftY) {
                log.debug("Applying shift (" + shiftX + ", " + shiftY + ")");
                for (var id in listByObjId) {
                    var cell = listByObjId[id],
                        pos = cell.getPos();

                    pos[0] += shiftX;
                    pos[1] += shiftY;
                    setPos(cell.getContent().objId, pos);
                }
            }
        }

        return [minX, maxX, minY, maxY];
    };


    flushDraw = function(boundaries) {
        var newCellSizeX,
            newCellSizeY,
            realHeight,
            realWidth,
            factorX,
            factorY,
            factor,
            newTop,
            newLeft,
            minX = boundaries[0],
            maxX = boundaries[1],
            minY = boundaries[2],
            maxY = boundaries[3];

        for (var key in drawList) {
            var id = drawList[key];
            listByObjId[id].draw();
        }

        realHeight = maxY - (minY - 1) + 1;
        realWidth = maxX - (minX - 1) + 1;
        factorY = rows / realHeight;
        factorX = cols / realWidth;
        // log.debug('incboard: minX(' + this.minX + ') maxX(' + this.maxX + ') minY(' + this.minY + ') maxY(' + this.maxY + ') cols: ' + this.cols + ", rows: " + this.rows);
        factor = Math.min(factorX, factorY);

        if (factor * cellSizeX > 120 || factor * cellSizeY > 90 || factor < 0) {
            newCellSizeX = 120;
            newCellSizeY = 90;
            factor =  120 / cellSizeX;
        } else {
            newCellSizeX = factor * cellSizeX;
            newCellSizeY = factor * cellSizeY;
        }



        newTop = (1 - factor) * cellSizeY * rows * 0.5;
        newLeft = ((1 - factor) * cellSizeX * cols * 0.5);

        $('#incboard').css('width', (newCellSizeX * cols) + 'px');
        $('#incboard').css('top', newTop + 'px');
        $('#incboard').css('left', newLeft + 'px');


        $.cssRule('.incboard-cell', 'width', newCellSizeX + 'px');
        $.cssRule('.incboard-cell', 'height', newCellSizeY + 'px');
        $.cssRule('.incboard-cell.album-square .cover > img', 'height', (newCellSizeY - 6) + 'px !important');
        $.cssRule('.incboard-cell.album-square .cover > img', 'width', (newCellSizeY - 6) + 'px !important');
        // $.cssRule('.incboard-cell.album-square .side', 'width', (newCellSizeX - newCellSizeY) + 'px');
        $.cssRule('.incboard-cell.album-square .side', 'height', (newCellSizeY - 6) + 'px');


        for (var i = 0; i < this.rows; i++) {
            $.cssRule('.incboard-row-' + i, 'top', (i * newCellSizeY) + "px");
        }

        for (var i = 0; i < this.cols; i++) {
            $.cssRule('.incboard-col-' + i, 'left', (i * newCellSizeX) + "px");
        }

        this.drawList = [];
    };

    resize = function() {
        if ($('form.search').length > 0) {
            var cell = new IncBoardCell(),
                boundaries;
            cols = parseInt( ( $(window).width() - 296 ) / cellSizeX );
            rows = parseInt( ( $(window).height() - $('form.search').height() - $('form.search').offset().top - $('.footer').height() ) / cellSizeY );
            removeOutOfBorder();
            boundaries = centralizeItems();
            flushDraw(boundaries);

            delete cell;
        }
    };

    focusArtist = function (artist) {
        $.each($('.incboard-cell'), function (i, e) {
            if($(this).attr('artist') === artist)
                $(this).addClass('focus');
            else
                $(this).removeClass('focus');
        });
    };

    animateCells = function () {
        $('.incboard-cell').live('mouseover', function (e) {
            $('.incboard-img').css('display', 'block');
            $('.incboard-cell').find('.inevidence').removeClass('inevidence');
            $(this).find('.object-music').addClass('inevidence');
            $(this).find('.incboard.img').css('display', 'none');

            focusArtist($(this).attr('artist'));
        });

        $('.incboard-cell').live('mouseleave', function (e) {
            $('.incboard-img').css('display', 'block');
            $('.incboard-cell').find('.inevidence').removeClass('inevidence');
            $('.incboard-cell').removeClass('focus');
        });

        $('#subtitle li').live('hover', function (e) {
            focusArtist($(this).attr('artist'));
            $('html').css('cursor', 'pointer');
        });

        $('#subtitle li').live('mouseleave', function (e) {
            $('.incboard-cell').removeClass('focus');
            $('html').css('cursor', 'default');
        });
    };

    this.clean = function () {
        listByObjId = [];
        listByPos = [];
        size = 0;
        drawList = [];

        resize();
        log.debug("COLS: " + cols);
        log.debug("ROWS: " + rows);

        $('#incboard-result').html('<div id="incboard"></div>');
    };

    this.init = function () {
        this.clean();
        animateCells();
    };

    this.getCols = function () {
        return cols;
    };

    this.getRows = function () {
        return rows;
    };

    this.posToInt = function (pos) {
        return (pos[1] * 10000) + pos[0];
    };

    this.intToPos = function (num) {
        return [num % 10000, Math.floor(num / 10000)];
    };

    this.getSize = function () {
        return size;
    };

    this.getIdList = function() {
        var list = [];

        for (var id in listByObjId) {
            list.push(id);
        }

        return list;
    };

    this.insert = function (obj, pos) {
        var ret,
            cell = new IncBoardCell(),
            intPos = this.posToInt(pos);

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
            delete cell;
            throw new Error('Invalid parameter given');
            ret = false;
        }

        this.fsckReport();

        return ret;
    };

    this.getByPos = function(pos) {
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

    this.getByObjId = function (objId) {
        return listByObjId[objId];
    };

    this.getAllMusic = function () {
        var list = [];

        for (var id in this.listByObjId) {
            var item = this.listByObjId[id];
            list[id] = item.getContent();
        }

        return list;
    };

    this.getPos = function (objId) {
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
    this.isPosOccupied = function (pos) {
        var total = 0;

        for (var id in this.getByPos(pos)) {
            total++;
        }

        return total > 0 ? total : false;
    };

    this.fsckReport = function() {
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

    this.fsck = function () {
        var counter = [],
            self = this,
            ret = true;

        for (var id in this.listByObjId) {
            counter[id] = 1;
        }

        try {
            var conflictedCells = 0;
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
                        log.debug(str);
                        ret = false;
                    }
                }

                for (var id in posList) {
                    if (id  !== posList[id].getContent.objId) {
                        throw new Error("objId on listByPos index doesn't match the content id: " + id);
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
                    log.debug(index);
                    log.debug(self.listByPos[intPos]);
                    throw new Error("merda 1");
                }

                if (!(cell.getContent().objId in self.listByPos[self.posToInt(cell.getPos())])) {
                    log.debug(cell);
                    log.debug(self);
                    throw new Error("merda 2");
                }
            }
        } catch (e) {
            console.log(e);
        }
        console.log("fsck end");

        return ret;
    };


    this.init();
}



