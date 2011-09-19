function setAudio(src, title) {
    $('#play').html('<p>Title: ' + title + '</p><audio controls preload><source src="' + src + '"></source></audio>');
}

function cleanTable() {
    thead = '<thead><tr class="topic"><th>Cover</th><th>Title</th><th>Options</th></tr></thead>';
    tbody = '<tbody></tbody>';
    $('#result').html('<table>' + thead + tbody + '</table>');
}

function appendTable(img, title, url) {
    img = '<img class="cover" src="' + img + '" alt="' + title + '"/>';
    options = '<a href="' + url + '" class="addplaylist" title="' + title + '"><img alt="playlist" src="/img/playlist.gif"/></a>';
    options += '<a href="' + url + '" class="download" title="' + title + '" target="_blank"><img alt="download" src="/img/download.gif"/></a>';
    tr = '<tr><td>' + img + '</td><td><p>' + title + '</p></td><td>' + options + '</td><td>' + '</tr>';
    $('#result table tbody').append(tr);
}

function message(text) {
    $('.message div').html(text);
    $('.message').css('opacity', '1');
}

function messageOff() {
    $('.message').css('opacity', '0');
}


$(document).ready(function() {
    $('#search').ajaxForm({
        dataType: 'json', 
        success: function (data) {
            messageOff();
            cleanTable();
            for(i = 0; i < data.length; i++)
                appendTable(data[i].pic, data[i].title, data[i].you2better);
        /*
                $('#result').append('<img src="' + data[i].pic + '"/><a title="' + data[i].title + '" class="audio" href="' + data[i].you2better + '">' +
                    data[i].title + '</a><br/>');
                    */
        },
        beforeSubmit: function() {
            message('Loading...');
        }
    });

    $('.audio').live('click', function(e) {
        e.preventDefault();
        setAudio($(this).attr('href'), $(this).attr('title'));
    });

    $('.addplaylist').live('click', function(e) {
        e.preventDefault();
        $('#play').dmplaylist.addToPlaylist($(this).attr('href'), $(this).attr('title'));
    });

    $('#play').draggable();
    $('#play').dmplaylist();

    $('#q').autocomplete('/api/autocomplete', {
        dateType: 'json',
        parse: function(data) {
            data = $.parseJSON(data);
            return $.map(data, function(row) {
                return {
                    data: row,
                    value: '<img src="' + row.pic + '"/> <span>' + row.name + '</span>',
                    result: row.name
                }
            });

        },
        formatItem: function(row, i, n) {
            return '<img src="' + row.pic + '"/> <span>' + row.name + '</span>';
        }
    });

    $('#q').change(function() {
        $('#search').submit();
    });
});
