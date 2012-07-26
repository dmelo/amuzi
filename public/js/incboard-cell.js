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
    sty = 'width: ' + this.cellSize + 'px; height: ' + this.cellSize + 'px; ';
    return '<div class="incboard-cell" style="' + sty + 'top: ' + (this.row * this.cellSize) + 'px; left: ' + (this.col * this.cellSize) + 'px;"><img src="' + this.content.cover + '"/></div>';
}
