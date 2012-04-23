<?php session_cache_limiter('private');    
session_regenerate_id();  
session_start();
$titre='Uploader son zDesign';
include('./unzip.php');
include('./includes/debut.php');?>
		<?php
		include('./includes/doctype.php');
		?>
	<head>
		<?php
		include('./includes/hdp.php');
		?>
	</head>
	<body>
		<div id="global">
			<?php
			//Menu principal
			include('./includes/menu_princ.php');
			
			//Menu gauche
			include('./includes/menu.php');
			
			?>
			
		
			<div id="corps">
				<!--Corp de la page-->
					

				<div class="plan">
					<p>Vous êtes ici : <a href="./index.html">zDesigns.fr</a> &gt; Accueil</p>
				</div> <!--div class=plan-->
				<h1>Upload ton zDesign !</h1>
				<img src="./images/icon_zuploader.png" alt="zUploader" />
				<br /><br />
				<?php if(isset($_SESSION['pseudo']))
				{
					if( isset($_POST['upload']) ) // si formulaire soumis
					{
					   

					    $tmp_file = $_FILES['fichier']['tmp_name'];

					    if( !is_uploaded_file($tmp_file) )
					    {
					        echo "Le fichier est introuvable";
					    }
						else
						{

						    // on vérifie maintenant l'extension
							$type_file = strtolower(substr($_FILES['fichier']['name'] ,-3));
						    if( !strstr($type_file, 'zip') )
						    {
						        echo "Le fichier n'est pas une archive zippée";
						    }
							else
							{
								if($_POST['fichier']['size'] > $taille_max_zip_zdesign)
								{
									echo "Le fichier est trop lourd";
								}
								else
								{
									$rep = "designs/";
									$dir = opendir($rep); 

									$issetfolder = false;
									while ($f = readdir($dir))
									   if($f == $_SESSION['id'])
										 $issetfolder = true;
									
									$path = './designs/'.$_SESSION['id'];
									echo $path;
									if(!$issetfolder)
									{
										mkdir($path, 0777);
									}

									closedir($dir); 
									
									$content_dir = $path; // dossier où sera déplacé le fichier
									
								    // on copie le fichier dans le dossier de destination
								    $name_file = $_FILES['fichier']['name'];
									mysql_query("INSERT INTO designs VALUES('', '".$_SESSION['id']."', '".$name_file."','','', '0', '0', '".time()."', '', '')");
									$idd = mysql_insert_id();
									$name = $idd.'.zip';
								    if( !move_uploaded_file($tmp_file, $content_dir.'/'.$name) )
								    {
								        echo "Impossible de copier le fichier dans $content_dir";
								    }
									else
									{
										mkdir($path.'/'.$idd, 0777);
										unzip('./designs/'.$_SESSION['id'].'/'.$idd.'/', './designs/'.$_SESSION['id'].'/'.$name, true);
										if(!droit_dacces(10))
										{
											$message = "Salut Cyril !\n\r
													".membre($_SESSION['id'], false, false)." vient d'uploader un pack_design s'appelant ".$name_file."\n\r
													Voici son lien :\n\r
													http://www.zdesigns.fr/mes_zdesigns-14-".$idd.".html";
											mail("heilmann.cyril@free.fr", "Nouveau zDesign !", $message);
										}
										
										message(146, './mes_zdesigns.html');
										
									}
								}
							}
						}
					}
					else
					{
						?>
						<form action="" enctype="multipart/form-data" method="post">
						<p>Archive *.zip uniquement | <?php echo $taille_max_zip_zdesign/1024;?> Ko maximum.<br />
						<input type="file" size="50" name="fichier" />
						<input name="upload" type="submit" /></p>
						</form>
						<?php
					}
				}
				else
				{
					echo 'Vous ne pouvez pas uploader car vous n\'êtes pas connecté<br />'; 
				}
				
				$texte
				?>
				<!-- Fin du corps de la page -->
			</div><!--div id=corps-->
			
		</div><!-- div id=global-->

		<?php
		include('./includes/bdp.php');
		?>
	</body>
</html>