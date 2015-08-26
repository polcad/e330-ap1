#DROP DATABASE asterisk;

CREATE DATABASE asterisk_events;

GRANT ALL
  ON asterisk_events.*
  TO 'asteriskuser'@'localhost'
  IDENTIFIED BY 'asterisk';

USE asterisk_events;

DROP TABLE IF EXISTS events;

CREATE TABLE events (
   id int(10) unsigned NOT NULL auto_increment,
   timestamp datetime NOT NULL default '0000-00-00 00:00:00',
   event LONGTEXT ,
   PRIMARY KEY (`id`)
); 
