
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

IncBoardCell.prototype.setPos = function(row, col) {
    this.row = row;
    this.col = col;
}

IncBoardCell.prototype.getPos = function() {
    return [this.row, this.col];
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
    return $('<div id="' + v.artistMusicTitleId + '" artist="' + v.artist + '" class="incboard-cell" style="' + sty + 'top: ' + (this.row * this.cellSizeY) + 'px; left: ' + (this.col * this.cellSizeX) + 'px;">' + img + resultSet.getMusicLarge(v, 'music') + '</div>');
}
