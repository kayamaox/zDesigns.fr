zDesigns.fr
===========

Découvrez ici toutes les sources du site zDesigns.fr.


# Installation

- Clonez ce dépôt chez vous
- Créez une base de donnée dont le nom est indiqué dans /classes/bdd.php à la ligne 8 (mysql_select_db)
- Importez le code SQL que vous trouverez plus bas
- Allez sur le site que vous venez d'installer, c'est parti !


# Configuration
- Tout se trouve dans /inc/core.php


# La base de donnée

    -- 
    -- Structure de la table ` antiflood ` 
    --  

    CREATE TABLE `antiflood` (
      `id_pseudo` smallint(2) unsigned NOT NULL,
      `IP` varchar(15) character set utf8 NOT NULL,
      `timestamp` int(4) unsigned NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Structure de la table ` bbcode ` 
    --  

    CREATE TABLE `bbcode` (
      `id` smallint(2) unsigned NOT NULL auto_increment,
      `preg` varchar(255) collate latin1_german2_ci NOT NULL default '',
      `replace` varchar(255) collate latin1_german2_ci NOT NULL default '',
      UNIQUE KEY `id` (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Contenu de la table ` bbcode` 
    --  

    INSERT INTO bbcode VALUES ('1','#\\&lt;couleur=(.+)\\&gt;(.+)\\&lt;\\/couleur\\&gt;#isU','<span class=''$1''>$2</span>');
    INSERT INTO bbcode VALUES ('5','#\\&lt;souligne\\&gt;(.+)\\&lt;/souligne\\&gt;#isU','<span class="souligne">$1</span>');
    INSERT INTO bbcode VALUES ('3','#\\&lt;gras\\&gt;(.+)\\&lt;/gras\\&gt;#isU','<span class=''gras''>$1</span>');
    INSERT INTO bbcode VALUES ('4','#\\&lt;italique\\&gt;(.+)\\&lt;/italique\\&gt;#isU','<span class="italique">$1</span>');
    INSERT INTO bbcode VALUES ('6','#\\&lt;barre\\&gt;(.+)\\&lt;/barre\\&gt;#isU','<span class="barre">$1</span>');
    INSERT INTO bbcode VALUES ('7','#\\&lt;citation=(.+)\\&gt;(.+)\\&lt;/citation\\&gt;#isU','<span class="citation">Citation :$1</span><div class="citation2">$2</div>');
    INSERT INTO bbcode VALUES ('8','#\\&lt;image\\&gt;(.+)\\&lt;/image\\&gt;#isU','<img src=''$1'' alt=''image poster par utilisateur'' />');
    INSERT INTO bbcode VALUES ('9','#\\&lt;lien=(.+)\\&gt;(.+)\\&lt;/lien\\&gt;#isU','<a href="$1">$2</a>');
    INSERT INTO bbcode VALUES ('10','#\\&lt;email=(.+)\\&gt;(.+)\\&lt;/email\\&gt;#isU','<a href="mailto:$1">$2</a>');
    INSERT INTO bbcode VALUES ('11','#\\&lt;position=(.+)\\&gt;(.+)\\&lt;/position\\&gt;#isU','<div class=''$1''>$2</div>');
    INSERT INTO bbcode VALUES ('12','#\\&lt;taille=(.+)\\&gt;(.+)\\&lt;/taille\\&gt;#isU','<span class="$1">$2</span>');
    INSERT INTO bbcode VALUES ('13','#\\&lt;police=(.+)\\&gt;(.+)\\&lt;/police\\&gt;#isU','<span class="$1">$2</span>');
    INSERT INTO bbcode VALUES ('14','#\\:\\)#isU','<img src="./images/smilies/smile.png" alt="smiley" />');
    INSERT INTO bbcode VALUES ('15','#\\:D#isU','<img src="./images/smilies/heureux.png" alt=''smiley'' />');
    INSERT INTO bbcode VALUES ('16','#\\:p#isU','<img src="./images/smilies/langue.png" alt=''smiley'' />');
    INSERT INTO bbcode VALUES ('17','#;\\)#isU','<img src="./images/smilies/clin.png" alt=''smiley'' />');
    INSERT INTO bbcode VALUES ('18','#:lol:#isU','<img src="./images/smilies/rire.gif" alt=''smiley'' />');
    INSERT INTO bbcode VALUES ('19','#:euh:#isU','<img src="./images/smilies/unsure.gif" alt=''smiley'' />');
    INSERT INTO bbcode VALUES ('20','#:\\(#isU','<img src="./images/smilies/triste.png" alt=''smiley'' />');
    INSERT INTO bbcode VALUES ('21','#:o#isU','<img src="./images/smilies/huh.png" alt=''smiley'' />');
    INSERT INTO bbcode VALUES ('22','#:colere:#isU','<img src="./images/smilies/mechant.png" alt=''smiley'' />');
    INSERT INTO bbcode VALUES ('23','#o_O#isU','<img src="./images/smilies/blink.gif" alt=''smiley'' />');
    INSERT INTO bbcode VALUES ('24','#\\^\\^#isU','<img src="./images/smilies/hihi.png" alt=''smiley'' />');
    INSERT INTO bbcode VALUES ('25','#::\\-?#isU','<img src="./images/smilies/siffle.png" alt=''smiley'' />');

    -- 
    -- Structure de la table ` cat ` 
    --  

    CREATE TABLE `cat` (
      `id` smallint(2) unsigned NOT NULL auto_increment,
      `nom` varchar(250) collate latin1_german2_ci NOT NULL,
      `ordre` tinyint(1) unsigned NOT NULL,
      `image` varchar(250) collate latin1_german2_ci NOT NULL,
      `couleur` varchar(7) collate latin1_german2_ci NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Contenu de la table ` cat` 
    --  

    INSERT INTO cat VALUES ('1','Système d''exploitation','3','','');
    INSERT INTO cat VALUES ('2','Nature','4','','');
    INSERT INTO cat VALUES ('3','TV / cinéma','5','','');
    INSERT INTO cat VALUES ('4','Sites connus','1','','');
    INSERT INTO cat VALUES ('5','Géographie','2','','');
    INSERT INTO cat VALUES ('7','Gothique','10','','');
    INSERT INTO cat VALUES ('8','Divers','12','','');
    INSERT INTO cat VALUES ('9','Couleurs','9','','');
    INSERT INTO cat VALUES ('10','Fantaisiste','8','','');
    INSERT INTO cat VALUES ('16','Alimentaire','11','','');
    INSERT INTO cat VALUES ('17','Jeux vidéo','6','','');
    INSERT INTO cat VALUES ('18','Evenements','7','','');

    -- 
    -- Structure de la table ` com_zdesigns ` 
    --  

    CREATE TABLE `com_zdesigns` (
      `id` smallint(2) unsigned NOT NULL auto_increment,
      `id_design` smallint(2) unsigned NOT NULL,
      `id_membre` smallint(2) unsigned NOT NULL,
      `pseudo` varchar(30) character set utf8 NOT NULL,
      `texte` text character set utf8 NOT NULL,
      `timestamp` int(4) unsigned NOT NULL,
      `visible` enum('0','1') character set utf8 NOT NULL default '1',
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=latin1; 

    -- 
    -- Structure de la table ` connectes ` 
    --  

    CREATE TABLE `connectes` (
      `id_pseudo` smallint(2) unsigned NOT NULL,
      `timestamp` int(4) unsigned NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Structure de la table ` cron ` 
    --  

    CREATE TABLE `cron` (
      `id` mediumint(3) NOT NULL auto_increment,
      `ip` varchar(16) collate latin1_german2_ci NOT NULL,
      `time` int(4) unsigned NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1338 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 


    -- 
    -- Structure de la table ` designs ` 
    --  

    CREATE TABLE `designs` (
      `id` int(4) unsigned NOT NULL auto_increment,
      `id_membre` smallint(2) NOT NULL,
      `titre` varchar(250) collate latin1_german2_ci NOT NULL,
      `imprim` varchar(50) collate latin1_german2_ci NOT NULL,
      `mini_imprim` varchar(50) collate latin1_german2_ci NOT NULL,
      `active` enum('0','1','2','3') collate latin1_german2_ci NOT NULL,
      `vu` smallint(2) unsigned NOT NULL,
      `date` int(4) unsigned NOT NULL,
      `date_suppr` int(4) unsigned NOT NULL default '0',
      `description` varchar(600) collate latin1_german2_ci NOT NULL,
      `id_cat` tinyint(1) unsigned NOT NULL,
      `complet` tinyint(1) unsigned NOT NULL default '0',
      `note` float unsigned NOT NULL default '0',
      `note_points` smallint(2) unsigned NOT NULL default '0',
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=879 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Structure de la table ` designs_votes ` 
    --  

    CREATE TABLE `designs_votes` (
      `id` mediumint(3) unsigned NOT NULL auto_increment,
      `id_design` smallint(2) unsigned NOT NULL,
      `note` tinyint(1) unsigned NOT NULL,
      `ip` varchar(25) collate latin1_german2_ci NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=106 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Structure de la table ` forum_cat ` 
    --  

    CREATE TABLE `forum_cat` (
      `id` int(20) NOT NULL auto_increment,
      `titre` varchar(300) collate latin1_german2_ci NOT NULL,
      `ordre` int(3) NOT NULL,
      `rang_dacces` int(3) NOT NULL,
      `rang_decriture` int(3) NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Contenu de la table ` forum_cat` 
    --  

    INSERT INTO forum_cat VALUES ('1','zDesigns','4','0','0');
    INSERT INTO forum_cat VALUES ('2','Communauté','5','0','0');
    INSERT INTO forum_cat VALUES ('3','Le site','6','0','0');
    INSERT INTO forum_cat VALUES ('4','Administration','1','10','0');
    INSERT INTO forum_cat VALUES ('5','Modération','3','9','0');
    INSERT INTO forum_cat VALUES ('6','Développeurs','2','8','0');

    -- 
    -- Structure de la table ` forum_forum ` 
    --  

    CREATE TABLE `forum_forum` (
      `id` int(20) NOT NULL auto_increment,
      `id_cat` int(20) NOT NULL,
      `titre` varchar(100) collate latin1_german2_ci NOT NULL,
      `description` varchar(300) collate latin1_german2_ci NOT NULL,
      `ordre` int(3) NOT NULL,
      `rang_dacces` int(3) NOT NULL,
      `rang_decriture` int(3) NOT NULL,
      KEY `id` (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Contenu de la table ` forum_forum` 
    --  

    INSERT INTO forum_forum VALUES ('1','1','Vos zDesigns','Montrez vos début de zDesigns et faites vous commenter !','1','0','1');
    INSERT INTO forum_forum VALUES ('2','2','Le bar','Discutez de ce que vous voulez ici','1','0','1');
    INSERT INTO forum_forum VALUES ('3','3','Développement du site','Suivez toute l\\''évolution du site.','1','0','1');
    INSERT INTO forum_forum VALUES ('4','3','Suggestions et commentaires','Mettez ici toutes vos idées, vos suggestions ou commentaires sur le site.','2','0','1');
    INSERT INTO forum_forum VALUES ('5','3','Rapport de bugs','Vous avez trouvez un bug ? Postez-le ici.','3','0','1');
    INSERT INTO forum_forum VALUES ('6','4','Développement','Développement du site ','1','10','10');
    INSERT INTO forum_forum VALUES ('7','5','Modération','Parler de la modération ! Ce forum vous est réservé.','1','9','9');
    INSERT INTO forum_forum VALUES ('8','6','Développement','Forum reservé aux développeurs','1','8','8');

    -- 
    -- Structure de la table ` forum_reponse ` 
    --  

    CREATE TABLE `forum_reponse` (
      `id` int(20) NOT NULL auto_increment,
      `id_sujet` int(20) NOT NULL,
      `id_pseudo` int(15) NOT NULL,
      `bbcode` longtext NOT NULL,
      `html` longtext NOT NULL,
      `initiale` tinyint(1) NOT NULL,
      `timestamp` int(20) NOT NULL,
      `timestamp_modif` int(20) NOT NULL,
      `id_pseudo_modif` int(20) NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=886 DEFAULT CHARSET=utf8; 

    -- 
    -- Structure de la table ` forum_suivre_topic ` 
    --  

    CREATE TABLE `forum_suivre_topic` (
      `id_topic` int(15) NOT NULL,
      `id_membre` int(15) NOT NULL,
      `envoye` enum('0','1') collate latin1_german2_ci NOT NULL,
      `id_reponse` int(15) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Structure de la table ` forum_sujet ` 
    --  

    CREATE TABLE `forum_sujet` (
      `id` int(20) NOT NULL auto_increment,
      `titre` varchar(250) collate latin1_german2_ci NOT NULL,
      `sous_titre` varchar(250) collate latin1_german2_ci NOT NULL,
      `type` enum('1','2') collate latin1_german2_ci NOT NULL,
      `id_forum` int(20) NOT NULL,
      `id_membre` int(20) NOT NULL,
      `ferme` enum('0','1') collate latin1_german2_ci NOT NULL,
      `resolu` enum('0','1') collate latin1_german2_ci NOT NULL default '0',
      `time` int(20) NOT NULL,
      `vu` int(5) NOT NULL,
      `rang_dacces` int(3) NOT NULL,
      `rang_decriture` int(3) NOT NULL,
      `timestamp_modif` int(20) NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Structure de la table ` livreor ` 
    --  

    CREATE TABLE `livreor` (
      `id` int(20) NOT NULL auto_increment,
      `id_membre` varchar(250) collate latin1_german2_ci NOT NULL,
      `pseudo` varchar(250) collate latin1_german2_ci NOT NULL,
      `message` text collate latin1_german2_ci NOT NULL,
      `date` varchar(100) collate latin1_german2_ci NOT NULL,
      `visible` enum('0','1') character set utf8 NOT NULL default '1',
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=296 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Structure de la table ` membres ` 
    --  

    CREATE TABLE `membres` (
      `id` int(11) NOT NULL auto_increment,
      `pseudo` varchar(250) collate latin1_german2_ci NOT NULL,
      `rid` int(20) NOT NULL,
      `mdp` varchar(250) collate latin1_german2_ci NOT NULL,
      `mail` varchar(250) collate latin1_german2_ci NOT NULL,
      `rang` int(3) NOT NULL,
      `time_inscrit` int(20) NOT NULL,
      `time_derniere_visite` int(20) NOT NULL,
      `icq` varchar(50) collate latin1_german2_ci NOT NULL,
      `msn` varchar(50) collate latin1_german2_ci NOT NULL,
      `aim` varchar(50) collate latin1_german2_ci NOT NULL,
      `jabber` varchar(50) collate latin1_german2_ci NOT NULL,
      `yahoo` varchar(50) collate latin1_german2_ci NOT NULL,
      `skype` varchar(50) collate latin1_german2_ci NOT NULL,
      `autorise` enum('0','1') collate latin1_german2_ci NOT NULL,
      `naissance` int(20) NOT NULL,
      `web` varchar(250) collate latin1_german2_ci NOT NULL,
      `ville` varchar(250) collate latin1_german2_ci NOT NULL,
      `etudes` varchar(250) collate latin1_german2_ci NOT NULL,
      `travail` varchar(250) collate latin1_german2_ci NOT NULL,
      `passions` varchar(500) collate latin1_german2_ci NOT NULL,
      `temp_vert` int(11) NOT NULL default '3',
      `pourcentage` int(3) NOT NULL default '0',
      `tchat` tinyint(1) NOT NULL default '1',
      `verif` varchar(11) collate latin1_german2_ci NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=396 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Structure de la table ` message_verrou ` 
    --  

    CREATE TABLE `message_verrou` (
      `id` int(15) NOT NULL auto_increment,
      `titre` varchar(250) collate latin1_german2_ci NOT NULL,
      `html` text collate latin1_german2_ci NOT NULL,
      `bbcode` text collate latin1_german2_ci NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Structure de la table ` messages ` 
    --  

    CREATE TABLE `messages` (
      `id` int(5) NOT NULL auto_increment,
      `message` varchar(300) collate latin1_german2_ci NOT NULL,
      `type` enum('0','1') collate latin1_german2_ci NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=164 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Contenu de la table ` messages` 
    --  

    INSERT INTO messages VALUES ('1','Vous êtes maintenant connecté(e).','0');
    INSERT INTO messages VALUES ('2','Vous venez de vous deconnecter.','0');
    INSERT INTO messages VALUES ('3','La demande de validation a été prise en compte.','0');
    INSERT INTO messages VALUES ('4','Vous n''avez pas le droit d''être ici !','1');
    INSERT INTO messages VALUES ('6','Vous n''êtes pas connecté !','1');
    INSERT INTO messages VALUES ('5','Cette action n''existe pas !','1');
    INSERT INTO messages VALUES ('7','Ce zDesign n''existe pas !','1');
    INSERT INTO messages VALUES ('8','Ce zDesign ne vous appartiens pas !','1');
    INSERT INTO messages VALUES ('9','Ce répertoire n''existe pas !','1');
    INSERT INTO messages VALUES ('10','Le répertoire hôte n''existe pas !','1');
    INSERT INTO messages VALUES ('11','Le fichier à bien été ajouté.','0');
    INSERT INTO messages VALUES ('12','Rien a été poste !','1');
    INSERT INTO messages VALUES ('13','Une erreur est survenue lors de l''ajout du fichier !','1');
    INSERT INTO messages VALUES ('14','Votre zDesigns est déjà validé !','1');
    INSERT INTO messages VALUES ('15','Le fichier CSS à bien été ajouté.','0');
    INSERT INTO messages VALUES ('16','Aucun fichier n''est selectionné !','1');
    INSERT INTO messages VALUES ('17','Ce nom de fichier n''est pas correcte !','1');
    INSERT INTO messages VALUES ('18','Le fichier a bien été modifié.','0');
    INSERT INTO messages VALUES ('19','Le nom du fichier à renommer n''est pas correcte !','1');
    INSERT INTO messages VALUES ('20','Le fichier a bien été renommé.','0');
    INSERT INTO messages VALUES ('21','Le nom n''a pas changé.','0');
    INSERT INTO messages VALUES ('22','Ce fichier CSS existe déjà !','1');
    INSERT INTO messages VALUES ('23','Ce fichier existe déjà !','1');
    INSERT INTO messages VALUES ('24','Un fichier portant ce nom existe déjà !','1');
    INSERT INTO messages VALUES ('25','Le fichier a bien été supprimé.','0');
    INSERT INTO messages VALUES ('26','Aucun dossier n''est selectionné !','1');
    INSERT INTO messages VALUES ('27','Ce nom de dossier n''est pas correcte !','1');
    INSERT INTO messages VALUES ('28','Le dossier a correctement été renommé.','0');
    INSERT INTO messages VALUES ('29','Ce nom de dossier existe déjà !','1');
    INSERT INTO messages VALUES ('30','Le dossier a bien été supprimé.','0');
    INSERT INTO messages VALUES ('31','Le dossier a bien été ajouté.','0');
    INSERT INTO messages VALUES ('32','Le fichier a bien été uploadé.','0');
    INSERT INTO messages VALUES ('33','Une erreur interne est survenue !','1');
    INSERT INTO messages VALUES ('34','Le fichier est trop lourd !','1');
    INSERT INTO messages VALUES ('35','Le fichier n''est pas une archive zippée !','1');
    INSERT INTO messages VALUES ('36','Le fichier est introuvable !','1');
    INSERT INTO messages VALUES ('37','Vous n''avez fait aucune demande !','1');
    INSERT INTO messages VALUES ('38','Le zDesign a bien été supprimé.','0');
    INSERT INTO messages VALUES ('39','La dévalidation du zDesign a été prise en compte.','0');
    INSERT INTO messages VALUES ('40','La rétraction de la validation a été prise en compte.','0');
    INSERT INTO messages VALUES ('41','Vous n''avez pas choisi de catégorie !','1');
    INSERT INTO messages VALUES ('42','Cette catégorie n''existe pas !','1');
    INSERT INTO messages VALUES ('43','La demande de validation a été prise en compte.','0');
    INSERT INTO messages VALUES ('44','Votre mot de passe est erroné!','1');
    INSERT INTO messages VALUES ('45','Votre pseudo n''existe pas !','1');
    INSERT INTO messages VALUES ('46','Il faut remplir tous les champs !','1');
    INSERT INTO messages VALUES ('47','Droits incorrects !','1');
    INSERT INTO messages VALUES ('48','La description a bien été mise à jour.','0');
    INSERT INTO messages VALUES ('49','Le fichier n''est pas dans un bon format !','1');
    INSERT INTO messages VALUES ('50','Le fichier n''est pas une image ou est un fichier bmp !','1');
    INSERT INTO messages VALUES ('51','La description est trop longue !','1');
    INSERT INTO messages VALUES ('52','Votre message a été pris en compte ! Merci ! ;-)','0');
    INSERT INTO messages VALUES ('53','Vous voilà inscrit ! Connectez-vous maintenant !','0');
    INSERT INTO messages VALUES ('54','Votre mot de passe n''est pas rempli !','1');
    INSERT INTO messages VALUES ('55','Votre mot de passe de confirmation n''est pas rempli !','1');
    INSERT INTO messages VALUES ('56','Votre mot de passe diffère de votre mot de passe de confirmation !','1');
    INSERT INTO messages VALUES ('57','Votre adresse e-mail n''est pas remplie !','1');
    INSERT INTO messages VALUES ('58','Votre adresse mail est déjà utilisé par un membre !','1');
    INSERT INTO messages VALUES ('59','Votre adresse mail n''a pas un format valide !','1');
    INSERT INTO messages VALUES ('60','Vous ne pouvez pas vous inscrire car vous êtes déja connecté !','1');
    INSERT INTO messages VALUES ('61','Votre pseudo n''est pas rempli !','1');
    INSERT INTO messages VALUES ('62','Votre pseudo est déjà utilisé par un membre !','1');
    INSERT INTO messages VALUES ('63','Votre adresse mail de confirmation n''est pas remplie !','1');
    INSERT INTO messages VALUES ('64','Votre adresse mail diffère de votre adresse mail de confirmation !','1');
    INSERT INTO messages VALUES ('65','Le code de vérification n''est pas rempli !','1');
    INSERT INTO messages VALUES ('66','Le code de vérification n''est pas bon !','1');
    INSERT INTO messages VALUES ('67','Vous n''avez pas donner de note au site !','1');
    INSERT INTO messages VALUES ('68','La note entrée n''est pas comprise entre 1 et 10 !','1');
    INSERT INTO messages VALUES ('69','Le nouveau message a bien été ajouté.','0');
    INSERT INTO messages VALUES ('70','Aucun message n''est selectionné !','1');
    INSERT INTO messages VALUES ('71','Ce message n''existe pas !','1');
    INSERT INTO messages VALUES ('72','Le message a bien été supprimé.','0');
    INSERT INTO messages VALUES ('73','Le message a bien été mis à jour.','0');
    INSERT INTO messages VALUES ('74','Le post-it a bien été supprimé.','0');
    INSERT INTO messages VALUES ('75','Le post-it a bien été modifié.','0');
    INSERT INTO messages VALUES ('76','Le post-it a bien été ajouté.','0');
    INSERT INTO messages VALUES ('77','L''ordre des post-it a bien été "lissé".','0');
    INSERT INTO messages VALUES ('78','L''ordre des post-it a bien été changé.','0');
    INSERT INTO messages VALUES ('79','Aucune catégorie n''est séléctionnée !','1');
    INSERT INTO messages VALUES ('80','La catégorie a bien été supprimée.','0');
    INSERT INTO messages VALUES ('81','Le message du livre d''or a bien été modifié.','0');
    INSERT INTO messages VALUES ('82','Le message du livre d''or a bien été supprimé.','0');
    INSERT INTO messages VALUES ('83','La catégorie a bien été mise à jour.','0');
    INSERT INTO messages VALUES ('84','La catégorie a bien été ajoutée.','0');
    INSERT INTO messages VALUES ('85','Ce zDesign n''appartient pas à ce membre !','1');
    INSERT INTO messages VALUES ('86','Ce zdesign a bien été supprimé.','0');
    INSERT INTO messages VALUES ('87','Ce zdesign a bien été accepté.','0');
    INSERT INTO messages VALUES ('88','Ce zdesign a bien été refusé.','0');
    INSERT INTO messages VALUES ('89','Aucun zDesign n''est selectionné !','1');
    INSERT INTO messages VALUES ('90','Vous allez être redirigé sur le site du zéro.','0');
    INSERT INTO messages VALUES ('91','Le nouveau zDesign a bien été créé.','0');
    INSERT INTO messages VALUES ('92','Votre message n''a pas une arborescence correcte !','1');
    INSERT INTO messages VALUES ('93','Il vous faut donner un titre à la news !','1');
    INSERT INTO messages VALUES ('94','La news à bien été ajoutée.','0');
    INSERT INTO messages VALUES ('95','Aucune news ne correspond à ce numéro !','1');
    INSERT INTO messages VALUES ('96','Aucune news n''est séléctionnée !','1');
    INSERT INTO messages VALUES ('97','La news a bien été éditée.','0');
    INSERT INTO messages VALUES ('98','La news a bien été supprimée.','0');
    INSERT INTO messages VALUES ('99','Ce forum n''existe pas !','1');
    INSERT INTO messages VALUES ('100','Aucun forum n''est selectionné !','1');
    INSERT INTO messages VALUES ('101','Aucun forum ne correspond à ce numéro !','1');
    INSERT INTO messages VALUES ('102','Aucune catégorie ne correspond à ce numéro !','1');
    INSERT INTO messages VALUES ('103','Vous n''avez pas le droit de visualiser ce forum !','1');
    INSERT INTO messages VALUES ('104','Vous n''avez pas le droit d''écrire sur ce forum !','1');
    INSERT INTO messages VALUES ('105','Vous devez rentrer un titre pour votre nouveau topic !','1');
    INSERT INTO messages VALUES ('106','Votre titre est trop long !','1');
    INSERT INTO messages VALUES ('107','Votre sous-titre est trop long !','1');
    INSERT INTO messages VALUES ('108','Votre nouveau topic à été ajouté.','0');
    INSERT INTO messages VALUES ('109','Aucun sujet n''est selectionné !','1');
    INSERT INTO messages VALUES ('110','Aucun sujet ne correspond à ce numéro !','1');
    INSERT INTO messages VALUES ('111','Vous n''avez pas le droit de visualiser ce forum !','1');
    INSERT INTO messages VALUES ('112','Vous n''avez pas le droit de lire ce sujet !','1');
    INSERT INTO messages VALUES ('113','Vous n''avez pas le droit d''écrire dans ce forum !','1');
    INSERT INTO messages VALUES ('114','Ce sujet est fermé ! ','1');
    INSERT INTO messages VALUES ('115','Votre réponse a bien été ajoutée.','0');
    INSERT INTO messages VALUES ('116','Cette réponse n''existe pas  !','1');
    INSERT INTO messages VALUES ('117','Vous n''avez pas le droit d''éditer cette réponse !','1');
    INSERT INTO messages VALUES ('118','Le message a bien été mis à jour.','0');
    INSERT INTO messages VALUES ('119','Ce sujet est maintenant notifié.','0');
    INSERT INTO messages VALUES ('120','Ce sujet a déjà été notifié !','1');
    INSERT INTO messages VALUES ('121','Ce sujet n''est maintenant plus notifié.','0');
    INSERT INTO messages VALUES ('122','Ce sujet n''est pas notifié, vous ne pous pas effectuer cette action !','1');
    INSERT INTO messages VALUES ('123','Vous ne pouvez pas poster deux fois d''affilée en 24h !','1');
    INSERT INTO messages VALUES ('124','Une erreur interne est survenue !','1');
    INSERT INTO messages VALUES ('125','Doucement !  (Contrôle antiflood 20sec)','1');
    INSERT INTO messages VALUES ('126','Ce message a été résolu.','0');
    INSERT INTO messages VALUES ('127','Ce message n''est plus résolu.','0');
    INSERT INTO messages VALUES ('128','Ce sujet ne vous appartient pas !','1');
    INSERT INTO messages VALUES ('129','Ce topic est déjà résolu !','1');
    INSERT INTO messages VALUES ('130','Ce topic n''est pas résolu !','1');
    INSERT INTO messages VALUES ('131','Vous devez remplir tous les champs !','1');
    INSERT INTO messages VALUES ('132','Votre message à bien été envoyé.','0');
    INSERT INTO messages VALUES ('133','Le messages de vérouillage a été ajouté avec succés.','0');
    INSERT INTO messages VALUES ('134','Vous n''avez pas les droits necéssaires !','1');
    INSERT INTO messages VALUES ('135','Ce sujet est déjà verrouiller !','1');
    INSERT INTO messages VALUES ('136','Ce sujet a bien été fermé.','0');
    INSERT INTO messages VALUES ('137','Ce sujet n''est pas verrouiller !','1');
    INSERT INTO messages VALUES ('138','Ce sujet a bien été déverrouiller.','0');
    INSERT INTO messages VALUES ('139','Le titre que vous avez choisi est trop long !','1');
    INSERT INTO messages VALUES ('140','Le titre de votre zDesign a bien été modifié.','0');
    INSERT INTO messages VALUES ('141','La taille de votre zDesign à atteind le maximum !','1');
    INSERT INTO messages VALUES ('142','Votre compte a bien été modifié.','0');
    INSERT INTO messages VALUES ('143','Votre zDesign contient trop d''erreurs !','1');
    INSERT INTO messages VALUES ('144','Vous devez ajouter un aperçu de votre zDesign!','1');
    INSERT INTO messages VALUES ('145','Vous devez ajouter une description !','1');
    INSERT INTO messages VALUES ('146','Votre pack design a bien été importé.','0');
    INSERT INTO messages VALUES ('147','Ce membre n''existe pas !','1');
    INSERT INTO messages VALUES ('148','Aucun membre n''est selectionné !','1');
    INSERT INTO messages VALUES ('149','Le nom du fichier à copier n''est pas correct !','1');
    INSERT INTO messages VALUES ('150','Le fichier à copier existe déjà dans votre répertoire !','1');
    INSERT INTO messages VALUES ('151','Le fichier a bien été ajouté à votre dossier.','0');
    INSERT INTO messages VALUES ('152','Les fichiers ont bien été placés dans le presse-papier.','0');
    INSERT INTO messages VALUES ('153','Aucun fichier n''a été selectionné !','1');
    INSERT INTO messages VALUES ('154','Aucun éléments dans le presse-papier !','1');
    INSERT INTO messages VALUES ('155','Les éléments ont bien été copié.','0');
    INSERT INTO messages VALUES ('156','Votre adresse e-mail n''existe pas !','1');
    INSERT INTO messages VALUES ('157','Votre adresse e-mail n''existe pas dans la table ou le code de vérification est faux.','1');
    INSERT INTO messages VALUES ('158','Vous êtes déjà connecté !','1');
    INSERT INTO messages VALUES ('159','Un e-mail avec des explications vient de vous être envoyé.','0');
    INSERT INTO messages VALUES ('160','Il manque des paramètres pour cette page !','1');
    INSERT INTO messages VALUES ('161','Un nouveau mot de passe vient de vous être envoyé par mail.','0');
    INSERT INTO messages VALUES ('162','Le zExplorer est fermé pour cause de migration vers une V2 (prévue dimanche soir 20 Mars)','1');
    INSERT INTO messages VALUES ('163','En cours de construction','1');

    -- 
    -- Structure de la table ` news ` 
    --  

    CREATE TABLE `news` (
      `id` smallint(2) unsigned NOT NULL auto_increment,
      `titre` varchar(300) character set utf8 collate utf8_unicode_ci NOT NULL,
      `bbcode` text character set utf8 collate utf8_unicode_ci NOT NULL,
      `html` text character set utf8 collate utf8_unicode_ci NOT NULL,
      `timestamp` int(4) unsigned NOT NULL,
      `id_pseudo` smallint(2) unsigned NOT NULL,
      `timestamp_modif` int(4) unsigned NOT NULL,
      `id_pseudo_modif` smallint(2) unsigned NOT NULL,
      `visible` enum('0','1') collate latin1_german2_ci NOT NULL default '1',
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Structure de la table ` tchat ` 
    --  

    CREATE TABLE `tchat` (
      `id` mediumint(3) unsigned NOT NULL auto_increment,
      `id_membre` smallint(2) unsigned NOT NULL,
      `id_auteur` smallint(2) unsigned NOT NULL,
      `message` text collate latin1_german2_ci NOT NULL,
      `timestamp` int(4) unsigned NOT NULL,
      `lu` enum('0','1') collate latin1_german2_ci NOT NULL default '0',
      `ignorer` tinyint(1) unsigned NOT NULL default '0',
      KEY `id` (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=818 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 

    -- 
    -- Structure de la table ` todolist ` 
    --  

    CREATE TABLE `todolist` (
      `id` smallint(2) unsigned NOT NULL auto_increment,
      `nom` text collate latin1_german2_ci NOT NULL,
      `ordre` smallint(2) unsigned NOT NULL,
      `statut` enum('1','2','3') collate latin1_german2_ci NOT NULL,
      `membre_concerne` smallint(2) unsigned NOT NULL,
      `date_ajout` int(4) unsigned NOT NULL,
      KEY `id` (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=165 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci; 