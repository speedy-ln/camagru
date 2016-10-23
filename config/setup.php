<?php
/**
 * Created by IntelliJ IDEA.
 * User: Lebelo Nkadimeng
 * Date: 2016/10/23
 * Time: 2:10 PM
 */
require_once 'database.php';
$DB_DSN = "mysql:host=127.0.0.1;dbname=mysql";
try {
    $conn = new PDO($DB_DSN, $DB_USER, $DB_PASS);
    $DB_DSN = "mysql:host=127.0.0.1;dbname=$DB_NAME";
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "DROP DATABASE IF EXISTS $DB_NAME;
    CREATE DATABASE $DB_NAME;";
    $conn->exec($sql);
    $sql = "USE $DB_NAME";
    $conn->exec($sql);
    $sql = 'SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `coment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment` varchar(255) NOT NULL,
  `upload_id` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`coment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `has_liked`;
CREATE TABLE `has_liked` (
  `user_upload_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `upload_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_upload_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `img_lib`;
CREATE TABLE `img_lib` (
  `img_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `likes`;
CREATE TABLE `likes` (
  `like_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `likes` int(10) unsigned NOT NULL DEFAULT \'1\',
  `upload_id` int(11) NOT NULL,
  PRIMARY KEY (`like_id`),
  UNIQUE KEY `upload_lieks` (`upload_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `uploads`;
CREATE TABLE `uploads` (
  `upload_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `visible` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`upload_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `upload_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_p` varchar(255) DEFAULT NULL,
  `confirm_email` varchar(255) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `mail` (`email`) USING BTREE,
  UNIQUE KEY `confirm_email` (`confirm_email`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

insert into img_lib (file_name) VALUES (\'http://localhost:8080/camagru/style/images/Autumn-Leaves-2.png\');
insert into img_lib (file_name) VALUES (\'http://localhost:8080/camagru/style/images/Beard-12.png\');
insert into img_lib (file_name) VALUES (\'http://localhost:8080/camagru/style/images/Woman-Lips-24.png\');
insert into img_lib (file_name) VALUES (\'http://localhost:8080/camagru/style/images/Beard-14.png\');
insert into img_lib (file_name) VALUES (\'http://localhost:8080/camagru/style/images/Beard-17.png\');
insert into img_lib (file_name) VALUES (\'http://localhost:8080/camagru/style/images/Cigarette-13.png\');
insert into img_lib (file_name) VALUES (\'http://localhost:8080/camagru/style/images/Dragon-13.png\');
insert into img_lib (file_name) VALUES (\'http://localhost:8080/camagru/style/images/Dragon-14.png\');
insert into img_lib (file_name) VALUES (\'http://localhost:8080/camagru/style/images/Eye-2.png\');
insert into img_lib (file_name) VALUES (\'http://localhost:8080/camagru/style/images/Glasses-52.png\');
insert into img_lib (file_name) VALUES (\'http://localhost:8080/camagru/style/images/Woman-Lips-16.png\');
insert into img_lib (file_name) VALUES (\'http://localhost:8080/camagru/style/images/Woman-Lips-21.png\');

SET FOREIGN_KEY_CHECKS = 1;';
    $conn->exec($sql);
}
catch (Exception $e)
{
    $conn = null;
    var_dump($e);
}
$conn = null;