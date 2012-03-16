delimiter $$
CREATE TRIGGER `playlist_name_check` BEFORE INSERT ON `playlist`
FOR EACH ROW 
BEGIN
    IF (substring(upper(new.name), 1, 1) < 'A' OR substring(upper(new.name), 1, 1) > 'Z') THEN
        SET new.name = null;
    END IF;
END;$$
delimiter ;
