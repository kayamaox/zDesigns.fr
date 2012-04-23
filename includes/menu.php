<?php session_cache_limiter('private');    
session_regenerate_id();  
session_start();
require("./classes/menu_class.php");
?>
	<div id="menu">
       <div class="element_menu">
			<?php if(droit_dacces(8))
			{		
				$admin = new Bloque("Administration");
				if(droit_dacces(10))
				{
					$admin->lien("./admin-1.html", "Les zDesigns en attente");
					$nombre = mysql_result(mysql_query("SELECT COUNT(*) FROM todolist WHERE (statut='1' OR statut='2') AND membre_concerne='".$_SESSION['id']."' "), 0);
					$admin->lien("./admin-20.html", "La Todo-Liste (".$nombre.")");
					$admin->lien("./admin.html", "Panneau d'admin");
				}
				$admin->lien("./v2/v2_brice/", "V2 Brice");
				$admin->lien("./v2/v2_cyril/", "V2 Cyril");
				$admin->lien("./v2/v2_alexd/", "V2 Alex-D");
				$admin->lien("./v2/v2_final/", "V2 final");
				$nombre = 0;
				$retour = mysql_query("SELECT DISTINCT id_membre FROM tchat") OR DIE (mysql_error());
				while($donnees = mysql_fetch_array($retour))
				{
					
					$retour2 = mysql_query("SELECT  membres.rang, tchat.ignorer, tchat.id_membre
											FROM tchat
											INNER JOIN membres
											ON(tchat.id_auteur = membres.id)
											AND tchat.id_membre = '".$donnees['id_membre']."' 
											ORDER BY tchat.id DESC
											LIMIT 1") OR DIE (mysql_error());
					while($don = mysql_fetch_array($retour2))
						if($don['rang']  != 10 AND $don['ignorer'] != 1)
							$nombre++;
				}
				if($nombre > 0)
					$admin->lien("./admin-45.html", 'Tchat ('.$nombre.')');
				echo $admin;
			}
			//designs validés
			$zdesign = new Bloque("Les zDesigns");
			$retour = mysql_query('SELECT COUNT(*) AS nbre_designs FROM designs WHERE active="2" ');
			$donnees = mysql_fetch_array($retour);
			$zdesign->lien("./zdesigns.html", "Tous les zDesigns (".$donnees['nbre_designs'].")");
			
			//designs en attente de validation
			$retour = mysql_query('SELECT COUNT(*) AS nbre_designs FROM designs WHERE active="1" ');
			$donnees = mysql_fetch_array($retour);
			if($donnees['nbre_designs'] < 2)
				$zdesign->texte($donnees['nbre_designs']." zDesign en validation");
			else
				$zdesign->texte($donnees['nbre_designs']." zDesigns en validation");
			
			//designs en cours de création
			$retour = mysql_query('SELECT COUNT(*) AS nbre_designs FROM designs WHERE active="0" ');
			$donnees = mysql_fetch_array($retour);
			if($donnees['nbre_designs'] < 2)
				$zdesign->texte($donnees['nbre_designs']." zDesign en création");
			else
				$zdesign->texte($donnees['nbre_designs']." zDesigns en création");
				
			//$zdesign->lien("#","Popularités");
			//$zdesign->lien("#","Créateurs");
			echo $zdesign;
			
			if(isset($_SESSION['pseudo']))
			{
				$compte = new Bloque("Mes Packs");
				$compte->lien("./upload.html", "zUploader");
				$compte->lien("./mes_zdesigns.html", "Mes zDesigns");
				//$compte->lien("./mon_compte.html", "Mon compte");
				echo $compte;
			}
			
			$communaute = new Bloque("Communauté");
			$communaute->lien("./les_membres.html", "Tous les membres");
			//Nombre de connecté
			$retour = mysql_query('SELECT COUNT(*) AS nbre_entrees FROM connectes');
			$donnees = mysql_fetch_array($retour);
			if($donnees['nbre_entrees'] == 1)
				$texte = '1 connecté';
			else
				$texte = $donnees['nbre_entrees'].' connectés';
			$communaute->texte($texte);
			//Fin nombre connecté
			
			//Nombre membre
			$retour = mysql_query('SELECT COUNT(*) AS nbre_membres FROM membres');
			$donnees = mysql_fetch_array($retour);
			if($donnees['nbre_membres'] == 1)
				$texte = '1 membre inscrit';
			else
				$texte = $donnees['nbre_membres'].' membres inscrits';
			$communaute->lien("./les_membres.html", $texte);
			//Fin nombre membre
			echo $communaute;
			
			/*$discussion = new Bloque("Discussion");
			if(droit_dacces(10))
				$discussion->lien("./forum.html", "Forum");
			$discussion->lien("./livredor.html", "Livre d'or"); 
			$discussion->lien("./contact.html", "Contact"); 
			echo $discussion;*/
			
			$liens = new Bloque("Liens");
			$liens->lien("http://www.siteduzero.com", "Le site du Zéro");
			echo $lien;
			
			if(!$_SESSION['rang'] == 10)
			{
				?>
				<div class="publicite">
							<h4>Publicité</h4>
								<ul>
									<li>
							<script type="text/javascript"><!--
google_ad_client = "pub-8524373340417301";
/* 160x600, date de création 28/07/08 */
google_ad_slot = "4689047741";
google_ad_width = 160;
google_ad_height = 600;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
								</li>
							</ul>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	
	
		