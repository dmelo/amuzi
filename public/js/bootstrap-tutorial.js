"use strict";

function Tutorial () {
}

Tutorial.prototype.welcome = function() {
    var self = this;
    $.get('/tutorial/welcome', function(data) {
        $.bootstrapLoadModalDisplay(data[0], data[1]);
        $('.modal').bind('hide', function() {
            $.get('/tutorial/setaccomplished', {
                name: 'welcome'
            }, function() {
                self.apply();
            });
        });
    }, 'json');
}

Tutorial.prototype.slide = function() {
    var self = this;
    $.get('/tutorial/slide', function(data) {
        $('.screens').attr('data-content', data);
        $('.screens').popover({placement: 'bottom'});
        $('.screens').popover('show');
        $('.screens').click(function(e) {
            $('.screens').popover('hide');
            $('.screens').unbind('click');
            $.get('/tutorial/setaccomplished', {
                name: 'slide'
            }, function() {
                self.apply();
            });
        });
    });
}

Tutorial.prototype.apply = function() {
    var self = this;
    $.get('/tutorial/getlist', function(data) {
        if (data.length > 0) {
            eval('self.' + data[0] + '()');
        }
    }, 'json');
}

$(document).ready(function() {
    var tutorial = new Tutorial();
    tutorial.apply();
});
