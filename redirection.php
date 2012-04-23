<?php
@session_start();
ob_start(); ////NE PAS SUPPRIMER !
$titre = 'Redirection';
require_once("./inc/core.php");
require_once('./inc/functions.php');
require_once('./includes/fonctions.php');

if(isset($_GET['idd']))
{
	$ret = mysql_query("SELECT COUNT(*) AS nombre FROM designs WHERE id = '".$_GET['idd']."' ");
	$donnees = mysql_fetch_array($ret);

	$nombre_designs = $donnees['nombre'];
	if($nombre_designs > '0')
	{
		if(antiflood())
		{
			mysql_query("UPDATE designs SET vu = vu + 1 WHERE id = '".$_GET['idd']."' ");
			$ret = mysql_query("SELECT id_membre FROM designs WHERE id = '".$_GET['idd']."' ");
			$donnees = mysql_fetch_array($ret);
			$idm = $donnees['id_membre'];
			$adresse = 'http://www.siteduzero.com/designs-394.html?design='.ROOT_ABS.'designs/'.$idm.'/'.$_GET['idd'].'';
			ajout_flood();
			header('Location: '.$adresse);
		}
		else
			mess(125);
	}
	else
		mess(7, './zdesigns.html');
}
else
	mess(89, './zdesigns.html');
	
ob_end_flush();// NE PAS SUPPRIMER !!
?>
