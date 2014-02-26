/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DROP SCHEMA IF EXISTS therminfo;
CREATE SCHEMA IF NOT EXISTS therminfo DEFAULT CHARACTER SET utf8;
USE therminfo;

DROP TABLE IF EXISTS characteristic;
CREATE TABLE IF NOT EXISTS characteristic (
  cid int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (cid)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS class;
CREATE TABLE IF NOT EXISTS class (
  cid int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (cid)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS contador;
CREATE TABLE IF NOT EXISTS contador (
  contid int(11) NOT NULL AUTO_INCREMENT,
  `day` varchar(2) NOT NULL,
  `month` varchar(2) NOT NULL,
  `year` varchar(4) NOT NULL,
  `hour` varchar(2) DEFAULT NULL,
  `minute` varchar(2) DEFAULT NULL,
  `second` varchar(2) DEFAULT NULL,
  ip varchar(30) NOT NULL,
  method int(2) DEFAULT NULL,
  country varchar(255) DEFAULT NULL,
  city varchar(255) DEFAULT NULL,
  PRIMARY KEY (contid)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `data`;
CREATE TABLE IF NOT EXISTS `data` (
  idmol int(11) NOT NULL DEFAULT '0',
  crys float(9,3) DEFAULT NULL,
  cerror float(9,3) DEFAULT NULL,
  liq float(9,3) DEFAULT NULL,
  lerror float(9,3) DEFAULT NULL,
  gas float(9,3) DEFAULT NULL,
  gerror float(9,3) DEFAULT NULL,
  phasecl float(9,3) DEFAULT NULL,
  pclerror float(9,3) DEFAULT NULL,
  phaselg float(9,3) DEFAULT NULL,
  plgerror float(9,3) DEFAULT NULL,
  phasecg float(9,3) DEFAULT NULL,
  pcgerror float(9,3) DEFAULT NULL,
  obs varchar(255) DEFAULT NULL,
  PRIMARY KEY (idmol)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS data_ref;
CREATE TABLE IF NOT EXISTS data_ref (
  idmol int(11) NOT NULL DEFAULT '0',
  refid int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idmol,refid),
  KEY idmol (idmol),
  KEY refid (refid)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS dbevolution;
CREATE TABLE IF NOT EXISTS dbevolution (
  eid int(11) NOT NULL AUTO_INCREMENT,
  `month` varchar(2) NOT NULL,
  `year` varchar(4) NOT NULL,
  nrcompounds varchar(30) NOT NULL,
  nrcompusers int(10) DEFAULT NULL,
  PRIMARY KEY (eid)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS family;
CREATE TABLE IF NOT EXISTS family (
  fid int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (fid)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS image;
CREATE TABLE IF NOT EXISTS image (
  iid int(11) NOT NULL AUTO_INCREMENT,
  image longblob,
  PRIMARY KEY (iid)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS molecule;
CREATE TABLE IF NOT EXISTS molecule (
  mid int(11) NOT NULL AUTO_INCREMENT,
  mol_id varchar(255) DEFAULT NULL,
  casrn varchar(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  formula varchar(255) DEFAULT NULL,
  mw decimal(7,2) DEFAULT NULL,
  state char(1) DEFAULT NULL,
  smile text,
  usmile text,
  family int(11) DEFAULT NULL,
  class int(11) DEFAULT NULL,
  PRIMARY KEY (mid),
  UNIQUE KEY casrn (casrn),
  UNIQUE KEY mol_id (mol_id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS mol_char;
CREATE TABLE IF NOT EXISTS mol_char (
  molecule int(11) NOT NULL DEFAULT '0',
  charact int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (molecule,charact),
  KEY molecule (molecule),
  KEY charact (charact)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS mol_image;
CREATE TABLE IF NOT EXISTS mol_image (
  molecule int(11) NOT NULL DEFAULT '0',
  img int(11) DEFAULT NULL,
  PRIMARY KEY (molecule)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS mol_user;
CREATE TABLE IF NOT EXISTS mol_user (
  molecule int(11) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (molecule,`user`),
  KEY molecule (molecule),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS othername;
CREATE TABLE IF NOT EXISTS othername (
  oid int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  mid int(11) DEFAULT NULL,
  PRIMARY KEY (oid),
  KEY oid (oid),
  KEY mid (mid),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS reference;
CREATE TABLE IF NOT EXISTS reference (
  refid int(11) NOT NULL AUTO_INCREMENT,
  reference_code varchar(255) DEFAULT NULL,
  author varchar(255) DEFAULT NULL,
  journal varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  volume varchar(255) DEFAULT NULL,
  bpage int(11) DEFAULT NULL,
  epage int(11) DEFAULT NULL,
  PRIMARY KEY (refid)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS subclass;
CREATE TABLE IF NOT EXISTS subclass (
  scid int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  class int(11) DEFAULT NULL,
  PRIMARY KEY (scid)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  uid int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  email varchar(255) DEFAULT NULL,
  institution varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (uid),
  UNIQUE KEY email (email)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
