/**
 * autocomplete.js
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2014  Diogo Oliveira de Melo
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

(function ($, undefined) {
    $(document).ready(function() {
        var callbackAutocomplete = function(data) {
            console.log('bla');
            console.log(data);
            var end = new Date(),
                count = 0,
                a = null !== data ? $.map(data, function (row) {
                return {
                    data: row,
                    label: '<div class="cover"><img src="' + ('' === row.cover ? '/img/album.png' : row.cover )+ '"/></div> <div class="description"><span>' + row.name + '</span></div>',
                    category: row.type,
                    value: row.name,
                    artist: row.artist,
                    musicTitle: row.musicTitle,
                    type: row.type
                };
            }, 'json') : [];

            globalResponse(a);
        };

        function nothing() {
        }

        function openAutocomplete() {
            $('.ui-autocomplete').addClass(0 === $('#userId').length ? 'ui-autocomplete-logout' : 'ui-autocomplete-login');
        }

        function acError(e) {
            $.bootstrapMessageAuto(
                $.i18n._('Error loading suggestions. Please, try reloading your browser.'),
                'error'
            );
        }

        $.widget( "custom.catcomplete", $.ui.autocomplete, {
            _renderMenu: function( ul, items ) {
                var that = this,
                    currentCategory = "";
                $.each( items, function( index, item ) {
                    if ( item.category != currentCategory ) {
                        var t = item.category.charAt(0).toUpperCase() + item.category.slice(1) + 's';
                        ul.append( "<li class='ui-autocomplete-category " + item.category + "'>" + t + "</li>" );
                        currentCategory = item.category;
                    }
                    that._renderItemData( ul, item );
                });
            }
        });

        var acOption = {
            source: function (request, response) {
                globalResponse = response;
                $.get('/autocomplete.php', {
                    q: request.term,
                }, callbackAutocomplete, 'json').error(acError);
            }, messages: {
                noResults: '',
                results: function() {}
            },
            focus: nothing,
            close: nothing,
            open: openAutocomplete
        };

        
        $.fn.lfmComplete = function(options) {
            if (null !== options & 'callback' in options && 'function' === typeof options.callback) {
                acOption.select = acOption.change = options.callback;
            }

            this.catcomplete(acOption);
        };
    });
}(jQuery, undefined));
