(function ($, undefined) {
    'use strict';
    $(document).ready(function () {
        if (1 === $('#locale').length) {
            var file = '/locale.php?lang=' + $('#locale').html();

            console.log('I18N-DICT ' + file);

            $.ajax({
                data: {},
                type: "POST",
                url: file,
                timeout: 20000,
                contentType: "application/json;charset=UTF-8",
                dataType: 'json',
                success: function(data) {
                    $.i18n.load(data);
                    console.log("Locale loading done");
                }, error: function(data) {
                    console.log('Locale loading error');
                }
            });
        }
    });
}(jQuery, undefined));
