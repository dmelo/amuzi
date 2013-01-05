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
    this.shiftList = [[-1, -1], [0, -1], [1, -1], [-1, 0], [1, 0], [-1, 1], [0, 1], [1, 1]];
    this.ibb = new IncBoardBoard();
    this.stochasticLength = 15;
    this.clean();
}

IncBoard.prototype.clean = function () {
    this.pos = 0;
    this.similarity = null;
    this.ibb.clean();
    this.searchSimilarList = [];
    this.incrementSimilarRunning = false;
};

IncBoard.prototype.posGreaterThan = function(posA, posB) {
    var maxA = Math.max(Math.abs(posA[0]), Math.abs(posA[1])),
        maxB = Math.max(Math.abs(posB[0]), Math.abs(posB[1])),
        qA,
        qB,
        sumA,
        sumB;

    if (maxA !== maxB) {
        return maxA > maxB;
    } else {
        qA = posA[1] === -maxA || posA[0] === maxA ? 0 : 1;
        qB = posB[1] === -maxA || posB[0] === maxA ? 0 : 1;

        if (qA !== qB) {
            return qA > qB;
        } else {
            sumA = posA[0] + posA[1];
            sumB = posB[0] + posB[1];
            if (0 === qA) {
                return sumA > sumB;
            } else {
                return sumB > sumA;
            }
        }
    }
};

IncBoard.prototype.stochasticItems = function(center) {
    var start = new Date().getTime();
    var self = this,
        list = [],
        idsList = this.ibb.getIdsList();

    this.ibb.getByPos(center).forEach(function(item) {
        var id = item.artistMusicTitleId;
        list.push(id);
        idsList.splice(idsList.indexOf(id), 1);
    });

    this.shiftList.forEach(function(shift) {
        var pos = [center[0] + shift[0], center[1] + shift[1]];
        self.ibb.getByPos(pos).forEach(function(item) {
            var id = item.artistMusicTitleId;
            list.push(id);
            idsList.splice(idsList.indexOf(id), 1);
        });
    });

    while (list.length < this.stochasticLength && idsList.length > 0) {
        var rid = Math.floor(Math.random() * idsList.length);
        list.push(idsList[rid]);
        idsList.splice(rid, 1);
    }

    var end = new Date().getTime();

    // console.log('stochasticItems ' + (end - start));

    return list;
}

IncBoard.prototype.get2DRank = function(music, musicList) {
    var center = this.ibb.getPos(music.artistMusicTitleId),
        list = [],
        rank = [],
        self = this,
        i,
        j;

    musicList.forEach(function(id) {
        var a = [],
            pos =  self.ibb.getPos(id);

        a['pos'] = [pos[0] - center[0], pos[1] - center[1]];
        a['id'] = id;

        list.push(a);
    });

    for (i = 0; i < list.length; i++) {
        for (j = i + 1; j < list.length; j++) {
            if (self.posGreaterThan(list[i].pos, list[j].pos)) {
                var aux = list[i];
                list[i] = list[j];
                list[j] = aux;
            }
        }
    }

    list.forEach(function(item) {
        rank.push(item.id);
    });

    return rank;
}

IncBoard.prototype.getNDRank = function(music, musicList) {
    var rank = new Array(),
        currentRank = 1,
        localSimilarity = new Array(),
        similarityPull = new Array(),
        artistMusicTitleId = music.artistMusicTitleId,
        rank = new Array(),
        self = this;

    musicList.forEach(function(id) {
        localSimilarity[id] = self.similarity[artistMusicTitleId][id];
        similarityPull.push(self.similarity[artistMusicTitleId][id]);
    });

    localSimilarity.forEach(function(trash) {
        var max = Math.max.apply(Math, similarityPull);
        var val = localSimilarity.indexOf(max);
        rank.push(val);
        delete localSimilarity[val];
        delete similarityPull[similarityPull.indexOf(val)];
    });

    return rank;
}

// Calculate Werr of element v.
IncBoard.prototype.calcError = function(v, musicList) {
    var s0 = new Date().getTime();
    var werr = 0,
        rank2D = this.get2DRank(v, musicList),
        s1 = new Date().getTime(),
        rankND = this.getNDRank(v, musicList),
        s2 = new Date().getTime(v),
        rN = 0,
        self = this;

    rank2D.forEach(function(item, r2) {
        rN = rankND.indexOf(item);
        if (rN !== r2)
            werr += Math.abs((rN - r2) * (self.ibb.getSize() - rN));
    });

    var s3 = new Date().getTime();

    // console.log("calcError times: " + (s1 - s0) + "#" + (s2 - s1) + "#" + (s3 - s2));


    return werr;
}

IncBoard.prototype.resolveConflict = function(mostSimilar, newMusic, visitedCells) {
    var self = this;
    if ('undefined' === typeof mostSimilar || 'undefined' === typeof newMusic) {
        // console.log(visitedCells);
    }

    var ncPos = this.ibb.getPos(newMusic.artistMusicTitleId),
        bestMsPos = null,
        bestNcPos = null,
        bestWerr = 10000000,
        bestState = 0,
        occupancy = 1000,
        musicList = this.stochasticItems(ncPos);


    [0, 1].forEach(function(state) {
        self.shiftList.forEach(function(shift) {
            var pos = [ncPos[0] + shift[0], ncPos[1] + shift[1]];
            if (-1 === visitedCells.indexOf(self.ibb.posToInt(pos))) {
                if(0 == state) {
                    self.ibb.setPos(mostSimilar.artistMusicTitleId, ncPos);
                    self.ibb.setPos(newMusic.artistMusicTitleId, pos);
                } else {
                    self.ibb.setPos(mostSimilar.artistMusicTitleId, pos);
                    self.ibb.setPos(newMusic.artistMusicTitleId, ncPos);
                }

                // TODO: verify if this is correct or if Werr = Werr(newMusic) + Werr(mostSimilart).
                var currentWerr = self.calcError(newMusic, musicList);
                if(currentWerr < bestWerr || (currentWerr == bestWerr && occupancy > self.ibb.isPosOccupied(pos))) {
                    bestWerr = currentWerr;
                    bestMsPos = self.ibb.getPos(mostSimilar.artistMusicTitleId);
                    bestNcPos = self.ibb.getPos(newMusic.artistMusicTitleId);
                    bestState = state;
                    occupancy = self.ibb.isPosOccupied(pos);
                }
            }
        });
    });


    if (null !== bestNcPos && null !== bestMsPos) {
        this.ibb.setPos(newMusic.artistMusicTitleId, bestNcPos);
        this.ibb.setPos(mostSimilar.artistMusicTitleId, bestMsPos);

        var externalCell = 0 === bestState ? newMusic : mostSimilar;
        var pos = this.ibb.getPos(externalCell.artistMusicTitleId);
        if (this.ibb.isPosOccupied(pos) >= 2) {
            visitedCells.push(this.ibb.posToInt(this.ibb.getPos(newMusic.artistMusicTitleId)));
            visitedCells.push(this.ibb.posToInt(this.ibb.getPos(mostSimilar.artistMusicTitleId)));

            var conflictList = this.ibb.getByPos(pos),
                first = conflictList[0],
                second = conflictList[1];

            this.resolveConflict(first, second, visitedCells);
        } else {
            // console.log('DONE WITH: ' + newMusic);
        }
    } else { // the cell is trapped.
        console.log("Cell is trapped: ");
        console.log(newMusic);
        console.log(mostSimilar);
    }
}

IncBoard.prototype.insert = function(v) {
    if (this.ibb.getByAMTId(v.artistMusicTitleId) !== undefined) {
        console.log("Trying to insert " + v.artistMusicTitleId + " that already is on incBoard. Discarding it...");
        return false;
    }

    // Find the most similar element already on incBoard.
    var maxSimilarity = 0,
        mostSimilar = null,
        nSwitches = 0,
        self = this;

    this.ibb.getAllMusic().forEach(function(e, artistMusicTitleId) {
        if(artistMusicTitleId in self.similarity && v.artistMusicTitleId in self.similarity[artistMusicTitleId] && maxSimilarity < self.similarity[artistMusicTitleId][v.artistMusicTitleId]) {
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

    console.log("inserted " + v.artistMusicTitleId);
    this.ibb.removeOutOfBorder();
    this.ibb.centralizeItems();
    this.ibb.flushDraw();

    return true;
}

IncBoard.prototype.searchMusic = function(set, num, callback) {
    var self = this,
        m = set.shift();

    console.log('searchMusic -- num: ' + num + '. length: ' + set.length);
    if (num > 0 && 'undefined' !== typeof m) {
        $.get('/api/searchmusic', {
            'artist': m.artist,
            'musicTitle': m.musicTitle
        }, function(v) {
            try {
                var start = new Date().getTime();
                if (null !== v && true === self.insert(v)) {
                    if ('function' === typeof callback) {
                        callback(v, set, num);
                    }
                    self.searchMusic(set, num - 1);
                } else {
                    self.searchMusic(set, num);
                }
                var end = new Date().getTime();
            } catch(e) {
                console.log(e.stack);
                console.log(e);
            }
        }, 'json');
    }
}

IncBoard.prototype.posToString = function (pos) {
    return "(" + pos[0] + "," + pos[1] + ")";
};

IncBoard.prototype.incrementSimilar = function() {
    var self = this;

    if (true === this.incrementSimilarRunning) {
        setTimeout(1000, this.incrementSimilar);
    } else {
        if (this.searchSimilarList.length > 0) {
            var obj = this.searchSimilarList.shift(),
                artist = obj[0],
                musicTitle = obj[1];

            console.log('INCREMENTING ' + artist + " - " + musicTitle);
            this.incrementSimilarRunning = true;
            $.post('/api/searchsimilar', {
                q: artist + ' - ' + musicTitle,
                artist: artist,
                musicTitle: musicTitle,
                artistMusicTitleIdList: incBoard.ibb.getIdsList()
            }, function(data) {
                loadSimilarMusic(data, 10);
                self.incrementSimilarRunning = false;
                self.incrementSimilar();
            }, 'json').error(function() {
                self.incrementSimilarRunning = false;
            });
        }
    }
};

var incBoard = new IncBoard();

function searchMusicCallbackCenter(v) {
    console.log(v);
    console.log(v.artistMusicTitleId);

    $('#' + v.artistMusicTitleId).addClass('center');
}

function loadSimilarMusic(data, num, callback) {
    $.bootstrapMessageOff();
    var total = 0;
    incBoard.similarity = data[1];
    incBoard.searchMusic(data[0], num, callback);
}

$(document).ready(function() {
    if (1 === $('#incboard-search').length) {
        $('.music-large').live('click', function(e) {
            var artist = $(this).parent().attr('artist');
            var musicTitle = $(this).parent().attr('musicTitle');
            incBoard.searchSimilarList.push([artist, musicTitle]);
            incBoard.incrementSimilar();
        });
    }

    $('#incboard-search').ajaxForm({
        dataType: 'json',
        success: function (data) {
            loadSimilarMusic(data, 10);
        },
        beforeSubmit: function() {
            $('#subtitle').subtitleInit();
            incBoard.searchString = $('#q').val();
            $.bootstrapMessage('Loading...', 'info');
            incBoard.clean();
            var obj = new Object();
            obj.artist = $('#artist').val();
            obj.musicTitle = $('#musicTitle').val();
            if (isSearchFormValid()) {
                incBoard.searchMusic([obj], 1, searchMusicCallbackCenter);
            }
        }
    });

    $(window).bind('resize', $.proxy(incBoard.ibb, 'resize'));
});
