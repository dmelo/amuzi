ALTER TABLE `user_listen_album` add column `repeat` int(1) NOT NULL default 0 AFTER `user_id`;
ALTER TABLE `user_listen_album` add column `shuffle` int(1) NOT NULL default 0 AFTER `repeat`;
