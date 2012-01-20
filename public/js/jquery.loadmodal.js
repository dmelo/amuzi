/**
 * Usage: $('a.identifier').loadModal();
 *
 * When an element is clicked, a modal is loaded with the content taken from
 * href. After loading the modal, the function callback#ID# (where #ID# is the
 * id of the DOM element) is called (if the function exists).
 *
 * If the element have the class "noForm" then the ajaxForm is not instantiated
 * for it.
 *
 * Author: Diogo Oliveira de Melo
 */

(function($, undefined) {
    $.fn.extend({
        bootstrapLoadModal: function() {
            var modalWrapper = '#load-modal-wrapper';

            $('body').append('<div id="load-modal-wrapper" class="modal hide fade"><div class="modal-header"><a href="#" class="close">&times;</a><h3></h3></div><div class="modal-body"></div></div>');

            $(modalWrapper).modal({
                backdrop: true,
                keyboard: true,});

            $('#cancel').live('click', function(e) {
                $(modalWrapper).modal('hide');
            });

            $(this).click(function(e) {
                e.preventDefault();
                var noForm = false;
                var id = $(this).attr('id');
                if($(this).hasClass('noForm'))
                    noForm = true;
                var title = $(this).attr('title');
                $.post($(this).attr('href'), {
                }, function(data) {
                    $(modalWrapper + ' .modal-body').html(data);
                    $(modalWrapper + ' h3').html(title);
                    $(modalWrapper).modal('show');
                    if(!noForm) {
                        $(modalWrapper + ' form').ajaxForm({
                            dataType: 'json',
                            success: function (data) {
                                messageAuto('Saved');
                            },
                            error: function(data) {
                                messageAuto('Error saving. Something went wrong', 'error');
                            },
                            beforeSubmit: function() {
                                $(modalWrapper).modal('hide');
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
