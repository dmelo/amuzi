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

"use strict";

function  IncBoard() {
    this.pos = 0;
    this.ibb = new IncBoardBoard();
    this.ibb.init();
    this.shiftList = [[-1, -1], [0, -1], [1, -1], [-1, 0], [1, 0], [-1, 1], [0, 1], [1, 1]];
    this.similarity = null;
    this.error = false;
}


// Calculate the shift on X and Y that must be applied to a position to get to 
// the next cell position
IncBoard.prototype.nextCell = function(center, shift, depth) {
    if (typeof depth === 'undefined')
        depth = 1;
    else
        depth++;

    if (depth > 5000) {
        return null;
    }

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
    } else {
        nShift = [-1, -1];
    }

    var nCenter = [center[0] + nShift[0], center[1] + nShift[1]];




    if (nCenter[0] >= 0 && nCenter[0] < this.ibb.getCols() && nCenter[1] >= 0 && nCenter[1] < this.ibb.getRows())
        return nShift;
    else {
        try {
            return this.nextCell(center, nShift, depth);
        } catch (e) {
            console.log("Trying to get nextCell of (" + center[0] + ", " + center[1] + ". shift: (" + shift[0] + ", " + shift[1]);
            return null;
        }
    }
}

IncBoard.prototype.get2DRank = function(v) {
    var center = this.ibb.getPos(v.artistMusicTitleId);
    var shift = null;
    var rank = new Array();

    while ((shift = this.nextCell(center, shift)) !== null) {
        var nShift = [center[0] + shift[0], center[1] + shift[1]];

        this.ibb.getByPos(nShift).forEach(function (item) {
            rank.push(item.artistMusicTitleList);
        });
    }

    return rank;
}

IncBoard.prototype.getNDRank = function(music) {
    var rank = new Array(),
        currentRank = 1,
        localSimilarity = new Array(),
        artistMusicTitleId = music.artistMusicTitleId,
        rank = new Array(),
        self = this;

    this.ibb.getAllMusic().forEach(function(item, id) {
        localSimilarity[id] = self.similarity[artistMusicTitleId][id];
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
        if (null !== val) {
            rank.push(val);
            localSimilarity.splice(localSimilarity.indexOf(val), 1);
        }

    });

    return rank;
}

// Calculate Werr of element v.
IncBoard.prototype.calcError = function(v) {
    var werr = 0,
        rank2D = this.get2DRank(v),
        rankND = this.getNDRank(v),
        rN = 0,
        self = this;

    rank2D.forEach(function(item, r2) {
        rN = rankND.indexOf(item);
        if (rN !== r2)
            werr += Math.abs((rN - r2) * (self.ibb.getSize() - rN));
    });


    return werr;
}

IncBoard.prototype.resolveConflict = function(mostSimilar, newMusic, visitedCells) {
    var self = this;
    if ('undefined' === typeof mostSimilar || 'undefined' === typeof newMusic) {
        console.log(visitedCells);
    }

    var msPos = this.ibb.getPos(mostSimilar.artistMusicTitleId),
        ncPos = this.ibb.getPos(newMusic.artistMusicTitleId),
        bestMsPos = null,
        bestNcPos = null,
        bestWerr = 10000000,
        bestState = 0;

    [0, 1].forEach(function(state) {
        self.shiftList.forEach(function(shift) {
            var pos = [ncPos[0] + shift[0], ncPos[1] + shift[1]];
            if (-1 === visitedCells.indexOf(self.ibb.posToInt(pos))) {
                if(0 == state) {
                    self.ibb.setPos(mostSimilar.artistMusicTitleId, msPos);
                    self.ibb.setPos(newMusic.artistMusicTitleId, pos);
                } else {
                    self.ibb.setPos(mostSimilar.artistMusicTitleId, pos);
                    self.ibb.setPos(newMusic.artistMusicTitleId, ncPos);
                }

                var currentWerr = self.calcError(mostSimilar) + self.calcError(newMusic);
                if(currentWerr < bestWerr || (currentWerr == bestWerr && !self.ibb.isPosOccupied(pos))) {
                    bestWerr = currentWerr;
                    bestMsPos = self.ibb.getPos(mostSimilar.artistMusicTitleId);
                    bestNcPos = self.ibb.getPos(newMusic.artistMusicTitleId);
                    bestState = state;
                }
            }
        });
    });


    this.ibb.setPos(newMusic.artistMusicTitleId, bestNcPos);
    this.ibb.setPos(mostSimilar.artistMusicTitleId, bestMsPos);

    var externalCell = 0 === bestState ? newMusic : mostSimilar;
    var pos = this.ibb.getPos(externalCell.artistMusicTitleId);
    if (this.ibb.isPosOccupied(pos) >= 2) {
        console.log(this.ibb.getByPos(pos));
        visitedCells.push(this.ibb.posToInt(this.ibb.getPos(newMusic.artistMusicTitleId)));
        visitedCells.push(this.ibb.posToInt(this.ibb.getPos(mostSimilar.artistMusicTitleId)));

        var conflictList = this.ibb.getByPos(pos),
            first = conflictList[0],
            second = conflictList[1];

        console.log(first);
        console.log(second);
        console.log(visitedCells);
        this.resolveConflict(first, second, visitedCells);
    } else {
        console.log('DONE WITH: ' + newMusic);
    }
}

IncBoard.prototype.insert = function(v) {
    // Find the most similar element already on incBoard.
    var maxSimilarity = 0,
        mostSimilar = null,
        nSwitches = 0,
        self = this;

    this.ibb.getAllMusic().forEach(function(e, artistMusicTitleId) {
        if(maxSimilarity < self.similarity[artistMusicTitleId][v.artistMusicTitleId]) {
            maxSimilarity = self.similarity[artistMusicTitleId][v.artistMusicTitleId];
            mostSimilar = e;
            nSwitches++;
        }
    });

    if(null !== mostSimilar) {
        this.ibb.insert(v, this.ibb.getPos(mostSimilar.artistMusicTitleId));
        this.resolveConflict(mostSimilar, v, []);
    } else {
        this.ibb.insert(v, [Math.floor(this.ibb.getCols() / 2), Math.floor(this.ibb.getRows() / 2)]);
    }
}

IncBoard.prototype.searchMusic = function(artist, musicTitle) {
    var self = this;

    $.get('/api/searchmusic', {
        'artist': artist,
        'musicTitle': musicTitle
    }, function(v) {
        if (false === false  /* this.error */) {
            try {
                self.insert(v);
            } catch(e) {
                console.log(e.stack);
                console.log(e);
                this.error = true;
            }
        }
    }, 'json');
}

IncBoard.prototype.clean = function() {
}

var incBoard = new IncBoard();

$(document).ready(function() {
    $('#incboard-search').ajaxForm({
        dataType: 'json',
        success: function (data) {
            $.bootstrapMessageOff();
            var total = 90;
            incBoard.similarity = data[1];
            var a = new Date();
            $.each(data[0], function(i, s) {
                if(total < 100) {
                    incBoard.searchMusic(s.artist, s.musicTitle);
                    total++;
                }
            });
            var b = new Date();
            console.log(total + " cells inserted in " + (b.getTime() - a.getTime()) + " secs");
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
