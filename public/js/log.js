function Log() {
    if (Log.prototype.singletonInstance) {
        return Log.prototype.singletonInstance;
    }

    Log.prototype.singletonInstance = this;

    var enabledList = ['DEBUG', 'INFO', 'WARN', 'ERROR'];

    function getDateTime() {
        var now = new Date(),
            y = now.getFullYear(),
            m = now.getMonth() + 1,
            d = now.getDate(),
            h = now.getHours(),
            M = now.getMinutes(),
            s = now.getSeconds();

        return y + '-' + m + '-' + d + ' ' + h + ':' + M + ':' + s;
    }

    function mainLog(msg, type) {
        if (enabledList.indexOf(type) !== -1) {
            console.log(getDateTime() + " " + type + ": " + msg);
        }
    }

    function switchType(type, action) {
        var i;

        if (true === action && -1 === enabledList.indexOf(type)) {
            enabledList.push(type);
        } else if (false === action && -1 !== (i = enabledList.indexOf(type))) {
            enabledList.splice(i, 1);
        }
    }

    this.enable = function (type) {
        switchType(type, true);
    };

    this.disable = function (type) {
        switchType(type, false);
    };

    this.debug = function (msg) {
        mainLog(msg, 'DEBUG');
    };

    this.info = function (msg) {
        mainLog(msg, 'INFO');
    };

    this.warn = function (msg) {
        mainLog(msg, 'WARN');
    };

    this.error = function (msg) {
        mainLog(msg, 'ERROR');
    };
}


