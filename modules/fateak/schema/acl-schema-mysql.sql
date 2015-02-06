--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `rid` int(11) NOT NULL,
  `permission` varchar(64) NOT NULL,
  `module` varchar(255) NOT NULL,
  PRIMARY KEY (`rid`,`permission`),
  KEY `permission` (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
