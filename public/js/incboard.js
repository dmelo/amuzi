var incBoard = new IncBoard();

function  IncBoard() {
    this.searchString = "";
}

IncBoard.prototype.append = new function(cover, title, url, duration) {
}

IncBoard.prototype.clean = new function() {
}


$(document).ready(function() {
    incBoard.f();
    $('#incboard-search').ajaxForm({
        dataType: 'json',
        success: function (data) {
            $.bootstrapMessageOff();
            incBoard.clean();
            $.each(data, function(i, v) {
                incBoard.append(v.cover, v.title, v.url, v.duration);
            });
        },
        beforeSubmit: function() {
            incBoard.searchString = $('#q').val();
            console.log($('#q').val());
            $.bootstrapMessage('Loading...', 'info');
        }
    });

});
