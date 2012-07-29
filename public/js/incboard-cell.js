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
    img = '<div class="incboard-img"><img src="' + this.content.cover + '"/></div>';
    console.log(this.content);
    info = '<div class="incboard-info"><p>bla bla bla bla</p><input type="button" val="add"/></div>';
    return '<div class="incboard-cell" style="' + sty + 'top: ' + (this.row * this.cellSize) + 'px; left: ' + (this.col * this.cellSize) + 'px;">' + img + info + '</div>';
}
