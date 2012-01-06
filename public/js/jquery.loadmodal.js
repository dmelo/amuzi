(function($, undefined) {
    $.fn.extend({
        loadModal: function() {
            $('body').append('<div id="load-modal-wrapper" class="modal hide fade"><div class="modal-header"><h3></h3><a href="#" class="close">&times;</a></div><div class="modal-body"></div></div>');
            $('#load-modal-wrapper').modal({
                backdrop: true,
                keyboard: true,});
            $(this).click(function(e) {
                e.preventDefault();
                var title = $(this).attr('title');
                $.post($(this).attr('href'), {
                }, function(data) {
                    $('#load-modal-wrapper .modal-body').html(data);
                    $('#load-modal-wrapper h3').html(title);
                    $('#load-modal-wrapper').modal('show');
                });
            });
        }
    });
})(jQuery);
