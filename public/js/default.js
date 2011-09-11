function setAudio(src, title) {
    $('#play').html('<p>Title: ' + title + '</p><audio controls preload><source src="' + src + '"></source></audio>');
}

function cleanTable() {
    thead = '<thead><tr class="topic"><th>Cover</th><th>Title</th><th>Options</th></tr></thead>';
    tbody = '<tbody></tbody>';
    $('#result').html('<table>' + thead + tbody + '</table>');
}

function appendTable(img, title, url) {
    img = '<img src="' + img + '" alt="' + title + '"/>';
    title = '<p>' + title + '</p>';
    options = '<a href="' + url + '" class="addplaylist">&nbsp;</a>';
    options += '<a href="' + url + '" class="download">&nbsp;</a>';
    tr = '<tr><td>' + img + '</td><td>' + title + '</td><td>' + options + '</td><td>' + '</tr>';
}

$(document).ready(function() {
    $('#search').ajaxForm({dataType: 'json', success: function (data) {
        $('#result').html(' ');
        for(i = 0; i < data.length; i++)
            $('#result').append('<img src="' + data[i].pic + '"/><a title="' + data[i].title + '" class="audio" href="' + data[i].you2better + '">' +
                data[i].title + '</a><br/>');
    }});

    $('.audio').live('click', function(e) {
        e.preventDefault();
        setAudio($(this).attr('href'), $(this).attr('title'));
    });

    $('.addplaylist').live('click', function(e) {
        e.preventDefault();
    });

    $('#play').draggable();
});
