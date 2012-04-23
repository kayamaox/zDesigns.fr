<?php
require_once('./inc/functions.php');

function unzip($path, $file, $effacer_zip=false)
{
	
	$zip = zip_open($file);

	if ($zip)
	{
		while ($zip_entry = zip_read($zip)) //Pour chaque fichier contenu dans le fichier zip
		{
			if (zip_entry_filesize($zip_entry) > 0)
			{
				$complete_path = $path.(removeAccents(dirname(zip_entry_name($zip_entry))));

				/*On supprime les �ventuels caract�res sp�ciaux et majuscules*/
				$nom_fichier = zip_entry_name($zip_entry);
				$nom_fichier = removeAccents($nom_fichier);

				$complete_name = $path.$nom_fichier; //Nom et chemin de destination

				if(!file_exists($complete_path))
				{
					$tmp = '';
					foreach(explode('/',$complete_path) AS $k)
					{
						$tmp .= $k.'/';

						if(!file_exists($tmp))
							mkdir($tmp, 0777);
					}
				}

				$type_file = strtolower(strrchr($complete_name  ,'.'));
				if($type_file == '.css' || $type_file == '.png' || $type_file == '.gif' || $type_file == '.jpg' || $type_file == '.jpeg'  || $type_file == '.pspimage'  || $type_file == '.ico')
				{
					//echo 'ok';
					/*On extrait le fichier*/
					if (zip_entry_open($zip, $zip_entry, "r"))
					{
						$fd = fopen($complete_name, 'w');
						//echo $complete_name.'<br />';
						fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));

						fclose($fd);
						zip_entry_close($zip_entry);
					}
				}
			}
		}

		zip_close($zip);

		/*On efface �ventuellement le fichier zip d'origine*/
		if ($effacer_zip)
		unlink($file);
	}

}



?>