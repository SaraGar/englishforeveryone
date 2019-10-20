-- Valentina Studio --
-- MySQL dump --
-- ---------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
-- ---------------------------------------------------------


-- Dump data of "app_users" --------------------------------
INSERT INTO `app_users`(`id`,`username`,`password`,`email`,`is_active`,`first_name`,`last_name`,`avatar_name`,`paid_lessons_left`,`roles`) VALUES 
( '1', 'admin', '$2y$13$iLODDmI1meNNqGXPChGkr.MSpQFWr8cRNol2EBAZWb2qLgHukCD1W', 'admin@gmail.com', '1', NULL, NULL, NULL, '0', 'ROLE_USER,ROLE_SUPER_ADMIN' ),
( '2', 'student', '$2y$13$j1dNP/7YUDBRU3PGZ1SWiewwZfhn/7q.i/S7KMFrnSKvzMPHQiN3.', 'student@gmail.com', '1', NULL, NULL, NULL, '0', 'ROLE_USER' ),
( '3', 'teacher', '$2y$13$OT2vN/H9BDQVKT.DiwWzgOrNax0poUTlAUAwmTVmkB.sgMyPcDQTa', 'teacher@gmail.com', '1', NULL, NULL, NULL, '0', 'ROLE_USER,ROLE_TEACHER' );
-- ---------------------------------------------------------


-- Dump data of "billing_address" --------------------------
-- ---------------------------------------------------------


-- Dump data of "invoice" ----------------------------------
-- ---------------------------------------------------------


-- Dump data of "invoice_line" -----------------------------
-- ---------------------------------------------------------


-- Dump data of "lesson" -----------------------------------
INSERT INTO `lesson`(`id`,`level`,`attendants`,`description`,`price_net`) VALUES 
( '1', 'A2', '20', 'Low level conversation and vocabulary', '10' ),
( '2', 'B1', '10', 'Intermediate conversation, vocabulary, definitions', '10' ),
( '3', 'B2', '10', 'Intermediate-advance level conversation, definitions, story telling', '10' ),
( '4', 'C1', '5', 'Advance level conversation, grammar, vocabylary, definitions', '15' );
-- ---------------------------------------------------------


-- Dump data of "plan" -------------------------------------
INSERT INTO `plan`(`id`,`lessons_number`,`price_net`,`price_total`,`disabled`) VALUES 
( '1', '4', '20', '20', '0' ),
( '2', '8', '40', '40', '0' ),
( '3', '12', '60', '60', '0' ),
( '4', '16', '80', '80', '0' ),
( '5', '20', '100', '100', '0' );
-- ---------------------------------------------------------


-- Dump data of "post" -------------------------------------
-- ---------------------------------------------------------


-- Dump data of "promo_code" -------------------------------
INSERT INTO `promo_code`(`id`,`code`,`percent_discount`,`end_date`,`max_times_used`,`times_used`) VALUES 
( '1', '50DISCOUNT', '50', '2020-01-01 00:00:00', '50', '0' ),
( '2', '20DISCOUNT', '20', '2020-01-01 00:00:00', '100', '0' );
-- ---------------------------------------------------------


-- Dump data of "reservation" ------------------------------
-- ---------------------------------------------------------


-- Dump data of "schedule" ---------------------------------
-- ---------------------------------------------------------


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- ---------------------------------------------------------


