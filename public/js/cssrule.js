(function ($, undefined) {
    'use strict';

    $.cssRule = function (selector, property, value) {
        var sheet = document.styleSheets[document.styleSheets.length - 1],
            i;

        if ('function' === typeof sheet.addRule) {
            sheet.addRule(selector, property + ': ' + value);
        } else if ('function' === typeof sheet.insertRule) {
            i = sheet.cssRules.length;
            sheet.insertRule(selector + '{ ' + property + ': ' + value + '}', i);
        }
    };
}(jQuery));
