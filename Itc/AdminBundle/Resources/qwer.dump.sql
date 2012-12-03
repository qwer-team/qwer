INSERT INTO `MenuSys` (`system_id`, `parent_id`, `tag`,  `visible`, `routing`, `title`, `translit`, `kod`, `content`, `description`, `metaTitle`, `metaDescription`, `metaKeyword`) VALUES
(1,NULL,'sys',1,'menu_sys','Системное меню','Sistemnoe_menyu_9',1,'','','','',''),
(2,NULL,'menu',1,'menu','Меню','Menyu',2,'','','','',''),
(3,NULL,'',1,'product_group','Товары','Tovaryi',3,'','','','',''),
(4,NULL,'',1,'brand','Бренды','Brendyi',4,'','','','',''),
(5,NULL,'',1,'translation','Переводы','Perevodyi',5,'','','','',''),
(6,NULL,'',1,'units','Единицы измерения','Edinitsyi_izmereniya_6',6,'','','','',''),
(7,NULL,'',1,'user','Пользователи','Polzovateli',7,'','','','',''),
(8,NULL,'',1,'commands','Команды','Komandyi',8,'','','','','')ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                tag = VALUES(tag),
                routing = VALUES(routing),
                translit = VALUES(translit),
                content = VALUES(content),
                description = VALUES(description),
                metaTitle = VALUES(metaTitle),
                metaDescription = VALUES(metaDescription);
INSERT INTO `MenuSysTranslation` (`translatable_id`, `locale`,  `property`,  `value`) VALUES
((SELECT id FROM MenuSys WHERE system_id = 1),'en','title','System menu'),
((SELECT id FROM MenuSys WHERE system_id = 1),'en','translit','System_menu'),
((SELECT id FROM MenuSys WHERE system_id = 1),'en','metaKeyword',''),
((SELECT id FROM MenuSys WHERE system_id = 1),'en','metaDescription',''),
((SELECT id FROM MenuSys WHERE system_id = 1),'en','metaTitle',''),
((SELECT id FROM MenuSys WHERE system_id = 1),'en','description',''),
((SELECT id FROM MenuSys WHERE system_id = 2),'en','title','Menu'),
((SELECT id FROM MenuSys WHERE system_id = 2),'en','translit','Menu'),
((SELECT id FROM MenuSys WHERE system_id = 2),'en','metaKeyword',''),
((SELECT id FROM MenuSys WHERE system_id = 2),'en','metaDescription',''),
((SELECT id FROM MenuSys WHERE system_id = 2),'en','metaTitle',''),
((SELECT id FROM MenuSys WHERE system_id = 2),'en','description','')ON DUPLICATE KEY UPDATE
            value = VALUES(value);
