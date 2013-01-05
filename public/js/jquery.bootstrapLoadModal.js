/**
 * jquery.bootstrapLoadModal.js
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2013  Diogo Oliveira de Melo
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
 * Considering ID to be the id attribute of the identifier link, if rendered_ID
 * is defined as a function, then it will be called after the modal is rendered.
 * If callback_ID is defined as a function, it will be called after confirm
 * button is clicked.
 *
 * Author: Diogo Oliveira de Melo
 */

(function($, undefined) {
    var lock = 0;

    $.bootstrapLoadModalInit = function() {
        if($('#load-modal-wrapper').length == 0) {
            $('body').append('<div id="load-modal-wrapper" class="modal hide fade"><div class="modal-header"><a href="#" class="close" data-dismiss="modal">&times;</a><h3></h3></div><div class="modal-body"></div></div>');
        }

        $('.loadModal').live('click', function(e) {
            e.preventDefault();
            var callback = $(this).attr('id');
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
                    $($.modalWrapper + ' .modal-body').html(data);
                    $($.modalWrapper + ' h3').html(title);
                    $($.modalWrapper).modal('show');
                    var funcName = 'rendered_' + callback;

                    try {
                        eval(funcName)();
                    } catch (err) {
                        if ('function' === typeof $[funcName]) {
                            $[funcName]();
                        } else {
                            console.log('function ' + funcName + ' is undefined');
                        }
                    }

                    if(!noForm) {
                        $($.modalWrapper + ' form').ajaxForm({
                            dataType: 'json',
                            success: function (data) {
                                callback = "callback_" + callback;
                                $.bootstrapMessageAuto(data[0], data[1]);
                                try {
                                    eval(callback)(data);
                                } catch (err) {
                                    if ('function' === typeof $[callback]) {
                                        $[callback](data);
                                    } else {
                                        console.log('Error trying to run ' + callback + '(data);');
                                    }
                                }
                            },
                            error: function(data) {
                                $.bootstrapMessageAuto('Error saving. Something went wrong', 'error');
                            },
                            beforeSubmit: function() {
                                $($.modalWrapper).modal('hide');
                                $.bootstrapMessage('Saving...');
                            }
                        });
                    }
                    func = "window." + name + 'Callback';
                    if(typeof eval(func) == 'function')
                        eval(func)();
                    lock--;
                }).error(function (e) {
                    $.bootstrapMessageAuto('Error loading page content.', 'error');
                    $($.modalWrapper).modal('hide');
                });
            }
        });

        $('#cancel').live('click', function(e) {
            $($.modalWrapper).modal('hide');
        });




    };

    $.bootstrapLoadModalDisplay = function(title, content, addClass) {
        $.bootstrapLoadModalInit();
        $($.modalWrapper + ' .modal-body').html(content);
        $($.modalWrapper + ' h3').html(title);
        $($.modalWrapper).modal('show');
        if ('undefined' !== typeof addClass) {
            $($.modalWrapper).addClass(addClass);
        }

        $($.modalWrapper).bind('hidden', function(e) {
            $($.modalWrapper + ' .modal-body').html(' ');
        });

        return $.modalWrapper;
    };

    $.bootstrapLoadModalLoading = function() {
        $.bootstrapLoadModalDisplay('Loading...', '<img src="/img/loading.gif"/>');
    };

    $.fn.extend({
        bootstrapLoadModalLock: 0,

        bootstrapLoadModal: function() {

            $.bootstrapLoadModalInit();

            $($.modalWrapper).modal({
                backdrop: true,
                keyboard: true,
                show: false});
        }
    });
})(jQuery);
