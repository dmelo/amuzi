CREATE TABLE `user` (
    `id` int(11) NOT NULL auto_increment,
    `facebook_id` varchar(63) collate utf8_swedish_ci default NULL,
    `name` varchar(31) collate utf8_swedish_ci default NULL,
    `email` varchar(2047) collate utf8_swedish_ci default NULL,
    `url` varchar(2047) collate utf8_swedish_ci default NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`facebookId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `track` (
    `id` int(11) NOT NULL auto_increment,
    `title` varchar(63) collate utf8_swedish_ci default NULL,
    `url` varchar(2047) collate utf8_swedish_ci default NULL, -- Must be cheched on PHP that url is unique.
    `cover` varchar(2047) collate utf8_swedish_ci default NULL,
    `duration` int(11) NOT NULL default 0,
    PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `playlist` (
    `id` int(11) NOT NULL auto_increment,
    `user_id` int(11) NOT NULL,
    `name` varchar(63) collate utf8_swedish_ci default NULL,
    `repeat` int(1) NOT NULL default 0,
    `shuffle` int(1) NOT NULL default 0,
    PRIMARY KEY(`id`),
    UNIQUE(`user_id`, `name`),
    CONSTRAINT `playlist_user_id_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `playlist_has_track` (
    `id` int(11) NOT NULL auto_increment,
    `playlist_id` int(11) NOT NULL,
    `track_id` int(11) NOT NULL,
    `sort` int(11) NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`playlist_id`, `sort`),
    CONSTRAINT `has_playlist_id_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `playlist`(`id`),
    CONSTRAINT `has_track_id_ibfk_1` FOREIGN KEY (`track_id`) REFERENCES `track`(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `user_listen_playlist` (
    `id` int(11) NOT NULL auto_increment,
    `user_id` int(11) NOT NULL,
    `playlist_id` int(11) NOT NULL,
    `sort` int(11) NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE(`user_id`, `playlist_id`),
    CONSTRAINT `user_listen_playlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
    CONSTRAINT `user_listen_playlist_ibfk_2` FOREIGN KEY (`playlist_id`) REFERENCES `playlist`(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
