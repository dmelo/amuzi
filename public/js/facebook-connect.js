/**
 * facebook-connect.js
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
"use strict";

function authenticate() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
        if ('undefined' !== typeof response) {
            console.log('redirecting');
            $('form#fblogin').submit();
        }
        console.log('Good to see you, ' + response.name + '.' + response.email);
    });
}

function login() {
    FB.login(function(response) {
        if (response.authResponse) {
            authenticate();
        } else {
            // cancelled
        }
    }, {scope: 'email'});
}

function connectWithFacebook() {
    FB.getLoginStatus(function(response) {
        console.log(response);
        if (response.status === 'connected') {
            // connected
            console.log("calling authenticate");
            authenticate();
        } else if (response.status === 'not_authorized') {
            // not_authorized
            console.log("calling login");
            login();
        } else {
            // not_logged_in
            console.log("calling login");
            login();
        }
    });
}

$(document).ready(function() {
    $('#facebook-connect').click(function(e) {
        e.preventDefault();
        connectWithFacebook();
    });
});
