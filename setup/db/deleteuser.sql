update user set current_playlist_id = null where email = 'melo@vonbraunlabs.com.br';
update user set current_album_id = null where email = 'melo@vonbraunlabs.com.br';
delete from playlist_has_track where playlist_id in (select id from playlist where user_id in (select id from user where email = 'melo@vonbraunlabs.com.br'));

delete from user_listen_playlist where user_id in (select id from user where email = 'melo@vonbraunlabs.com.br');

delete from playlist where user_id in (select id from user where email = 'melo@vonbraunlabs.com.br');

delete from music_track_link where user_id in (select id from user where email = 'melo@vonbraunlabs.com.br');

delete from tutorial_accomplished where user_id in (select id from user where email = 'melo@vonbraunlabs.com.br');

delete from user where email = 'melo@vonbraunlabs.com.br';
