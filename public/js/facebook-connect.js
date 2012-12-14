"use strict";

function authenticate() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
        console.log("---> " + typeof response);
        console.log("---> " + response.status);
        if ('undefined' !== typeof response) {
            console.log('redirecting');
            $('#email').val(response.email);
            $('#authority').val('facebook');
            $('#submit').click();
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
