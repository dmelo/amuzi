"use strict";

function Log() {
    if (Log.prototype.singletonInstance) {
        return Log.prototype.singletonInstance;
    }

    Log.prototype.singletonInstance = this;

    var enabledList = ['DEBUG', 'INFO', 'WARN', 'ERROR'],
        getDateTime = function () {
            var now = new Date(),
                y = now.getFullYear(),
                m = now.getMonth() + 1,
                d = now.getDate(),
                h = now.getHours(),
                M = now.getMinutes(),
                s = now.getSeconds();

            return y + '-' + m + '-' + d + ' ' + h + ':' + M + ':' + s;
        };

    this.mainLog = function (msg, type) {
        if (enabledList.indexOf(type) !== -1) {
            console.log(getDateTime() + " " + type + ": " + msg);
        }
    };

    this.switchType = function (type, action) {
        var i;

        if (true === action && -1 === enabledList.indexOf(type)) {
            enabledList.push(type);
        } else if (false === action && -1 !== (i = enabledList.indexOf(type))) {
            enabledList.splice(i, 1);
        }
    };
}

Log.prototype.enable = function (type) {
    this.switchType(type, true);
};

Log.prototype.disable = function (type) {
    this.switchType(type, false);
};

Log.prototype.debug = function (msg) {
    this.mainLog(msg, 'DEBUG');
};

Log.prototype.info = function (msg) {
    this.mainLog(msg, 'INFO');
};

Log.prototype.warn = function (msg) {
    this.mainLog(msg, 'WARN');
};

Log.prototype.error = function (msg) {
    this.mainLog(msg, 'ERROR');
};
