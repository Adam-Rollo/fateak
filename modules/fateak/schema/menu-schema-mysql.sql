DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(128) NOT NULL,
  name varchar(128) NOT NULL,
  descp varchar(255) DEFAULT NULL,
  image varchar(255) DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  params text,
  active tinyint(3) NOT NULL DEFAULT '1',
  pid int(11) unsigned NOT NULL DEFAULT '0',
  lft int(10) unsigned DEFAULT NULL,
  rgt int(10) unsigned DEFAULT NULL,
  lvl int(10) unsigned DEFAULT NULL,
  scp int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
