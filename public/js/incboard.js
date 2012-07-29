var incBoard = new IncBoard();

function  IncBoard() {
    this.searchString = "";
    this.rows = 7;
    this.cols = 14;
    this.cellSize = 50;
}

IncBoard.prototype.insert = function(v, row, col) {
    cell = new IncBoardCell(this.cellSize);
    cell.setContent(v);
    cell.setPos(row, col);
    cellHtml = cell.getHtml();
    $('#incboard-result #incboard').append(cellHtml);
}

IncBoard.prototype.clean = function() {
    var table = $('<div id="incboard"></div>');
    table.css('width', this.cols * this.cellSize);
    table.css('height', this.rows * this.cellSize);
    $('#incboard-result').html(table);
}

IncBoard.prototype.init = function() {
    this.clean();
    this.animateCells();
}

IncBoard.prototype.animateCells = function() {
    $('.incboard-cell').live('mouseover', function(e) {
        $('.incboard-img').css('display', 'block');
        $('.incboard-cell').find('.inevidence').removeClass('inevidence');
        $(this).find('.object-music').addClass('inevidence');
        $(this).find('.incboard.img').css('display', 'none');
    });

    $('.incboard-cell').live('mouseleave', function(e) {
        $('.incboard-img').css('display', 'block');
        $('.incboard-cell').find('.inevidence').removeClass('inevidence');
    });
}

$(document).ready(function() {
    incBoard.init();
    $('#incboard-search').ajaxForm({
        dataType: 'json',
        success: function (data) {
            $.bootstrapMessageOff();
            incBoard.clean();
            $.each(data, function(i, s) {
                if(i < incBoard.cols * incBoard.rows) {
                    $.get('/api/searchmusic', {
                        'artist': s.artist,
                        'musicTitle': s.musicTitle
                    }, function(v) {
                        incBoard.insert(v, Math.floor(i / incBoard.cols), i % incBoard.cols);
                    }, 'json');
                }
            });
        },
        beforeSubmit: function() {
            incBoard.searchString = $('#q').val();
            console.log($('#q').val());
            $.bootstrapMessage('Loading...', 'info');
        }
    });

});
