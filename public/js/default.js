function setAudio(src, title) {
    $('#play').html('<p>Title: ' + title + '</p><audio controls preload><source src="' + src + '"></source></audio>');
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
});
