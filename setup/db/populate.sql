INSERT INTO `bond`(`id`, `name`, `priority`, `comment`)
    VALUES(1, 'search', 0, 'A artist / music_title search lead to the track'),
          (2, 'insert_playlist', 8, 'The track was chosen to go to the playlist'),
          (3, 'vote_up', 16, 'The user gave a positive vote to the track'),
          (4, 'vote_down', 16, 'The user gave a negative vote to the track');

INSERT INTO `user`(`name`, `email`, `password`)
    VALUES('Diogo Melo', 'dmelo87@gmail.com', '30a8caa8b6a0ff02a958c31d8c5f7f622f12232c');

INSERT INTO `playlist`(user_id, name) VALUES(last_insert_id(), 'Diogo Melo');

INSERT INTO `task_type`(`name`, `duration`) values('SearchSimilar', 60 * 60 * 24 * 30);
