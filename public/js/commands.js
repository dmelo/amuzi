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
    if(2 == command.split("$$::$$").length) {
        var commandName = command.split("$$::$$")[0];
        var commandParams = command.split("$$::$$")[1].split(":::");

        // addTrack$$::$$title:::mp3:::pic
        if("addTrack" == commandName) {
            if(3 == commandParams.length) {
                myPlaylist.add({title: commandParams[0], mp3: commandParams[1], free: true}, true);
                addTrack(commandParams[0], commandParams[1], commandParams[2]);
            }
            else
                $.bootstrapMessageAuto("Invalid parameters for addTrack", "error");
        }
        else {
            $.bootstrapMessageAuto("Command not found", "error");
        }
    }
}

// Interpret and run commands
// The separator for the commands is "&&::&&"
Commands.prototype.runProgram = function() {
    this.isRunCommand = false;
    url = $.url(window.location.href);
    program = url.attr('fragment');

    if('string' == typeof(program)) {
        commandArray = program.split("&&::&&");
        for(var i = 0; i < commandArray.length; i++)
            this.runCommand(commandArray[i]);
    }
}
