--
-- Database: therminfo2
--

USE therminfo2;
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS mol_user;
DROP TABLE IF EXISTS entry_user;
DROP TABLE IF EXISTS contador;
DROP TABLE IF EXISTS dbevolution;
DROP TABLE IF EXISTS news;
DROP TABLE IF EXISTS serialize_values;


-- -----------------------------------------------------
-- (1) Table therminfo2.user
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS user (
  uid BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  u_first_name VARCHAR(40) NOT NULL,
  u_last_name VARCHAR(40) NOT NULL,
  email VARCHAR(45) NOT NULL,
  password VARCHAR(100) NOT NULL,
  institution VARCHAR(100) NULL,
  type ENUM('guest', 'admin', 'superadmin') NOT NULL,
  validated INT(1) UNSIGNED NOT NULL DEFAULT 0,
  outdated INT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (uid) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX ix_email ON user (email ASC);

DELETE FROM user;
ALTER TABLE user AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (2) Table therminfo2.mol_user
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS mol_user (
  molecule BIGINT UNSIGNED NOT NULL,
  user BIGINT UNSIGNED NOT NULL,
  create_date DATE NULL,
  PRIMARY KEY (molecule, user),
  CONSTRAINT fk_moluser_molecule
    FOREIGN KEY (molecule)
    REFERENCES molecule (mid)
	ON DELETE CASCADE,
  CONSTRAINT fk_moluser_user
    FOREIGN KEY (user)
    REFERENCES user (uid) 
	ON DELETE CASCADE )
ENGINE = InnoDB;

CREATE INDEX ix_moluser_molecule ON mol_user (molecule ASC);
CREATE INDEX ix_moluser_user ON mol_user (user ASC);

DELETE FROM mol_user;

-- -----------------------------------------------------
-- (3) Table therminfo2.entry_user
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS entry_user (
  user BIGINT UNSIGNED NOT NULL,
  value_entry BIGINT UNSIGNED NOT NULL,
  create_date DATE NULL,
  PRIMARY KEY (user, value_entry),
  CONSTRAINT fk_entry_user
    FOREIGN KEY (user)
    REFERENCES user (uid)
	ON DELETE CASCADE,
  CONSTRAINT fk_entry_value
    FOREIGN KEY (value_entry)
    REFERENCES molecule_data_ref (value_id) 
	ON DELETE CASCADE )
ENGINE = InnoDB;

CREATE INDEX ix_entry_user ON entry_user (user ASC);
CREATE INDEX ix_entry_value ON entry_user (value_entry ASC);

DELETE FROM entry_user;

-- -----------------------------------------------------
-- (4) Table therminfo2.contador
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS contador (
  contid BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  day INT(2) UNSIGNED NOT NULL,
  month INT(2) UNSIGNED NOT NULL,
  year YEAR NOT NULL,
  hour INT(2) UNSIGNED NULL,
  minute INT(2) UNSIGNED NULL,
  second INT(2) UNSIGNED NULL,
  ip VARCHAR(30) NOT NULL,
  method INT(2) UNSIGNED NOT NULL,
  method_type INT(2) UNSIGNED NULL,
  search_detail VARCHAR(255) NULL,
  country VARCHAR(45) NULL,
  city VARCHAR(45) NULL,
  PRIMARY KEY (contid) )
ENGINE = InnoDB;

DELETE FROM contador;
ALTER TABLE contador AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (5) Table therminfo2.dbevolution
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS dbevolution (
  eid INT UNSIGNED NOT NULL AUTO_INCREMENT,
  month INT(2) UNSIGNED NOT NULL,
  year YEAR NOT NULL,
  nrcompounds BIGINT UNSIGNED NOT NULL,
  nrcompusers BIGINT UNSIGNED NOT NULL,
  last_update TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (eid) )
ENGINE = InnoDB;

DELETE FROM dbevolution;
ALTER TABLE dbevolution AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (6) Table therminfo2.news
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS news (
  nid INT UNSIGNED NOT NULL AUTO_INCREMENT,
  date VARCHAR(100) NOT NULL,
  year YEAR NOT NULL,
  month SMALLINT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  PRIMARY KEY (nid) )
ENGINE = InnoDB;

DELETE FROM news;
ALTER TABLE news AUTO_INCREMENT = 1;

-- -----------------------------------------------------
-- (7) Table therminfo2.serialize_values
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS serialize_values (
  s_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  date DATE NULL,
  value TEXT NOT NULL,
  PRIMARY KEY (s_id) )
ENGINE = InnoDB;

DELETE FROM serialize_values;
ALTER TABLE serialize_values AUTO_INCREMENT = 1;