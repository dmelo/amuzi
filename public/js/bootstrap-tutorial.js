/**
 * bootstrap-tutorial.js
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2013  Diogo Oliveira de Melo
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

function Tutorial () {
}

Tutorial.prototype.welcome = function() {
    var self = this;
    $.get('/tutorial/welcome', function(data) {
        $.bootstrapLoadModalDisplay(data[0], data[1]);
        $('.modal').bind('hide', function() {
            $('.modal').unbind('hide');
            $.get('/tutorial/setaccomplished', {
                name: 'welcome'
            }, function() {
                self.apply();
            });
        });
    }, 'json');
}

Tutorial.prototype.search = function() {
    var self = this;
    $.get('/tutorial/search', function(data) {
        var e = 'form.search .input-append';
        $(e).attr('data-content', data);
        $(e).popover({placement: 'top'});
        $(e).popover('show');
        window.tutorialCloseSearch = function() {
            $(e).popover('hide');
            $.get('/tutorial/setaccomplished', {
                name: 'search'
            }, function() {
                self.apply();
            });
        }
    });
}

Tutorial.prototype.slide = function() {
    var self = this;
    $.get('/tutorial/slide', function(data) {
        var ele = $('.screens .screen img:first');
        ele.attr('data-content', data);
        ele.popover({placement: 'bottom'});
        ele.popover('show');
        ele.click(function(e) {
            ele.popover('hide');
            ele.unbind('click');
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
