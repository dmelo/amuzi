/**
 * Parse and run commands given by url fragment.
 */

var commands = new Commands();

/**
 * Constructor
 */
function Commands() {
    this.isRunCommand = false;
}

Commands.prototype.runCommand = function(command) {
    if('t' === command[0]) {
        id = command.substr(1);
        $.get('/api/gettrack', {
            id: id
        }, function(data) {
            addTrack(data.id);

        }, 'json');

    }
    else if ('p' === command[0]) {
    }
}

// Interpret and run commands
// The separator for the commands is "&&::&&"
Commands.prototype.runProgram = function() {
    this.isRunCommand = false;
    url = $.url(window.location.href);
    program = url.attr('fragment');
    index = program.indexOf("!");
    program = program.substr(index + 1);

    if('string' === typeof(program)) {
        this.runCommand(program);
    }
}
