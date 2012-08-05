
function IncBoardCell(cellSize) {
    this.cellSize = cellSize;
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

IncBoardCell.prototype.getHtml = function() {
    var v = this.content;
    var resultSet = new ResultSet();
    sty = 'width: ' + this.cellSize + 'px; height: ' + this.cellSize + 'px; ';
    img = '<div class="incboard-img"><img src="' + v.cover + '"/></div>';
    console.log(this.content);
    title = '<span class="title">' + v.artist + ' - ' + v.musicTitle + '</span>';
    duration = '<p>' + resultSet.secondsToHMS(v.duration) + '</p>';
    info = '<div class="incboard-info">' + title + duration + '</div>';
    control = '<div class="incboard-control play">' + resultSet.getControl(v) + '</div>';
    return $('<div artist="' + v.artist + '" class="incboard-cell" style="' + sty + 'top: ' + (this.row * this.cellSize) + 'px; left: ' + (this.col * this.cellSize) + 'px;">' + img + resultSet.getMusicLarge(v, 'music') + '</div>');
}
