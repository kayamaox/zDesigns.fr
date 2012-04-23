<?php //session_cache_limiter('private');    session_regenerate_id();  
//session_start();?>
	<div id="header">
	<div style="margin-left:auto; margin-right: auto; text-align: center">
		<a href="./index.html"><img src="./images/header.jpg" alt="zDesigns.fr" /></a>
	</div>
	
	</div>
	
	<div id="speedbarre">
		<div class="gauche">
			<ul>
					<li class="speedbarre_gauche" id="speedbarre_accueil"><a href="./index.html" title="Accueil"><img src="./images/speedbarre/accueil.png" alt="" />&nbsp;Accueil&nbsp;&nbsp;</a></li>
					<li class="speedbarre_gauche" id="speedbarre_zdesigns"><a href="./zdesigns.html" title="Tous les zDesigns"><img src="./images/speedbarre/zdesigns_image.png" alt="" />&nbsp;zDesigns&nbsp;&nbsp;</a></li>
					<li class="speedbarre_gauche" id="speedbarre_news"><a href="./news.html" title="Les news du site"><img src="./images/speedbarre/news.png" alt="" />&nbsp;zNews&nbsp;&nbsp;</a></li>
				<?php if(droit_dacces(8))
				{
					?>
					<li class="speedbarre_gauche" id="speedbarre_forum"><a href="./forum.html" title="Venez parler sur le forum"><img src="./images/speedbarre/forum_icone.png" alt="" />&nbsp;Forum&nbsp;&nbsp;</a></li>
					<?php
				}
				?>
					<!--<li class="speedbarre_gauche" id="speedbarre_guestbook"><a href="./livredor.html" title="Venez signer le livre d'or !"><img src="./images/speedbarre/livredor.png" alt="" />&nbsp;Livre D'or&nbsp;&nbsp;</a></li>-->
					<li class="speedbarre_gauche" id="speedbarre_faq"><a href="./faq.html" title="Foire aux Questions"><img src="./images/speedbarre/faq.png" alt="" />&nbsp;FAQ&nbsp;&nbsp;</a></li>
					<!--<li class="speedbarre_gauche" id="speedbarre_faq"><a href="http://bugs.zdesigns.fr" title="Rapport de bugs"><img src="./images/speedbarre/bugs.png" alt="" />&nbsp;Rapport de bugs&nbsp;&nbsp;</a></li>-->
				<?php if(droit_dacces(10))
				{
					$retour = mysql_query("SELECT COUNT(*) AS nombredesigns FROM designs WHERE active='1' ") OR DIE (mysql_error());
					$donnees = mysql_fetch_array($retour);
					$nombredesigns = $donnees['nombredesigns'];
					?>
					<li class="speedbarre_gauche" id="speedbarre_admin" style="width:145px;"><a href="./admin.html" title="Administration"><img src="./images/speedbarre/password.png" alt="" />&nbsp;Admin </a><?php if($nombredesigns > '0') echo ' / <a href="./admin-1.html">('.$nombredesigns.')</a>';?></li>
					<li class="speedbarre_gauche" id="speedbarre_admin" style="width:145px;"><a href="http://www.zdesigns.fr:2082/" title="Panel"><img src="./images/speedbarre/panel.png" alt="" />&nbsp;Panel </a></li>
					<li class="speedbarre_gauche" id="speedbarre_admin" style="width:145px;"><a href="http://www.zdesigns.fr:2095/3rdparty/roundcube/" title="Mail zDesigns"><img src="./images/speedbarre/mail.png" alt="" />&nbsp;Mails </a></li>
					<?php
				}
				?>
				<li class="speedbarre_gauche" id="speedbarre_admin" style="width:145px;"><a href="./version2.html" title="Avancement de la version 2"><img src="./images/speedbarre/password.png" alt="" />&nbsp;V2 </a></li>
			</ul>
		</div>
		<div class="droite">
			
				<?php if(isset($_SESSION['pseudo']))
				{
					?>
					<!--<li class="speedbarre_droite" id="speedbarre_mp"><a href="" title="Messages privés"><img src="./images/speedbarre/message_privee.png" alt="" />&nbsp;Message(s) privé(s)&nbsp;&nbsp;</a></li>-->
						<form id="deconnect" action="">
						<ul>
							<li class="speedbarre_droite" id="speedbarre_logout"><a href="#" onclick="javascript:deco();" title="Déconnexion"><img src="./images/speedbarre/deconnexion.png" alt="" />&nbsp;Déconnexion&nbsp;(<?php echo $_SESSION['pseudo'];?>)&nbsp;</a></li>
							<li><input type="hidden" name="logout" /></li>
							<li class="speedbarre_gauche" id="speedbarre_mon_compte"><a href="./mon_compte.html" title="Gestion de mes infos"><img src="./images/speedbarre/mon_compte.png" alt="" />&nbsp;Mon compte&nbsp;&nbsp;</a></li>
						</ul>
						</form>
					
					<?php
				}
				else
				{
					?>
					<ul>
					<li class="speedbarre_droite" id="speedbarre_connexion"><a href="./connexion.html" title="Connexion"><img src="./images/speedbarre/connexion.png" alt="" />&nbsp;Connexion&nbsp;&nbsp;</a></li>
					<li class="speedbarre_droite" id="speedbarre_inscription"><a href="./inscription.html" title="Inscription"><img src="./images/speedbarre/inscription.png" alt="" />&nbsp;S'inscrire&nbsp;&nbsp;</a></li>
					</ul>
					<?php
				}
				?>
			
		</div>
	</div>
	<center><span style="color:red;">ATTENTION ! le site est en reconstruction totale. Le design ainsi que la programmation vont être repensés. <a href="./news.html#31">En savoir plus.</a></span></center>
	
	