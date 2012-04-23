<?php
@session_start();
require_once('./includes/pclzip.lib.php');


//fonction permettant de retrouver la page sur la quelle se trouve le sujet ancr�
function ancre($id_s,$id_r, $nb_mess_par_page, $nb_page)
{
	$rang = mysql_result(mysql_query("SELECT COUNT(id) FROM forum_reponse WHERE id_sujet = '".$id_s."' AND id < '".$id_r."' "), 0);
	$rang++;
	$page = ceil($rang/$nb_mess_par_page);
	if($rang/$page == $nb_mess_par_page AND $page < $nb_page)
		return $page + 1;
	else
		return $page;
}

//Fonction parsant les pages (1 2 3 4 ... w x y z)
function parse_pages($page_courante, $nb_page, $debut_lien, $fin_lien, $prec_suiv=true)
{
	$html = '';
	//Affichage de pr�c�dente
	if($prec_suiv)
	{
		if($page_courante != 1)
		{
			$prec= $page_courante-1;
			$html .= ' <a href="'.$debut_lien.$prec.$fin_lien.'">Précédente</a>';
		}
	}
	//Affichage des pages
	$trois_points = false; // Pas encore �crit les 3 petits points
	$trois_points_gauche = false;
	$trois_points_droite = false;
	for($i=1; $i<=$nb_page; $i++) // boucles des pages
	{
		if($nb_page <= 8) // s'il y a moins de huit pages, on affiche tout
		{
			if($i == $page_courante)
				$html .= ' <a href="'.$debut_lien.$i.$fin_lien.'"><span class="page_active">'.$i.'</span></a>';
			else
				$html .= ' <a href="'.$debut_lien.$i.$fin_lien.'">'.$i.'</a>';
		}
		else
		{
			if($page_courante == 1 OR $page_courante == $nb_page OR $page_courante == 0) //si on est sur la 1ere page ou la derniere, on affiche 1 2 3 4 ... w x y z
			{
				if($i <= 4 OR $i > $nb_page-4)
				{
					if($i == $page_courante)
						$html .= ' <a href="'.$debut_lien.$i.$fin_lien.'"><span class="page_active">'.$i.'</span></a>';
					else
						$html .= ' <a href="'.$debut_lien.$i.$fin_lien.'">'.$i.'</a>';
				}
				else
				{
					if(!$trois_points)
					{
						$html .= ' ...';
						$trois_points = true;
					}
				}
			}
			else //Sinon, il faut encore parser
			{
				if($i <= 3 OR $i > $nb_page-3 OR $i == $page_courante OR ($i > $page_courante - 4 AND $i < $page_courante + 4)) //Toutes les conditions qui permettent d'afficher le num�ro de la page
				{
					if($i == $page_courante)
						$html .= ' <a href="'.$debut_lien.$i.$fin_lien.'"><span class="page_active">'.$i.'</span></a>';
					else
						$html .= ' <a href="'.$debut_lien.$i.$fin_lien.'">'.$i.'</a>';
				}
				else // On affiche les petites points
				{
					if(!$trois_points_gauche AND $i < $page_courante)
					{
						$html .= ' ...';
						$trois_points_gauche = true;
					}
					if(!$trois_points_droite AND $i > $page_courante)
					{
						$html .= ' ...';
						$trois_points_droite = true;
					}
				}
			}
		}
	}
	
	//Affichage de suivante
	if($prec_suiv)
	{
		if($page_courante < $nb_page)
		{
			$suivante= $page_courante+1;
			$html .= ' <a href="'.$debut_lien.$suivante.$fin_lien.'">Suivante</a>';
		}
	}
	
	return $html;
}

//message de notification de r�ponse de topic
function notif_topic($id_membre_reponse, $membre_reponse, $membre_notifie, $titre, $lien, $mail_notifie) 
{
	$to = trim($mail_notifie);
	$headers ='From: "Contact - zDesigns.fr"<notification@zdesigns.fr>'."\n";
	$headers .='Content-Type: text/html; charset="utf-8"'."\n";
    $headers .='Content-Transfer-Encoding: 8bit'; 
    $message ='<div style="text-align:center;"> <img src="http://www.zdesigns.fr/images/logo_zdesigns_contact.jpg" alt="Logo du site" align="center" width="600px" height="153px" /></div><br /><br />'; 
	$message .= '<strong>Bonjour '.stripslashes($membre_notifie).',</strong><br />';
	$message .= '&nbsp;&nbsp;&nbsp;<a href="www.zdesigns.fr/membres-'.$id_membre_reponse.'.html">'.stripslashes($membre_reponse).'</a> a répondu à un des sujets que vous suivez sur <a href="http://www.zdesigns.fr">zDesigns.fr</a><br /><br />';
	$message .= 'Cette réponse concerne le post suivant : ';
	$message .= '<a href="'.stripslashes($lien).'" style="margin-left: 20px;">'.stripslashes($titre).'</a><br /><br />';
	$message .= 'Notez qu\'il peut y avoir plusieurs réponses, mais un seul e-mail vous a été envoyé pour éviter d\'encombrer votre boîte de messagerie.<br /><br />';
	$message .= '<italic>L\'équipe de zDesigns</italic><br /><br />';		$message .= '<h5>Veuillez ne pas répondre à ce mail délivré automatiquement.</h5>';
	mail($to, 'Réponse du topic : '.stripslashes($titre), $message, $headers);
}

function mail_tchat($id_membre, $id_admin)
{
	$retour = mysql_query("SELECT mail FROM membres WHERE id='".$id_membre."' ");
	$donnees = mysql_fetch_array($retour);
	
	$to = trim($donnees['mail']);
	$headers ='From: "Contact - Zdesigns.fr"<notification@zdesigns.fr>'."\n";
	$headers .='Content-Type: text/html; charset="utf-8"'."\n";
    $headers .='Content-Transfer-Encoding: 8bit'; 
    $message ='<div style="text-align:center;"> <img src="http://www.zdesigns.fr/images/logo_zdesigns_contact.jpg" alt="Logo du site" align="center" width="600px" height="153px" /></div><br /><br />'; 
	$message .= '<strong>Bonjour '.membre($id_membre).',</strong><br />';
	$message .= '&nbsp;&nbsp;&nbsp;'.membre($id_admin, true).' a �crit un message sur le tchat de <a href="http://www.zdesigns.fr">Zdesigns.fr</a><br /><br />';
	$message .= '<a href="http://www.zdesigns.fr/tchat.php" style="margin-left: 20px;">Cliquez-ici pour voir de quoi il s\'agit.</a><br /><br />';
	$message .= '<br /><br />';
	$message .= '<italic>L\'�quipe de Zdesigns</italic>';
	mail($to, 'Tchat : '.membre($id_admin), $message, $headers);

}

//Controle antiflood
function antiflood()
{
	if(!(empty($_SESSION['id'])))
	{
		if(droit_dacces(10))
			return true;
		else
		{
			$nombre = mysql_result(mysql_query("SELECT COUNT(*) FROM antiflood WHERE id_pseudo = '".$_SESSION['id']."' "), 0);
			if($nombre)
			{
				$time = mysql_result(mysql_query("SELECT timestamp FROM antiflood WHERE id_pseudo = '".$_SESSION['id']."' "), 0);
				
				if(time() - $time < 21)
					return false;
				else
					return true;
			}
			else
				return true;
		}
	}
	else
	{
		$ip =  $_SERVER["REMOTE_ADDR"];
		$nombre = mysql_result(mysql_query("SELECT COUNT(*) FROM antiflood WHERE IP = '".$ip."' "), 0);
		if($nombre)
		{
			$time = mysql_result(mysql_query("SELECT timestamp FROM antiflood WHERE IP = '".$ip."' "), 0);
			
			if(time() - $time < 21)
				return false;
			else
				return true;
		}
		else
			return true;
	}
}

//fonction qui ajoute l'�v�nement de l'action dans le controle antiflood
function ajout_flood()
{
	if(!(empty($_SESSION['id'])))
	{
		if(!droit_dacces(10))
		{
			$nombre = mysql_result(mysql_query("SELECT COUNT(*) FROM antiflood WHERE id_pseudo = '".$_SESSION['id']."' "), 0);
			if($nombre)
				mysql_query("UPDATE antiflood SET timestamp = '".time()."' WHERE id_pseudo = '".$_SESSION['id']."' ") OR DIE (mysql_error());
			else
				mysql_query("INSERT INTO antiflood VALUES ('".$_SESSION['id']."', '', '".time()."') ") OR DIE (mysql_error());
		}
	}
	else
	{
		$ip =  $_SERVER["REMOTE_ADDR"];
		echo $ip;
		$nombre = mysql_result(mysql_query("SELECT COUNT(*) FROM antiflood WHERE IP = '".$ip."' "), 0);
			if($nombre)
				mysql_query("UPDATE antiflood SET timestamp = '".time()."' WHERE IP = '".$ip."' ") OR DIE (mysql_error());
			else
				mysql_query("INSERT INTO antiflood VALUES ('0', '".$ip."', '".time()."') ") OR DIE (mysql_error());
	}
}

//fonction cr�ant la liste d�roulante des diff�rents forum
function liste_forum($courant)
{
	$html = '

	<form action="forum-2.html" method="get" class="liste_forum">
	<p>

	<select name="idf" onchange="document.location = \'forum-2-\' + this.options[this.selectedIndex].value + \'.html\';">';
		

	$retour1 = mysql_query("SELECT id, titre, rang_dacces FROM forum_cat ORDER BY ordre ASC") OR DIE (mysql_error());
	while($cat = mysql_fetch_array($retour1))
	{
		if(droit_dacces($cat['rang_dacces']))
		{
			$html .= '
			<optgroup label="'.stripslashes($cat['titre']).'">';
			$retour2 = mysql_query ("SELECT id, titre, rang_dacces FROM forum_forum WHERE id_cat = '".$cat['id']."' ORDER BY ordre ASC") OR DIR (mysql_error());
			while($forum = mysql_fetch_array($retour2))
			{
				if(droit_dacces($forum['rang_dacces']))
				{
					if($courant == $forum['id'])
						$html .= '
						<option value="'.$forum['id'].'" selected="selected" > 
							'.stripslashes($forum['titre']).'
						</option>';
					else
						$html .= '
						<option value="'.$forum['id'].'"> 
							'.stripslashes($forum['titre']).'
						</option>';
				}
			}
			$html .= '
			</optgroup>';
		}
	}
	
	$html .= '</select>

			<input type="submit" value="Go" />
		</p>
	</form>';
	
	return $html;
}

//fonction affichant le pseudo du membre, avec ou pas le lien et la couleur
function membre($id_m, $lien=false, $couleur=false)
{
	
	$nombre = mysql_result(mysql_query("SELECT COUNT(*) FROM membres WHERE id = '".$id_m."' "), 0);
	if($nombre != 0)
	{
		$retour = mysql_query("SELECT pseudo, rang FROM membres WHERE id = '".$id_m."' ")OR DIE (mysql_error());
		$donnees = mysql_fetch_array($retour);
		if($lien)
		{
			if($couleur)
			{
				$html = '<a href="./membres-'.$id_m.'.html">'.stripslashes($donnees['pseudo']).'</a>';
			}
			else
			{
				$html = '<a href="./membres-'.$id_m.'.html">'.stripslashes($donnees['pseudo']).'</a>';
			}
		}
		else
		{
			if($couleur)//identique pour l'instant
			{
				if($donnees['rang'] == 10)
					$html = '<span style="color:red">'.stripslashes($donnees['pseudo']).'</span>';
				else
					$html = stripslashes($donnees['pseudo']);
			}
			else
				$html = stripslashes($donnees['pseudo']);
		}
	}
	else
		$html = 'Anonyme';
	
	return $html;
		
}

//Fonction permettant de v�rifier si le zDesign ne d�passe pas les 1Mo max.
function taille_max_zdesign($src)
{
											
	$size=0;
	$h = opendir($src);
	while (($o = readdir($h)) !== FALSE)  
	{
		if (($o != '.') and ($o != '..'))   
		{
			if (is_dir($src.DIRECTORY_SEPARATOR.$o))
				$size=$size + taille_max_zdesign($src.DIRECTORY_SEPARATOR.$o);
			else
				$size=$size+filesize($src.DIRECTORY_SEPARATOR.$o);
		}
	}
	closedir($h);
	
	return $size;
											
}

//Fonction permettant de v�rifier si le zdesign ne contien pas d'erreur
function orange($chemin)
{
	$orange = false;
	$dossier_officiel = preg_replace('`./designs/[0-9]+/[0-9]+/`isU', './designs/officiel/', $chemin);
	
	//coherance
	$dir = opendir($chemin);
	while ($f = readdir($dir)) 
	{
		
		if(is_dir($chemin.'/'.$f) && $f != '.' && $f != '..')  // repertoire
		{
			if(is_dir($dossier_officiel.'/'.$f))
			{
				if(orange($chemin.'/'.$f))
					$orange=true;
			}
			else
				$orange=true;
		}
		else // fichier
		{
			if(!(file_exists($dossier_officiel.'/'.$f)))
			{
				$orange=true;
			}
		}
	}
	
	if(!$orange)
	{
		//manquant
		$dir = opendir($dossier_officiel);
		while ($f = readdir($dir)) 
		{
			
			if(is_dir($dossier_officiel.'/'.$f) && $f != '.' && $f != '..')  // repertoire
			{
				if(is_dir($chemin.'/'.$f))
				{
					if(orange($chemin.'/'.$f))
						$orange=true;
				}
				else
					$orange=true;
			}
			else // fichier
			{
				if(!(file_exists($chemin.'/'.$f)))
				{
					$orange=true;
				}
			}
		}
	}
	closedir($dir);
	return $orange;
}

//fonction permettant de telecharger son pack design
function telecharger_pack($idd, $idm)
{
	$retour = mysql_query("SELECT COUNT(*) AS nombredesigns FROM designs WHERE id='".$idd."' AND id_membre = '".$idm."' ") OR DIE (mysql_error());
	$donnees = mysql_fetch_array($retour);
	$nombredesigns = $donnees['nombredesigns'];
	if($nombredesigns > '0')
	{
		if(file_exists('./designs/'.$idm.'/'.$idm.'.zip'))
			unlink('./designs/'.$idm.'/pack_design_'.$idd.'.zip');
		$pack = new PclZip('pack_design_'.$idd.'.zip');
		$pack->add('./designs/'.$idm.'/'.$idd);
		copy('./pack_design_'.$idd.'.zip', './designs/'.$idm.'/pack_design_'.$idd.'.zip');
		unlink('./pack_design_'.$idd.'.zip');
		
		header('Refresh: 2; url=./designs/'.$idm.'/pack_design_'.$idd.'.zip');
	}
}

//fonction retournant le nombre dde fichier et de dossier manquants dans le zDesign du membre
function nombre_fichiers_manquants($dossier_officiel, $chemin_membre)
{
	
	$nombre_fichiers_manquants = 0;
	
	$dir = opendir($dossier_officiel);
	while ($f = readdir($dir)) 
	{
		if($f != '.' && $f != '..')
		{
			if(is_dir($dossier_officiel.'/'.$f))  // repertoire
			{
				if(is_dir($chemin_membre.'/'.$f)) //existe t'il dans celui du membre ??
					$nombre_fichiers_manquants += nombre_fichiers_manquants($dossier_officiel.'/'.$f, $chemin_membre.'/'.$f);
				else // on le parcours quand meme pour rajouter le nombre de fichiers du dossier manqant au pourcentage
				{
					$nombre_fichiers_manquants++;
					$nombre_fichiers_manquants += nombre_fichiers_manquants($dossier_officiel.'/'.$f, $chemin_membre.'/'.$f);
				}
			}
			else // fichier
			{
				if(!(file_exists($chemin_membre.'/'.$f)) && $f != 'pack_design.zip')
				{
					$nombre_fichiers_manquants++;
				}
			}
		}
	}
	
	return $nombre_fichiers_manquants;	
}

//Fonction retournant le nombre de fichier et de dossier dans le design officiel
function nombre_fichiers_officiels($dossier_officiel)
{
	
	$nombre_fichiers = 0;
	
	$dir = opendir($dossier_officiel);
	while ($f = readdir($dir)) 
	{
		if($f != '.' && $f != '..')
		{
			if(is_dir($dossier_officiel.'/'.$f))  // repertoire
			{
				
					$nombre_fichiers++;
					$nombre_fichiers += nombre_fichiers_officiels($dossier_officiel.'/'.$f);
				
			}
			else // fichier
			{
				if($f != 'pack_design.zip')
				{
					$nombre_fichiers++;
				}
			}
		}
	}
	
	return $nombre_fichiers;	
}

//fonction utilsant les 2 prec�dentes pour calculer le pourcentage du "correct" d'un design officiel
function pourcentage_design_correct($chemin, $correct=true)
{
	if($correct)
		return round(100 - (nombre_fichiers_manquants("./designs/officiel", $chemin) * 100 /  nombre_fichiers_officiels("./designs/officiel")));
	else
		return round(nombre_fichiers_manquants("./designs/officiel", $chemin) * 100 /  nombre_fichiers_officiels("./designs/officiel"));
}

//fonction permettant d'afficher la barre d'outil du zexploreur
function tools_barre($titre, $path='')
{
	/*NOUVEAU DOSSIER/FICHIER/CSS*/
	if(empty($path)) 
	{
		echo stripslashes($titre).'
			<br/ ><br />
			<a href="./mes_zdesigns-15-'.$_GET['idd'].'.html" ><img src="./images/zexploreur/folder_green.png" height="45" title="Ajouter un nouveau dossier" alt="nouveau dossier" /></a>
			&nbsp;
			<a href="./mes_zdesigns-26-'.$_GET['idd'].'.html" ><img src="./images/zexploreur/css_green.png" height="45" title="Ajouter un nouveau fichier CSS" alt="nouveau css" /></a>
			&nbsp;
			<a href="./mes_zdesigns-27-'.$_GET['idd'].'.html" ><img src="./images/zexploreur/new_file.png" height="45" title="Ajouter un nouveau fichier" alt="nouveau fichier" /></a>';
		}
	else
	{
		echo '<a href="./mes_zdesigns-14-'.$_GET['idd'].'.html">'.stripslashes($titre).'</a>';
		$dire = explode('/', $path);
		for($i=0; $i<count($dire)-2; $i++)
		{
			$lien ='./mes_zdesigns-14-'.$_GET['idd'].'.html?path=';
			for($j=0; $j<=$i; $j++)
				$lien .= $dire[$j].'/';
			echo ' &gt; <a href="'.$lien.'">'.$dire[$i].'</a>';
		}
		echo ' &gt; '.$dire[$i].'
			<br/ ><br />
			<a href="./mes_zdesigns-15-'.$_GET['idd'].'.html?path='.$path.'" ><img src="./images/zexploreur/folder_green.png" height="45" title="Ajouter un nouveau dossier" alt="nouveau dossier" /></a>
			&nbsp;
			<a href="./mes_zdesigns-26-'.$_GET['idd'].'.html?path='.$path.'" ><img src="./images/zexploreur/css_green.png" height="45" title="Ajouter un nouveau fichier CSS" alt="nouveau css" /></a>
			&nbsp;
			<a href="./mes_zdesigns-27-'.$_GET['idd'].'.html?path='.$path.'" ><img src="./images/zexploreur/new_file.png" height="45" title="Ajouter un nouveau fichier" alt="nouveau fichier" /></a>';
	}	

		//s�paration
		echo '<img src="./images/zexploreur/separation.png" alt="s�paration" />';
	
		/* COPIER / COUPER / COLLER */
		
		
				$nb_dossiers=count($_SESSION['presse_papier_dossiers']);
				$nb_fichiers=count($_SESSION['presse_papier_fichiers']);
				
				if($nb_dossiers > 0) //au moins 1 dossier
				{
					if($nb_dossiers == 1) //exactemet 1 dossier
					{
						if($nb_fichiers > 0) //au moins 1 fichier
						{
							if($nb_fichiers == 1) //exactement 1 fichier
								$title = "1 dossier et 1 fichier dans le presse-papier";
							else // plus d'1 fichier
								$title = "1 dossier et $nb_fichiers fichiers dans le presse-papier";
						}
						else // aucun fichier
						{
							$title = "1 dossier dans le presse-papier";
						}
					}
					else // plus d'un dossier
					{
						if($nb_fichiers > 0) //au moins 1 fichier
						{
							if($nb_fichiers == 1) //exactement 1 fichier
								$title = "$nb_dossiers dossiers et 1 fichier dans le presse-papier";
							else // plus d'1 fichier
								$title = "$nb_dossiers dossiers et $nb_fichiers fichiers dans le presse-papier";
						}
						else // aucun fichier
						{
							$title = "$nb_dossiers dossiers dans le presse-papier";
						}
					}
				}
				else // aucun fichier
				{
					if($nb_fichiers > 0) //au moins 1 fichier
					{
						if($nb_fichiers == 1) //exactement 1 fichier
							$title = "1 fichier dans le presse-papier";
						else // plus d'1 fichier
							$title = "$nb_fichiers fichiers dans le presse-papier";
					}
					else // aucun fichier
					{
						$title = "Rien dans le presse-papier";
					}
				}
		
		if(empty($path)) 
		{
			echo '<a href="#" onclick="javascript:copy(\'./mes_zdesigns-32-'.$_GET['idd'].'.html\');" ><img src="./images/zexploreur/copy.png" height="45" title="Copier les fichiers s�l�ctionn�s" alt="copier" /></a>
				&nbsp;
				<a href="./mes_zdesigns-33-'.$_GET['idd'].'.html" ><img src="./images/zexploreur/cut.png" height="45" title="Couper les fichiers s�l�ctionn�s" alt="couper" /></a>
				&nbsp;';
			if((!(empty($_SESSION['presse_papier_fichiers']))) OR (!(empty($_SESSION['presse_papier_dossiers']))))
			{
				echo '<a href="./mes_zdesigns-34-'.$_GET['idd'].'.html" ><img src="./images/zexploreur/paste_enable.png" height="45" alt="coller" title="'.$title.'" /></a>';
			}
			else	
				echo '<img src="./images/zexploreur/paste_disable.png" height="45" title="'.$title.'" alt="coller" />';	
		}
		else
		{
			echo '<a href="#" onclick="javascript:copy(\'./mes_zdesigns-32-'.$_GET['idd'].'.html?path='.$path.'\');" ><img src="./images/zexploreur/copy.png" height="45" title="Copier les fichiers s�l�ctionn�s" alt="copier" /></a>
				&nbsp;
				<a href="./mes_zdesigns-33-'.$_GET['idd'].'.html?path='.$path.'" ><img src="./images/zexploreur/cut.png" height="45" title="Couper les fichiers s�l�ctionn�s" alt="couper" /></a>
				&nbsp;';
			if((!(empty($_SESSION['presse_papier_fichiers']))) OR (!(empty($_SESSION['presse_papier_dossiers']))))
				echo '<a href="./mes_zdesigns-34-'.$_GET['idd'].'.html?path='.$path.'" ><img src="./images/zexploreur/paste_enable.png" height="45" title="'.$title.'" /></a>';
			else	
				echo '<img src="./images/zexploreur/paste_disable.png" height="45" title="'.$title.'" alt="coller" />';
		}
		
		//s�paration
		echo '<img src="./images/zexploreur/separation.png" alt="s�paration" />';
		
		if(empty($path)) 
		{
			echo '<a href="#"><img src="./images/zexploreur/delete.png" height="45" title="Supprimer les fichiers s�l�ctionn�s" alt="supprimer" /></a>';
		}
		else
		{
			echo '<a href="#"><img src="./images/zexploreur/delete.png" height="45" title="Supprimer les fichiers s�l�ctionn�s" alt="supprimer" /></a>';
		}
		
	
}


//Fonction permettant de copier un dossier complet
function copy_dir($src,$dst) 
{
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) 
	{
        if (( $file != '.' ) && ( $file != '..' )) 
		{
            if ( is_dir($src . '/' . $file) ) 
			{
                copy_dir($src . '/' . $file,$dst  . $file);
            }
            else 
			{
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
} 


function tchat($id_du_membre)
{
	if(isset($_GET['tchat']))
	{
		if($_GET['tchat'] == 1)
		{
			mysql_query("UPDATE membres SET tchat = '1' WHERE id = '".$_SESSION['id']."' ") OR DIE (mysql_error());
			$_SESSION['tchat'] = 1;
		}
		if($_GET['tchat'] == 0)
		{
			mysql_query("UPDATE membres SET tchat = '0' WHERE id = '".$_SESSION['id']."' ") OR DIE (mysql_error());
			$_SESSION['tchat'] = 0;
		}
	}
	if(isset($_POST['message']) AND !(empty($_POST['message'])))
	{
		if(antiflood())
		{
			$mess = mysql_real_escape_string(stripslashes(htmlspecialchars($_POST['message'])));
			if($id_du_membre == $_SESSION['id'])
				mysql_query("INSERT INTO tchat VALUES ('', '".$id_du_membre."', '".$_SESSION['id']."', '".$mess."', '".time()."', '1', '0') ") OR DIE (mysql_error());
			else
			{
				mysql_query("INSERT INTO tchat VALUES ('', '".$id_du_membre."', '".$_SESSION['id']."', '".$mess."', '".time()."', '0', '0') ") OR DIE (mysql_error());
				
				mail_tchat($id_du_membre, $_SESSION['id']);
			}
			ajout_flood();
		}
		else
			message(125, './mes_zdesigns.html');
	}
	
	if($_SESSION['tchat'])
	{
		if(isset($_GET['path']))
			echo '<a href="'.$_ENV['REQUEST_URI'].'&tchat=0">Masquer le tchat</a>';
		else
			echo '<a href="'.$_ENV['REQUEST_URI'].'?tchat=0">Masquer le tchat</a>';
	}
	else
	{
		$nombre_non_lu = mysql_result(mysql_query("SELECT COUNT(*) FROM tchat WHERE lu='0' AND id_membre = '".$id_du_membre."' "), 0);
		if($nombre_non_lu == 0)
		{
			if(isset($_GET['path']))
				echo '<a href="'.$_ENV['REQUEST_URI'].'&tchat=1">Afficher le tchat</a>';
			else
				echo '<a href="'.$_ENV['REQUEST_URI'].'?tchat=1">Afficher le tchat</a>';
		}
		if($nombre_non_lu == 1)
		{
			if(isset($_GET['path']))
				echo '<a href="'.$_ENV['REQUEST_URI'].'&tchat=1">Afficher le tchat (1 message)</a>';
			else
				echo '<a href="'.$_ENV['REQUEST_URI'].'?tchat=1">Afficher le tchat (1 message)</a>';
		}
		if($nombre_non_lu > 1)
		{
			if(isset($_GET['path']))
				echo '<a href="'.$_ENV['REQUEST_URI'].'&tchat=1">Afficher le tchat ('.$nombre_non_lu.' messages)</a>';
			else
				echo '<a href="'.$_ENV['REQUEST_URI'].'?tchat=1">Afficher le tchat ('.$nombre_non_lu.' messages)</a>';
		}
	}
	
	if($_SESSION['tchat'])
	{
		?>
		<div class="contenu_message">
			<div class="contenu_titre">
				<div class="titre_h2">
					<?php 
						if(droit_dacces(10))
							echo 'Tchat avec '.membre($id_du_membre, true, false);
						else
							echo 'Tchat avec les admins !';
					?>
				</div>
			</div>
			<p>
			<?php
			$nombre = mysql_result(mysql_query("SELECT COUNT(*) FROM tchat WHERE id_membre = '".$id_du_membre."' "), 0);
			if($nombre < 5)
				echo '<em>&gt;<span style="color:green">Syst�me :</span></em> tchatez ici avec les admins<br /><br />';
			if($nombre > 0)
			{
				$requete = mysql_query("SELECT id, id_auteur, message, timestamp FROM tchat WHERE id_membre = '".$id_du_membre."' ORDER BY id DESC LIMIT 5") OR DIE (mysql_error());
				$tableau;
				$i =0;
				while($tchat = mysql_fetch_array($requete))
				{
					if($id_du_membre == $_SESSION['id'])
						mysql_query("UPDATE tchat SET lu = '1' WHERE id = '".$tchat['id']."' ");
					$tableau[$i][0] = $tchat['id_auteur'];
					$tableau[$i][1] = $tchat['timestamp'];
					$tableau[$i++][2] = $tchat['message'];
				}
				for($j=$i-1; $j>=0; $j--)
				{
					echo '<em>&gt;'.membre($tableau[$j][0], false,true).'</em> <span style="color:blue">['.parse_date($tableau[$j][1]).']</span> : <strong>'.$tableau[$j][2].'</strong><br /><br />';
				}
			}
				
			echo '<form action="" method="post" >
					<input type="text" size="100" name="message" />
					<input type="submit" value="Envoyer" />
				</form>
				<br />
				';
			if($id_du_membre != $_SESSION['id'])
				echo '<a href="./tchat.html?idm='.$id_du_membre.'">Voir tout le dialogue</a>';
			else
				echo '<a href="./tchat.html">Voir tout le dialogue</a>';
			?>
			</p>
		</div>	
		<?php
	}
}

//fonct

?>