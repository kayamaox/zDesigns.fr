<?php if(!$_SESSION['mobile'] && isset($_SESSION['pseudo'])){
if(droit_dacces(10))
{
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
	$req_tchat = $BDD->query("SELECT id FROM tchat WHERE lu = '0' AND ignorer = '0'");
	$nb_tchat = 0;
	$nb_tchat = mysql_num_rows($req_tchat);
}
?>
<div id="barre_bas">
    <div id="compte" class="fl">
        <?php if($_SESSION['rang'] == 10){
            $req_valid = $BDD->query("SELECT id FROM designs WHERE active = '1'");
            $req_todo = $BDD->query("SELECT id FROM todolist WHERE statut != '3' AND membre_concerne = '".$_SESSION['id']."'");

            $nb_valid = $nb_todo = 0;
            
            $nb_valid = mysql_num_rows($req_valid);
            $nb_todo = mysql_num_rows($req_todo);

            $somme_admin = $nb_tchat + $nb_valid + $nb_todo;
            ?>
        <div class="menuDeroulant">
            <div class="contentMenu">
                <div class="contentInMenu" id="menu_plus_admin">
                    <ul><li class="titre"><span class="fl">Les zDesigns</span> <span class="fr">(<?php echo $nb_valid + $nb_tchat; ?>)</span></li>
                        <li><a href="<?php echo ROOT ?>admin-zdesigns.html"><span class="fl">Tout voir</span></a></li>                        
                        <li><a href="<?php echo ROOT ?>admin-zdesigns-f2-en-validation.html"><span class="fl">En Validation</span> <span class="fr">(<?php echo $nb_valid; ?>)</span></a></li>
                        <li><a href="<?php echo ROOT ?>tchat.html"><span class="fl">T'chat Admin</span> <span class="fr">(<?php echo $nb_tchat; ?>)</span></a></li>
                    </ul>
                    <ul><li class="titre"><span class="fl">Todo List</span> <span class="fr">(<?php echo $nb_todo; ?>)</span></li>
                        <li><a href="<?php echo ROOT ?>admin-todo.html">Ma todo liste</a></li>
                        <li><a href="<?php echo ROOT ?>admin-todo-add.html">Ajouter tâche</a></li>
                    </ul>
                    <ul><li class="titre"><span class="fl">Plus</span> </li>
                        <li><a href="http://www.zdesigns.fr:2082">cPanel</a></li>
                        <li><a href="<?php echo ROOT ?>forum.html">Forum</a></li>
                        <li><a href="<?php echo ROOT ?>admin.html">Admin >></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <a href="<?php echo ROOT; ?>admin.html" id="plus_admin" class="btn_bas noborder"><span class="label fl">Admin (<?php echo $somme_admin; ?>)</span><span class="fleche">Plus</span></a>
        <?php } else { ?>
        <a href="<?php echo ROOT; ?>tchat.html" id="tchat_link" class="btn_bas noborder"><span class="label">Tchat (<?php echo $nb_tchat; ?>)</span></a>
        <?php } ?>
        <a href="<?php echo ROOT; ?>mon_compte.html" id="mon_compte" class="btn_bas noborder"><span class="label">Mon Compte</span></a>
        <a href="<?php echo ROOT; ?>deconnexion.php" id="deconnexion" class="btn_bas noborder"><span class="label">Deconnexion</span></a>
    </div>
    <div id="centreBarreBas" class="fl"><a id="haut" href="#head" rel="scroll" title="Haut de la page">Haut</a></div>


    <?php
    $req_mes_ds = $BDD->query('SELECT id, id_membre, titre, active, date
                               FROM designs
                               WHERE id_membre = "'.$_SESSION['id'].'"
                               ORDER BY date DESC');

    $online = array(
        'nb'      => 0,
        'designs' => array(),
        'class'   => ''
    );
    $valid  = array(
        'nb'      => 0,
        'designs' => array(),
        'class'   => ''
    );
    $offline= array(
        'nb'      => 0,
        'designs' => array(),
        'class'   => ''
    );
    $suppr= array('designs' => array());
    $status = array(
        0 => 'offline',
        1 => 'valid',
        2 => 'online',
        3 => 'suppr');
    while($d = mysql_fetch_array($req_mes_ds)){
        ${$status[$d['active']]}['designs'][] = $d;
    }
    $online['nb'] = count($online['designs']);
    $valid['nb'] = count($valid['designs']);
    $offline['nb'] = count($offline['designs']);

    $online['class'] = ($online['nb'] == 0) ? 'class="grp_vide"' : '';
    $valid['class'] = ($valid['nb'] == 0) ? 'class="grp_vide"' : '';
    $offline['class'] = ($offline['nb'] == 0) ? 'class="grp_vide"' : '';
    ?>


    <div id="mes_designs" class="fl">
        <div class="menuDeroulant fl">
            <div class="contentMenu">
                <div class="contentInMenu" id="menu_voir_designs">
                    <?php
                    echo '
                    <ul '.$online['class'].'><li class="titre"><span class="fl">En ligne</span> <span class="fr">('.$online['nb'].')</span></li>';
                        foreach($online['designs'] as $d){
                            echo '<li><a href="'.ROOT.'mes_zdesigns-'.$d['id'].'.html" class="titre_design_'.$d['id'].'">'.$d['titre'].'</a></li>';
                        }
                    echo '
                    </ul>
                    <ul '.$valid['class'].'><li class="titre"><span class="fl">En Validation</span> <span class="fr">('.$valid['nb'].')</span></li>';
                        foreach($valid['designs'] as $d){
                            echo '<li><a href="'.ROOT.'mes_zdesigns-'.$d['id'].'.html" class="titre_design_'.$d['id'].'">'.$d['titre'].'</a></li>';
                        }
                    echo '
                    </ul>
                    <ul '.$offline['class'].'><li class="titre"><span class="fl">Hors Ligne</span> <span class="fr">('.$offline['nb'].')</span></li>';
                        foreach($offline['designs'] as $d){
                            echo '<li><a href="'.ROOT.'mes_zdesigns-'.$d['id'].'.html" class="titre_design_'.$d['id'].'">'.$d['titre'].'</a></li>';
                        }
                    echo '
                    </ul>
                    <ul><li class="titre"><span class="fl">Plus</span></li>
                        <li><a href="'.ROOT.'mes_zdesigns-nouveau.html">Nouveau zDesign</a></li>
                        <li><a href="'.ROOT.'mes_zdesigns.html">Tous mes zDesigns</a></li>
                    </ul>';
                    ?>
                </div>
            </div>
        </div>
        <a href="#" id="masquer_bar" class="btn_bas noborder"><span class="label fl">Masquer</span><span class="fleche fleche_x">Masquer cette barre</span></a>
        <a href="<?php echo ROOT; ?>mes_zdesigns.html" id="voir_designs" class="btn_bas noborder"><span class="label fl">Mes zDesigns</span><span class="fleche">Voir</span></a>
        <a href="<?php echo ROOT; ?>zuploader.html" id="zuploader" class="btn_bas noborder"><span class="label">zUploader</span></a>
    </div>
</div>
<div><a href="#" id="afficher_bar" class="noborder dn"><span class="label fl">Afficher</span><span class="fleche">Voir</span></a></div>
<?php } ?>