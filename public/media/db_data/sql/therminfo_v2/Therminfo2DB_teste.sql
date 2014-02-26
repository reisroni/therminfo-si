--
-- Database: therminfo2_teste
--

DROP SCHEMA IF EXISTS therminfo2_teste;
CREATE SCHEMA IF NOT EXISTS therminfo2_teste DEFAULT CHARACTER SET utf8;
USE therminfo2_teste;

-- -----------------------------------------------------
-- (1) Table therminfo2_teste.family
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS family (
  fid INT UNSIGNED NOT NULL AUTO_INCREMENT,
  f_name VARCHAR(100) NOT NULL,
  PRIMARY KEY (fid) )
ENGINE = InnoDB;

DELETE FROM family;
ALTER TABLE family AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (2) Table therminfo2_teste.class
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS class (
  cid INT UNSIGNED NOT NULL AUTO_INCREMENT,
  c_name VARCHAR(100) NOT NULL,
  PRIMARY KEY (cid) )
ENGINE = InnoDB;

DELETE FROM class;
ALTER TABLE class AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (3) Table therminfo2_teste.subclass
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS subclass (
  scid INT UNSIGNED NOT NULL AUTO_INCREMENT,
  sc_name VARCHAR(100) NOT NULL,
  PRIMARY KEY (scid) )
ENGINE = InnoDB;

DELETE FROM subclass;
ALTER TABLE subclass AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (4) Table therminfo2_teste.characteristic
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS characteristic (
  cid INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ch_name VARCHAR(100) NOT NULL,
  PRIMARY KEY (cid) )
ENGINE = InnoDB;

DELETE FROM characteristic;
ALTER TABLE characteristic AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (5) Table therminfo2_teste.molecule_type
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS molecule_type (
  mtid INT UNSIGNED NOT NULL AUTO_INCREMENT,
  mt_name VARCHAR(60) NOT NULL,
  PRIMARY KEY (mtid) )
ENGINE = InnoDB;

DELETE FROM molecule_type;
ALTER TABLE molecule_type AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (6) Table therminfo2_teste.molecule
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS molecule (
  mid BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  therminfo_id VARCHAR(45) NOT NULL,
  casrn VARCHAR(20) NULL,
  name VARCHAR(255) NULL,
  formula VARCHAR(255) NULL,
  mw FLOAT(7,3) NULL,
  state CHAR(1) NULL,
  phi_form VARCHAR(150) NULL,
  smiles TEXT NULL,
  usmiles TEXT NULL,
  inchi TEXT NULL,
  inchikey CHAR(27) NULL,
  s_inchi TEXT NULL,
  s_inchikey CHAR(27) NULL,
  mol_file LONGTEXT NULL,
  family INT UNSIGNED NULL,
  class INT UNSIGNED NULL,
  subclass INT UNSIGNED NULL,
  mol_type INT UNSIGNED NULL,
  img_path VARCHAR(200) NULL,
  validated INT(1) UNSIGNED NOT NULL DEFAULT 0,
  outdated INT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (mid),
  CONSTRAINT fk_molecule_family
    FOREIGN KEY (family)
    REFERENCES family (fid),
  CONSTRAINT fk_molecule_class
    FOREIGN KEY (class)
    REFERENCES class (cid),
  CONSTRAINT fk_molecule_subclass
    FOREIGN KEY (subclass)
    REFERENCES subclass (scid),
  CONSTRAINT fk_molecule_type
    FOREIGN KEY (mol_type)
    REFERENCES molecule_type (mtid) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX ix_therminfoid ON molecule (therminfo_id ASC);
CREATE UNIQUE INDEX ix_casrn ON molecule (casrn ASC);
CREATE INDEX ix_inchikey ON molecule (inchikey ASC);
CREATE INDEX ix_sinchikey ON molecule (s_inchikey ASC);
CREATE INDEX ix_molecule_family ON molecule (family ASC);
CREATE INDEX ix_molecule_class ON molecule (class ASC);
CREATE INDEX ix_molecule_subclass ON molecule (subclass ASC);
CREATE INDEX ix_molecule_type ON molecule (mol_type ASC);

DELETE FROM molecule;
ALTER TABLE molecule AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (7) Table therminfo2_teste.mol_char
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS mol_char (
  molecule BIGINT UNSIGNED NOT NULL,
  charact INT UNSIGNED NOT NULL,
  PRIMARY KEY (molecule, charact),
  CONSTRAINT fk_molchar_molecule
    FOREIGN KEY (molecule)
    REFERENCES molecule (mid)
	ON DELETE CASCADE,
  CONSTRAINT fk_molchar_char
    FOREIGN KEY (charact)
    REFERENCES characteristic (cid) 
	ON DELETE CASCADE )
ENGINE = InnoDB;

CREATE INDEX ix_molchar_molecule ON mol_char (molecule ASC);
CREATE INDEX ix_molchar_char ON mol_char (charact ASC);

DELETE FROM mol_char;

-- -----------------------------------------------------
-- (8) Table therminfo2_teste.othername
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS othername (
  oid BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  synonym VARCHAR(255) NOT NULL,
  molecule BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (oid),
  CONSTRAINT fk_othername_molecule
    FOREIGN KEY (molecule)
    REFERENCES molecule (mid) 
	ON DELETE CASCADE )
ENGINE = InnoDB;

CREATE INDEX ix_othername_id ON othername (oid ASC);
CREATE INDEX ix_othername_synonym ON othername (synonym ASC);
CREATE INDEX ix_othername_molecule ON othername (molecule ASC);

DELETE FROM othername;
ALTER TABLE othername AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (9) Table therminfo2_teste.other_db_name
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS other_db_name (
  odbn_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  db_name VARCHAR(40) NOT NULL,
  PRIMARY KEY (odbn_id) )
ENGINE = InnoDB;

DELETE FROM other_db_name;
ALTER TABLE other_db_name AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (10) Table therminfo2_teste.other_db
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS other_db (
  odb_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  molecule BIGINT UNSIGNED NOT NULL,
  db INT UNSIGNED NOT NULL,
  value VARCHAR(200) NULL,
  PRIMARY KEY (odb_id),
  CONSTRAINT fk_otherdb_molecule
    FOREIGN KEY (molecule)
    REFERENCES molecule (mid)
    ON DELETE CASCADE,
  CONSTRAINT fk_otherdb_name
    FOREIGN KEY (db)
    REFERENCES other_db_name (odbn_id)
    ON DELETE CASCADE )
ENGINE = InnoDB;

CREATE INDEX ix_otherdb_molecule ON other_db (molecule ASC);
CREATE INDEX ix_otherdb_name ON other_db (db ASC);

DELETE FROM other_db;
ALTER TABLE other_db AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (11) Table therminfo2_teste.data_type
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS data_type (
  dtid INT UNSIGNED NOT NULL AUTO_INCREMENT,
  t_name VARCHAR(50) NOT NULL,
  PRIMARY KEY (dtid) )
ENGINE = InnoDB;

DELETE FROM data_type;
ALTER TABLE data_type AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (12) Table therminfo2_teste.data
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS data (
  did INT UNSIGNED NOT NULL AUTO_INCREMENT,
  d_name VARCHAR(100) NOT NULL,
  d_full_name VARCHAR(200) NULL,
  type INT UNSIGNED NOT NULL,
  units VARCHAR(45) NULL,
  is_numeric INT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (did),
  CONSTRAINT fk_data_datatype
    FOREIGN KEY (type)
    REFERENCES data_type (dtid) )
ENGINE = InnoDB;

CREATE INDEX ix_data_datatype ON data (type ASC);

DELETE FROM data;
ALTER TABLE data AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (13) Table therminfo2_teste.reference
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS reference (
  refid BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  reference_code VARCHAR(150) NOT NULL,
  ref_type VARCHAR(45) NOT NULL,
  title VARCHAR(100) NULL,
  chapter VARCHAR(100) NULL,
  journal VARCHAR(200) NULL,
  book VARCHAR(200) NULL,
  year YEAR NOT NULL,
  volume VARCHAR(45) NULL,
  issue VARCHAR(100) NULL,
  bpage VARCHAR(15) NULL,
  epage VARCHAR(15) NULL,
  editor VARCHAR(100) NULL,
  publisher VARCHAR(100) NULL,
  ref_all TEXT NULL,
  doi VARCHAR(50) NULL,
  PRIMARY KEY (refid) )
ENGINE = InnoDB;

DELETE FROM reference;
ALTER TABLE reference AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (14) Table therminfo2_teste.author
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS author (
  athid BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  a_first_name VARCHAR(50) NOT NULL,
  a_last_name VARCHAR(50) NOT NULL,
  PRIMARY KEY (athid) )
ENGINE = InnoDB;

DELETE FROM author;
ALTER TABLE author AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (15) Table therminfo2_teste.author_ref
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS author_ref (
  reference BIGINT UNSIGNED NOT NULL,
  author BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (reference, author),
  CONSTRAINT fk_authorref_reference
    FOREIGN KEY (reference)
    REFERENCES reference (refid)
	ON DELETE CASCADE,
  CONSTRAINT fk_authorref_author
    FOREIGN KEY (author)
    REFERENCES author (athid) 
	ON DELETE CASCADE )
ENGINE = InnoDB;

CREATE INDEX ix_authorref_reference ON author_ref (reference ASC);
CREATE INDEX ix_authorref_author ON author_ref (author ASC);

DELETE FROM author_ref;

-- -----------------------------------------------------
-- (16) Table therminfo2_teste.molecule_data_ref
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS molecule_data_ref (
  value_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  molecule BIGINT UNSIGNED NOT NULL,
  data INT UNSIGNED NOT NULL,
  reference BIGINT UNSIGNED NOT NULL,
  value VARCHAR(50) NULL,
  error VARCHAR(10) NULL,
  obs TEXT NULL,
  advised ENUM('yes', 'no') NOT NULL DEFAULT 'no',
  validated INT(1) UNSIGNED NOT NULL DEFAULT 0,
  outdated INT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (value_id),
  CONSTRAINT fk_moldataref_molecule
    FOREIGN KEY (molecule)
    REFERENCES molecule (mid)
	ON DELETE CASCADE,
  CONSTRAINT fk_moldataref_data
    FOREIGN KEY (data)
    REFERENCES data (did)
	ON DELETE CASCADE,
  CONSTRAINT fk_moldataref_reference
    FOREIGN KEY (reference)
    REFERENCES reference (refid) 
	ON DELETE CASCADE )
ENGINE = InnoDB;

CREATE INDEX ix_moldataref_molecule ON molecule_data_ref (molecule ASC);
CREATE INDEX ix_moldataref_data ON molecule_data_ref (data ASC);
CREATE INDEX ix_moldataref_reference ON molecule_data_ref (reference ASC);

DELETE FROM molecule_data_ref;

SHOW TABLES;

-- ---------------------------------------------------------

--
-- Data: therminfo2_teste
--

--
-- (1) Family
--
INSERT INTO family (fid, f_name) VALUES
(1, 'Alkanes'),
(2, 'Alkenes'),
(3, 'Allenes'),
(4, 'Alkadienes'),
(5, 'Alkatrienes'),
(6, 'Alkynes'),
(7, 'Alkadiynes'),
(8, 'Alkenynes'),
(9, 'Alcohols (primary)'),
(10, 'Alcohols (tertiary)'),
(11, 'Alcohols (secondary)'),
(12, 'Diols'),
(13, 'Triols'),
(14, 'Alcohols (polyhydric)'),
(15, 'Ethers'),
(16, 'Dialkoxyalkanes'),
(17, 'Trialkoxyalkanes'),
(18, 'Tetraalkoxyalkanes'),
(19, 'Polyoxaalkanes'),
(20, 'Alkoxyalkenes'),
(21, 'Oxaalkenes'),
(22, 'Oxaalkanols'),
(23, 'Polyoxaalkanols'),
(24, 'Alkyl Hydroperoxides'),
(25, 'Dialkyl Peroxides'),
(26, 'Dihydroxyalkyl Peroxides'),
(27, 'Alkyl Hydroxyalkyl Peroxides'),
(28, 'Alkyl Hydroxyoxaalkyl Peroxides'),
(29, 'Aldehydes'),
(30, 'Ketones'),
(31, 'Diones'),
(32, 'Ketenes'),
(33, 'Monocarboxylic Acids'),
(34, 'Dicarboxylic Acids'),
(35, 'Hydroxycarboxylic Acids'),
(36, 'Hydroxydicarboxylic Acids'),
(37, 'Hydroxytricarboxylic Acids'),
(38, 'Tricarboxylic Acids'),
(39, 'Anhydrides'),
(40, 'Alkanoic Acid Esters'),
(41, 'Alkanoic Acid Esters (other)'),
(42, 'Alkenoic Acid Esters'),
(43, 'Alkynoic Acid Esters'),
(44, 'Peroxoic Acids'),
(45, 'Peroxoic Acid Esters'),
(46, 'Dialkanoyl Peroxides'),
(47, 'Amines (primary)'),
(48, 'Amines (secondary)'),
(49, 'Amines (tertiary)'),
(50, 'Diamines'),
(51, 'Amines (other)'),
(52, 'Hydroxyalkylamines'),
(53, 'Dialkoxyamines'),
(54, 'Amides'),
(55, 'Amides (other)'),
(56, 'Diamides'),
(57, 'Urea and Derivatives'),
(58, 'Aminoacids'),
(59, 'Aminoacids (other)'),
(60, 'Ammonium Salts'),
(61, 'Carbamic Acid Esters'),
(62, 'Hydrazines'),
(63, 'Aldimines'),
(64, 'Imines'),
(65, 'Diazenes'),
(66, 'Dioximes'),
(67, 'Tetrazenes'),
(68, 'Isonitriles'),
(69, 'Nitriles'),
(70, 'Dinitriles'),
(71, 'Polycarbonitriles'),
(72, 'Cyanamides'),
(73, 'Cyanohydrazines'),
(74, 'N-Oxides'),
(75, 'Nitroalkanes'),
(76, 'Nitroamines'),
(77, 'Dinitroalkanes'),
(78, 'Polynitroalkanes'),
(79, 'Nitroalkanols'),
(80, 'Nitroalkanoic Acid Esters'),
(81, 'Polynitroamines'),
(82, 'Polynitrooxaamines'),
(83, 'Oximes'),
(84, 'Nitroimines'),
(85, 'Alkyl Nitrates'),
(86, 'Alkoxyl Nitrates'),
(87, 'Thiols'),
(88, 'Dithiols'),
(89, 'Thioic Acids'),
(90, 'Dithioic Acids'),
(91, 'Thioalkanoic Acids'),
(92, 'Thiocyanates'),
(93, 'Thioethers'),
(94, 'Thioic Acid Esters'),
(95, 'Sulphur Compounds (other)'),
(96, 'Disulphides'),
(97, 'Thiocarbonyls'),
(98, 'Thioamides'),
(99, 'Trithioic Acids'),
(100, 'Thiocarbamic Acids'),
(101, 'Thiocarbamic Acid Esters'),
(102, 'Sulphoxides'),
(103, 'Sulphones'),
(104, 'Sulphites'),
(105, 'Sulphates'),
(106, 'Monofluoroalkanes'),
(107, 'Monochloroalkanes'),
(108, 'Monobromoalkanes'),
(109, 'Monoiodoalkanes'),
(110, 'Dihaloalkanes'),
(111, 'Trihaloalkanes'),
(112, 'Perhaloalkanes'),
(113, 'Polyhaloalkanes'),
(114, 'Haloalkanes (mixed)'),
(115, 'Monohaloalkenes'),
(116, 'Dihaloalkenes'),
(117, 'Trihaloalkenes'),
(118, 'Tetrahaloalkenes'),
(119, 'Perfluoroalkenes'),
(120, 'Haloalcohols'),
(121, 'Haloethers'),
(122, 'Halocarbonyls'),
(123, 'Haloalkanoic Acids'),
(124, 'Chloroalkanoic Acid Esters'),
(125, 'Fluoroalkanoic Acid Esters'),
(126, 'Alkylammonium Salts'),
(127, 'Fluoronitrogen Compounds (other)'),
(128, 'Chloronitrogen Compounds (other)'),
(129, 'Bromonitrogen Compounds (other)'),
(130, 'Iodonitrogen Compounds (other)'),
(131, 'Cyclopropanes'),
(132, 'Aminocycloalkanes'),
(133, 'Cycloalkanecarbonitriles'),
(134, 'Halocycloalkanes'),
(135, 'Bicycloalkyls'),
(136, 'Alkylidenecycloalkanes'),
(137, 'Cyclopropenes'),
(138, 'Oxiranes'),
(139, 'Aziridines'),
(140, 'Diazirenes'),
(141, 'Thiiranes'),
(142, 'Cyclobutanes'),
(143, 'Cycloalkanoic Acid Esters'),
(144, 'Cyclobutenes'),
(145, 'Oxetanes'),
(146, 'Cyclobutanediones'),
(147, 'Cyclobutenediones'),
(148, 'Oxetanones'),
(149, 'Diazetidinones'),
(150, 'Diazetines'),
(151, 'Thietanes'),
(152, 'Thietedioxides'),
(153, 'Cyclopentanes'),
(154, 'Cyclopentanols'),
(155, 'Dioxolanes'),
(156, 'Azidocycloalkanes'),
(157, 'Cyclopentenes'),
(158, 'Cycloalkenecarbonitriles'),
(159, 'Cyclopentadienes'),
(160, 'Alkylidenecycloalkenes'),
(161, 'Tetrahydrofurans'),
(162, 'Sugars and Derivatives'),
(163, 'Furans'),
(164, 'Cycloketones'),
(165, 'Tetrahydrofuran-2-ones'),
(166, 'Dihydrofurandiones'),
(167, 'Lactones'),
(168, 'Furandiones'),
(169, 'Pyrrolidines'),
(170, 'Pyrroles'),
(171, 'Bipyrroles'),
(172, 'Oxazolines'),
(173, 'Pyrrolidinones'),
(174, 'Pyrrolidinediones'),
(175, 'Imidazolidinediones'),
(176, 'Maleimides'),
(177, 'Oxazolidinediones'),
(178, 'Pyrazolines'),
(179, 'Furazans'),
(180, 'Dihydroisoxazoles'),
(181, 'Oxazoles'),
(182, 'Isoxazoles'),
(183, 'Triazoles'),
(184, 'Tetrazoles'),
(185, 'Bitetrazoles'),
(186, 'Azotetrazoles'),
(187, 'Pyrazoles'),
(188, 'Imidazoles'),
(189, 'Triazolones'),
(190, 'Imidazolones'),
(191, 'Furoxans (Furazan 2-Oxides)'),
(192, 'Tetrahydrothiophenes'),
(193, 'Thiophenes'),
(194, 'Dihydrothiophenes'),
(195, 'Dihydrothiophenones'),
(196, 'Dithiolanones'),
(197, 'Dithiolones'),
(198, 'Thiazoles'),
(199, 'Dithiolanethiones'),
(200, 'Dithiolethiones'),
(201, 'Dihydrothiophene Dioxides'),
(202, 'Cyclohexanes'),
(203, 'Cyclohexanols'),
(204, 'Cyclohexyl Hydroperoxides'),
(205, 'Cyclohexylamines'),
(206, 'Perfluorocyclohexanes'),
(207, 'Cyclohexenes'),
(208, 'Cyclohexenols'),
(209, 'Perfluorocyclohexenes'),
(210, 'Cyclohexadienes'),
(211, 'Tetrahydropyrans'),
(212, 'Dioxans'),
(213, 'Trioxans'),
(214, 'Dihydropyrans'),
(215, 'Cyclohexadienediones'),
(216, 'Tetrahydropyranones'),
(217, 'Tetrahydropyrandiones'),
(218, 'Piperidines'),
(219, 'Piperazines'),
(220, 'Hexahydrotriazines'),
(221, 'Tetrahydropyridines'),
(222, 'Piperidinones'),
(223, 'Piperidinediones'),
(224, 'Piperazinediones'),
(225, 'Azine Compounds (other)'),
(226, 'Pyrimidinediones'),
(227, 'Hydrazino Compounds (other)'),
(228, 'Triazines'),
(229, 'Imino Compounds (other)'),
(230, 'Pyridines'),
(231, 'Pyrazines'),
(232, 'Pyrimidines'),
(233, 'Pyridazines'),
(234, 'Pyrimidones'),
(235, 'Pyridine N-Oxides'),
(236, 'Tetrahydrothiopyrans'),
(237, 'Tetrahydrothiopyranones'),
(238, 'Dihydrothiopyranones'),
(239, 'Dithianethiones'),
(240, 'Cycloheptanes'),
(241, 'Cycloheptenes'),
(242, 'Dioxepanes'),
(243, 'Cycloheptatrienones'),
(244, 'Hexahydroazepinones'),
(245, 'Thiocycloalkanes'),
(246, 'Cyclooctanes'),
(247, 'Cyclooctenes'),
(248, 'Polyoxacyclooctanes'),
(249, 'Hexahydroazocinones'),
(250, 'Cycloalkane Compounds (other)'),
(251, 'Cycloalkene Compounds (other)'),
(252, 'Cycloalkynes'),
(253, 'Polyoxacycloalkane Compounds (other)'),
(254, 'Tetraazacycloalkanes'),
(255, 'Alkylbenzenes'),
(256, 'Biphenyls'),
(257, 'Polyphenylalkanes'),
(258, 'Polyphenylbenzenes'),
(259, 'Alkenylbenzenes'),
(260, 'Polyphenylalkenes'),
(261, 'Alkynylbenzenes'),
(262, 'Polyphenylalkynes'),
(263, 'Phenols'),
(264, 'Benzenediols'),
(265, 'Benzenetriols'),
(266, 'Alkanolylbenzenes'),
(267, 'Alkoxybenzenes'),
(268, 'Phenyl Hydroperoxides'),
(269, 'Phenyl Peroxides'),
(270, 'Alkanoylbenzenes'),
(271, 'Benzophenones'),
(272, 'Benzoic Acids'),
(273, 'Benzenepolycarboxylic Acids'),
(274, 'Hydroxybenzoic Acids'),
(275, 'Phenylalkanoic Acids'),
(276, 'Hydroxyphenylketones'),
(277, 'Alkanoylbenzenediols'),
(278, 'Phenylalkenoic Acids'),
(279, 'Phenylalkanoic Acid Esters'),
(280, 'Phenylalkanoic Acid Esters (other)'),
(281, 'Benzoic Acid Esters'),
(282, 'Benzenepolycarboxylic Acid Esters'),
(283, 'Benzoic Acid Anhydrides'),
(284, 'Alkoxybenzoic Acids'),
(285, 'Benzoyl Peroxides'),
(286, 'Phenylamines'),
(287, 'Aminophenols'),
(288, 'Phenylureas'),
(289, 'Benzamides'),
(290, 'Phenylacetamides'),
(291, 'Aminophenylketones'),
(292, 'Aminobenzoic Acids'),
(293, 'Phenylcarbamic Acid Esters'),
(294, 'Phenylhydrazines'),
(295, 'Phenylglyoximes'),
(296, 'Benzonitriles'),
(297, 'Isocyanates'),
(298, 'Mononitrobenzenes'),
(299, 'Dinitrobenzenes'),
(300, 'Trinitrobenzenes'),
(301, 'Nitroalkylbenzenes'),
(302, 'Nitroalkenylbenzenes'),
(303, 'Alkenylnitrobenzenes'),
(304, 'Nitrophenols'),
(305, 'Alkoxynitrobenzenes'),
(306, 'Nitrobenzoic Acids'),
(307, 'Nitroanilines'),
(308, 'Dinitroanilines'),
(309, 'Trinitroanilines'),
(310, 'Nitrophenylamines'),
(311, 'Benzene N-Oxides'),
(312, 'Thioethers (aromatic)'),
(313, 'Thiobenzamides'),
(314, 'Sulphones (aromatic)'),
(315, 'Halobenzene Compounds (other)'),
(316, 'Fluorobenzenes'),
(317, 'Chlorobenzenes'),
(318, 'Bromobenzenes'),
(319, 'Iodobenzenes'),
(320, 'Halobenzenes (mixed)'),
(321, 'Halophenols'),
(322, 'Halobenzenediols'),
(323, 'Halocarbonyls (aromatic)'),
(324, 'Halobenzoic Acids'),
(325, 'Halobenzoic Acid Esters'),
(326, 'Aniline Hydrochlorides'),
(327, 'Phenylcycloalkanes'),
(328, 'Phenylazetidines'),
(329, 'Phenyltrioxolanes'),
(330, 'Phenylfurazans'),
(331, 'Oxadiazoles'),
(332, 'Oxadiazolones'),
(333, 'Phenyltetrazoles'),
(334, 'Phenylcycloalkenes'),
(335, 'Morpholines'),
(336, 'Nitropyridines'),
(337, 'Dithins'),
(338, 'Spiro Compounds'),
(339, 'Bicyclo[1.1.0]butanes'),
(340, 'Pentacycloalkanes (other)'),
(341, 'Bicyclo[2.1.0]pentanes'),
(342, 'Bicyclo[3.3.0]octanes'),
(343, 'Bicyclo[2.2.1]heptanes'),
(344, 'Bicyclo[2.2.1]heptenes'),
(345, 'Oxabicycloalkanes'),
(346, 'Diazabicycloalkenes'),
(347, 'Indenes and Derivatives'),
(348, 'Tricyclo Compounds (other)'),
(349, 'Bicyclohexanes'),
(350, 'Tetracyclo Compounds (other)'),
(351, 'Naphthalenes and Derivatives'),
(352, 'Pyranopyrandiones'),
(353, 'Bicycloalkanes (other)'),
(354, 'Bicycloalkenes (other)'),
(355, 'Azabicycloalkanes'),
(356, 'Quinolines and Derivatives'),
(357, 'Adamantanes and Derivatives'),
(358, 'Anthracenes and Derivatives'),
(359, 'Phenalenes and Derivatives'),
(360, 'Diamantanes and Derivatives'),
(361, 'Benzoxadiazoles'),
(362, 'Purines and Derivatives'),
(363, 'Hexacycloalkanes'),
(364, 'Oxabicycloalkenes'),
(365, 'Azabicycloalkenes'),
(366, 'Azulenes'),
(367, 'Phenanthrenes'),
(368, 'Benzanthracenes'),
(369, 'Benzophenanthrenes'),
(370, 'Perylenes'),
(371, 'Indanes and Derivatives'),
(372, 'Acenaphthenes and Derivatives'),
(373, 'Indoles and Derivatives'),
(374, 'Carbazoles'),
(375, 'Indazoles'),
(376, 'Benzothiophenes'),
(377, 'Benzothiazoles'),
(378, 'Pentacenes'),
(379, 'Benzopentaphenes'),
(380, 'Benzopyrans'), 
(381, 'Xanthones'),
(382, 'Acridines and Derivatives'),
(383, 'Phenazines'),
(384, 'Cinnolines'),
(385, 'Indenopyrans'),
(386, 'Benzocycloalkenones'),
(387, 'Cyclophanes');

--
-- (2) Class
--
INSERT INTO class (cid, c_name) VALUES
(1, '01 - Acyclic Compounds'),
(2, '02 - Ring Systems Containing Only Isolated Non-Benzenoid Rings'),
(3, '03 - Ring Systems Containing Only Isolated Benzenoid Rings'),
(4, '04 - Ring Systems Containing Isolated Benzenoid and Non-Benzenoid Rings'),
(5, '05 - Condensed Non-Benzenoid Ring Systems'),
(6, '06 - Condensed Benzenoid Ring Systems');

--
--  (3) Subclass
--
INSERT INTO subclass (scid, sc_name) VALUES
(1, '01 - Hydrocarbons'),
(2, '02 - Oxygen Compounds'),
(3, '03 - Nitrogen Compounds'),
(4, '04 - Sulphur Compounds'),
(5, '05 - Halogen Compounds'),
(6, '06 - Three-Membered Ring Compounds'),
(7, '07 - Four-Membered Ring Compounds'),
(8, '08 - Five-Membered Ring Compounds'),
(9, '09 - Six-Membered Ring Compounds'),
(10, '10 - Seven-Membered Ring Compounds'),
(11, '11 - Eight-Membered Ring Compounds'),
(12, '12 - Ring Larger Than Eight-Membered Compounds');

--
-- (4) Characteristic
--
INSERT INTO characteristic (cid, ch_name) VALUES
(1, 'Alkane'),
(2, 'Alkene'),
(3, 'Alkyne'),
(4, 'Arene'),
(5, 'Alcohol'),
(6, 'Ether'),
(7, 'Peroxide'),
(8, 'Amine'),
(9, 'Hydrazine'),
(10, 'Imine'),
(11, 'Nitrile/Isonitrile'),
(12, 'NOx'),
(13, 'Aldehyde'),
(14, 'Ketone'),
(15, 'Carboxylic Acid'),
(16, 'Ester'),
(17, 'Amide'),
(18, 'Thiol'),
(19, 'Thioether'),
(20, 'Polysulphide'),
(21, 'Thiocarbonyl'),
(22, 'SOx'),
(23, 'Halogen'),
(24, 'Solvation'),
(25, 'Charges'),
(26, 'Ionic'),
(27, 'Polymer'),
(28, 'Radical');

--
-- (5) molecule_type
--
INSERT INTO molecule_type (mtid, mt_name) VALUES
(1, 'Organic'),
(2, 'Inorganic'),
(3, 'Organometallic'),
(4, 'Element');

--
-- (6) other_db_name
--
INSERT INTO other_db_name (odbn_id, db_name) VALUES
(1, 'Chemspider'),
(2, 'Chebi'),
(3, 'Wikipedia'),
(4, 'Beilstein'),
(5, 'Pubchem');

--
-- (7) data_type
--
INSERT INTO data_type (dtid, t_name) VALUES
(1, 'Physical Constant'),
(2, 'Standard Thermodynamic Property'),
(3, 'Critical Constant'),
(4, 'General Information'),
(5, 'Structural Data'),
(6, 'Radical'),
(7, 'Other');