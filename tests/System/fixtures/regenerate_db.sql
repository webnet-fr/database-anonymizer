DROP DATABASE IF EXISTS `anonymizer`;
CREATE DATABASE `anonymizer`;
USE `anonymizer`;

CREATE TABLE `user` (
  `id` INT NOT NULL,
  `name` VARCHAR(256) NULL,
  `lastname` VARCHAR(256) NULL,
  `birthdate` DATETIME NULL,
  `phone` VARCHAR(20) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `ID_UNIQUE` (`id` ASC));

INSERT INTO `user`
  VALUES (1, NULL, NULL, NULL, NULL),
  (2, NULL, NULL, NULL, NULL),
  (3, NULL, NULL, NULL, NULL),
  (4, NULL, NULL, NULL, NULL)
;

CREATE TABLE `order` (
  `id` INT NOT NULL,
  `date` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `ID_UNIQUE` (`id` ASC));

INSERT INTO `order`
  VALUES (1, NULL),
  (2, NULL, NULL),
  (3, NULL, NULL),
  (4, NULL, NULL)
;
