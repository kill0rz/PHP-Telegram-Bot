-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `tb_kennzeichen`;
CREATE TABLE `tb_kennzeichen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(20) CHARACTER SET utf8 COLLATE utf8_german2_ci NOT NULL,
  `kennzeichen` varchar(500) NOT NULL,
  `notified` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kennzeichen` (`kennzeichen`),
  KEY `id` (`id`),
  KEY `userid` (`userid`),
  CONSTRAINT `tb_kennzeichen_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `tb_lastseen_users` (`userid`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tb_lastseen_users`;
CREATE TABLE `tb_lastseen_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_german2_ci NOT NULL,
  `atname` varchar(50) CHARACTER SET utf8 COLLATE utf8_german2_ci DEFAULT NULL,
  `time` varchar(50) CHARACTER SET utf8 COLLATE utf8_german2_ci NOT NULL,
  `userid` varchar(20) CHARACTER SET utf8 COLLATE utf8_german2_ci NOT NULL,
  `wbb_userid` int(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`),
  UNIQUE KEY `wbb_userid` (`wbb_userid`),
  KEY `userid_index` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tb_pictures_queue`;
CREATE TABLE `tb_pictures_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(500) NOT NULL,
  `location` varchar(500) NOT NULL,
  `threadname` varchar(500) DEFAULT NULL,
  `current` tinyint(1) NOT NULL DEFAULT '0',
  `telegramfileid` varchar(500) NOT NULL,
  `postedby` varchar(500) NOT NULL,
  `postedat` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tb_set_topic`;
CREATE TABLE `tb_set_topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topicname` varchar(999) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tb_todolist`;
CREATE TABLE `tb_todolist` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(500) COLLATE utf8_german2_ci NOT NULL,
  `isactive` smallint(1) NOT NULL DEFAULT '1',
  `isintodo` smallint(1) NOT NULL DEFAULT '0',
  `wishby` varchar(20) COLLATE utf8_german2_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `wishby` (`wishby`),
  CONSTRAINT `tb_todolist_ibfk_1` FOREIGN KEY (`wishby`) REFERENCES `tb_lastseen_users` (`userid`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;


DROP TABLE IF EXISTS `tb_votes`;
CREATE TABLE `tb_votes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `vote_option_id` int(11) NOT NULL,
  `telegram_id` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `telegram_id` (`telegram_id`),
  KEY `vote_option_id` (`vote_option_id`),
  CONSTRAINT `tb_votes_ibfk_2` FOREIGN KEY (`vote_option_id`) REFERENCES `tb_vote_options` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tb_vote_options`;
CREATE TABLE `tb_vote_options` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `vote_option` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tb_word_stats`;
CREATE TABLE `tb_word_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(500) NOT NULL,
  `count` bigint(20) NOT NULL DEFAULT '1',
  `firstusedby` varchar(500) NOT NULL,
  `firstusedat` varchar(500) NOT NULL,
  `lastusedby` varchar(500) NOT NULL,
  `lastusedat` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `word` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2017-06-15 08:22:18