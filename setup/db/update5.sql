ALTER TABLE `user` ADD COLUMN `lang` varchar(10) not null default 'en_US' AFTER `current_album_id`;
