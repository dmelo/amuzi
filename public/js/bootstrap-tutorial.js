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
