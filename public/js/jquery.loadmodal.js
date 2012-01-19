(function($, undefined) {
    $.fn.extend({
        loadModal: function(func) {
            $('body').append('<div id="load-modal-wrapper" class="modal hide fade"><div class="modal-header"><a href="#" class="close">&times;</a><h3></h3></div><div class="modal-body"></div></div>');
            $('#load-modal-wrapper').modal({
                backdrop: true,
                keyboard: true,});

            $('#cancel').live('click', function(e) {
                $('#load-modal-wrapper').modal('hide');
            });

            $(this).click(function(e) {
                var noForm = false;
                var id = $(this).attr('id');
                if($(this).hasClass('noForm'))
                    noForm = true;
                e.preventDefault();
                var title = $(this).attr('title');
                $.post($(this).attr('href'), {
                }, function(data) {
                    $('#load-modal-wrapper .modal-body').html(data);
                    $('#load-modal-wrapper h3').html(title);
                    $('#load-modal-wrapper').modal('show');
                    if(!noForm) {
                        $('#load-modal-wrapper form').ajaxForm({
                            dataType: 'json',
                            success: function (data) {
                                messageAuto('Saved');
                            },
                            error: function(data) {
                                messageAuto('Error saving. Something went wrong', 'error');
                            },
                            beforeSubmit: function() {
                                $('#load-modal-wrapper').modal('hide');
                               message('Saving...');
                            }
                        });
                    }
                    func = id + 'Callback';
                    alert(func);
                    func();
                    if(typeof eval("window." + func) == 'function')
                        alert(id);
                });
            });
        }
    });
})(jQuery);
