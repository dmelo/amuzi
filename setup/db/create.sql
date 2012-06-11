CREATE TABLE `artist` (
    `id` int(11) NOT NULL auto_increment,
    `name` varchar(63) collate utf8_swedish_ci NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`name`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE now()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `artist_created_trigger` BEFORE INSERT ON `artist` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `music_title` (
    `id` int(11) NOT NULL auto_increment,
    `name` varchar(63) collate utf8_swedish_ci NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`name`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE now()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `music_title_created_trigger` BEFORE INSERT ON `music_title` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `artist_music_title` (
    `id` int(11) NOT NULL auto_increment,
    `artist_id` int(11) NOT NULL,
    `music_title_id` int(11) NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`artist_id`, `music_title_id`),
    CONSTRAINT `artist_music_title_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artist`(`id`),
    CONSTRAINT `artist_music_title_ibfk_2` FOREIGN KEY (`music_title_id`) REFERENCES `music_title`(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE now()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `artist_music_title_created_trigger` BEFORE INSERT ON `artist_music_title` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `user` (
    `id` int(11) NOT NULL auto_increment,
    `facebook_id` varchar(63) collate utf8_swedish_ci default NULL,
    `name` varchar(31) collate utf8_swedish_ci default NULL,
    `email` varchar(255) collate utf8_swedish_ci default NULL,
    `password` varchar(40) collate utf8_swedish_ci default NULL,
    `token` varchar(40) collate utf8_swedish_ci default NULL,
    `url` varchar(2047) collate utf8_swedish_ci default NULL,
    `privacy` enum('public', 'private') NOT NULL default 'public',
    `current_playlist_id` int(11),
    PRIMARY KEY(`id`),
    UNIQUE(`email`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE now()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `user_created_trigger` BEFORE INSERT ON `user` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `track` (
    `id` int(11) NOT NULL auto_increment,
    `title` varchar(63) collate utf8_swedish_ci default NULL,
    `url` varchar(2047) collate utf8_swedish_ci default NULL, -- Must be checked on PHP that url is unique.
    `cover` varchar(2047) collate utf8_swedish_ci default NULL,
    `duration` int(11) NOT NULL default 0,
    PRIMARY KEY(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE now()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `track_created_trigger` BEFORE INSERT ON `track` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

CREATE TABLE `music_track_vote` (
    `id` int(11) NOT NULL auto_increment,
    `artist_music_title_id` int(11) NOT NULL,
    `track_id` int(11) NOT NULL,
    `vote` int(11) NOT NULL,
    PRIMARY KEY(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE now()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `music_track_vote_created_trigger` BEFORE INSERT ON `music_track_vote` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

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
    `last_updated` TIMESTAMP ON UPDATE now()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `playlist_created_trigger` BEFORE INSERT ON `playlist` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

ALTER TABLE `user` ADD CONSTRAINT `user_playlist_id_ibfk_1` FOREIGN KEY (`current_playlist_id`) REFERENCES `playlist`(`id`);

CREATE TABLE `playlist_has_track` (
    `id` int(11) NOT NULL auto_increment,
    `playlist_id` int(11) NOT NULL,
    `track_id` int(11) NOT NULL,
    `sort` int(11) NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`playlist_id`, `sort`),
    CONSTRAINT `has_playlist_id_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `playlist`(`id`),
    CONSTRAINT `has_track_id_ibfk_1` FOREIGN KEY (`track_id`) REFERENCES `track`(`id`),
    `created` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    `last_updated` TIMESTAMP ON UPDATE now()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `playlist_has_track_created_trigger` BEFORE INSERT ON `playlist_has_track` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;

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
    `last_updated` TIMESTAMP ON UPDATE now()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
CREATE TRIGGER `user_listen_playlist_created_trigger` BEFORE INSERT ON `user_listen_playlist` FOR EACH ROW SET NEW.created = CURRENT_TIMESTAMP;
