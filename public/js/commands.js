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
    if('s' === command[0]) {
        /*
        myPlaylist.add({title: commandParams[0], mp3: commandParams[1], free: true}, true);
        addTrack(commandParams[0], commandParams[1], commandParams[2]);
        */

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

    if('string' == typeof(program)) {
        this.runCommand(program);
    }
}
