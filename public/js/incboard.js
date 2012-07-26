var incBoard = new IncBoard();

function  IncBoard() {
    this.searchString = "";
    this.rows = 10;
    this.cols = 10;
    this.cellSize = 30;
}

IncBoard.prototype.insert = function(v, row, col) {
    cell = new IncBoardCell(this.cellSize);
    cell.setContent(v);
    cell.setPos(row, col);
    cellHtml = cell.getHtml();
    $('#incboard-result #incboard').append(cellHtml);
}

IncBoard.prototype.clean = function() {
}

IncBoard.prototype.init = function() {
    var table = $('<div id="incboard"></div>');
    table.css('width', this.cols * this.cellSize);
    table.css('height', this.rows * this.cellSize);
    $('#incboard-result').html(table);
}


$(document).ready(function() {
    incBoard.init();
    $('#incboard-search').ajaxForm({
        dataType: 'json',
        success: function (data) {
            $.bootstrapMessageOff();
            incBoard.clean();
            $.each(data, function(i, v) {
                console.log(v);
                incBoard.insert(v, Math.floor(i / incBoard.cols), i % incBoard.cols);
            });
        },
        beforeSubmit: function() {
            incBoard.searchString = $('#q').val();
            console.log($('#q').val());
            $.bootstrapMessage('Loading...', 'info');
        }
    });

});
