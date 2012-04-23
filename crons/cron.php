<?php 
include("../includes/config.php");
include("../includes/identifiants.php");
include("../is_pd_exists.php5");
mysql_connect($adresse, $nom, $motdepasse);
mysql_select_db($database);

function rm_recursive($filepath) //Virer les anciens dossiers
{
	if (is_dir($filepath) && !is_link($filepath))
	{
		if ($dh = opendir($filepath))
		{
			while (($sf = readdir($dh)) !== false)
			{
				if ($sf == '.' || $sf == '..')
				{
					continue;
				}
				if (!rm_recursive($filepath.'/'.$sf))
				{
					echo 'prob';
				}
			}
			closedir($dh);
		}
		return rmdir($filepath);
	}
	return unlink($filepath);
}

function unzip($path, $file, $effacer_zip=false)
{
/*Méthode qui permet de décompresser un fichier zip $file dans un répertoire de destination $path
  et qui retourne un tableau contenant la liste des fichiers extraits
  Si $effacer_zip est égal à true, on efface le fichier zip d'origine $file*/
	$tab_liste_fichiers = array(); //Initialisation

	
	$zip = zip_open($file);
	
	
		
	if($zip)
	{	
		while ($zip_entry = zip_read($zip)) //Pour chaque fichier contenu dans le fichier zip
		{
			if (zip_entry_filesize($zip_entry) > 0)
			{
				
				$complete_path = $path.dirname(zip_entry_name($zip_entry));
				$complete_path = preg_replace('#simpleit-zDesign-[a-z0-9]*/#', '', $complete_path);
				/*On supprime les éventuels caractères spéciaux et majuscules*/
				$nom_fichier = zip_entry_name($zip_entry);
				

				/*On ajoute le nom du fichier dans le tableau*/
				array_push($tab_liste_fichiers,$nom_fichier);

				$complete_name = $path.$nom_fichier; //Nom et chemin de destination
				$complete_name = preg_replace('#simpleit-zDesign-[a-z0-9]*/#', '', $complete_name);
				//echo $complete_name;
				if(!file_exists($complete_path))
				{
					$tmp = '';
					foreach(explode('/',$complete_path) AS $k)
					{
						$tmp .= $k.'/';
						
						if(!file_exists($tmp))
						{ mkdir($tmp, 0755); }
						
					}
				}
				
				$type_file = strtolower(strrchr($complete_name  ,'.'));
				if($type_file == '.css' || $type_file == '.png' || $type_file == '.gif' || $type_file == '.jpg' || $type_file == '.jpeg'  || $type_file == '.pspimage'  || $type_file == '.ico')
				{
					/*On extrait le fichier*/
					if (zip_entry_open($zip, $zip_entry, "r"))
					{
						$fd = fopen($complete_name, 'w');
						fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));

						fclose($fd);
						zip_entry_close($zip_entry);
					}
				}
				
			}
			
		}

		zip_close($zip);

		/*On efface éventuellement le fichier zip d'origine*/
		if ($effacer_zip == true)
			unlink($file);
	}

	return $tab_liste_fichiers;
}

$message = '';

/*if(is_pd_exists($adresse_pack_design_distant))
{*/
	
	if(file_exists('./designs/officiel/pack_design.zip'))
	{
		if(rm_recursive('./designs/officiel/css')) //on vire le vieux dossier CSS
			$message .= "suppression de CSS : Ok\n\r";
		else
			$message .= "suppression de CSS : échoué\n\r";
		if(rm_recursive('./designs/officiel/images')) //On vire le vieux dossier images
			$message .= "suppression de Image : Ok\n\r";
		else
			$message .= "suppression de Image : échoué\n\r";
		/*if(unlink('./designs/officiel/pack_design.zip')) // On vire le vieux zip
			$message .= "suppression du pack : Ok\n\r";
		else
			$message .= "suppression du pack : échoué\n\r";*/
		
	/*	if(copy($adresse_pack_design_distant, './designs/officiel/pack_design.zip')) //On copie depuis le zip du site du zéro
			$message .= "Telechargement du pack : Ok\n\r";
		else
		/*	$message .= "Telechargement du pack : échoué\n\r";*/
		echo $message; 
		unzip('./designs/officiel/', './designs/officiel/pack_design.zip', true); //On extrait le nouvau fichier

	} else {
		$message .= 'Pack design introuvable sur le serveur !';
		echo $message;
    }
		
	mysql_query("INSERT INTO cron VALUES ('', '".$_SERVER['REMOTE_ADDR']."', '".time()."') ") OR DIE (mysql_error());

	//$message = '<html><head></head><body>' . $message . '</body></html>';
	                                        
	$sujet = 'Cron : Rapport';
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers = 'From: webmaster@zdesigns.fr';

	if(mail("cyril@zdesigns.fr", $sujet, $message, $headers))
		echo 'mail envoyé';
	else
		echo 'Erreur lors de l\'envoie';
/*}
else
{
	$message .= 'Pack distant introuvable. Conservation de l\'ancienne version.';
	echo $message;
	
	$sujet = 'Cron : Rapport';
	/*$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";*/
/*	$headers = 'From: webmaster@zdesigns.fr';

	if(mail("cyril@zdesigns.fr", $sujet, $message, $headers))
		echo 'mail envoyé';
	else
		echo 'Erreur lors de l\'envoie';
}*/


?>