<?php session_cache_limiter('private');    
session_regenerate_id();  
session_start();
$titre='Formulaire';
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
				<!--Corps de la page-->
					

				<div class="plan">
					<p>Vous êtes ici : <a href="./index.html">zDesigns.fr</a> &gt; Accueil</p>
				</div> <!--div class=plan-->
				
				<h1>The formulaire</h1>
				<div class="news">
							
				<div class="news_titre">
						
						<p class="angle_h_d">
							</p>		
							
						<p class="angle_h_g">							
							</p>
							<p class="titre_news" align="center">Nouveau Sujet</p>
				</div> <!-- Fin div class=news_titre -->	
				
				<div class="bordure_droite">
				<div class="bordure_gauche">
				<div class="news_texte">	
				<br />
				<br />
						<form action="formulaire_ok.php" method="post" />
							<?php include('./includes/editeur.php');?>
							<input type="submit">
						</form>
				</div> <!-- Fin div class=news_contenu -->
				</div>
				</div>		
						<div class="news_bottom">
						<p class="angle_b_d">
							</p>							
						<p class="angle_b_g">							
							</p>
						</div> <!-- Fin div class=news_bottom -->		
				</div>
				<!-- Fin du corps de la page -->
			</div><!--div id=corps-->			
		</div><!-- div id=global-->

		<?php
		include('./includes/bdp.php');
		?>
	</body>
</html>
	