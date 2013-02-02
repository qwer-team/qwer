-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 10, 2012 at 11:53 AM
-- Server version: 5.5.28
-- PHP Version: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `symfony`
--

-- --------------------------------------------------------

--
-- Table structure for table `Menu`
--

CREATE TABLE IF NOT EXISTS `Menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kod` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `tag` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_create` datetime NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `translit` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `icon` varchar(123) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `routing` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `metaTitle` longtext COLLATE utf8_unicode_ci,
  `metaDescription` longtext COLLATE utf8_unicode_ci,
  `metaKeyword` longtext COLLATE utf8_unicode_ci,
  `smallIcon` varchar(123) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DD3795AD727ACA70` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=33 ;

--
-- Dumping data for table `Menu`
--

INSERT INTO `Menu` (`id`, `kod`, `title`, `parent_id`, `tag`, `date_create`, `visible`, `translit`, `icon`, `description`, `content`, `routing`, `metaTitle`, `metaDescription`, `metaKeyword`, `smallIcon`) VALUES
(10, 4, 'Портфолио', NULL, 'tag44680', '2012-11-29 15:14:47', 1, 'Partfolio', NULL, 'sdafsdf', '<p>sdfsdfsdf</p>', 'portfolio', NULL, NULL, NULL, NULL),
(24, 7, 'Подвал', NULL, 'tag11178', '2012-11-30 15:32:41', 0, 'Podval', NULL, NULL, '      <div id="back-top-wrapper">\r\n         <p id="back-top">\r\n            <a href="#top"><span></span></a>\r\n         </p>\r\n      </div>\r\n         \r\n      <div class="footer-widget-area">\r\n         <div class="container_12 clearfix">\r\n            <div class="grid_12">\r\n            	<div class="wrapper">\r\n      \r\n                  <div class="grid_3 alpha">\r\n                     <div id="text-2"><h4>Socialize With Us</h4>			<div class="textwidget">8901 Marmora Road,<br>Glasgow, D04 89GR.<br>Telephone:  +1 800 603 6035</div>\r\n		</div><div id="social_networks-2">		\r\n      \r\n		</div>                  </div>\r\n                  \r\n                  <div class="grid_3">\r\n                     		<div id="recent-posts-2">		<h4>Recent Posts</h4>		<ul>\r\n				<li><a href="http://livedemo00.template-help.com/wordpress_37934/sed-tincidunt-mauris/lorem-ipsum-dolor-sit-ametcos/" title="Lorem ipsum dolor sit ametcos">Lorem ipsum dolor sit ametcos</a></li>\r\n				<li><a href="http://livedemo00.template-help.com/wordpress_37934/sed-tincidunt-mauris/ectetuer-adipiscing-elitsed-diamn/" title="Ectetuer adipiscing elitsed diamn">Ectetuer adipiscing elitsed diamn</a></li>\r\n				<li><a href="http://livedemo00.template-help.com/wordpress_37934/sed-tincidunt-mauris/ummy-nibh-euismod-tincidunt-ut/" title="Ummy nibh euismod tincidunt ut">Ummy nibh euismod tincidunt ut</a></li>\r\n				<li><a href="http://livedemo00.template-help.com/wordpress_37934/sed-tincidunt-mauris/laoreet-dolore-magna-aliqu/" title="Laoreet dolore magna aliqu">Laoreet dolore magna aliqu</a></li>\r\n				</ul>\r\n		</div>                  </div>\r\n                  \r\n                  <div class="grid_3">\r\n                     		<div id="recent-posts-2">		<h4>Recent Posts</h4>		<ul>\r\n				<li><a href="http://livedemo00.template-help.com/wordpress_37934/sed-tincidunt-mauris/lorem-ipsum-dolor-sit-ametcos/" title="Lorem ipsum dolor sit ametcos">Lorem ipsum dolor sit ametcos</a></li>\r\n				<li><a href="http://livedemo00.template-help.com/wordpress_37934/sed-tincidunt-mauris/ectetuer-adipiscing-elitsed-diamn/" title="Ectetuer adipiscing elitsed diamn">Ectetuer adipiscing elitsed diamn</a></li>\r\n				<li><a href="http://livedemo00.template-help.com/wordpress_37934/sed-tincidunt-mauris/ummy-nibh-euismod-tincidunt-ut/" title="Ummy nibh euismod tincidunt ut">Ummy nibh euismod tincidunt ut</a></li>\r\n				<li><a href="http://livedemo00.template-help.com/wordpress_37934/sed-tincidunt-mauris/laoreet-dolore-magna-aliqu/" title="Laoreet dolore magna aliqu">Laoreet dolore magna aliqu</a></li>\r\n				</ul>\r\n		</div>                  </div>\r\n                  \r\n                  <div class="grid_3 omega">\r\n                     		<div id="recent-posts-2">		<h4>Recent Posts</h4>		<ul>\r\n				<li><a href="http://livedemo00.template-help.com/wordpress_37934/sed-tincidunt-mauris/lorem-ipsum-dolor-sit-ametcos/" title="Lorem ipsum dolor sit ametcos">Lorem ipsum dolor sit ametcos</a></li>\r\n				<li><a href="http://livedemo00.template-help.com/wordpress_37934/sed-tincidunt-mauris/ectetuer-adipiscing-elitsed-diamn/" title="Ectetuer adipiscing elitsed diamn">Ectetuer adipiscing elitsed diamn</a></li>\r\n				<li><a href="http://livedemo00.template-help.com/wordpress_37934/sed-tincidunt-mauris/ummy-nibh-euismod-tincidunt-ut/" title="Ummy nibh euismod tincidunt ut">Ummy nibh euismod tincidunt ut</a></li>\r\n				<li><a href="http://livedemo00.template-help.com/wordpress_37934/sed-tincidunt-mauris/laoreet-dolore-magna-aliqu/" title="Laoreet dolore magna aliqu">Laoreet dolore magna aliqu</a></li>\r\n				</ul>\r\n		</div>                                    </div>\r\n         	\r\n            	</div>\r\n            </div>\r\n         </div>\r\n      </div>\r\n', 'footer', NULL, NULL, NULL, NULL),
(25, 8, 'Интернет проекты', 10, 'web_project', '2012-12-03 18:38:20', 1, 'Internet_proektyi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 9, 'Интернет магазины', 10, 'web_shop', '2012-12-03 18:46:11', 1, 'Internet_magazinyi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 10, 'Промо сайты', 10, 'promo_sites', '2012-12-03 18:47:54', 1, 'Promo_saytyi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 11, 'Сайты визитки', 10, 'sites', '2012-12-03 18:49:54', 1, 'Saytyi_vizitki', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Menu`
--
ALTER TABLE `Menu`
  ADD CONSTRAINT `FK_DD3795AD727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `Menu` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
