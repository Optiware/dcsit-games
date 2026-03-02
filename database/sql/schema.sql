CREATE DATABASE IF NOT EXISTS campus_it CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE campus_it;

DROP TABLE IF EXISTS consommation;
DROP TABLE IF EXISTS application;
DROP TABLE IF EXISTS ressource;

CREATE TABLE application (
  app_id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE ressource (
  res_id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(30) NOT NULL UNIQUE,
  unite VARCHAR(10) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE consommation (
  conso_id INT AUTO_INCREMENT PRIMARY KEY,
  app_id INT NOT NULL,
  res_id INT NOT NULL,
  mois DATE NOT NULL,
  volume DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_conso_app FOREIGN KEY (app_id) REFERENCES application(app_id),
  CONSTRAINT fk_conso_res FOREIGN KEY (res_id) REFERENCES ressource(res_id),
  INDEX idx_mois (mois),
  INDEX idx_app (app_id),
  INDEX idx_res (res_id)
) ENGINE=InnoDB;
