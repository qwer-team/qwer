-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 29, 2012 at 01:57 PM
-- Server version: 5.5.27
-- PHP Version: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `qwer`
--

--
-- Dumping data for table `Menu`
--

INSERT INTO `Menu` (`id`, `parent_id`, `tag`, `date_create`, `visible`, `description`, `routing`, `title`, `translit`, `icon`, `smallIcon`, `kod`, `content`, `metaTitle`, `metaDescription`, `metaKeyword`) VALUES
(1, NULL, 'main_page', '2012-11-29 13:48:21', 1, NULL, NULL, 'Главная страница', 'Glavnaya_stranitsa', NULL, NULL, 1, NULL, NULL, NULL, NULL);

--
-- Dumping data for table `MenuClosure`
--

INSERT INTO `MenuClosure` (`id`, `ancestor`, `descendant`, `depth`) VALUES
(1, 1, 1, 0);

--
-- Dumping data for table `MenuSys`
--

INSERT INTO `MenuSys` (`id`, `parent_id`, `tag`, `date_create`, `visible`, `routing`, `title`, `translit`, `icon`, `smallIcon`, `kod`, `content`, `description`, `metaTitle`, `metaDescription`, `metaKeyword`) VALUES
(1, NULL, 'tag1500', '2012-11-29 13:37:06', 1, 'menu_sys', 'Системное меню', 'Sistemnoe_menyu', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL),
(2, NULL, 'tag30029', '2012-11-29 13:38:34', 1, 'menu', 'Меню', 'Menyu', NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL),
(3, NULL, 'tag97926', '2012-11-29 13:39:11', 1, 'product_group', 'Товары', 'Tovaryi', NULL, NULL, 3, NULL, NULL, NULL, NULL, NULL),
(4, NULL, 'tag85545', '2012-11-29 13:40:52', 1, 'brand', 'Бренды', 'Brendyi', NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL),
(5, NULL, 'tag35920', '2012-11-29 13:41:28', 1, 'translation', 'Переводы', 'Perevodyi', NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL),
(6, NULL, 'tag79193', '2012-11-29 13:42:22', 1, 'units', 'Единицы измерения', 'Edinitsyi_izmereniya_6', NULL, NULL, 6, NULL, NULL, NULL, NULL, NULL);

--
-- Dumping data for table `MenuSysClosure`
--

INSERT INTO `MenuSysClosure` (`id`, `ancestor`, `descendant`, `depth`) VALUES
(1, 1, 1, 0),
(2, 2, 2, 0),
(3, 3, 3, 0),
(4, 4, 4, 0),
(5, 5, 5, 0),
(6, 6, 6, 0);

--
-- Dumping data for table `Units`
--

INSERT INTO `Units` (`id`, `title`) VALUES
(1, 'метры');

--
-- Dumping data for table `User`
--

INSERT INTO `User` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`, `status`, `tel`, `address`, `fio`, `date_registrate`) VALUES
(1, 'roma', 'roma', 'rasom@ukr.net', 'rasom@ukr.net', 1, 'i5mh4fkopy8k4w0oko8s8kogso4g08c', 'doJRUv4/tIZ9XEve5l3Vwk79CRbrWx8vmxn90FF1CVzJGeW4FjX+xj9ws5phgodeFx/eoxl4Kg3FLRg3IB7PTA==', '2012-11-29 13:31:37', 0, 0, NULL, NULL, NULL, 'a:0:{}', 0, NULL, NULL, NULL, NULL, NULL, '2012-11-29 11:47:46');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
