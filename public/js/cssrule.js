

$.cssRule = function(selector, property, value) {
    var sheet = document.styleSheets[document.styleSheets.length - 1];

    console.log("Applying: " + selector + "#" + property + "#" + value);
    if ('function' === typeof sheet.addRule) {
        sheet.addRule(selector, property + ': ' + value);
    } else if ('function' === typeof sheet.insertRule) {
        var i = sheet.cssRules.length;
        sheet.insertRule(selector + '{ ' + property + ': ' + value + '}', i);
    }
}


