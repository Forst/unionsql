SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
SET NAMES utf8;

START TRANSACTION;

SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS `sqlinj` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sqlinj`;

CREATE USER 'sqlinj_admin'@'localhost' IDENTIFIED BY 'PWD_ADMIN';
GRANT USAGE ON *.* TO 'sqlinj_admin'@'localhost';
GRANT SELECT, INSERT ON `sqlinj`.* TO 'sqlinj_admin'@'localhost';

CREATE USER 'sqlinj_anon'@'localhost' IDENTIFIED BY 'PWD_ANON';
GRANT USAGE ON *.* TO 'sqlinj_anon'@'localhost';
GRANT SELECT ON `sqlinj`.* TO 'sqlinj_anon'@'localhost';

CREATE TABLE `config` (
  `ckey` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Название параметра',
  `cvalue` text COLLATE utf8mb4_unicode_ci COMMENT 'Значение параметра',
  `ctype` enum('string','int','bool','json') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string' COMMENT 'Тип значения параметра'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Конфигурация';

INSERT INTO `config` (`ckey`, `cvalue`, `ctype`) VALUES
('name_short', 'SQL-инъекция', 'string'),
('desc_short', 'Веб-сайт для тренировки навыка выполнения SQL-инъекций', 'string'),
('keywords', 'sql,injection,инъекция,атака,attack', 'string'),
('session_duration', '86400', 'int'),
('hmac_key', 'PWD_HMAC', 'string');

CREATE TABLE `nyoows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `author` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Новости';

INSERT INTO `nyoows` (`id`, `author`, `created`, `title`, `text`) VALUES
(1, 'admean', '2017-10-07 15:16:17', 'Задание', 'Всем привет!\r\n\r\nЦель этого задания — дать возможность потренироваться выполнять SQL-инъекции. Уязвимость здесь одна и очень просто эксплуатируется (а может и не одна, если найдёте ещё, плюсик вам в карму).\r\n\r\nВаша основная задача: запостить новость на главной странице, в тексте которой будет ваше имя или ник.\r\n\r\nУдачи!');

CREATE TABLE `uzwers` (
  `username` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Имя пользователя',
  `password` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Хеш пароля',
  `displayname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Отображаемое имя'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Пользователи';

INSERT INTO `uzwers` (`username`, `password`, `displayname`) VALUES
('admean', '', 'Администратор'),
('yooser', '2ab96390c7dbe3439de74d0c9b0b1767', 'Пользователь');


ALTER TABLE `config`
  ADD PRIMARY KEY (`ckey`);

ALTER TABLE `nyoows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author` (`author`);
ALTER TABLE `nyoows` ADD FULLTEXT KEY `text` (`text`);

ALTER TABLE `uzwers`
  ADD PRIMARY KEY (`username`);


ALTER TABLE `nyoows`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `nyoows`
  ADD CONSTRAINT `nyoows_ibfk_1` FOREIGN KEY (`author`) REFERENCES `uzwers` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

FLUSH PRIVILEGES;
