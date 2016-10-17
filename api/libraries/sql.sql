CREATE TABLE `camagru`.`users` (
	`user_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
	`first_name` varchar(255) NOT NULL,
	`last_name` varchar(255) NOT NULL,
	`email` varchar(255) NOT NULL,
	`password` varchar(255) NOT NULL,
	`reset_p` varchar(255),
	PRIMARY KEY (`user_id`)
) COMMENT='';

ALTER TABLE `camagru`.`users` ADD UNIQUE `mail` USING BTREE (`email`) comment '';

