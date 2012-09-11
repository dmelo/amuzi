/**
 * jquery.bootstrapLoadModal.js
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2012  Diogo Oliveira de Melo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
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
    var lock = 0;

    $.bootstrapLoadModalInit = function() {
        if($('#load-modal-wrapper').length == 0)
            $('body').append('<div id="load-modal-wrapper" class="modal hide fade"><div class="modal-header"><a href="#" class="close" data-dismiss="modal">&times;</a><h3></h3></div><div class="modal-body"></div></div>');
    };

    $.bootstrapLoadModalLoading = function() {
        var modalWrapper = '#load-modal-wrapper';
        $.bootstrapLoadModalInit();
        $(modalWrapper + ' .modal-body').html('<img src="/img/loading.gif"/>');
        $(modalWrapper + ' h3').html('Loading...');
        $(modalWrapper).modal('show');
    };
    $.fn.extend({
        bootstrapLoadModalLock: 0,

        bootstrapLoadModal: function() {
            var modalWrapper = '#load-modal-wrapper';

            $.bootstrapLoadModalInit();

            $(modalWrapper).modal({
                backdrop: true,
                keyboard: true,
                show: false});

            $('#cancel').live('click', function(e) {
                $(modalWrapper).modal('hide');
            });

            $(this).click(function(e) {
                e.preventDefault();
                if(0 == lock) {
                    lock++;
                    var noForm = false;
                    var name = $(this).attr('name');
                    if($(this).hasClass('noForm'))
                        noForm = true;
                    var title = $(this).attr('title');


                    $.bootstrapLoadModalLoading();

                    $.post($(this).attr('href'), {
                    }, function(data) {
                        $(modalWrapper + ' .modal-body').html(data);
                        $(modalWrapper + ' h3').html(title);
                        $(modalWrapper).modal('show');
                        if(!noForm) {
                            $(modalWrapper + ' form').ajaxForm({
                                dataType: 'json',
                                success: function (data) {
                                    $.bootstrapMessageAuto('Saved');
                                },
                                error: function(data) {
                                    $.bootstrapMessageAuto('Error saving. Something went wrong', 'error');
                                },
                                beforeSubmit: function() {
                                    $(modalWrapper).modal('hide');
                                   $.bootstrapMessage('Saving...');
                                }
                            });
                        }
                        func = "window." + name + 'Callback';
                        if(typeof eval(func) == 'function')
                            eval(func)();
                        lock--;
                    });
                }
            });
        }
    });
})(jQuery);
