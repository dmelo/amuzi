(function ($, undefined) {
    'use strict';

    $.cssRule = function (selector, property, value) {
        var sheet = document.styleSheets[document.styleSheets.length - 1],
            i;

        if ('addRule' in sheet) {
            sheet.addRule(selector, property + ': ' + value);
        } else if ('insertRule' in sheet) {
            i = sheet.cssRules.length;
            sheet.insertRule(selector + '{ ' + property + ': ' + value + '}', i);
        }
    };
}(jQuery));
