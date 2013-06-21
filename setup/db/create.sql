CREATE TABLE `artist` (
    `id` int(11) NOT NULL auto_increment,
    `name` varchar(63) collate utf8_swedish_ci NOT NULL,
    `cover` varchar(2047) collate utf8_swedish_ci default NULL,
    `info` text collate utf8_swedish_ci default NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`name`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `artist_created_trigger` BEFORE INSERT ON `artist` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `artist_similarity` (
    `id` int(11) NOT NULL auto_increment,
    `f_artist_id` int(11) NOT NULL,
    `s_artist_id` int(11) NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`f_artist_id`, `s_artist_id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `artist_similarity_created_trigger` BEFORE INSERT ON `artist_similarity` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;


CREATE TABLE `music_title` (
    `id` int(11) NOT NULL auto_increment,
    `name` varchar(255) collate utf8_swedish_ci NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`name`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `music_title_created_trigger` BEFORE INSERT ON `music_title` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;
ALTER TABLE `music_title` auto_increment = 10000;

CREATE TABLE `artist_music_title` (
    `id` int(11) NOT NULL auto_increment,
    `artist_id` int(11) NOT NULL,
    `music_title_id` int(11) NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`artist_id`, `music_title_id`),
    CONSTRAINT `artist_music_title_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artist`(`id`),
    CONSTRAINT `artist_music_title_ibfk_2` FOREIGN KEY (`music_title_id`) REFERENCES `music_title`(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `artist_music_title_created_trigger` BEFORE INSERT ON `artist_music_title` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;
ALTER TABLE `artist_music_title` auto_increment = 20000;

CREATE TABLE `music_similarity` (
    `id` int(11) NOT NULL auto_increment,
    `f_artist_music_title_id` int(11) NOT NULL,
    `s_artist_music_title_id` int(11) NOT NULL,
    `similarity` SMALLINT NOT NULL DEFAULT 0,
    `degree` int(3) NOT NULL DEFAULT 0,
    PRIMARY KEY(`id`),
    UNIQUE(`f_artist_music_title_id`, `s_artist_music_title_id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `music_similarity_created_trigger` BEFORE INSERT ON `music_similarity` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;
ALTER TABLE `music_similarity` auto_increment = 30000;

CREATE TABLE `user` (
    `id` int(11) NOT NULL auto_increment,
    `facebook_id` varchar(63) collate utf8_swedish_ci default NULL,
    `name` varchar(31) collate utf8_swedish_ci default NULL,
    `email` varchar(255) collate utf8_swedish_ci default NULL,
    `password` varchar(40) collate utf8_swedish_ci default NULL,
    `token` varchar(40) collate utf8_swedish_ci default NULL,
    `url` varchar(2047) collate utf8_swedish_ci default NULL,
    `privacy` enum('public', 'private') NOT NULL default 'public',
    `view` enum('default', 'incboard') NOT NULL default 'default',
    `current_playlist_id` int(11),
    `current_album_id` int(11),
    PRIMARY KEY(`id`),
    UNIQUE(`email`),
    CONSTRAINT `user_ibfk_1` FOREIGN KEY(`current_playlist_id`) REFERENCES `playlist`(`id`),
    CONSTRAINT `user_ibfk_2` FOREIGN KEY(`current_album_id`) REFERENCES `album`(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `user_created_trigger` BEFORE INSERT ON `user` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;
ALTER TABLE `user` auto_increment = 40000;

CREATE TABLE `track` (
    `id` int(11) NOT NULL auto_increment,
    `title` varchar(63) collate utf8_swedish_ci default NULL,
    `fid` varchar(15) collate utf8_swedish_ci default NULL, -- foreign system id - e.g.: S1xUBlM5erE
    `fcode` varchar(7) collate utf8_swedish_ci default 'y', -- foreign system code - default is youtube (y)
    `cover` varchar(2047) collate utf8_swedish_ci default NULL,
    `duration` int(11) NOT NULL default 0,
    PRIMARY KEY(`id`),
    UNIQUE(`fid`, `fcode`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `track_created_trigger` BEFORE INSERT ON `track` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;
ALTER TABLE `track` auto_increment = 50000;

CREATE TABLE `bond` (
    `id` int(11) NOT NULL auto_increment,
    `name` varchar(63) collate utf8_swedish_ci NOT NULL,
    `priority` int(11) NOT NULL,
    `comment` varchar(127) collate utf8_swedish_ci NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`name`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `bond_created_trigger` BEFORE INSERT ON `bond` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;
ALTER TABLE `bond` auto_increment = 60000;

CREATE TABLE `music_track_link` (
    `id` int(11) NOT NULL auto_increment,
    `artist_music_title_id` int(11) NOT NULL,
    `track_id` int(11) NOT NULL,
    `user_id` int(11),
    `bond_id` int(11) NOT NULL,
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`id`),
    UNIQUE(artist_music_title_id, track_id, user_id),
    CONSTRAINT `music_track_link_ibfk_1` FOREIGN KEY (`artist_music_title_id`) REFERENCES `artist_music_title`(`id`),
    CONSTRAINT `music_track_link_ibfk_2` FOREIGN KEY (`track_id`) REFERENCES `track`(`id`),
    CONSTRAINT `music_track_link_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
    CONSTRAINT `music_track_link_ibfk_4` FOREIGN KEY (`bond_id`) REFERENCES `bond`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `music_track_link_created_trigger` BEFORE INSERT ON `music_track_link` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;
ALTER TABLE `music_track_link` auto_increment = 70000;

CREATE TABLE `playlist` (
    `id` int(11) NOT NULL auto_increment,
    `user_id` int(11) NOT NULL,
    `name` varchar(63) collate utf8_swedish_ci NOT NULL,
    `repeat` int(1) NOT NULL default 0,
    `shuffle` int(1) NOT NULL default 0,
    `current_track` int(11) NOT NULL default 0,
    `privacy` enum('public', 'private') NOT NULL DEFAULT 'public',
    PRIMARY KEY(`id`),
    UNIQUE(`user_id`, `name`),
    CONSTRAINT `playlist_user_id_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `playlist_created_trigger` BEFORE INSERT ON `playlist` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;
ALTER TABLE `playlist` auto_increment = 80000;

ALTER TABLE `user` ADD CONSTRAINT `user_playlist_id_ibfk_1` FOREIGN KEY (`current_playlist_id`) REFERENCES `playlist`(`id`);

CREATE TABLE `playlist_has_track` (
    `id` int(11) NOT NULL auto_increment,
    `playlist_id` int(11) NOT NULL,
    `track_id` int(11) NOT NULL,
    `artist_music_title_id` int(11),
    `sort` int(11) NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`playlist_id`, `sort`),
    CONSTRAINT `playlist_has_track_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `playlist`(`id`),
    CONSTRAINT `playlist_has_track_ibfk_2` FOREIGN KEY (`track_id`) REFERENCES `track`(`id`),
    CONSTRAINT `playlist_has_track_ibfk_3` FOREIGN KEY (`artist_music_title_id`) REFERENCES `artist_music_title`(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `playlist_has_track_created_trigger` BEFORE INSERT ON `playlist_has_track` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;
ALTER TABLE `playlist_has_track` auto_increment = 90000;

CREATE TABLE `user_listen_playlist` (
    `id` int(11) NOT NULL auto_increment,
    `user_id` int(11) NOT NULL,
    `playlist_id` int(11) NOT NULL,
    `sort` int(11) NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`user_id`, `playlist_id`),
    CONSTRAINT `user_listen_playlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
    CONSTRAINT `user_listen_playlist_ibfk_2` FOREIGN KEY (`playlist_id`) REFERENCES `playlist`(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `user_listen_playlist_created_trigger` BEFORE INSERT ON `user_listen_playlist` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;
ALTER TABLE `user_listen_playlist` auto_increment = 100000;

CREATE TABLE `task_type` (
    `id` int(11) NOT NULL auto_increment,
    `name` varchar(31),
    `duration` int(11), -- After the task is done, how many seconds it should wait to accept running the same task again.
    PRIMARY KEY(`id`),
    UNIQUE(`name`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `task_type_created_trigger` BEFORE INSERT ON `task_type` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `task_set` (
    `id` int(11) NOT NULL auto_increment,
    `task_type_id` int(11),
    `done` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `expiration` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY(`id`),
    CONSTRAINT `task_set_ibfk_1` FOREIGN KEY (`task_type_id`) REFERENCES `task_type`(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `task_set_created_trigger` BEFORE INSERT ON `task_set` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `task_request` (
    `id` int(11) NOT NULL auto_increment,
    `task_set_id` int(11) NOT NULL,
    PRIMARY KEY(`id`),
    CONSTRAINT `task_request_ibfk_1` FOREIGN KEY (`task_set_id`) REFERENCES `task_set`(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `task_request_created_trigger` BEFORE INSERT ON `task_request` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `task_parameter` (
    `id` int(11) NOT NULL auto_increment,
    `task_set_id` int(11) NOT NULL,
    `order` int(11) NOT NULL,
    `param` varchar(255) NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`task_set_id`, `order`),
    CONSTRAINT `task_parameter_ibfk_1` FOREIGN KEY (`task_set_id`) REFERENCES `task_set`(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `task_parameter_created_trigger` BEFORE INSERT ON `task_parameter` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `feedback` (
    `id` int(11) NOT NULL auto_increment,
    `user_id` int(11),
    `subject` varchar(255) NOT NULL,
    `comment` text,
    PRIMARY KEY(`id`),
    CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `feedback_created_trigger` BEFORE INSERT ON `feedback` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `tutorial` (
    `id` int(11) NOT NULL auto_increment,
    `name` varchar(31) NOT NULL,
    PRIMARY KEY(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `tutorial_created_trigger` BEFORE INSERT ON `tutorial` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `tutorial_accomplished` (
    `id` int(11) NOT NULL auto_increment,
    `user_id` int(11) NOT NULL,
    `tutorial_id` int(11) NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`user_id`, `tutorial_id`),
    CONSTRAINT `tutorial_accomplished_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
    CONSTRAINT `tutorial_accomplished_ibfk_2` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorial`(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `tutorial_accomplished_created_trigger` BEFORE INSERT ON `tutorial_accomplished` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `album` (
    `id` int(11) NOT NULL auto_increment,
    `name` varchar(255) NOT NULL,
    `cover` varchar(2047) collate utf8_swedish_ci default NULL,
    `artist_id` int(11) NOT NULL, -- In case there is many artist ("Various Artists" from last.fm) then this info is not recorded.
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`id`),
    UNIQUE(`name`, `artist_id`),
    CONSTRAINT `album_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artist`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `album_created_trigger` BEFORE INSERT ON `album` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `album_has_artist_music_title` (
    `id` int(11) NOT NULL auto_increment,
    `album_id` int(11) NOT NULL,
    `artist_music_title_id` int(11) NOT NULL,
    `sort` int(11) NOT NULL,
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`id`),
    UNIQUE(`album_id`, `sort`),
    CONSTRAINT `album_has_artist_music_title_ibfk_1` FOREIGN KEY (`album_id`) REFERENCES `album`(`id`),
    CONSTRAINT `album_has_artist_music_title_ibfk_2` FOREIGN KEY (`artist_music_title_id`) REFERENCES `artist_music_title`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `album_has_artist_music_title_created_trigger` BEFORE INSERT ON `album_has_artist_music_title` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `user_listen_album` (
    `id` int(11) NOT NULL auto_increment,
    `album_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `current_artist_music_title_id` int(11),
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`id`),
    UNIQUE(`album_id`, `user_id`),
    CONSTRAINT `user_listen_album_ibfk_1` FOREIGN KEY (`album_id`) REFERENCES `album`(`id`),
    CONSTRAINT `user_listen_album_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
    CONSTRAINT `user_listen_album_ibfk_3` FOREIGN KEY (`current_artist_music_title_id`) REFERENCES `artist_music_title`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `user_listen_album_created_trigger` BEFORE INSERT ON `user_listen_album` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `artist_full` (
    `id` int(11) NOT NULL auto_increment,
    `name` varchar(63) collate utf8_swedish_ci NOT NULL,
    PRIMARY KEY(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `artist_full_created_trigger` BEFORE INSERT ON `artist_full` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `log_action` (
    `id` int(11) NOT NULL auto_increment,
    `name` varchar(255),
    PRIMARY KEY(`id`),
    UNIQUE(`name`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `log_action_created_trigger` BEFORE INSERT ON `log_action` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `log` (
    `id` int(11) NOT NULL auto_increment,
    `user_id` int(11) NOT NULL,
    `window_id` int(11) NOT NULL,
    `view` enum('default', 'incboard') NOT NULL,
    `log_action_id` int(11) NOT NULL,
    `album_id` int(11),
    `track_id` int(11),
    PRIMARY KEY(`id`),
    CONSTRAINT `log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
    CONSTRAINT `log_ibfk_2` FOREIGN KEY (`log_action_id`) REFERENCES `log_action`(`id`),
    CONSTRAINT `log_ibfk_3` FOREIGN KEY (`album_id`) REFERENCES `album`(`id`),
    CONSTRAINT `log_ibfk_4` FOREIGN KEY (`track_id`) REFERENCES `track`(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `log_created_trigger` BEFORE INSERT ON `log` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;
