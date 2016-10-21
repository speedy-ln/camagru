create DATABASE camagru;

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

CREATE TABLE camagru.uploads
(
    upload_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    file_name VARCHAR(255),
    visible BIT DEFAULT 0 NOT NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT uploads_users_user_id_fk FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
);