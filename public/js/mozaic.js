$(document).ready(function() {
    $.get('/api/gettop', {},
    function(data) {
        console.log(data);
    }, 'json');
});
