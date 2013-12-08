INSERT INTO `bond`(`id`, `name`, `priority`, `comment`)
    VALUES(1, 'search', 0, 'A artist / music_title search lead to the track'),
          (2, 'insert_playlist', 8, 'The track was chosen to go to the playlist'),
          (3, 'vote_up', 16, 'The user gave a positive vote to the track'),
          (4, 'vote_down', 16, 'The user gave a negative vote to the track');

INSERT INTO `user`(`name`, `email`, `password`)
    VALUES('Diogo Melo', 'dmelo87@gmail.com', sha1('123456'));

INSERT INTO `playlist`(user_id, name) VALUES(last_insert_id(), 'Diogo Melo');

update user set current_playlist_id = (select id from playlist where name = 'Diogo Melo' limit 1) where name = 'Diogo Melo';

INSERT INTO `task_type`(`name`, `duration`) values('SearchSimilar', 60 * 60 * 24 * 30), ('SearchString', 60 * 60 * 24 * 30 * 6);

INSERT INTO `tutorial`(`name`) values('welcome'), ('search'), ('slide');

INSERT INTO `log_action`(`name`) values('add_album'), ('add_track'), ('change_view'), ('ping');

source artist_full.sql;
