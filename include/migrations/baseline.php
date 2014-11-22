<?php

$migration->up = array(
    <<<SQL
    CREATE TABLE `version` (
        id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        dbversion INT NOT NULL,
        appversion DECIMAL(4,2) NOT NULL,
        committed DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `aktiviteter` (
      `id` int(11) NOT NULL auto_increment,
      `navn` varchar(256) NOT NULL,
      `kan_tilmeldes` enum('ja','nej') NOT NULL,
      `note` text,
      `foromtale` text,
      `type` enum('braet','rolle','live','figur','dag5') NOT NULL,
      `varighed_per_afvikling` int(11) NOT NULL,
      `min_deltagere_per_hold` int(11) NOT NULL,
      `max_deltagere_per_hold` int(11) NOT NULL,
      `spilledere_per_hold` int(11) NOT NULL,
      `pris` int(11) NOT NULL default '20',
      `lokale_eksklusiv` enum('ja','nej') NOT NULL default 'ja',
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `afviklinger` (
      `id` int(11) NOT NULL auto_increment,
      `aktivitet_id` int(11) NOT NULL,
      `start` datetime NOT NULL,
      `slut` datetime NOT NULL,
      `lokale_id` int(11) default NULL,
      PRIMARY KEY  (`id`),
      UNIQUE KEY `periode_aktivitet` (`aktivitet_id`,`start`,`slut`),
      CONSTRAINT `afviklinger_ibfk_1` FOREIGN KEY (`aktivitet_id`) REFERENCES `aktiviteter` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `afviklinger_multiblok` (
      `id` int(11) NOT NULL auto_increment,
      `afvikling_id` int(11) NOT NULL,
      `start` datetime NOT NULL,
      `slut` datetime NOT NULL,
      PRIMARY KEY  (`id`),
      UNIQUE KEY `periode_aktivitet` (`afvikling_id`,`start`,`slut`),
      CONSTRAINT `afviklinger_multiblok_ibfk_1` FOREIGN KEY (`afvikling_id`) REFERENCES `afviklinger` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `brugerkategorier` (
      `id` int(11) NOT NULL auto_increment,
      `navn` varchar(256) NOT NULL,
      `arrangoer` enum('ja','nej') NOT NULL default 'nej',
      `beskrivelse` varchar(512) default NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `deltagere` (
      `id` int(11) NOT NULL auto_increment,
      `fornavn` varchar(128) NOT NULL,
      `efternavn` varchar(256) NOT NULL,
      `gender` enum('m','k') NOT NULL,
      `alder` int(11) NOT NULL,
      `email` varchar(512) NOT NULL,
      `tlf` varchar(16) default NULL,
      `mobiltlf` varchar(16) default NULL,
      `adresse1` varchar(128) NOT NULL,
      `adresse2` varchar(128) default NULL,
      `postnummer` varchar(8) NOT NULL,
      `by` varchar(128) NOT NULL,
      `land` varchar(64) NOT NULL default 'Danmark',
      `medbringer_mobil` enum('ja','nej') NOT NULL default 'nej',
      `sprog` set('dansk','engelsk','skandinavisk') NOT NULL default 'dansk',
      `brugerkategori_id` int(11) NOT NULL,
      `forfatter` enum('ja','nej') NOT NULL default 'nej',
      `international` enum('ja','nej') NOT NULL default 'nej',
      `knutepunkt` enum('ja','nej') NOT NULL default 'nej',
      `knutepunkt_bil` enum('ja','nej') NOT NULL default 'nej',
      `geekbookdrive` enum('ja','nej') NOT NULL default 'nej',
      `arrangoer_naeste_aar` enum('ja','nej') NOT NULL default 'nej',
      `betalt_beloeb` int(11) NOT NULL default '0',
      `rel_karma` int(11) NOT NULL default '0',
      `abs_karma` int(11) NOT NULL default '0',
      `deltaget_i_fastaval` int(11) NOT NULL default '0',
      `deltager_note` text,
      `admin_note` text,
      `beskeder` text,
      `created` datetime NOT NULL,
      `hash` varchar(64) NOT NULL,
      `flere_gdsvagter` enum('ja','nej') NOT NULL default 'nej',
      `supergm` enum('ja','nej') NOT NULL default 'nej',
      `supergds` enum('ja','nej') NOT NULL default 'nej',
      `rig_onkel` enum('ja','nej') NOT NULL default 'nej',
      `arbejdsomraade` varchar(256) default NULL,
      `scenarie` varchar(256) default NULL,
      `udeblevet` enum('ja','nej') NOT NULL default 'nej',
      `rabat` enum('ja','nej') NOT NULL default 'nej',
      PRIMARY KEY  (`id`),
      KEY `brugerkategori_id` (`brugerkategori_id`),
      KEY `rel_karma` (`rel_karma`),
      KEY `abs_karma` (`abs_karma`),
      CONSTRAINT `deltagere_ibfk_1` FOREIGN KEY (`brugerkategori_id`) REFERENCES `brugerkategorier` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `gds` (
      `id` int(11) NOT NULL auto_increment,
      `navn` varchar(64) NOT NULL,
      `beskrivelse` varchar(512) default NULL,
      `moedested` varchar(256) default NULL,
      PRIMARY KEY  (`id`),
      UNIQUE KEY `navn` (`navn`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `gdsvagter` (
      `id` int(11) NOT NULL auto_increment,
      `gds_id` int(11) NOT NULL,
      `antal_personer` int(11) NOT NULL,
      `start` datetime NOT NULL,
      `slut` datetime NOT NULL,
      PRIMARY KEY  (`id`),
      UNIQUE KEY `gds_id` (`gds_id`,`start`),
      CONSTRAINT `gdsvagter_ibfk_1` FOREIGN KEY (`gds_id`) REFERENCES `gds` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `lokaler` (
      `id` int(11) NOT NULL auto_increment,
      `beskrivelse` varchar(256) default NULL,
      `omraade` varchar(32) default NULL,
      `skole` varchar(64) NOT NULL,
      `kan_bookes` enum('ja','nej') NOT NULL default 'ja',
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `hold` (
      `id` int(11) NOT NULL auto_increment,
      `afvikling_id` int(11) NOT NULL,
      `holdnummer` int(11) NOT NULL,
      `lokale_id` int(11) NOT NULL,
      PRIMARY KEY  (`id`),
      UNIQUE KEY `afvikling_id` (`afvikling_id`,`holdnummer`),
      KEY `lokale_id` (`lokale_id`),
      CONSTRAINT `hold_ibfk_1` FOREIGN KEY (`afvikling_id`) REFERENCES `afviklinger` (`id`) ON DELETE CASCADE,
      CONSTRAINT `hold_ibfk_2` FOREIGN KEY (`lokale_id`) REFERENCES `lokaler` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `indgang` (
      `id` int(11) NOT NULL auto_increment,
      `type` varchar(64) NOT NULL,
      `pris` int(11) NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `log` (
      `id` int(11) NOT NULL auto_increment,
      `type` varchar(32) NOT NULL,
      `message` varchar(256) NOT NULL,
      `user_id` int(11) NOT NULL,
      `created` datetime NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `mad` (
      `id` int(11) NOT NULL auto_increment,
      `kategori` varchar(32) NOT NULL,
      `pris` int(11) NOT NULL,
      PRIMARY KEY  (`id`),
      UNIQUE KEY `kategori` (`kategori`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `madtider` (
      `id` int(11) NOT NULL auto_increment,
      `mad_id` int(11) NOT NULL,
      `dato` date NOT NULL,
      PRIMARY KEY  (`id`),
      UNIQUE KEY `mad_id` (`mad_id`,`dato`),
      CONSTRAINT `madtider_ibfk_1` FOREIGN KEY (`mad_id`) REFERENCES `mad` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `pladser` (
      `hold_id` int(11) NOT NULL,
      `pladsnummer` int(11) NOT NULL,
      `type` enum('spilleder','spiller') NOT NULL,
      `deltager_id` int(11) NOT NULL,
      PRIMARY KEY  (`hold_id`,`pladsnummer`),
      UNIQUE KEY `hold_id` (`hold_id`,`deltager_id`),
      KEY `deltager_id` (`deltager_id`),
      CONSTRAINT `pladser_ibfk_1` FOREIGN KEY (`hold_id`) REFERENCES `hold` (`id`) ON DELETE CASCADE,
      CONSTRAINT `pladser_ibfk_2` FOREIGN KEY (`deltager_id`) REFERENCES `deltagere` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `privileges` (
      `id` int(11) NOT NULL auto_increment,
      `controller` varchar(128) NOT NULL,
      `method` varchar(128) NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `roles` (
      `id` int(11) NOT NULL auto_increment,
      `name` varchar(128) NOT NULL,
      `description` text,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `roles_privileges` (
      `role_id` int(11) NOT NULL,
      `privilege_id` int(11) NOT NULL,
      PRIMARY KEY  (`role_id`,`privilege_id`),
      KEY `privilege_id` (`privilege_id`),
      CONSTRAINT `roles_privileges_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
      CONSTRAINT `roles_privileges_ibfk_2` FOREIGN KEY (`privilege_id`) REFERENCES `privileges` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `smslog` (
      `id` int(11) NOT NULL auto_increment,
      `nummer` int(11) NOT NULL,
      `deltager_id` int(11) NOT NULL,
      `sendt` datetime NOT NULL,
      `besked` text NOT NULL,
      `return_val` text NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `todoitems` (
      `id` int(11) NOT NULL auto_increment,
      `note` text NOT NULL,
      `note_body` text,
      `created` datetime NOT NULL,
      `updated` datetime NOT NULL,
      `role_id` int(11) NOT NULL,
      `done` enum('ja','nej') default 'nej',
      PRIMARY KEY  (`id`),
      KEY `role_id` (`role_id`),
      CONSTRAINT `todoitems_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `translations` (
      `id` int(11) NOT NULL auto_increment,
      `table` varchar(64) NOT NULL,
      `field` varchar(64) NOT NULL,
      `row_id` int(11) NOT NULL,
      `english` text NOT NULL,
      PRIMARY KEY  (`id`),
      UNIQUE KEY `table` (`table`,`field`,`row_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `users` (
      `id` int(11) NOT NULL auto_increment,
      `user` varchar(32) NOT NULL,
      `pass` varchar(64) NOT NULL,
      `disabled` enum('ja','nej') NOT NULL default 'nej',
      PRIMARY KEY  (`id`),
      UNIQUE KEY `user` (`user`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `users_roles` (
      `user_id` int(11) NOT NULL,
      `role_id` int(11) NOT NULL,
      PRIMARY KEY  (`user_id`,`role_id`),
      KEY `role_id` (`role_id`),
      CONSTRAINT `users_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
      CONSTRAINT `users_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `videoer` (
      `id` int(11) NOT NULL auto_increment,
      `navn` varchar(256) NOT NULL,
      `email` varchar(256) NOT NULL,
      `titel` varchar(512) NOT NULL,
      `instruktoer` varchar(512) NOT NULL,
      `skuespillere` text NOT NULL,
      `genre` varchar(512) NOT NULL,
      `laengde` varchar(64) NOT NULL,
      `foromtale` text NOT NULL,
      `introduktion` enum('ja','nej') NOT NULL default 'nej',
      `question_session` enum('ja','nej') NOT NULL default 'nej',
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `wear` (
      `id` int(11) NOT NULL auto_increment,
      `navn` varchar(64) NOT NULL,
      `size_range` varchar(16) NOT NULL,
      `beskrivelse` text,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `wearpriser` (
      `id` int(11) NOT NULL auto_increment,
      `wear_id` int(11) NOT NULL,
      `brugerkategori_id` int(11) NOT NULL,
      `pris` int(11) NOT NULL default '0',
      PRIMARY KEY  (`id`),
      UNIQUE KEY `wear_id` (`wear_id`,`brugerkategori_id`),
      KEY `brugerkategori_id` (`brugerkategori_id`),
      CONSTRAINT `wearpriser_ibfk_1` FOREIGN KEY (`wear_id`) REFERENCES `wear` (`id`) ON DELETE CASCADE,
      CONSTRAINT `wearpriser_ibfk_2` FOREIGN KEY (`brugerkategori_id`) REFERENCES `brugerkategorier` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `deltagere_gdstilmeldinger` (
      `deltager_id` int(11) NOT NULL,
      `gdsvagt_id` int(11) NOT NULL,
      PRIMARY KEY  (`deltager_id`,`gdsvagt_id`),
      KEY `gdsvagt_id` (`gdsvagt_id`),
      CONSTRAINT `deltagere_gdstilmeldinger_ibfk_1` FOREIGN KEY (`deltager_id`) REFERENCES `deltagere` (`id`) ON DELETE CASCADE,
      CONSTRAINT `deltagere_gdstilmeldinger_ibfk_2` FOREIGN KEY (`gdsvagt_id`) REFERENCES `gdsvagter` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `deltagere_gdsvagter` (
      `deltager_id` int(11) NOT NULL,
      `gdsvagt_id` int(11) NOT NULL,
      PRIMARY KEY  (`deltager_id`,`gdsvagt_id`),
      KEY `gdsvagt_id` (`gdsvagt_id`),
      CONSTRAINT `deltagere_gdsvagter_ibfk_1` FOREIGN KEY (`deltager_id`) REFERENCES `deltagere` (`id`) ON DELETE CASCADE,
      CONSTRAINT `deltagere_gdsvagter_ibfk_2` FOREIGN KEY (`gdsvagt_id`) REFERENCES `gdsvagter` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `deltagere_indgang` (
      `deltager_id` int(11) NOT NULL,
      `indgang_id` int(11) NOT NULL,
      PRIMARY KEY  (`deltager_id`,`indgang_id`),
      KEY `indgang_id` (`indgang_id`),
      CONSTRAINT `deltagere_indgang_ibfk_1` FOREIGN KEY (`deltager_id`) REFERENCES `deltagere` (`id`) ON DELETE CASCADE,
      CONSTRAINT `deltagere_indgang_ibfk_2` FOREIGN KEY (`indgang_id`) REFERENCES `indgang` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `deltagere_madtider` (
      `deltager_id` int(11) NOT NULL,
      `madtid_id` int(11) NOT NULL,
      PRIMARY KEY  (`deltager_id`,`madtid_id`),
      KEY `madtid_id` (`madtid_id`),
      CONSTRAINT `deltagere_madtider_ibfk_1` FOREIGN KEY (`deltager_id`) REFERENCES `deltagere` (`id`) ON DELETE CASCADE,
      CONSTRAINT `deltagere_madtider_ibfk_2` FOREIGN KEY (`madtid_id`) REFERENCES `madtider` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `deltagere_tilmeldinger` (
      `deltager_id` int(11) NOT NULL,
      `prioritet` int(11) NOT NULL,
      `afvikling_id` int(11) NOT NULL,
      `tilmeldingstype` enum('spiller','spilleder') NOT NULL default 'spiller',
      PRIMARY KEY  (`deltager_id`,`prioritet`,`afvikling_id`),
      KEY `afvikling_id` (`afvikling_id`),
      CONSTRAINT `deltagere_tilmeldinger_ibfk_1` FOREIGN KEY (`deltager_id`) REFERENCES `deltagere` (`id`) ON DELETE CASCADE,
      CONSTRAINT `deltagere_tilmeldinger_ibfk_2` FOREIGN KEY (`afvikling_id`) REFERENCES `afviklinger` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `deltagere_wear` (
      `deltager_id` int(11) NOT NULL,
      `wearpris_id` int(11) NOT NULL,
      `antal` int(11) NOT NULL,
      `size` varchar(8) NOT NULL,
      `received` ENUM('t','f') NOT NULL DEFAULT 'f',
      PRIMARY KEY  (`deltager_id`,`wearpris_id`,`size`),
      KEY `wearpris_id` (`wearpris_id`),
      CONSTRAINT `deltagere_wear_ibfk_1` FOREIGN KEY (`wearpris_id`) REFERENCES `wearpriser` (`id`) ON DELETE CASCADE,
      CONSTRAINT `deltagere_wear_ibfk_2` FOREIGN KEY (`deltager_id`) REFERENCES `deltagere` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `deltagerearrangoerer` (
      `deltager_id` int(11) NOT NULL,
      `sovesal` enum('ja','nej') NOT NULL default 'nej',
      PRIMARY KEY  (`deltager_id`),
      CONSTRAINT `deltagerearrangoerer_ibfk_1` FOREIGN KEY (`deltager_id`) REFERENCES `deltagere` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
    ,<<<SQL
    CREATE TABLE `deltagereungdomsskole` (
      `deltager_id` int(11) NOT NULL,
      `ungdomsskole` varchar(128) NOT NULL,
      PRIMARY KEY  (`deltager_id`),
      CONSTRAINT `deltagereungdomsskole_ibfk_1` FOREIGN KEY (`deltager_id`) REFERENCES `deltagere` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL
);

$migration->down = array(
    "SET foreign_key_checks = 0",
    "DROP TABLE IF EXISTS version",
    "DROP TABLE IF EXISTS afviklinger",
    "DROP TABLE IF EXISTS afviklinger_multiblok",
    "DROP TABLE IF EXISTS aktiviteter",
    "DROP TABLE IF EXISTS brugerkategorier",
    "DROP TABLE IF EXISTS deltagere",
    "DROP TABLE IF EXISTS deltagere_gdstilmeldinger",
    "DROP TABLE IF EXISTS deltagere_gdsvagter",
    "DROP TABLE IF EXISTS deltagere_indgang",
    "DROP TABLE IF EXISTS deltagere_madtider",
    "DROP TABLE IF EXISTS deltagere_tilmeldinger",
    "DROP TABLE IF EXISTS deltagere_wear",
    "DROP TABLE IF EXISTS deltagerearrangoerer",
    "DROP TABLE IF EXISTS deltagereungdomsskole",
    "DROP TABLE IF EXISTS gds",
    "DROP TABLE IF EXISTS gdsvagter",
    "DROP TABLE IF EXISTS hold",
    "DROP TABLE IF EXISTS indgang",
    "DROP TABLE IF EXISTS log",
    "DROP TABLE IF EXISTS lokaler",
    "DROP TABLE IF EXISTS mad",
    "DROP TABLE IF EXISTS madtider",
    "DROP TABLE IF EXISTS pladser",
    "DROP TABLE IF EXISTS privileges",
    "DROP TABLE IF EXISTS roles",
    "DROP TABLE IF EXISTS roles_privileges",
    "DROP TABLE IF EXISTS smslog",
    "DROP TABLE IF EXISTS todoitems",
    "DROP TABLE IF EXISTS translations",
    "DROP TABLE IF EXISTS users",
    "DROP TABLE IF EXISTS users_roles",
    "DROP TABLE IF EXISTS videoer",
    "DROP TABLE IF EXISTS wear",
    "DROP TABLE IF EXISTS wearpriser",
    "SET foreign_key_checks = 1",
);
