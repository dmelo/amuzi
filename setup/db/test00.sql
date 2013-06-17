INSERT INTO `user`(`name`, `email`, `password`) VALUES('Test 00 User 00', 'test00user00@gmail.com', md5('123456'));
INSERT INTO `playlist`(`user_id`, `name`) VALUES(last_insert_id(), 'Test 00 User 00');


