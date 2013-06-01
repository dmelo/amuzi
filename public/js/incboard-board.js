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

(function ($, undefined) {
    'use strict';
    function IncBoardBoard() {
        var rows = 7,
            cols = 14,
            /* cellSizeX = 56,
            cellSizeY = 44, */
            cellSizeX = 120,
            cellSizeY = 90,
            listByObjId = [], // Have a list of the all indexed by the ObjId
            listByPos = [], // Have a list of all cells indexed by it's position.
            size = 0,
            topPadding = $('form.search').height() + $('form.search').offset().top + 20,
            leftPadding = 127,
            drawList = [],
            log = new Log(),
            self = this;

        function focusArtist(artist) {
            $('.incboard-cell').removeClass('focus');
            $('.incboard-cell[artist="' + artist.replace('"', '\\"') + '"]').addClass('focus');
        };

        function animateCells() {
            $('.incboard-cell').live('mouseover', function (e) {
                $('.incboard-img').css('display', 'block');
                $('.incboard-cell').find('.inevidence').removeClass('inevidence');
                $(this).find('.object-music').addClass('inevidence');
                $(this).find('.incboard.img').css('display', 'none');

                focusArtist($(this).attr('artist'));
            }).live('mouseleave', function (e) {
                $('.incboard-img').css('display', 'block');
                $('.incboard-cell').find('.inevidence').removeClass('inevidence');
                $('.incboard-cell').removeClass('focus');
            });

            $('#subtitle li').live('hover', function (e) {
                focusArtist($(this).attr('artist'));
            }).live('mouseleave', function (e) {
                $('.incboard-cell').removeClass('focus');
            });
        };

        function getBoundaries() {
            var obj = {};

            obj.minX = obj.minY = 1000;
            obj.maxX = obj.maxY = 0;
            obj.isEmpty = true;

            for (var id in listByObjId) {
                var cell = listByObjId[id];

                obj.isEmpty = false;
                obj.minX = Math.min(obj.minX, cell.col);
                obj.maxX = Math.max(obj.maxX, cell.col);
                obj.minY = Math.min(obj.minY, cell.row);
                obj.maxY = Math.max(obj.maxY, cell.row);
            }

            return obj;
        }

        this.posToInt = function (pos) {
            return (pos[1] * 10000) + pos[0];
        };

        /**
         * Safely remove an element from the board.
         */
        this.remove = function(objId) {
            log.debug("removing " + objId);
            var pos = listByObjId[objId].getPos(),
                key;

            // cell.remove()
            listByObjId[objId].remove();

            // size
            size--;

            // listByObjId
            delete listByObjId[objId];

            // listByPos
            delete listByPos[self.posToInt(pos)][objId];

            // drawList
            if (-1 !== (key = drawList.indexOf(objId))) {
                delete drawList[key];
            }
        };

        /**
         * Remove elements that are out of the border.
         */
        this.removeOutOfBorder = function() {
            for (var id in listByObjId) {
                var cell = listByObjId[id],
                    pos = cell.getPos();

                if (pos[0] < 0 || pos[0] >= cols || pos[1] < 0 || pos[1] >= rows) {
                    this.remove(cell.getContent().objId);
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
                oldPos = this.posToInt(cell.getPos());
                intPos = this.posToInt(pos);

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
        this.centralizeItems = function() {
            var b = getBoundaries();


            if (!b.isEmpty) {
                var shiftX = parseInt(((cols - b.maxX - 1) - b.minX) / 2);
                var shiftY = parseInt(((rows - b.maxY - 1) - b.minY) / 2);

                if (0 !== shiftX || 0 !== shiftY) {
                    log.debug("Applying shift (" + shiftX + ", " + shiftY + ")");
                    for (var id in listByObjId) {
                        var cell = listByObjId[id],
                            pos = cell.getPos();

                        pos[0] += shiftX;
                        pos[1] += shiftY;
                        this.setPos(cell.getContent().objId, pos);
                    }
                }
            }
        };


        this.flushDraw = function() {
            var newCellSizeX,
                newCellSizeY,
                realHeight,
                realWidth,
                factorX,
                factorY,
                factor,
                newTop,
                newLeft,
                b = getBoundaries();

            for (var key in drawList) {
                var id = drawList[key];
                listByObjId[id].draw();
            }

            realHeight = b.maxY - (b.minY - 1) + 1;
            realWidth = b.maxX - (b.minX - 1) + 1;
            factorY = rows / realHeight;
            factorX = cols / realWidth;
            factor = Math.min(factorX, factorY);

            console.log('real (' + cols + ', ' + rows + '). transformed (' + realWidth + ', ' + realHeight + ')');

            if (factor * cellSizeX > 120 || factor * cellSizeY > 90 || factor < 0) {
                newCellSizeX = 120;
                newCellSizeY = 90;
                factor =  120 / cellSizeX;
            } else {
                newCellSizeX = factor * cellSizeX;
                newCellSizeY = factor * cellSizeY;
            }

            newTop = (1 - factor) * cellSizeY * rows * 0.5;
            newLeft = (1 - factor) * cellSizeX * cols * 0.5;

            console.log('realHeight: ' + realHeight + ". realWidth: " + realWidth + ". newCellSize(" + newCellSizeX + ", " + newCellSizeY + ") newTop: " + newTop + ". newLeft: " + newLeft);

            $('#incboard').css('width', (newCellSizeX * cols) + 'px');
            $('#incboard').css('top', (newTop + topPadding) + 'px');
            $('#incboard').css('left', (newLeft + leftPadding) + 'px');


            $.cssRule('.incboard-cell', 'width', newCellSizeX + 'px');
            $.cssRule('.incboard-cell', 'height', newCellSizeY + 'px');
            $.cssRule('.incboard-cell.album-square .cover > img', 'height', (newCellSizeY - 6) + 'px !important');
            $.cssRule('.incboard-cell.album-square .cover > img', 'width', (newCellSizeY - 6) + 'px !important');
            // $.cssRule('.incboard-cell.album-square .side', 'width', (newCellSizeX - newCellSizeY) + 'px');
            $.cssRule('.incboard-cell.album-square .side', 'height', (newCellSizeY - 6) + 'px');

            for (var i = b.minY; i <= b.maxY; i++) {
                $.cssRule('.incboard-row-' + i, 'top', (i * newCellSizeY) + "px");
            }

            for (var i = b.minX; i <= b.maxX; i++) {
                $.cssRule('.incboard-col-' + i, 'left', (i * newCellSizeX) + "px");
            }

            drawList = [];
        };

        this.resize = function() {
            if ($('form.search').length > 0) {
                var boundaries;

                cols = parseInt( ( $(window).width() - leftPadding ) / cellSizeX );
                rows = parseInt( ( $(window).height() - topPadding - 170 - $('.footer').height() ) / cellSizeY );
                this.removeOutOfBorder();
                this.centralizeItems();
                this.flushDraw();
            }
        };

        this.clean = function () {
            listByObjId = [];
            listByPos = [];
            size = 0;
            drawList = [];

            this.resize();
            $('#incboard-result').html('<div id="incboard"></div>');
        };

        this.init = function () {
            this.clean();
            animateCells();
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
                cell,
                intPos;

            if ('undefined' === typeof pos) {
                pos = [Math.floor(cols / 2), Math.floor(rows / 2)];
            }
            intPos = this.posToInt(pos);

            if ('object' === typeof pos && 'object' === typeof obj && -1 === listByObjId.indexOf(obj.objId)) {
                cell = new $.IncBoardCell();
                cell.setContent(obj);
                cell.setPos(pos);

                // Fill listByObjId
                listByObjId[obj.objId] = cell;

                if (!(intPos in listByPos)) {
                    listByPos[intPos] = [];
                }

                // Fill listByPos
                listByPos[intPos][obj.objId] = cell;

                ret = true;
                size++;
                if (drawList.indexOf(obj.objId) === -1) {
                    drawList.push(obj.objId);
                }
            } else {
                throw new Error('Invalid parameter given');
                ret = false;
            }

            this.fsckReport();

            return ret;
        };

        this.getByPos = function(pos) {
            var pos = this.posToInt(pos),
                list = [];

            if (pos in listByPos) {
                for (var id in listByPos[pos]) {
                    var cell = listByPos[pos][id];
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

            for (var id in listByObjId) {
                var item = listByObjId[id];
                list[id] = item.getContent();
            }

            return list;
        };

        this.getPos = function (objId) {
            return (objId in listByObjId) ? listByObjId[objId].getPos() : false;
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

        // TODO: debugging only. take this away.
        this.listByObjId = listByObjId;
        this.listByPos = listByPos;

        this.fsck = function () {
            var counter = [],
                self = this,
                ret = true;

            for (var id in listByObjId) {
                counter[id] = 1;
            }

            try {
                var conflictedCells = 0;
                for (var pos in listByPos) {
                    var posList = listByPos[pos];
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
                        if (id  != posList[id].getContent().objId) {
                            throw new Error("objId on listByPos index doesn't match the content id: " + id);
                        } else {
                            counter[id]--;
                            if (counter[id] !== 0) {
                                throw new Error("objId " + id + " happens on listByPos more than once (" + pos + ")");
                            }
                        }
                    }
                }

                for (var id in listByObjId) {
                    var cell = listByObjId[id];
                    if (cell.getContent().objId != id) {
                        throw new Error("objId on listByPos index doesn't match the content id: " + id + ". contentid: " + cell.getContent().objId);
                    }

                    var intPos = this.posToInt(cell.getPos());
                    if (!(intPos in listByPos)) {
                        log.debug(index);
                        log.debug(listByPos[intPos]);
                        throw new Error("merda 1");
                    }

                    if (!(cell.getContent().objId in listByPos[this.posToInt(cell.getPos())])) {
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

    $.IncBoardBoard = IncBoardBoard;
}(jQuery, undefined));
