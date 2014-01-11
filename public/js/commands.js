/**
 * commands.js
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2014  Diogo Oliveira de Melo
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
/**
 * Parse and run commands given by url fragment.
 */

/**
 * Constructor
 */
function Commands() {
}

Commands.prototype.runCommand = function(command) {
    console.log('I GOT CALLED for command: ' + command);
    if('t' === command[0]) {
        id = command.substr(1);
        $.get('/api/gettrack', {
            id: id
        }, function(data) {
            $.addTrack(data.id, undefined, undefined, true);
            $('.slide-next').trigger('click');
        }, 'json').error(function (e) {
            $.bootstrapMessageAuto(
                'Error loading track. Please, try reloading the page.', 'error'
            );
        });

    } else if ('p' === command[0]) {
        id = command.substr(1);
        $.loadPlaylist(id);
    } else if ('a' === command[0]) {
        $.addAlbum(command.substr(1));
        $.loadPlaylist(command.substr(1), {isAlbum: true});
    }
};

Commands.prototype.getCommandOnFragment = function() {
    var ret;
    url = $.url(window.location.href);
    program = url.attr('fragment');
    index = program.indexOf("!");
    program = program.substr(index + 1);

    if ('' === program) {
        ret = null;
    } else {
        ret = program;
    }

    console.log('getCommandOnFragment returning #' + ret + '#');
    return ret;
};

// Only one command is allowed.
Commands.prototype.runProgram = function() {
    var program = null;
    if (1 === $('#userId').length) { // User is logged in.
        console.log('command - logged in');
        if (null !== $.cookie('commandc') && 'nnn' !== $.cookie('commandc')) {
            console.log('command - command on cookie');
            program = $.cookie('commandc');
            $.cookie('commandc', 'nnn', {path: '/'});
        }

        if (null !== this.getCommandOnFragment()) {
            console.log('command - trying to find command on fragment.');
            program = this.getCommandOnFragment();
        }

        if ('string' === typeof(program)) {
            console.log('command - running command ' + program);
            this.runCommand(program);
        }
    } else { // User is logged out.
        console.log('command - logged off');
        program = this.getCommandOnFragment();
        if ('string' === typeof(program)) {
            console.log('command - storing command on cookie.');
            $.cookie('commandc', program, {path: '/'});
        }
    }
};
