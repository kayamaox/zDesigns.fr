<?php
if(!isset($_SESSION)){session_start();}

header("Content-Type: text/html; charset=utf-8");
ob_start('ob_gzhandler');

require_once("./inc/core.php");
require_once("./inc/functions.php");


/***************************************
 *             CONNEXION
 **************************************/
if(!isset($_SESSION['pseudo']) && isset($_COOKIE['pseudo']) && !empty($_COOKIE['pseudo'])){
    $_POST['json'] = 'cookies';
    $_POST['pseudo'] = $_COOKIE['pseudo'];
    $_POST['pass'] = $_COOKIE['pass'];
    include('./connexion.php');
    if($r['erreur_pass'] == 'ok'){
        $messageInfo = 'Bienvenue '.$_SESSION['pseudo'];
    }
}



if(isset($_SESSION['pseudo'])){
    $BDD->query("UPDATE membres
                 SET time_derniere_visite = '".time()."'
                 WHERE id='".$_SESSION['id']."'");
}

if(!isset($_SESSION['mobile'])){
    require_once('./inc/mobile_device_detect.php');
    $_SESSION['mobile'] = mobile_device_detect(true,false,true,true,true,true,true,false,false);
}

if(isset($titre) && $titre == "Forums"){
    $css[] = 'forum';
}

$titre = (isset($titre))?$titre.' :: zDesigns.fr':'zDesigns.fr';
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php echo $titre; ?></title>

        <link rel="shortcut icon" type="image/png" href="<?php echo ROOT; ?>favicon.png" />
        <link rel="icon" type="image/png" href="<?php echo ROOT; ?>favicon.png" />
        
        <link rel="stylesheet" href="<?php echo DESIGN_DIR.'css/design.css'; ?>" />
        <link rel="stylesheet" href="<?php echo ROOT; ?>zform/zcode.css" />
        <?php if(isset($js) && in_array('zoombox/zoombox', $js)){ ?>
        <link rel="stylesheet" href="<?php echo ROOT; ?>js/zoombox/zoombox.css" />
        <?php
        }
        if(isset($css)){
            foreach($css as $name){
                echo '<link rel="stylesheet" href="'.DESIGN_DIR.'css/'.$name.'.css" />';
            }
        }
        ?>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo ROOT; ?>js/jquery.easing.js"></script>
        <script type="text/javascript" src="<?php echo ROOT; ?>js/anim.js"></script>
        <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo ROOT; ?>js/flot/excanvas.js"></script><![endif]-->
        <?php
            if(isset($js)){
                foreach($js as $name){
                    echo '<script type="text/javascript" src="'.ROOT.'js/'.$name.'.js"></script>';
                }
            }
            if(isset($js) && in_array('zoombox/zoombox', $js)){ ?>
            <script type="text/javascript">
                $(function(){
                    $('a.zoombox').zoombox();
                    $.zoombox.options = {
                        theme       :'darkprettyphoto', //available themes : zoombox,lightbox, prettyphoto, darkprettyphoto, simple
                        opacity     : 0.6,              // Black overlay opacity
                        duration    : 800,              // Animation duration
                        animation   : true,             // Do we have to animate the box ?
                        width       : 600,              // Default width
                        height      : 400,              // Default height
                        gallery     : false,            // Allow gallery thumb view
                        autoplay    : false             // Autoplay for video
                    }
                });
            </script>
        <?php
        }
        
        if(isset($active_zcode) || isset($active_zform)){
            echo '<link rel="stylesheet" href="'.ROOT.'zform/zcode.css" />';
            
            if(isset($active_zform)){
                echo '
                    <script type="text/javascript" src="'.ROOT.'zform/js/textarea-tools.js" ></script>
                    <script type="text/javascript" src="'.ROOT.'zform/js/zcode.js" ></script>
                    
                    <link rel="stylesheet" href="'.ROOT.'zform/zform.css" />
                ';
            }
        }
        ?>

    </head>
    <body<?php if($_SESSION['mobile'] || !isset($_SESSION['pseudo'])){ echo ' class="mobile barre_bas_hidden"'; } elseif(droit_dacces(10)){ echo ' class="admin"'; } ?>>
        <div id="page">
            <div id="head">
                <div id="logo" class="fl">
                    <a href="<?php echo ROOT; ?>"><img src="<?php echo DESIGN_DIR; ?>images/logo.jpg" alt="Logo de zDesigns.fr" width="257" height="108" /></a>
                </div>
                <div id="menu" class="fl">
                    <div id="menu_in">
                        <a id="accueil" href="<?php echo ROOT; ?>">Accueil<br /><span>Bienvenue !</span></a>
                        <a id="galerie" href="<?php echo ROOT; ?>zdesigns.html">zDesigns<br /><span>La Galerie</span></a>
                        <a id="news" href="<?php echo ROOT; ?>news.html">Nouvelles<br /><span>Quoi de neuf ?</span></a>
                    </div>
                </div>
                <div id="logbox" class="fr">
                    <?php
                    if(isset($_SESSION['pseudo']) && $_SESSION['pseudo'] != null){
                        if(!isset($_SESSION['avatar'])){
                            $url_avatar = './images/avatars/'.$_SESSION['id'].'.png';
                            $_SESSION['avatar'] = (is_file($url_avatar)) ? $url_avatar : DESIGN_DIR.'images/no_avatar.jpg';
                        }
                        ?>
                        <div id="logboxIn">
                            <img id="avatarLogbox" class="fl" src="<?php echo $_SESSION['avatar']; ?>" alt="Votre avatar" width="50" />
                            <span id="pseudoLogbox"><?php echo $_SESSION['pseudo']; ?></span>
                            <a href="./deconnexion.html" title="Se déconnecter" class="deconnexion" rel="infobulle"><span>Se déconnecter</span></a>
                            <?php
                                if(droit_dacces(10))
								{
                                   /* $req_tchat = $BDD->query("SELECT id
                                                              FROM tchat
                                                              WHERE lu = '0'
                                                                AND ignorer = '0'");*/
									$nb_tchat = 0;
									$retour = $BDD->query("SELECT DISTINCT id_membre FROM tchat");
									while($donnees = mysql_fetch_array($retour))
									{
										
										$retour2 = $BDD->query("SELECT  membres.rang, tchat.ignorer, tchat.id_membre
																FROM tchat
																INNER JOIN membres
																ON(tchat.id_auteur = membres.id)
																AND tchat.id_membre = '".$donnees['id_membre']."' 
																ORDER BY tchat.id DESC
																LIMIT 1") OR DIE (mysql_error());
										while($don = mysql_fetch_array($retour2))
											if($don['rang']  != 10 AND $don['ignorer'] != 1)
												$nb_tchat++;
									}
                                }
								else 
								{
                                    $req_tchat = $BDD->query("SELECT id
                                                              FROM tchat
                                                              WHERE lu = '0'
                                                                AND ignorer = '0'
                                                                AND id_membre = '".$_SESSION['id']."'");
									$nb_tchat = mysql_num_rows($req_tchat);
                                }
                                
                            ?>
                            <a href="<?php echo ROOT; ?>tchat.html" id="lienTchat">Tchat Admins<span><?php echo pluralize($nb_tchat, '{Pas de|Un|{#}} nouveau{x} message{s}'); ?></span></a>
                        </div>
                    <?php } else { ?>
                        <div id="logboxIn">
                            <div id="logboxSlide">
                                <div class="ligne_logbox">
                                    <a href="<?php echo ROOT; ?>connexion.html" id="lienConnexion">Se Connecter<span>Créez et gérez vos zDesigns !</span></a>
                                    <a href="<?php echo ROOT; ?>inscription.html" id="lienInscription">S'Inscrire<span>Profitez pleinement de zDesigns.fr</span></a>
                                </div>
                                <div class="ligne_logbox">
                                    <a href="#" id="retourLogbox" class="fr">Retour</a>
                                    <form action="./connexion.html" method="post" id="login">
                                        <label for="pseudo" class="maskLabel">Pseudo</label>
                                        <input type="text" id="pseudo" value="Pseudo" onfocus="if(this.value == 'Pseudo'){ this.value = ''; }" onblur="if(this.value == ''){ this.value = 'Pseudo'; }" /><br/>
                                        <label for="password" class="maskLabel">Mot de passe</label>
                                        <input type="password" id="password" value="mot_de_passe" onfocus="if(this.value == 'mot_de_passe'){ this.value = ''; }" onblur="if(this.value == ''){ this.value = 'mot_de_passe'; }" /><br/>
                                        <input type="checkbox" name="auto_connect" id="connexion_auto" /><label for="connexion_auto">Connexion automatique</label>
                                        <input type="submit" value="Ok" />
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <br/>
                <div id="messageInfo" class="message">
                    <span class="loading">
                        <img src="<?php echo DESIGN_DIR; ?>images/loading.gif" alt="Chargement..." width="42" height="42" /><br/>
                        <span class="label">Chargement...</span>
                    </span>
                    <?php include('./inc/messages.php'); ?>
                </div>
            </div>
            <div id="ombre_g">
                <div id="ombre_d">
                    <div id="corps_out">
                        <div id="corps">
                            <div id="barre_bas_mobile" <?php if(!$_SESSION['mobile']){ echo 'class="dn"'; } ?>>
                                <?php if(isset($_SESSION['rang']) && $_SESSION['rang'] == 10){ 
                                    $req_tchat = $BDD->query("SELECT id FROM tchat WHERE lu = '0' AND ignorer = '0'");
                                    $req_valid = $BDD->query("SELECT id FROM designs WHERE active = '1'");
                                    $req_todo = $BDD->query("SELECT id FROM todolist WHERE statut != '3' AND membre_concerne = '".$_SESSION['id']."'");
                                    
                                    $nb_tchat = $nb_valid = $nb_todo = 0;
                                    $nb_tchat = mysql_num_rows($req_tchat);
                                    $nb_valid = mysql_num_rows($req_valid);
                                    $nb_todo = mysql_num_rows($req_todo);
                                    
                                    $somme_admin = $nb_tchat + $nb_valid + $nb_todo;
                                    ?>
                                    <a href="<?php echo ROOT; ?>admin.html">Admin (<?php echo $somme_admin; ?>)</a>
                                <?php } ?>|
                                <a href="<?php echo ROOT; ?>mon_compte.html">Mon Compte</a>|
                                <a href="<?php echo ROOT; ?>deconnexion.html">Deconnexion</a>|
                                <a href="<?php echo ROOT; ?>mes_zdesigns.html">Mes zDesigns</a>
                            </div>