<?php
require_once("./inc/core.php");
require_once('./inc/functions.php');

if(isset($_GET['action']) && droit_dacces(10)){
    $idm = (int) $_GET['id_com'];
    switch($_GET['action']){
        case 'supprimer':
            $BDD->query("UPDATE com_zdesigns
                         SET visible = '0'
                         WHERE id = '".$_GET['id_com']."'");
            $_SESSION['message']['info'] = 'Commentaire supprimé';
            header('Location: '.ROOT.'zdesigns.html');
            exit();
            break;

        case 'editer':
            $_SESSION['message']['alert'] = 'Fonction indiponible';
            header('Location: '.ROOT.'zdesigns.html');
            exit();
            break;
    }
}

$js[] = 'zdesigns';
$js[] = 'zoombox/zoombox';


if(isset($_GET['id']) && !empty($_GET['id'])){
    $titre = 'zDesigns';
    include('./inc/head.php');
    
    $idd = (int) $_GET['id'];
    
    $req_design = $BDD->query("SELECT membres.id AS idm, designs.id AS idd, designs.titre AS titre, membres.pseudo AS pseudo, imprim, description, vu, complet, note
                               FROM designs
                               INNER JOIN membres ON membres.id = designs.id_membre
                               WHERE designs.id='".$idd."' AND designs.active='2' AND designs.complet > '".POURCENT_MIN."'
                               LIMIT 1");
    
    $data4 = mysql_fetch_assoc($req_design);
    ?>
    <div id="zdesigns">
        <div id="details_design_<?php echo $data4['idd']; ?>" class="details_design"  style="height: 100%; overflow: visible;">
            <div class="details_design_in" style="background: none; overflow: hidden;">
                <h1><?php echo ifdecode($data4['titre']); ?></h1>
                <div class="content" style="height: 100%;">
                    <div class="detail_design_home" style="height: 100%;">
                        <div class="apercu">
                            <a href="<?php echo $data4['imprim']; ?>" onclick="$.zoombox.open('<?php echo $data4['imprim']; ?>'); return false;">
                                <img src="<?php echo $data4['imprim']; ?>" alt="" />
                            </a>
                        </div>
                        <div class="informations">
                            <div class="fl description">
                                 <?php echo ifdecode($data4['description']); ?>
                            </div>
                            <div class="fr infos">
                                Designé par  <?php echo $data4['pseudo']; ?><br/>
                                Essayé <?php echo $data4['vu']; ?> fois<br/>
                                Ce design <?php echo $completion[(round($data4['complet']/10)*10)]; ?><br />
                                <?php note($data4['note']); ?>
                            </div>
                        </div>
                        <hr class="clear" />
                        <div class="btns">
                            <a href="/redirection-<?php echo $data4['idd'] ?>.html"
                               class="essayer" style="margin: 0 0 0 225px;">Essayer ce design sur le Site du Zéro</a>
                        </div>
                        <hr class="clear" />
                    </div>
                    <br /><br /><br />
                    <div class="coms" style="height: 100%; border-bottom: none;">
                        <br/><br/>
                        <div class="coms_in">
                            <div class="form_com">
                                <?php
                                if(isset($_SESSION['pseudo'])){
                                    $pseudo = $_SESSION['pseudo'].'<input type="hidden" name="pseudo" value="'.$_SESSION['pseudo'].'" />';
                                } else {
                                    $pseudo = '<label for="pseudo_'.$data4['idd'].'">Pseudo : </label><input type="text" name="pseudo" id="pseudo_'.$data4['idd'].'" value="" />';
                                }
                                $rang = (isset($_SESSION['rang'])) ? $_SESSION['rang'] : 0;
                                if(isset($_SESSION['id']) && $data4['idm'] == $_SESSION['id'] && $rang == 10){
                                    $rang = 'Créateur du design & '.$rangs[$rang];
                                } else if(isset($_SESSION['id']) && $data4['idm'] == $_SESSION['id']){
                                    $rang = 'Créateur du design';
                                } else {
                                    $rang = $rangs[$rang];
                                }
                                $droite = (isset($_SESSION['id']) && $data4['idm'] == $_SESSION['id'] || droit_dacces(10)) ? ' droite' : '';
                                ?>
                                <div class="com<?php echo $droite; ?>">
                                    <form action="./ajax/design.php?nojs=true" method="post">
                                        <span class="auteur"><?php echo $pseudo; ?><span class="rang"><?php echo $rang; ?></span></span>
                                        <div class="com_content">
                                            <label for="message_com_<?php echo $data4['idd']; ?>">Message : </label>
                                            <textarea name="message" id="message_com_<?php echo $data4['idd']; ?>" cols="50" rows="10"></textarea><br />
                                            <input type="hidden" name="id" value="<?php echo $data4['idd']; ?>" />
                                            <input type="hidden" name="url" value="<?php echo url_zdesigns(0, 0, $data4['idd'], ifdecode($data4['titre'])); ?>" />
                                            <input type="hidden" name="action" value="post_com" />
                                            <input type="submit" value="Poster" />
                                        </div>
                                    </form>
                                </div>
                                <hr class="clear" />
                                <br/><br/>
                            </div>

                            <!-- Là il faut boucler pour les commentaires suivant cette strucutre -->
                            <?php
                            $coms = $BDD->query("SELECT com_zdesigns.id AS id, com_zdesigns.id_design AS idd, com_zdesigns.id_membre AS idm, com_zdesigns.pseudo AS com_pseudo,
                                                        com_zdesigns.texte AS com, com_zdesigns.timestamp AS date,
                                                        membres.pseudo AS membre_pseudo, membres.id AS idm, membres.rang AS membre_rang
                                                 FROM com_zdesigns
                                                 INNER JOIN membres
                                                    ON membres.id = com_zdesigns.id_membre
                                                 WHERE com_zdesigns.id_design = '".$data4['idd']."'
                                                    AND com_zdesigns.visible = '1'
                                                 ORDER BY date DESC");
                            if(mysql_num_rows($coms) > 0){
                                while($com = mysql_fetch_assoc($coms)){

                                    if($com['membre_pseudo'] == 'membre'){
                                        $pseudo = ($com['com_pseudo'] != '') ? $com['com_pseudo'] : 'Anonyme';
                                        $rang = $rangs[0];
                                        $droite = '';
                                    } else {
                                        $pseudo = $com['membre_pseudo'];
                                        $rang = $com['membre_rang'];
                                        $droite = ($data4['idm'] == $com['idm'] || $rang == 10) ? ' droite' : '';
                                        if($data4['idm'] == $com['idm'] && $rang == 10){
                                            $rang = 'Créateur du design & '.$rangs[$rang];
                                        } else if($data4['idm'] == $com['idm']){
                                            $rang = 'Créateur du design';
                                        } else {
                                            $rang = $rangs[$rang];
                                        }
                                    }
                                ?>
                                    <div class="com<?php echo $droite; ?>">
                                        <span class="auteur"><?php echo ifdecode($pseudo); ?><span class="rang"><?php echo $rang; ?></span></span>
                                        <p class="commentaire">
                                            <?php echo ifdecode(stripslashes(nl2br($com['com']))); ?>
                                        </p>
                                        <span class="date">
                                            <?php if(droit_dacces(10)){ ?>
                                            <span class="actions_com">
                                                <a href="<?php echo ROOT.'zdesigns-m'.$com['id'].'-editer.html'; ?>">Editer</a> |
                                                <a href="<?php echo ROOT.'zdesigns-m'.$com['id'].'-supprimer.html'; ?>">Supprimer</a> |
                                            </span>
                                            <?php } ?>
                                            Posté <?php echo parse_date($com['date'], false, 'le'); ?>
                                        </span>
                                    </div>
                                    <hr class="clear" />
                                <?php
                                }
                            } else { ?>
                                <br/><br/><br/>
                                <div style="text-align: center;">- Aucun commentaire -</div>
                            <?php } ?>
                            <hr class="clear" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    include('./inc/pied.php');
    include('./inc/barre_bas.php');
    include('./inc/end.php');
    exit();
}

if(isset($_GET['id_cat']) && !(empty($_GET['id_cat']))){
    $id_categorie = (int) $_GET['id_cat'];
} else {
    $id_categorie = 0; 
}
if(isset($_GET['id_tri']) && !(empty($_GET['id_tri']))){
    $id_tri = (int) $_GET['id_tri'];
} else {
    $id_tri = 0; 
}


$titre = 'zDesigns';
include('./inc/head.php');
?>

<div id="arianne">
    <a href="<?php echo ROOT; ?>designs.html">Les zDesigns</a>
</div>
<br/>
<h1>La galerie de zDesigns</h1>

<div id="zdesigns">
    <div class="menu_cats">
        <div class="bloc_cats">
            <span class="titre">Les Catégories : </span>
            <?php
                $retour1 = $BDD->query("SELECT cat.nom, cat.id FROM cat 
                                        INNER JOIN designs
                                        ON (designs.id_cat = cat.id)
                                        WHERE designs.active='2' AND designs.complet > '".POURCENT_MIN."'
										GROUP BY cat.id
                                        ORDER BY cat.nom ASC");
                while($data1 = mysql_fetch_array($retour1)) {
                    $data1['nom'] = ifdecode($data1['nom']);
                    // '<a href="./zdesigns-c'.$data1['id'].'-'.str_replace('_', '-', strtolower(uniforme($data1['nom']))).'.html"';
                    echo '<a href="'.url_zdesigns($data1['id'], $id_tri, null, $data1['nom']).'"';
                    if($id_categorie == $data1['id'])
                         echo ' class="active" ';
                    echo '>'.$data1['nom'].'</a>';
                }
            ?>
        </div>
        <div class="bloc_cats dn">
            <span class="titre">Trier par : </span>
            <a href="<?php echo url_zdesigns($id_categorie, 1, null, 'nombre essais'); ?>">Nombre d'essais</a>
            <a href="<?php echo url_zdesigns($id_categorie, 2, null, 'nombre utilisateurs'); ?>">Nombre d'utilisateurs</a>
            <a href="<?php echo url_zdesigns($id_categorie, 3, null, 'note'); ?>">Note</a>
            <a href="<?php echo url_zdesigns($id_categorie, 4, null, 'completion'); ?>">Pourcentage de complétion</a>
            <a href="<?php echo url_zdesigns($id_categorie, 5, null, 'nombre-commentaires'); ?>">Nombre de commentaires</a>
        </div>
    </div>

    <!-- Là les deux premiers designs en gros -->

    <div class="line_design_une">
        <?php
            if($id_categorie){
                $retour2 = $BDD->query("SELECT id_membre AS idm, id AS idd, titre, imprim, complet, note
                                        FROM designs
                                        WHERE id_cat='".$id_categorie."' AND active='2' AND complet > '".POURCENT_MIN."'
                                        ORDER BY vu DESC
                                        LIMIT 0, 2");
            } else {
                $retour2 = $BDD->query("SELECT id_membre AS idm, id AS idd, titre, imprim, complet, note
                                        FROM designs
                                        WHERE active='2' AND complet > '".POURCENT_MIN."'
                                        ORDER BY vu DESC
                                        LIMIT 0, 2");
            }

            while ($data2 = mysql_fetch_array($retour2)){
                ?>
                <div class="design_une">
                    <div class="design_une_in">
                        <div class="infos_out">
                            <div class="infos">
                                <span class="nom"><?php echo ifdecode($data2['titre']); ?></span>
                                <?php note($data2['note']); ?>
                                <span class="btns">
                                    <a href="/redirection-<?php echo $data2['idd'] ?>.html"
                                       class="essayer">Essayer sur le Site du Zéro</a>
                                    <a href="<?php echo url_zdesigns(0, 0, $data2['idd'], ifdecode($data2['titre'])); ?>" class="details">Détails</a>
                                </span>
                            </div>
                        </div>
                        <img src="<?php echo $data2['imprim']?>" alt="" />
                    </div>
                </div>
                <?php
            }

        ?>
    </div>
    <hr class="clear" />

    <!--
    Là ce sont tous les petits designs
    il faut boucler pour les afficher en gardant la strucutre les id comme il faut y tout y tout
    tous les 3 designs il faut mettre un <hr class="dn sep3" />
    tous les 4 designs il faut mettre un <hr class="dn sep4" />
    -->
    <div class="liste_designs">
        <?php
            if($id_categorie){
                $retour3 = $BDD->query("SELECT id_membre AS idm, id AS idd, titre, mini_imprim, description, complet, note
                                        FROM designs
                                        WHERE id_cat='".$id_categorie."' AND active='2' AND complet > '".POURCENT_MIN."'
                                        ORDER BY vu DESC
                                        LIMIT 2, 1000");
            } else {
                $retour3 = $BDD->query("SELECT id_membre AS idm, id AS idd, titre, mini_imprim, description, complet, note
                                        FROM designs
                                        WHERE active='2' AND complet > '".POURCENT_MIN."'
                                        ORDER BY vu DESC
                                        LIMIT 2, 1000");
            }

            $compteur = 1;
            while ($data3=mysql_fetch_array($retour3))
            {
                 ?>
                <div class="cell_design">
                    <div id="<?php echo $data3['idd']?>" class="design">
                        <div class="apercu">
                            <img src="<?php echo $data3['mini_imprim']?>" alt="" />
                            <div class="infos">
                                <span class="nom"><?php echo ifdecode($data3['titre']); ?></span>
                                <?php note($data3['note']); ?>
                            </div>
                        </div>
                        <span class="description">
                            <?php echo ifdecode($data3['description']); ?>
                        </span>
                        <div class="btns">
                            <a href="/redirection-<?php echo $data3['idd'] ?>.html"
                               class="essayer">Essayer sur le Site du Zéro</a>
                            <a href="<?php echo url_zdesigns(0, 0, $data3['idd'], $data3['titre']); ?>" class="details">Détails</a>
                        </div>
                    </div>
                </div>
                <?php
                if($compteur > 2)
                    echo '<hr class="dn sep'.$compteur.'" />';
                
                $compteur++;
                if($compteur>4)
                     $compteur=1;
            }
        ?>
        <hr class="dn sep3" />
        <hr class="dn sep4" />
    </div>
    <hr class="clear" />
</div>
<hr class="clear" />


<!-- Là il faut boucler pour afficher les détails avec l'id de la div de cette forme là details_design_ID -->
<?php
if($id_categorie){
    $retour4 = $BDD->query("SELECT membres.id AS idm, designs.id AS idd, designs.titre AS titre, membres.pseudo AS pseudo, imprim, description, vu, complet, note
                        FROM designs
                        INNER JOIN membres ON membres.id = designs.id_membre
                        WHERE id_cat='".$id_categorie."' AND active='2' AND complet > '".POURCENT_MIN."'
                        ORDER BY vu DESC");
} else {
    $retour4 = $BDD->query("SELECT membres.id AS idm, designs.id AS idd, designs.titre AS titre, membres.pseudo AS pseudo, imprim, description, vu, complet, note
                        FROM designs
                        INNER JOIN membres ON membres.id = designs.id_membre
                        WHERE active='2' AND complet > '".POURCENT_MIN."'
                        ORDER BY vu DESC");
}

while($data4=mysql_fetch_array($retour4)) {
    ?>
    <div id="details_design_<?php echo $data4['idd']; ?>" class="dn details_design">
        <div class="details_design_in">
            <h1><?php echo ifdecode($data4['titre']); ?></h1>
            <a href="#fermer" class="fermer">Fermer</a>
            <div class="content">
                <div class="detail_design_home fl">
                    <div class="apercu">
                        <a href="<?php echo $data4['imprim']; ?>" onclick="$.zoombox.open('<?php echo $data4['imprim']; ?>'); return false;">
                            <img src="<?php echo $data4['imprim']; ?>" alt="" />
                        </a>
                    </div>
                    <div class="informations">
                        <div class="fl description">
                             <?php echo ifdecode($data4['description']); ?>
                        </div>
                        <div class="fr infos">
                            Designé par  <?php echo $data4['pseudo']; ?><br/>
                            Essayé <?php echo $data4['vu']; ?> fois<br/>
                            Ce design <?php echo $completion[(round($data4['complet']/10)*10)]; ?><br />
                            <?php note($data4['note']); ?> <a href="#voter" class="vote_link">Voter</a>
                            <div class="form_note dn">
                                <ul id="note_<?php echo $data4['idd']; ?>" class="zone_etoiles">
                                    <li>
                                        <label for="note01" title="Note : 1 sur 5" class="ico etoile">1</label>
                                        <input type="radio" name="note" id="note01" value="1" />
                                    </li>
                                    <li>
                                        <label for="note02" title="Note : 2 sur 5" class="ico etoile">2</label>
                                        <input type="radio" name="note" id="note02" value="2" />
                                    </li>
                                    <li>
                                        <label for="note03" title="Note : 3 sur 5" class="ico etoile">3</label>
                                        <input type="radio" name="note" id="note03" value="3" />
                                    </li>
                                    <li>
                                        <label for="note04" title="Note : 4 sur 5" class="ico etoile">4</label>
                                        <input type="radio" name="note" id="note04" value="4" />
                                    </li>
                                    <li>
                                        <label for="note05" title="Note : 5 sur 5" class="ico etoile">5</label>
                                        <input type="radio" name="note" id="note05" value="5" />
                                    </li>
                                </ul>
                                <a href="#quit_voter" class="vote_link">Annuler</a>
                            </div>
                        </div>
                    </div>
                    <hr class="clear" />
                    <div class="btns">
                        <a href="/redirection-<?php echo $data4['idd'] ?>.html"
                           class="essayer">Essayer ce design sur le Site du Zéro</a>
                        <a href="#voir_coms" class="details">Voir les commentaires | Commenter</a>
                    </div>
                    <hr class="clear" />
                </div>
                <div class="fl coms">
                    <div class="btns dn">
                        <a href="#voir_fiche" class="details">Retour à la fiche du design</a>
                    </div>
                    <a href="#form_com" class="link_go_form">Poster un commentaire</a>
                    <br/><br/>
                    <div class="coms_in">
                        <div class="form_com dn">
                            <?php
                            if(isset($_SESSION['pseudo'])){
                                $pseudo = $_SESSION['pseudo'].'<input type="hidden" name="pseudo" value="'.$_SESSION['pseudo'].'" />';
                            } else {
                                $pseudo = '<label for="pseudo_'.$data4['idd'].'">Pseudo : </label><input type="text" name="pseudo" id="pseudo_'.$data4['idd'].'" value="" />';
                            }
                            $rang = (isset($_SESSION['rang'])) ? $_SESSION['rang'] : 0;
                            if(isset($_SESSION['id']) && $data4['idm'] == $_SESSION['id'] && $rang == 10){
                                $rang = 'Créateur du design & '.$rangs[$rang];
                            } else if(isset($_SESSION['id']) && $data4['idm'] == $_SESSION['id']){
                                $rang = 'Créateur du design';
                            } else {
                                $rang = $rangs[$rang];
                            }
                            $droite = (isset($_SESSION['id']) && $data4['idm'] == $_SESSION['id'] || droit_dacces(10)) ? ' droite' : '';
                            ?>
                            <div class="com<?php echo $droite; ?>">
                                <form action="./ajax/design.php" method="post">
                                    <span class="auteur"><?php echo $pseudo; ?><span class="rang"><?php echo $rang; ?></span></span>
                                    <div class="com_content">
                                        <label for="message_com_<?php echo $data4['idd']; ?>">Message : </label>
                                        <textarea name="message" id="message_com_<?php echo $data4['idd']; ?>" cols="50" rows="10"></textarea><br />
                                        <input type="submit" value="Poster" />
                                    </div>
                                </form>
                            </div>
                            <hr class="clear" />
                            <br/><br/>
                        </div>
                        
                        <!-- Là il faut boucler pour les commentaires suivant cette strucutre -->
                        <?php
                        $coms = $BDD->query("SELECT com_zdesigns.id AS id, com_zdesigns.id_design AS idd, com_zdesigns.id_membre AS idm, com_zdesigns.pseudo AS com_pseudo,
                                                    com_zdesigns.texte AS com, com_zdesigns.timestamp AS date,
                                                    membres.pseudo AS membre_pseudo, membres.id AS idm, membres.rang AS membre_rang
                                             FROM com_zdesigns
                                             INNER JOIN membres
                                                ON membres.id = com_zdesigns.id_membre
                                             WHERE com_zdesigns.id_design = '".$data4['idd']."'
                                                AND com_zdesigns.visible = '1'
                                             ORDER BY date DESC");
                        if(mysql_num_rows($coms) > 0){
                            while($com = mysql_fetch_assoc($coms)){

                                if($com['membre_pseudo'] == 'membre'){
                                    $pseudo = ($com['com_pseudo'] != '') ? $com['com_pseudo'] : 'Anonyme';
                                    $rang = $rangs[0];
                                    $droite = '';
                                } else {
                                    $pseudo = $com['membre_pseudo'];
                                    $rang = $com['membre_rang'];
                                    $droite = ($data4['idm'] == $com['idm'] || $rang == 10) ? ' droite' : '';
                                    if($data4['idm'] == $com['idm'] && $rang == 10){
                                        $rang = 'Créateur du design & '.$rangs[$rang];
                                    } else if($data4['idm'] == $com['idm']){
                                        $rang = 'Créateur du design';
                                    } else {
                                        $rang = $rangs[$rang];
                                    }
                                }
                            ?>
                                <div class="com<?php echo $droite; ?>">
                                    <span class="auteur"><?php echo ifdecode($pseudo); ?><span class="rang"><?php echo $rang; ?></span></span>
                                    <p class="commentaire">
                                        <?php echo ifdecode(stripslashes(nl2br($com['com']))); ?>
                                    </p>
                                    <span class="date">
                                        <?php if(droit_dacces(10)){ ?>
                                        <span class="actions_com">
                                            <a href="<?php echo ROOT.'zdesigns-m'.$com['id'].'-editer.html'; ?>">Editer</a> |
                                            <a href="<?php echo ROOT.'zdesigns-m'.$com['id'].'-supprimer.html'; ?>">Supprimer</a> |
                                        </span>
                                        <?php } ?>
                                        Posté <?php echo parse_date($com['date'], false, 'le'); ?>
                                    </span>
                                </div>
                                <hr class="clear" />
                            <?php
                            }
                        } else { ?>
                            <br/><br/><br/>
                            <div style="text-align: center;">- Aucun commentaire -</div>
                        <?php } ?>
                        <hr class="clear" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
}
?>

<div id="empty_com" class="dn">
    <div class="com empty">
        <span class="auteur"><span class="rang"></span></span>
        <p class="commentaire">

        </p>
        <span class="date">Posté il y a quelques secondes</span>
    </div>
    <hr class="clear" />
</div>

<?php
include('./inc/pied.php');
include('./inc/barre_bas.php');
include('./inc/end.php');
?>