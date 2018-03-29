# Dump of table category
# ------------------------------------------------------------

CREATE TABLE `category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `collectionId` int(11) DEFAULT NULL,
  `icon` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Dump of table collection
# ------------------------------------------------------------

CREATE TABLE `collection` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `member` text,
  `public` int(11) DEFAULT '0',
  `updated` int(11) DEFAULT '0',
  `created` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Dump of table document
# ------------------------------------------------------------

CREATE TABLE `document` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL DEFAULT '0',
  `categoryId` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `request` text,
  `requestContentType` tinyint(1) NOT NULL DEFAULT '1',
  `requestUrl` varchar(100) DEFAULT NULL,
  `requestMethod` varchar(40) NOT NULL DEFAULT 'GET',
  `requestBody` text,
  `requestHeader` text,
  `responseHttpCodeStatus` int(4) NOT NULL DEFAULT '200',
  `response` text,
  `responseType` varchar(4) DEFAULT 'json',
  `updateTime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Dump of table user
# ------------------------------------------------------------

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(40) NOT NULL DEFAULT '',
  `isSuper` tinyint(1) NOT NULL DEFAULT '0',
  `password` varchar(40) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
