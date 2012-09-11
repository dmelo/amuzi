/**
 * incboard.js
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
var incBoard = new IncBoard();

function  IncBoard() {
    this.searchString = "";
    this.rows = 7;
    this.cols = 14;
    this.similarity = null;
    this.artistMusicTitleList = []; // list of incBoard cells.
    this.l = []; // list of elements inside incboard.
    this.cachePos = []; // Caches the l elements by position.

    this.shiftList = [[-1, -1], [0, -1], [1, -1], [-1, 0], [1, 0], [-1, 1], [0, 1], [1, 1]];
}

// Calculate the shift on X and Y that must be applied to a position to get to 
// the next cell position
IncBoard.prototype.nextCell = function(center, shift) {
    var nShift;

    if (shift !== null) {
        var max = Math.max(Math.abs(shift[0]), Math.abs(shift[1]));
        nShift = [shift[0], shift[1]];
        // No way to continue.
        if (max > Math.max(this.rows, this.cols))
            return null;
        
        if(-max === nShift[0] && 1 - max === nShift[1]) {
            // go to the first element of the outer layer.
            max++;
            nShift[0] = -max;
            nShift[1] = -max;
        } else if(-max === nShift[1] && max > nShift[0]) {
            // top border moving to right.
            nShift[0]++;
        } else if(max === nShift[0] && max > nShift[1]) {
            // right border moving down.
            nShift[1]++;
        } else if(max === nShift[1] && -max < nShift[0]) {
            // bottom border moving left.
            nShift[0]--;
        } else if(-max === nShift[0] && -max < nShift[1]) {
            // left border moving up.
            nShift[1]--;
        }
    } else
        nShift = [-1, -1];

    var nCenter = [center[0] + nShift[0], center[1] + nShift[1]];

    if (nCenter[0] >= 0 && nCenter[0] < this.cols && nCenter[1] >= 0 && nCenter[1] < this.rows)
        return nShift;
    else
        return this.nextCell(center, nShift);
}

IncBoard.prototype.get2DRank = function(v) {
    var center = [v.col, v.row];
    var shift = null;
    var currentRank = 1;
    var rank = new Array();

    while ((shift = this.nextCell(center, shift)) !== null) {
        var nShift = [center[0] + shift[0], center[1] + shift[1]];
        if ('undefined' !== typeof this.cachePos[nShift[0]] && 'undefined' !== typeof this.cachePos[nShift[0]][nShift[1]])
            rank[currentRank++] = this.cachePos[nShift[0]][nShift[1]].content.artistMusicTitleId;
    }

    return rank;
}

IncBoard.prototype.getNDRank = function(cell) {
    if (!cell instanceof IncBoardCell)
        return null;

    var rank = new Array();

    var currentRank = 1;
    var localSimilarity = new Array();
    var artistMusicTitleId = cell.content.artistMusicTitleId;
    var rank = new Array();

    this.l.forEach(function(item, id) {
        localSimilarity[id] = incBoard.similarity[artistMusicTitleId][id];
    });

    localSimilarity.forEach(function(trash) {
        var max = 0;
        var val = null;

        localSimilarity.forEach(function(item, id) {
            if(item > max && rank.indexOf(id) === -1) {
                max = item;
                val = id;
            }
        });
        if (null !== val)
            rank[currentRank++] = val;

    });

    return rank;
}

// Calculate Werr of element v.
IncBoard.prototype.calcError = function(v) {
    if (!v instanceof IncBoardCell)
        return null;
    var werr = 0;
    var rank2D = this.get2DRank(v);
    var rankND = this.getNDRank(v);

    rank2D.forEach(function(item, r2) {
        rN = rankND.indexOf(item);
        if (rN !== r2)
            werr += Math.abs((rN - r2) * (incBoard.l.length - rN));
    });

    return werr;
}

IncBoard.prototype.resolveConflict = function(mostSimilar, newCell, visitedCells) {
    var msPos = mostSimilar.getPos();
    var ncPos = newCell.getPos();

    var bestMsPos = null;
    var bestNcPos = null;
    var bestWerr = 10000000;

    [0, 1].forEach(function(state) {
        incBoard.shiftList.forEach(function(shift) {
            if(0 == state) {
                mostSimilar.setPos(msPos[0], msPos[1]);
                newCell.setPos(ncPos[0] + shift[0], ncPos[1] + shift[1]);
            } else {
                mostSimilar.setPos(msPos[0] + shift[0], msPos[1] + shift[1]);
                newCell.setPos(ncPos[0], ncPos[1]);
            }

            var currentWerr = incBoard.calcError(mostSimilar) + incBoard.calcError(newCell);
            if(currentWerr < bestWerr) {
                bestWerr = currentWerr;
                bestMsPos = mostSimilar.getPos();
                bestNcPos = newCell.getPos();
            }
        });
    });

    newCell.setPos(bestNcPos[0], bestNcPos[1]);
    mostSimilar.setPos(bestMsPos[0], bestMsPos[1]);

    // Check if the solutino of this conflict caused another conflic.
    

    console.log('solved element: ' + newCell.content.artistMusicTitleId + " -- " + newCell.getPos()[0] + "#" + newCell.getPos()[1]);
}

IncBoard.prototype.insert = function(v) {
    // Find the most similar element already on incBoard.
    var maxSimilarity = 0;
    var mostSimilar = null;
    var nSwitches = 0;
    this.l.forEach(function(e, artistMusicTitleId) {
        if(maxSimilarity < incBoard.similarity[artistMusicTitleId][v.artistMusicTitleId]) {
            maxSimilarity = incBoard.similarity[artistMusicTitleId][v.artistMusicTitleId];
            mostSimilar = e;
            nSwitches++;
        }
    });

    cell = new IncBoardCell();
    cell.setContent(v);


    
    if(null !== mostSimilar) {
        // console.log(v.artist + '#' + v.musicTitle + ' is very similar to ' + mostSimilar.content.artist + '#' + mostSimilar.content.musicTitle + ' (' + mostSimilar.getPos()[0] + ', ' + mostSimilar.getPos()[1] + ') -- ' + maxSimilarity + "--" + nSwitches);

        //console.log(cell);
        //console.log(mostSimilar);
        //console.log("error v: " + this.calcError(cell));
        //console.log("error mostSimilar: " + this.calcError(mostSimilar));

        cell.setPos(mostSimilar.col, mostSimilar.row);
        this.resolveConflict(mostSimilar, cell, []);
    } else
        cell.setPos(Math.floor(this.cols / 2), Math.floor(this.rows / 2));

    
    cellHtml = cell.getHtml();
    $('#subtitle').subtitleAdd(v.artist);
    cellHtml.css('background-color', $('#subtitle').subtitleGetColor(v.artist));
    $('#incboard-result #incboard').append(cellHtml);
    this.l[v.artistMusicTitleId] = cell;
    if('undefined' === typeof this.cachePos[cell.col])
        this.cachePos[cell.col] = [];

    this.cachePos[cell.col][cell.row] = cell;
}

IncBoard.prototype.clean = function() {
    var table = $('<div id="incboard"></div>');
    table.css('width', this.cols * this.cellSizeX);
    table.css('height', this.rows * this.cellSizeY);
    this.similarity = [];
    this.artistMusicTitleList = [];
    this.l = [];
    this.cachePos = [];
    $('#incboard-result').html(table);
}

IncBoard.prototype.init = function() {
    this.clean();
    this.animateCells();
}

IncBoard.prototype.focusArtist = function(artist) {
    $.each($('.incboard-cell'), function(i, e) {
        if($(this).attr('artist') === artist)
            $(this).addClass('focus');
        else
            $(this).removeClass('focus');
    });
}

IncBoard.prototype.animateCells = function() {
    $('.incboard-cell').live('mouseover', function(e) {
        $('.incboard-img').css('display', 'block');
        $('.incboard-cell').find('.inevidence').removeClass('inevidence');
        $(this).find('.object-music').addClass('inevidence');
        $(this).find('.incboard.img').css('display', 'none');

        incBoard.focusArtist($(this).attr('artist'));
    });

    $('.incboard-cell').live('mouseleave', function(e) {
        $('.incboard-img').css('display', 'block');
        $('.incboard-cell').find('.inevidence').removeClass('inevidence');
        $('.incboard-cell').removeClass('focus');
    });

    $('#subtitle li').live('hover', function(e) {
        incBoard.focusArtist($(this).attr('artist'));
        $('html').css('cursor', 'pointer');
    });

    $('#subtitle li').live('mouseleave', function(e) {
        $('.incboard-cell').removeClass('focus');
        $('html').css('cursor', 'default');
    });
}

IncBoard.prototype.searchMusic = function(artist, musicTitle) {
    $.get('/api/searchmusic', {
        'artist': artist,
        'musicTitle': musicTitle
    }, function(v) {
        incBoard.insert(v);
    }, 'json');
}


$(document).ready(function() {
    incBoard.init();
    $('#incboard-search').ajaxForm({
        dataType: 'json',
        success: function (data) {
            $.bootstrapMessageOff();
            var total = 90;
            incBoard.similarity = data[1];
            $.each(data[0], function(i, s) {
                if(total < incBoard.cols * incBoard.rows) {
                    incBoard.artistMusicTitleList[s.artistMusicTitleId] = s;
                    incBoard.searchMusic(s.artist, s.musicTitle);
                    total++;
                }
            });
        },
        beforeSubmit: function() {
            $('#subtitle').subtitleInit();
            incBoard.searchString = $('#q').val();
            $.bootstrapMessage('Loading...', 'info');
            incBoard.clean();
            incBoard.searchMusic($('#artist').val(), $('#musicTitle').val(), 0);
        }
    });
});
