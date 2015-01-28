-- 1. This table uses InnoDB that is much better for writing operations like logging.
-- 2. It has microsecond resolutions for timers.
-- 3. It has more fields that are used in recent Asterisk versions.
-- 4. It has indexes that make search and select operations faster

#DROP DATABASE asterisk_cdr;

CREATE DATABASE asterisk_cdr;

GRANT ALL
  ON asterisk_cdr.*
  TO 'asteriskuser'@'localhost'
  IDENTIFIED BY 'asterisk';

USE asterisk_cdr;

DROP TABLE IF EXISTS `cdr`;

CREATE TABLE `cdr` (
   `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
   `calldate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
   `clid` VARCHAR(80) NOT NULL DEFAULT '',
   `src` VARCHAR(80) NOT NULL DEFAULT '',
   `dst` VARCHAR(80) NOT NULL DEFAULT '',
   `dcontext` VARCHAR(80) NOT NULL DEFAULT '',
   `lastapp` VARCHAR(200) NOT NULL DEFAULT '',
   `lastdata` VARCHAR(200) NOT NULL DEFAULT '',
   `duration` FLOAT UNSIGNED NULL DEFAULT NULL,
   `billsec` FLOAT UNSIGNED NULL DEFAULT NULL,
   `disposition` ENUM('ANSWERED','BUSY','FAILED','NO ANSWER','CONGESTION') NULL DEFAULT NULL,
   `channel` VARCHAR(50) NULL DEFAULT NULL,
   `dstchannel` VARCHAR(50) NULL DEFAULT NULL,
   `amaflags` VARCHAR(50) NULL DEFAULT NULL,
   `accountcode` VARCHAR(20) NULL DEFAULT NULL,
   `uniqueid` VARCHAR(32) NOT NULL DEFAULT '',
   `userfield` FLOAT UNSIGNED NULL DEFAULT NULL,
   `answer` DATETIME NOT NULL,
   `end` DATETIME NOT NULL,
   PRIMARY KEY (`id`),
   INDEX `calldate` (`calldate`),
   INDEX `dst` (`dst`),
   INDEX `src` (`src`),
   INDEX `dcontext` (`dcontext`),
   INDEX `clid` (`clid`)
)
COLLATE=`utf8_bin`
ENGINE=InnoDB;
