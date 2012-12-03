INSERT INTO `MenuSys` (`system_id`, `parent_id`, `tag`, `date_create`, `visible`, `routing`, `title`, `translit`, `icon`, `smallIcon`, `kod`, `content`, `description`, `metaTitle`, `metaDescription`, `metaKeyword`) VALUES
(1, NULL, '', '2012-11-29 13:37:06', 1, 'menu_sys', 'Системное меню', 'Sistemnoe_menyu', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL),
(2, NULL, '', '2012-11-29 13:38:34', 1, 'menu', 'Меню', 'Menyu', NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL),
(3, NULL, '', '2012-11-29 13:39:11', 1, 'product_group', 'Товары', 'Tovaryi', NULL, NULL, 3, NULL, NULL, NULL, NULL, NULL),
(4, NULL, '', '2012-11-29 13:40:52', 1, 'brand', 'Бренды', 'Brendyi', NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL),
(5, NULL, '', '2012-11-29 13:41:28', 1, 'translation', 'Переводы', 'Perevodyi', NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL),
(6, NULL, '', '2012-11-29 13:42:22', 1, 'units', 'Единицы измерения', 'Edinitsyi_izmereniya_6', NULL, NULL, 6, NULL, NULL, NULL, NULL, NULL),
(7, NULL, '', '2012-11-30 15:57:23', 1, 'user', 'Пользователи', 'Polzovateli', NULL, NULL, 7, NULL, NULL, NULL, NULL, NULL),
(8, NULL, '', '2012-11-30 15:57:45', 1, 'commands', 'Команды', 'Komandyi', NULL, NULL, 8, NULL, NULL, NULL, NULL, NULL)
ON DUPLICATE KEY UPDATE
title = VALUES(title),
tag = VALUES(tag),
routing = VALUES(routing),
translit = VALUES(translit),
content = VALUES(content),
description = VALUES(description),
metaTitle = VALUES(metaTitle),
metaDescription = VALUES(metaDescription);
