<?php session_cache_limiter('private');    
session_regenerate_id();  
session_start();
ob_start(); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s" ) . " GMT" ); // Je pense que ça te fera perdre tes données
header("Pragma: no-cache");  //Aucun cache, ça tu garde, c'est la seule qu'il faut absolument que tu garde !
//==========================================================
//INTERDICTION DE METTRE DE L'HTML DANS CE FICHIER !!!!!!!!
//==========================================================
$chronotemps = microtime();
$chronotemps = explode(' ', $chronotemps);
$chronodebut = $chronotemps[1] + $chronotemps[0];
//
include("./includes/identifiants.php");
mysql_connect($adresse, $nom, $motdepasse);
mysql_select_db($database);
// 
require_once("./includes/config.php");
require_once("./includes/fonctions.php");
//SECURISATION DES GET ET POST PASSES PAR ADRESSE
if(isset($_GET) && is_array($_GET))
    foreach($_GET as $key => $value){
        if(ini_get(register_globals))$value=stripslashes($value);
        $value=mysql_real_escape_string(stripslashes($value)); //htmlentities    ,ENT_QUOTES
        $_GET[$key]=$value;
        ${$key}=$value;
    }
if($noprotect == false)
	if(isset($_POST) && is_array($_POST))
		foreach($_POST as $key => $value)
		{
			if(ini_get(register_globals))$value=stripslashes($value);
			$value=mysql_real_escape_string(stripslashes($value)); //htmlentities    ,ENT_QUOTES
			$_POST[$key]=$value;
			${$key}=$value;
		}
//FIN DE SECURISATION DES GET ET POST PASSES PAR ADRESSE

//connexion auto :
if( !empty( $_COOKIE['pseudo'] ) && !empty( $_COOKIE['pass'] ) && empty( $_SESSION['pseudo']))
{
     $sql_count = mysql_query( 'SELECT COUNT(*) FROM membres WHERE pseudo = \'' . $_COOKIE['pseudo'] . '\' AND mdp = \'' . $_COOKIE['pass'] . '\'' ) or die(mysql_error());
     if( mysql_result( $sql_count, 0 ) == 1 )
     {
		
		$pseudo = stripslashes($_COOKIE['pseudo']);
		$_SESSION['pseudo'] = $pseudo;
		$requete1 = mysql_query(' SELECT  id, rang, tchat FROM membres WHERE pseudo = "'.$pseudo.'" ') or die    (mysql_error());
		$data1 = mysql_fetch_assoc($requete1);
		{
			$_SESSION['rang'] = $data1['rang'];
			$_SESSION['id'] = $data1['id'];
			$_SESSION['tchat'] = $data1['tchat'];
		}
		$time= time();
		$pseudo = $_SESSION['pseudo'];
		mysql_query('UPDATE membres SET time_derniere_visite="' . $time . '" WHERE pseudo="' . $pseudo.'" ') OR DIE (mysql_error());
     }
}
//Fin connexion auto
//
//connexion
require_once('./includes/fonctions.php');
$message ='';
if(isset($_POST['login']))
{
  if(isset($_POST['pseudo']) && !empty($_POST['pseudo'])) // S'il a posté un login et qu'il n'est pas vide
  {
	if(isset($_POST['pass']) && !empty($_POST['pass'])) // S'il a posté un mot de pass et qu'il n'est pas vide
	{
	  $pseudo = mysql_real_escape_string(htmlentities($_POST['pseudo'],ENT_QUOTES)); //on sécurise
	  $password = $_POST['pass']; //on sécurise
	  $pass1= md5($password);
	  $pass2 = sha1($password);
	  $pass= $pass1.$pass2;
	  $nombrepseudo = mysql_result(mysql_query("SELECT COUNT(*) FROM membres WHERE pseudo = '".$pseudo."'"), 0);
	  if($nombrepseudo == 1)  //Le pseudo existe
	  {
		$requete = mysql_query(' SELECT id, mdp, pseudo, rang, tchat FROM membres WHERE pseudo = "'.$pseudo.'" ') Or DIE (mysql_error());
		$donnees = mysql_fetch_assoc($requete);
		
		if($donnees['mdp'] == $pass ) //Accès ok, on peut se connecter
		{
			 if (isset($_POST['connexion_auto']) && $_POST['connexion_auto'] == 'on')
			{
				$expire = time() + 3600 * 24 * 365;//Temps d'expiration des cookies (1 an).
				setcookie('pseudo', $pseudo, $expire); // On écrit un cookie
				setcookie('pass', $pass, $expire);
			}
		  $_SESSION['pseudo'] = stripslashes($donnees['pseudo']); // On enlève juste les anti-slashes (\) automatiques
		  $_SESSION['rang'] = $donnees['rang'];
		  $_SESSION['id'] = $donnees['id'];
		  $_SESSION['tchat'] = $donnees['tchat'];
		  $time= time();
		  mysql_query('UPDATE membres SET time_derniere_visite="' . $time . '" WHERE pseudo="' . $pseudo.'" ') OR DIE (mysql_error());
		  message(1);
		}
		else
			message(44, './connexion.html');
	  }
	  else
		message(45, './connexion.html');
	}
	else
		message(46, './connexion.html');
  }
  else
	message(46, './connexion.html');
}
//Fin connexion
//
//deco
if(isset($_POST['logout']))
{
	session_destroy();
	setcookie('pseudo', '', 0);
	setcookie('pass', '', 0);
	message(2);
}
//Fin déco
//Statistique
define("_BBC_PAGE_NAME", $titre);  
define("_BBCLONE_DIR", "bbclone/");  
define("COUNTER", _BBCLONE_DIR."mark_page.php");  
if (is_readable(COUNTER)) include_once(COUNTER); 
//fin statistique
//
//Recalculer le rang du membre
if(isset($_SESSION['id']))
{
	$req = mysql_query("SELECT rang FROM membres WHERE id = '".$_SESSION['id']."' ") or die (mysql_error());
	$data = mysql_fetch_assoc($req);
	$_SESSION['rang'] == $data['rang'];
}//fin de recalcul du rang du membre
//
//Mise à jour des membres connectés
if($_SESSION['id'] > 0)
{
	// ETAPE 1 : on vérifie si l'IP se trouve déjà dans la table
	// Pour faire ça, on n'a qu'à compter le nombre d'entrées dont le champ "ip" est l'adresse ip du visiteur
	$retour = mysql_query('SELECT COUNT(*) AS nbre_entrees FROM connectes WHERE id_pseudo=\'' . $_SESSION['id'] . '\'') OR DIE (mysql_error());
	$donnees = mysql_fetch_array($retour);
	if ($donnees['nbre_entrees'] == 0) // L'ip ne se trouve pas dans la table, on va l'ajouter
	{
	    mysql_query('INSERT INTO connectes VALUES(\'' . $_SESSION['id'] . '\', ' . time() . ')') OR DIE (mysql_error());
	}
	else // L'ip se trouve déjà dans la table, on met juste à jour le timestamp
	{
	    mysql_query('UPDATE connectes SET timestamp=' . time() . ' WHERE id_pseudo=\'' . $_SESSION['id'] . '\'') OR DIE (mysql_error());
	}
	// -------
	// ETAPE 2 : on supprime toutes les entrées dont le timestamp est plus vieux que 5 minutes
	// On stocke dans une variable le timestamp qu'il était il y a 5 minutes :
	$timestamp_5min = time() - (60 * 5); // 60 * 5 = nombre de secondes écoulées en 5 minutes
	mysql_query('DELETE FROM connectes WHERE timestamp < ' . $timestamp_5min);
}
//Fin mise à jour des membres connecté
//
//Mettre le rang à zéro si on est pas connecté
if(!(isset($_SESSION['id'])))
	$_SESSION['rang'] = 0;
?>