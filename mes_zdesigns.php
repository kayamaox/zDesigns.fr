<?php
session_start();
if(!isset($_SESSION['pseudo'])){
    $_SESSION['message']['alert'] = "Vous devez être connecté";
    header('Location: ./');
    exit();
}


require_once('./inc/core.php');
require_once('./inc/functions.php');

$is_design = false;
if(isset($_GET['id'])){
    $idd = (int) $_GET['id'];    
    $req_d = $BDD->query("SELECT designs.id AS id, designs.id_membre AS id_membre, designs.titre AS titre, designs.description AS description, designs.imprim AS imprim,
                                 designs.mini_imprim AS mini_imprim, designs.active AS active, designs.complet AS complet, designs.id_cat AS id_cat, designs.note AS note,
                                 designs.date AS date, membres.id AS idm, membres.pseudo AS pseudo
                          FROM designs
                          INNER JOIN membres
                              ON designs.id_membre = membres.id
                          WHERE designs.id='".$idd."
                          LIMIT 0, 1'");
    
    if(mysql_num_rows($req_d) != 1){
        $_SESSION['message']['alert'] = "Ce design n'existe pas";
        header('Location: ./mes_zdesigns.html');
        exit();
    }
    $design = mysql_fetch_assoc($req_d);

    if(!droit_dacces(10) && $design['id_membre'] != $_SESSION['id']){
        $is_design = false;
        $_SESSION['message']['alert'] = "Ce design n'est pas le votre";
        header('Location: ./mes_zdesigns.html');
        exit();
    } else {
        $design_dir = './designs/'.$design['id_membre'].'/'.$idd.'_dev/';
        if(is_dir($design_dir)){
            $is_design = true;
        } else {
            $suppr_dir = './designs/'.$design['id_membre'].'/'.$idd.'_del/';
            $public_dir = './designs/'.$design['id_membre'].'/'.$idd.'/';
            if(is_dir($public_dir)){
                copy_folder($public_dir, $design_dir);
                $is_design = true;
            } elseif(is_dir($suppr_dir) && isset($_GET['action']) && ($_GET['action'] == 'supprimer' || $_GET['action'] == 'recuperer')){
                $is_design = true;
            } else {
                $_SESSION['message']['alert'] = "Ce design est introuvable";
                header('Location: ./mes_zdesigns.html');
                exit();
            }
        }
    }
}


// Liste des catégories
$cats_req = $BDD->query("SELECT id, nom FROM cat");
$cats = array(
    0 => "Aucune"
);
while($cat = mysql_fetch_assoc($cats_req)){
    $cats[(int)$cat['id']] = $cat['nom'];
}


// On ne veux pas accéder à un design
//             OU
// Ce design n'existe pas
// Donc on affiche la liste des designs de l'auteur
if(!$is_design && !isset($_GET['action'])){
    $titre = 'Mes zDesigns';
    $js[] = 'popup';
    include('./inc/head.php');
    ?>
    <div id="arianne">
        <a href="<?php echo ROOT; ?>mes_zdesigns.html">Mes zDesigns</a>
    </div>
    <br />
    <h1>Mes zDesigns</h1>

    <div id="mes_zdesigns">
        <?php
        $designs_suppr = array();
        $mes_zdesigns = $BDD->query("SELECT id, id_membre, titre, description, imprim, mini_imprim, active, complet, id_cat AS cat, note, date_suppr
                                     FROM designs
                                     WHERE id_membre = '".$_SESSION['id']."'
                                     ORDER BY active DESC");

        while($d = mysql_fetch_assoc($mes_zdesigns)){
            if($d['active'] != '3'){
                $nb_coms = mysql_num_rows($BDD->query("SELECT id_design, visible
                                                       FROM com_zdesigns
                                                       WHERE id_design = '".$d['id']."'
                                                           AND visible = '1'"));
                ?>
                <div class="design">
                    <h3><?php echo $d['titre']; ?></h3>
                    <div class="proprietes_design_in">
                        <div class="apercu">
                            <?php if(is_file($d['imprim'])){ ?>
                            <a href="<?php echo $d['imprim']; ?>" class="zoombox zgallery_mes_zdesigns">
                                <img src="<?php echo $d['mini_imprim']; ?>" alt="<?php echo $d['titre']; ?>" />
                            </a>
                            <?php } else { ?>
                            <br /><br />
                            <i>Visuel indisponible</i>
                            <?php } ?>
                        </div>
                        <div class="description">
                            <?php echo $d['description']; ?>
                            <a href="./mes_zdesigns-<?php echo $d['id']; ?>.html" class="btn_editer">Editer avec le zExplorer <span class="fleche"></span></a>
                        </div>
                        <div class="options">
                            <?php note($d['note']); ?><br />
                            <span><?php echo pluralize($nb_coms, '{Aucun|Un|{#}} commentaire{s}'); ?></span><br />
                            <span>Complet à <?php echo $d['complet']; ?>%</span><br />
                            <span>Catégorie : <?php echo $cats[(int)$d['cat']];  ?></span><br />
                            <span>Etat :
                                <b style="color: <?php echo $etats[(int)$d['active']]['couleur']; ?>">
                                    <?php echo $etats[(int)$d['active']]['etat']; ?>
                                </b>
                            </span><br />
                            <hr />
                            L'Essayer sur le SdZ : <a href="http://www.siteduzero.com/designs.html?design=<?php echo ROOT_ABS.'designs/'.$d['id_membre'].'/'.$d['id'].'_dev/'; ?>">Dev</a>
                            <?php if($d['active'] == 2){ ?> | <a href="http://www.siteduzero.com/designs.html?design=<?php echo ROOT_ABS.'designs/'.$d['id_membre'].'/'.$d['id'].'/'; ?>">Public</a><?php } ?><br/>
                            <a href="./mes_zdesigns-<?php echo $d['id']; ?>-telecharger.html">Télécharger les sources</a><br/>
                            <a href="javascript:void(0);" rel="./mes_zdesigns-<?php echo $d['id']; ?>-supprimer.html"
                               onclick="$('#suppr_design').fadeIn(200); $('#suppr_design form').attr('action', $(this).attr('rel')); return false;">
                                Supprimer <em><span class="titre_design_<?php echo $d['id']?>"><?php echo $d['titre']; ?></span></em>
                            </a><br/><br />
                        </div>
                    </div>
                </div>
                <?php
            } else {
                $designs_suppr[] = $d;
            }
        }

        if(count($designs_suppr) > 0){
            ?>
            <br/><br/>
            <h1>Designs supprimés encore récupérables</h1>
            Les zdesigns sont encore récupérables environs une semaine après leur suppression.
            <ul class="zcode">
                <?php
                foreach($designs_suppr as $d){
                    ?>
                    <li>
                        <b class="titre"><?php echo $d['titre']; ?></b> | 
                        <span>Supprimé <?php echo parse_date($d['date_suppr'], false, 'le'); ?></span>
                        <a href="./mes_zdesigns-<?php echo $d['id']; ?>-recuperer.html">Récupérer</a>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php
        }
        ?>
        <br/><br/>
    </div>

    <div id="suppr_design">
        <form action="./mes_zdesigns.html" method="post">
            <input type="hidden" name="suppr" value="oui" />
            <input type="submit" value="Supprimer ce design" />
            <input type="button" value="Annuler" onclick="$('#suppr_design').fadeOut(200); return false;" />
        </form>
    </div>

    <script type="text/javascript">
        $('#suppr_design').popup({
            titre:'Supprimer le design',
            zIndexMin:80,
            autoClean: false
        });
    </script>

    <?php
    include('./inc/pied.php');
    include('./inc/barre_bas.php');
    include('./inc/end.php');
    exit();
} elseif(!$is_design && isset($_GET['action'])){
    if($_GET['action'] == 'nouveau'){
        $titre = 'Nouveau zDesign';
        $js[] = 'popup';
        include('./inc/head.php');
        ?>
        <div id="arianne">
            <a href="<?php echo ROOT; ?>mes_zdesigns.html">Mes zDesigns</a> > <a href="<?php echo ROOT; ?>mes_zdesigns-nouveau.html">Nouveau design</a>
        </div>
        <br />
        <h1>Créer un nouveau design</h1>
        <div class="zcode" style="text-align: center;">
            <img src="<?php echo DESIGN_DIR; ?>images/new_zdesign.jpg" alt="Nouveau zDesigns" /><br /><br />
            <a href="./zuploader.html">zUploader</a> |
            <a href="./mes_zdesigns-newvide.html">À partir du design officiel</a>
        </div>
        <?php
        include('./inc/pied.php');
        include('./inc/barre_bas.php');
        include('./inc/end.php');
        exit();
    } elseif($_GET['action'] == 'newvide'){
        if(!is_dir('./designs/'.$_SESSION['id'].'/')){
            mkdir('./designs/'.$_SESSION['id'], 0777);
        }
        $BDD->query("INSERT INTO designs (id_membre, titre, active, vu, date, complet, note)
                                   VALUES('".$_SESSION['id']."', 'design_sans_titre', '0', '0', '".time()."', '0', '0')");
        $idd = mysql_insert_id();
        copy_folder(DESIGN_ORIGINAL, './designs/'.$_SESSION['id'].'/'.$idd.'/');
        header('Location: ./mes_zdesigns-'.$idd.'.html');
        exit();
    }
}


function redir(){
    global $design;
    header('Location: '.ROOT.'mes_zdesigns-'.$design['id'].'.html');
    exit();
}
if(isset($_GET['action'])){
    switch($_GET['action']){
        case 'telecharger':
            require_once('./classes/zip.php');
            // on récupère le chemin et le contenu de tous
            // les fichiers contenus dans le dossier qu'on veut zipper
            $files = scanFolderToZip($design_dir);

            // on créé l'archive
            $zip = new zipfile();
            foreach($files as $file) {
               $zip->addfile($file["content"], str_replace($design_dir, '', $file["name"]));
            }
            $archive = $zip->file();

            // on récupère le fichier zip
            header('Content-Type: application/x-zip');
            header('Content-Disposition: inline; filename="'.str_replace(' ', '-', $design['titre']).'_'.date('H\hi_d-m-Y').'.zip"');
            echo $archive;
            exit();
            break;


        case 'valider':
            if(droit_dacces(10)){
                require_once('./classes/rapport.php');

                $idd = $design['id'];
                $idm = $design['id_membre'];

                copy_folder('./designs/'.$idm.'/'.$idd.'_dev/', './designs/'.$idm.'/'.$idd.'/');
                $rapport = new Rapport($exts);
                $rapport->compare('./designs/'.$idm.'/'.$idd.'/', DESIGN_ORIGINAL);
                
                $BDD->query("UPDATE designs
                             SET active = '2', complet = '".$rapport->getPourcent()."'
                             WHERE id = '".$idd."'");
                $design['active'] = 2;

                $_SESSION['message']['info'] = "Le design est validé";
            }
            redir();
            break;


        case 'demandevalidation':
            $BDD->query("UPDATE designs
                         SET active = '1'
                         WHERE id = '".$design['id']."'");
            $design['active'] = 1;
            $_SESSION['message']['info'] = "Demande de validation émise";

            if(!droit_dacces(10)){
                $message = "Salut les admins !\n\r".$_SESSION['pseudo']." vient de demander la validation de son pack_design!\n\rVoici son lien :\n\rhttp://www.zdesigns.fr/mes_zdesigns-".$design['id'].".html";
                $sujet = "zDesign en attente de validation !";
                @mail_all($sujet, $message);
            }

            redir();
            break;


        case 'devalider':
            $BDD->query("UPDATE designs
                         SET active = '0'
                         WHERE id = '".$design['id']."'");
            $design['active'] = 0;
            $_SESSION['message']['info'] = "Le design est dévalidé";
            redir();
            break;


        case 'supprimer':
            if(isset($_POST['suppr'])){
                $BDD->query("UPDATE designs
                             SET active = '3', date_suppr = '".time()."'
                             WHERE id = '".$design['id']."'");

                rename($design_dir, str_replace('_dev', '_del', $design_dir));
                supprDir(str_replace('_dev', '', $design_dir));
                $_SESSION['message']['info'] = "Design supprimé";
                if(isset($_GET['from'])){
                    header('Location: ./'.$_GET['from'].'.html');
                } else {
                    header('Location: ./mes_zdesigns.html');
                }
                exit();
            }
            break;


        case 'recuperer':
            $BDD->query("UPDATE designs
                         SET active = '0', date_suppr = ''
                         WHERE id = '".$design['id']."'");
            rename(str_replace('_dev', '_del', $design_dir), $design_dir);
            $_SESSION['message']['info'] = "Le design a été récupéré";
            redir();
            break;


        case 'uploadapercu':
            if(isset($_FILES['uploadFile']) && $_FILES['uploadFile']['error'] == 0) {
                $tmp = './designs/'.$design['id_membre'].'/temp_'.$design['id'].'.jpg';

                $filename = uniforme($_FILES['uploadFile']['name']);
                $extension = strtolower(end(explode(".", $filename)));
                
                if(array_key_exists($extension, $exts)){
                    $dest1 = './designs/'.$design['id_membre'].'/'.$design['id'].'.jpg';
                    $largeur1 = 1350;

                    $dest2 = './designs/'.$design['id_membre'].'/mini_'.$design['id'].'.jpg';
                    $largeur2 = 250;

                    if(is_file($dest1)){
                        unlink($dest1);
                        unlink($dest2);
                    }
                    if(is_file($tmp)){
                        unlink($tmp);
                    }
                    if(@move_uploaded_file($_FILES['uploadFile']['tmp_name'], $tmp)){
                        require_once('./classes/images.php');
                        if(@Image::resize($tmp, $dest1, $largeur1) && @Image::resize($tmp, $dest2, $largeur2)){
                            $BDD->query("UPDATE designs
                                         SET imprim = '".$dest1."', mini_imprim = '".$dest2."'
                                         WHERE id = '".$design['id']."'");
                        }
                        unlink($tmp);
                    }
                }
            }
            redir();
            break;
    }
}


$titre = 'zExplorer, '.$design['titre'];

$js[] = 'zexplorer';
$js[] = 'popup';
$js[] = 'codemirror/js/codemirror';
$js[] = 'zoombox/zoombox'; // Inclu automatiquement le CSS associé
$js[] = "flot/flot";
$js[] = "flot/selection";

include('./inc/head.php');

$sizeDesign = sizethis($design_dir);
$pourcent = round(100 * ($sizeDesign/SIZE_MAX));
$pourcent = ($pourcent > 100) ? 100 : $pourcent;
$sizeDesign = ($sizeDesign > 1024) ? round($sizeDesign/1024).' ko' : $sizeDesign.' o';
$maxSize = ($pourcent >= 90) ? 'maxSize': '';

/*
$req_stats2 = $BDD->query("SELECT ip, id_design, time
                           FROM stats_designs
                           WHERE id_design = '".$design['id']."'
                               AND time > '".(time()-(3600*24*7))."'
                           ORDER BY time");
 * 
 */
?>

<script type="text/javascript">
    var message = null;
    var messageType = null;
    if($('#messageInfo div').html() != ''){
        message = $('#messageInfo div').text();
        messageType = $('#messageInfo div').attr('class');
        $('#messageInfo div').empty().hide();
    }
    $('#messageInfo .loading .label').empty().append('Chargement de la <b>page</b>');
    $('#messageInfo .loading').css({'display': 'block'});
    $(window).ready(function(){
        $('#messageInfo .loading .label').slideUp(250, function(){
            $(this).empty().append('Chargement des <b>images</b>').slideDown();
        });
        $('#block_all').fadeOut(150, function(){
            $(this).remove();
        });
    });
    window.onload = function(){
        $('#messageInfo .loading').hide();
        addMessage('info', 'Chargement terminé');
        if(message != null){
            setTimeout(function(){
                addMessage(messageType, message);
            }, 1500);
        }
        setTimeout(function(){
            animJauge(<?php echo $pourcent; ?>, 2500);
        }, 1000);

        if($('#rapport_link').hasClass('anim_valid')){
            setTimeout(function(){
                $('#rapport_link').flashValid(true, 1000);
            }, 4500);
        } else {
            var nbFlash = 3;
            for(var i = 1; i <= nbFlash; i++){
                setTimeout(function(){
                    $('#rapport_link').flashValid(false, 500, '#ff9f9f');
                }, 3000 + 1500*i);
            }
        }
    };
</script>

<div id="arianne">
    <a href="<?php echo ROOT; ?>mes_zdesigns.html">Mes zDesigns</a> > zExplorer > <a href="<?php echo ROOT; ?>mes_zdesigns-<?php echo $design['id']?>.html" class="titre_design_<?php echo $design['id']?>"><?php echo $design['titre']; ?></a>
</div>
<br/>
<h1><span class="titre_design_<?php echo $design['id']; ?>"><?php echo $design['titre']; ?></span><?php if(droit_dacces(10)){ echo ' by <a href="'.ROOT.'tchat-'.$design['id_membre'].'.html">'.$design['pseudo'].'</a>'; } ?></h1>
<a id="proprietes_design_link" href="javascript:void();" rel="infobulle" title="Accès à toutes les options du design">
    Afficher les propriétés
</a>
<div id="jauge_design">
    <span class="infos <?php echo $maxSize; ?>">
        <?php
        $sizeMax = (SIZE_MAX > 1024) ? round(SIZE_MAX/1024).' ko' : SIZE_MAX.' o';
        echo 'Taille du zDesign : <span id="size_design">'.$sizeDesign.'</span> / '.$sizeMax.' <span class="fr">'.$pourcent.'%</span>';
        $width = (280/100)*$pourcent;
        $color = colJauge($pourcent);
        ?>
    </span>
    <div id="jauge_design_in" style="width: <?php echo $width; ?>px; background-color: <?php echo $color; ?>;"></div>
    <script type="text/javascript">
        $('#jauge_design_in').css({
            'width': 0,
            'background-color': '#8aff00'
        });
    </script>
</div>

<div id="block_all"></div>

<div id="proprietes_design" class="dn">
    <h3>
        Propriétés de <span class="titre_design_<?php echo $design['id']; ?>"><?php echo $design['titre']; ?></span> | Catégorie <em id="cat_design"><?php echo $cats[$design['id_cat']]; ?></em>
        <?php if(FALSE && mysql_num_rows($req_stats2) != 0){ ?>
        <a href="javascript:void(0);" id="stats_link"><span class="label">Afficher les Statistiques d'utilisation</span><span class="ico arrow-down"></span></a>
        <?php } ?>
    </h3>
    <?php if(FALSE && mysql_num_rows($req_stats2) != 0){ ?>
    <div id="stats" class="dn">
        <div id="col_graph1">
            <b>Utilisateurs</b>
            <div id="graph1"></div>
            <div id="graph1_nav"></div>
        </div>

        <div id="col_graph2">
            <b>Pages chargées avec <?php echo $design['titre']; ?></b>
            <div id="graph2"></div>
            <div id="graph2_nav"></div>
        </div>

        <hr class="clear" />
        <?php
        $stats = array(
            'jour' => array(),
            'semaine' => array(),
            'mois' => array(),
            'an' => array()
        );
        $stats2 = array(
            'jour' => array(),
            'semaine' => array(),
            'mois' => array(),
            'an' => array()
        );
        $req_stats = $BDD->query("SELECT ip, id_design, time
                                  FROM stats_designs
                                  WHERE id_design = '".$design['id']."'
                                      AND time > '".(time()-(3600*24*7))."'
                                  GROUP BY ip
                                  ORDER BY time");

        while($s = mysql_fetch_assoc($req_stats)){
            if($s['time'] >= mktime(0, 0, 0)){
                $stats['jour'][] = $s;
            }
            if($s['time'] >= (mktime(0, 0, 0)-3600*24*7)){
                $stats['semaine'][] = $s;
            }
            if($s['time'] >= (mktime(0, 0, 0)-3600*24*7*4)){
                $stats['mois'][] = $s;
            }
            if($s['time'] >= (mktime(0, 0, 0)-3600*24*7*4*12)){
                $stats['an'][] = $s;
            }
        }
        while($s2 = mysql_fetch_assoc($req_stats2)){
            $r_stats[get_time_hour($s2['time'])][$s2['ip']] = $s2;
            $r_stats2[] = $s2;
            if($s2['time'] >= mktime(0, 0, 0)){
                $stats2['jour'][] = $s2;
            }
            if($s2['time'] >= (mktime(0, 0, 0)-3600*24*7)){
                $stats2['semaine'][] = $s2;
            }
            if($s2['time'] >= (mktime(0, 0, 0)-3600*24*7*4)){
                $stats2['mois'][] = $s2;
            }
            if($s2['time'] >= (mktime(0, 0, 0)-3600*24*7*4*12)){
                $stats2['an'][] = $s2;
            }
        }
        
        sort($r_stats2);


        echo '<b>'.count($stats['jour']).'</b> personnes ont utilisé '.$design['titre'].' aujourd\'hui<br />';
        echo '<b>'.count($stats['semaine']).'</b> personnes ont utilisé '.$design['titre'].' ces 7 derniers jours<br />';
        echo '<b>'.count($stats['mois']).'</b> personnes ont utilisé '.$design['titre'].' ces 30 derniers jours<br />';
        echo '<b>'.count($stats['an']).'</b> personnes ont utilisé '.$design['titre'].' ces 365 derniers jours<br />';
        echo '<b>'.mysql_num_rows($req_stats).'</b> personnes ont déjà utilisé '.$design['titre'].'<br />';
        echo '<b>'.count($stats2['jour']).'</b> pages chargées sur le SdZ utilisant '.$design['titre'].' aujourd\'hui<br />';
        echo '<b>'.count($stats2['semaine']).'</b> pages chargées sur le SdZ utilisant '.$design['titre'].' ces 7 derniers jours<br />';
        echo '<b>'.count($stats2['mois']).'</b> pages chargées sur le SdZ utilisant '.$design['titre'].' ces 30 derniers jours<br />';
        echo '<b>'.count($stats2['an']).'</b> pages chargées sur le SdZ utilisant '.$design['titre'].' ces 365 derniers jours<br />';
        echo '<b>'.mysql_num_rows($req_stats2).'</b> pages chargées sur le SdZ utilisant '.$design['titre'].'<br />';
        
        $g1 = array();      $d1 = '';
        $g2 = array();      $d2 = '';

        // $first_time = get_time_hour($r_stats2[0]['time']);
        $first_time = get_time_hour(time()-3600*24*7);
        $last_time = get_time_hour(time())-1;
        $nb_hour = ($last_time - $first_time)/3600;

        for($i = 0; $i <= $nb_hour; $i++){
            if($first_time + 3600*$i != get_time_hour(time())){
                $g1[$first_time + 3600*$i] = 0;
                $g2[$first_time + 3600*$i] = 0;
            }
        }

        foreach($r_stats as $h=>$s){
            foreach($s as $k=>$v){
                if($h != get_time_hour(time())){
                    $g1[$h] += 1;
                }
            }
        }

        foreach($g1 as $d=>$n){
            if(date('I', $d) == 1){
                $d += 3600;
            }
            $d1 .= '['.($d+3600).'000, '.$n.'], ';
        }
        $d1 = substr($d1, 0, -2);


        foreach($r_stats2 as $s){
            if(get_time_hour($s['time']) != get_time_hour(time())){
                $g2[get_time_hour($s['time'])] += 1;
            }
        }

        foreach($g2 as $d=>$n){
            if(date('I', $d) == 1){
                $d += 3600;
            }
            $d2 .= '['.($d+3600).'000, '.$n.'], ';
        }
        $d2 = substr($d2, 0, -2);
        ?>
        <script id="source" type="text/javascript">
            function showStats(){
                // var d = [[1196463600000, 0], [1196550000000, 0], [1196636400000, 0], [1196722800000, 77], [1196809200000, 3636], [1196895600000, 3575], [1196982000000, 2736], [1197068400000, 1086], [1197154800000, 676], [1197241200000, 1205], [1197327600000, 906], [1197414000000, 710], [1197500400000, 639], [1197586800000, 540], [1197673200000, 435], [1197759600000, 301], [1197846000000, 575], [1197932400000, 481], [1198018800000, 591], [1198105200000, 608], [1198191600000, 459], [1198278000000, 234], [1198364400000, 1352], [1198450800000, 686], [1198537200000, 279], [1198623600000, 449], [1198710000000, 468], [1198796400000, 392], [1198882800000, 282], [1198969200000, 208], [1199055600000, 229], [1199142000000, 177], [1199228400000, 374], [1199314800000, 436], [1199401200000, 404], [1199487600000, 253], [1199574000000, 218], [1199660400000, 476], [1199746800000, 462], [1199833200000, 448], [1199919600000, 442], [1200006000000, 403], [1200092400000, 204], [1200178800000, 194], [1200265200000, 327], [1200351600000, 374], [1200438000000, 507], [1200524400000, 546], [1200610800000, 482], [1200697200000, 283], [1200783600000, 221], [1200870000000, 483], [1200956400000, 523], [1201042800000, 528], [1201129200000, 483], [1201215600000, 452], [1201302000000, 270], [1201388400000, 222], [1201474800000, 439], [1201561200000, 559], [1201647600000, 521], [1201734000000, 477], [1201820400000, 442], [1201906800000, 252], [1201993200000, 236], [1202079600000, 525], [1202166000000, 477], [1202252400000, 386], [1202338800000, 409], [1202425200000, 408], [1202511600000, 237], [1202598000000, 193], [1202684400000, 357], [1202770800000, 414], [1202857200000, 393], [1202943600000, 353], [1203030000000, 364], [1203116400000, 215], [1203202800000, 214], [1203289200000, 356], [1203375600000, 399], [1203462000000, 334], [1203548400000, 348], [1203634800000, 243], [1203721200000, 126], [1203807600000, 157], [1203894000000, 288]];
                var d = [<?php echo $d1; ?>];

                // helper for returning the weekends in a period
                function weekendAreas(axes) {
                    var markings = [];
                    var d = new Date(axes.xaxis.min);
                    // go to the first Saturday
                    d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
                    d.setUTCSeconds(0);
                    d.setUTCMinutes(0);
                    d.setUTCHours(0);
                    var i = d.getTime();
                    do {
                        // when we don't set yaxis, the rectangle automatically
                        // extends to infinity upwards and downwards
                        markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
                        i += 7 * 24 * 60 * 60 * 1000;
                    } while (i < axes.xaxis.max);

                    return markings;
                }

                var options = {
                    xaxis: { mode: "time", tickLength: 5 },
                    selection: { mode: "x" },
                    grid: { markings: weekendAreas, hoverable: true },
                    series: {
                        lines: { show: true },
                        points: { show: true }
                    }
                };

                var plot = $.plot($("#graph1"), [d], options);

                var overview = $.plot($("#graph1_nav"), [d], {
                    series: {
                        lines: { show: true, lineWidth: 1 },
                        shadowSize: 0
                    },
                    xaxis: { ticks: [], mode: "time" },
                    yaxis: { ticks: [], min: 0, autoscaleMargin: 1 },
                    selection: { mode: "x" }
                });

                // now connect the two

                $("#graph1").bind("plotselected", function (event, ranges) {
                    // do the zooming
                    plot = $.plot($("#graph1"), [d],
                                  $.extend(true, {}, options, {
                                      xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
                                  }));

                    // don't fire event on the overview to prevent eternal loop
                    overview.setSelection(ranges, true);
                });

                $("#graph1_nav").bind("plotselected", function (event, ranges) {
                    plot.setSelection(ranges);
                });


                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                var d2 = [<?php echo $d2; ?>];

                var options2 = {
                    xaxis: { mode: "time", tickLength: 5 },
                    selection: { mode: "x" },
                    grid: { markings: weekendAreas, hoverable: true },
                    series: {
                        lines: { show: true },
                        points: { show: true }
                    }
                };

                var plot2 = $.plot($("#graph2"), [d2], options);

                var overview2 = $.plot($("#graph2_nav"), [d2], {
                    series: {
                        lines: { show: true, lineWidth: 1 },
                        shadowSize: 0
                    },
                    xaxis: { ticks: [], mode: "time" },
                    yaxis: { ticks: [], min: 0, autoscaleMargin: 1 },
                    selection: { mode: "x" }
                });

                // now connect the two

                $("#graph2").bind("plotselected", function (event, ranges) {
                    // do the zooming
                    plot2 = $.plot($("#graph2"), [d2],
                                  $.extend(true, {}, options2, {
                                      xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
                                  }));

                    // don't fire event on the overview to prevent eternal loop
                    overview2.setSelection(ranges, true);
                });

                $("#graph2_nav").bind("plotselected", function (event, ranges) {
                    plot2.setSelection(ranges);
                });


                function showTooltip(x, y, contents) {
                    $('<div id="tooltip" class="infobulle">' + contents + '</div>').css( {
                        display: 'none',
                        top: y - 30,
                        left: x + 5,
                        opacity: 0.8
                    }).appendTo("body").fadeIn(200);
                }

                var previousPoint = null;
                $("#graph1, #graph2").bind("plothover", function (event, pos, item) {
                    if(item) {
                        if (previousPoint != item.dataIndex) {
                            previousPoint = item.dataIndex;

                            $("#tooltip").remove();
                            var y = item.datapoint[1].toFixed(0);

                            showTooltip(item.pageX, item.pageY, y);
                        }
                    } else {
                        $("#tooltip").remove();
                        previousPoint = null;
                    }
                });
            }
        </script>
    </div>
    <?php } ?>
    <div class="proprietes_design_in">
        <div class="apercu">
            <?php if(is_file($design['imprim'])){ ?>
            <a href="<?php echo $design['imprim']; ?>" class="zoombox" title="Aperçu de <?php echo $design['titre']; ?>">
                <img src="<?php echo $design['mini_imprim'] ?>" alt="Apercu du design"/>
            </a><br/>
            <?php } else { ?>
            <br /><br />
            <i>Visuel indisponible</i>
            <?php } ?>
        </div>
        <div class="description">
            <span id="id_design" class="dn"><?php echo $design['id']; ?></span>
            <b class="titre_design_<?php echo $design['id']?>"><?php echo $design['titre']; ?></b>
            <input type="text" name="titre_design" value="<?php echo $design['titre']; ?>" id="edit_titre_design" class="dn" />
            <a href="#annul-valid" id="annul_edit" class="dn">Annuler</a>
            <a href="#edit-valid" id="valid_edit" class="dn"><span>Enregistrer</span><img src="<?php echo DESIGN_DIR; ?>images/loading_mini.gif" alt="loading" class="dn" /></a>
            <p><?php echo $design['description']; ?></p>
            <textarea name="description_design" id="edit_description_design" cols="30" rows="6" class="dn"><?php echo $design['description']; ?></textarea>
        </div>
        <div class="options">
            <a href="#uploadapercu">Changer l'aperçu</a><br/>
            <a href="#edit-titre">Editer de nom</a><br/>
            <a href="#edit-description">Editer la description</a><br/>
            <hr/>
            <?php if($design['active'] == 0){ ?>
            <a href="./mes_zdesigns-<?php echo $design['id']; ?>-demandevalidation.html">Demander la validation</a><br/>
            <?php } elseif($design['active'] == 1 && droit_dacces(10)){ ?>
            <a href="./mes_zdesigns-<?php echo $design['id']; ?>-valider.html">Valider</a> | <a href="./mes_zdesigns-<?php echo $design['id']; ?>-devalider.html">Refuser</a> ce design<br/>
            <?php } elseif($design['active'] == 1){ ?>
            En attente de validation <a href="./mes_zdesigns-<?php echo $design['id']; ?>-devalider.html">(Annuler)</a><br/>
            <?php } elseif($design['active'] == 2){ ?>
            <a href="./mes_zdesigns-<?php echo $design['id']; ?>-devalider.html">Dévalider</a><br/>
            <?php } ?>
            <a href="#changer-cat">Changer de catégorie</a><br/>
            <hr/>
            L'Essayer sur le SdZ : <a href="http://www.siteduzero.com/designs.html?design=<?php echo ROOT_ABS.'designs/'.$design['id_membre'].'/'.$idd.'_dev/'; ?>">Dev</a>
            <?php if($design['active'] == 2){ ?> | <a href="http://www.siteduzero.com/designs.html?design=<?php echo ROOT_ABS.'designs/'.$design['id_membre'].'/'.$idd.'/'; ?>">Public</a><?php } ?><br/>
            <a href="./mes_zdesigns-<?php echo $design['id']; ?>-telecharger.html">Télécharger les sources</a><br/>
            <a href="javascript:void(0);" rel="./mes_zdesigns-<?php echo $design['id']; ?>-supprimer.html"
               onclick="$('#suppr_design').fadeIn(200); $('#suppr_design form').attr('action', $(this).attr('rel')); return false;">
                Supprimer
                <em class="titre_design_<?php echo $design['id']?>"><?php echo $design['titre']; ?></em>
            </a><br/>
        </div>
    </div>
    <hr class="clear" />
</div>

<div id="arbre_contener">
    <ul id="arbre">
        <li class="dossier root" id="folder-root"><span><a href="<?php echo $design_dir; ?>" rel="<?php echo ROOT_ABS; ?>" class="titre_design_<?php echo $design['id']?>"><?php echo $design['titre']; ?></a></span>
            <ul>
                <?php
                $dossiers_et_fichiers = array();
                $arbre = array();

                parcourir($design_dir, $exts);
                function parcourir($dir){
                    global $arbre;
                    global $exts;
                    global $dossiers_et_fichiers;
                    global $design_dir;

                    $dossier = opendir($dir);
                    while ($f = readdir($dossier)) {
                        $size = '';
                        $dim = '';
                        if($f != '.' && $f != ".."){
                            if(is_file($dir.$f)){
                                $extension = strtolower(end(explode(".", $dir.$f)));
                                $type = (isset($exts[$extension])) ? $exts[$extension] : 'undifined';

                                if($type == 'img'){
                                    $dims = getimagesize($dir.$f);
                                    $dim = array(
                                        'l' => $dims[0],
                                        'h' => $dims[1]
                                    );
                                }

                                $size = filesize($dir.$f);
                                $size = ($size > 1024) ? round($size/1024).' ko' : $size.' o';

                                $dossiers_et_fichiers[$dir][] = array(
                                    'name' => $f,
                                    'type' => $type,
                                    'dim' => $dim,
                                    'size' => $size,
                                    'extension' => $extension
                                );
                            }
                            if(is_dir($dir.$f)){
                                parcourir($dir.$f.'/');

                                $size = sizethis($dir.$f);
                                $size = ($size > 1024) ? round($size/1024).' ko' : $size.' o';

                                $arbre[$dir][] = $f;
                                $dossiers_et_fichiers[$dir][] = array(
                                    'name' => $f,
                                    'type' => 'folder',
                                    'dim' => '',
                                    'size' => $size,
                                    'extension' => ''
                                );
                            }
                        }
                    }
                    foreach($dossiers_et_fichiers as $d => $array){
                        usort($dossiers_et_fichiers[$d], "cmp");
                    }
                    foreach($arbre as $url => $name){
                        usort($arbre[$url], "cmpArbre");
                    }
                }
                function cmp($a, $b) {
                    return strcmp(strtolower($a['name']), strtolower($b['name']));
                }
                function cmpArbre($a, $b){
                    return strcmp(strtolower($a), strtolower($b));
                }


                // Affiche l'arbre
                if(count($arbre) != 0){
                    arbre($design_dir);
                } else {
                    $dossiers_et_fichiers = array(
                        $design_dir => array()
                    );
                }
                function arbre($root){
                    global $arbre;
                    global $design_dir;

                    $array = $arbre[$root];

                    foreach($array as $k=>$v){
                        echo '<li class="dossier"><span><a href="'.$root.$v.'/'.'" id="arbre_'.str_replace('/', '-', str_replace($design_dir, '', $root.$v)).'">'.$v.'</a></span>';
                                    if(isset($arbre[$root.$v.'/'])){
                                        echo '<ul>';
                                        arbre($root.$v.'/');
                                        echo '</ul>';
                                    }
                        echo '</li>';
                    }
                }
                ?>
            </ul>
        </li>
    </ul>
</div>

<script type="text/javascript">
    var $$ = $('#arbre').addClass('arbre');
    $('li:has(ul):not(.dossier)', $$).addClass('dossier');
    $('.dossier > span > a', $$).addClass('label');
    $('.dossier > ul', $$).hide();
    $('.root').addClass('explore');
</script>

<div id="fichiers_contener">
    <ul id="barre_outils">
        <li><a href="#upload-file"><span class="ico upload-file">icone</span>Envoyer un fichier</a></li>
        <li><a href="#new-css"><span class="ico new-file">icone</span>Nouvelle CSS</a></li>
        <li><a href="#new-folder"><span class="ico new-folder">icone</span>Nouveau dossier</a></li>
        <li class="for_selection inactive"><span class="db"><span class="ico for-selection">icone</span>Pour la selection<span class="ico arrow-down">icone</span></span>
            <ul class="actions">
                <li class="actions-edit action-first-elem"><a href="#selection-edit"><span class="ico selection-edit">icone</span>Editer</a></li>
                <li class="actions-rename"><a href="#selection-rename"><span class="ico selection-rename">icone</span>Renommer</a></li>
                <li class="actions-del"><a href="#selection-del"><span class="ico selection-del">icone</span>Supprimer</a></li>
            </ul>
        </li>
        <?php if($design['active'] == 2){ ?>
        <li><a href="#publier" title="Rendre publiques les modifs de la version dev" rel="infobulle"><span class="ico publish">icone</span>Publier</a></li>
        <?php } else { ?>
        <li class="inactive"><a href="javascript:void(0);"><span class="ico publish">icone</span>Publier</a></li>
        <?php } ?>
        <li class="last_item"><a href="#aide"><span class="ico aide">icone</span>Aide</a></li>
    </ul>

    <?php
    require_once('./classes/rapport.php');
    $rapport = new Rapport($exts);
    $rapport->compare($design_dir, DESIGN_ORIGINAL);
    $nb_files = count($rapport->getFichiers());
    $nb_dossiers = count($rapport->getDossiers());
    $nb_tot = $nb_files + $nb_dossiers;
    $classAnim = ($rapport->getPourcent() == 100) ? 'class="anim_valid"' : '';
    
    if($design['active'] < 2){
        $BDD->query("UPDATE designs
                     SET complet = '".$rapport->getPourcent()."'
                     WHERE id = '".$design['id']."'");
    }
    ?>

    <div id="file_exporer">
        <h3>
            <a href="<?php echo substr($design_dir, 0, -1); ?>" id="root_link" class="titre_design_<?php echo $design['id']; ?>"><?php echo $design['titre']; ?></a><span id="dyn_arianne"></span>
            <a href="javascript:void(0);" id="rapport_link" <?php echo $classAnim; ?> rel="infobulle" title="Différences entre votre zDesign et New Wave">
                Correct à <span id="rapport_pourcent"><?php echo $rapport->getPourcent(); ?></span>% | <span class="aff_mask">Afficher</span> le rapport<span class="ico arrow-down"></span>
            </a>
        </h3>

        <div id="rapport" class="dn">
            <?php include('./inc/rapport.php'); ?>
        </div>

        <div id="col_entete">
            <a href="#check_all_files" class="label_col ico file_check" title="Selectionner tous les éléments" rel="infobulle">Selectionner tous les éléments</a>
            <span class="label_col file_name">Nom du fichier</span>
            <span class="label_col file_dim">Dimentions</span>
            <span class="label_col file_size">Taille</span>
            <span class="label_col file_actions">Actions</span>
        </div>
        <div id="files">
            <?php
            foreach($dossiers_et_fichiers as $d => $f){
                $i = 0;
                $folder_name = ($d != $design_dir)?str_replace('/', '-', str_replace($design_dir, '', substr($d, 0, -1))):'root';
            ?>
            <div id="<?php echo $folder_name; ?>" class="files-folder">
                <?php if($d != $design_dir){ ?>
                    <div class="file fd_fonce to_parent" rel="folder">
                        <span class="file_name">
                            <a href="javascript:void();" class="di folder_link" rel="<?php echo substr(preg_replace('#([a-zA-Z0-9._-]+\/)$#', '', $d), 0, -1); ?>" title="Aller au dossier parent">
                                <span class="ico folder">icone</span>
                                <span class="label">..</span>
                            </a>
                        </span>
                    </div>
                <?php
                }

                foreach($f as $folder){
                    if($folder['type']=='folder'){
                        $i++;
                        $c = (($i%2) == 0)?'fd_fonce':'fd_clair';
                    ?>
                    <div class="file <?php echo $c; ?>" rel="folder">
                        <a href="#check_file" class="file_check ico" title="Ajouter/retirer ce dossier de la sélection" rel="infobulle">Ajouter/retirer ce dossier à la sélection</a>
                        <span class="file_name">
                            <a href="javascript:void();" class="di folder_link" rel="<?php echo $d.$folder['name']; ?>">
                                <span class="ico folder">icone</span>
                                <span class="label"><?php echo $folder['name']; ?></span>
                            </a>
                            <input type="text" name="file_name" class="dn" maxlength="30" value="<?php echo $folder['name']; ?>" />
                            <img src="<?php echo DESIGN_DIR; ?>images/loading_mini.gif" alt="loading" class="dn fr" />
                        </span>
                        <span class="file_dim"></span>
                        <span class="file_size"><?php echo $folder['size']; ?></span>
                        <div class="file_actions">
                            <span class="ico arrow-down">Arrow down</span>
                            <ul class="actions">
                                <li class="actions-rename action-first-elem"><a href="#folder-rename"><span class="ico selection-rename">icone</span>Renommer</a></li>
                                <li class="actions-del"><a href="#selection-del"><span class="ico selection-del">icone</span>Supprimer</a></li>
                            </ul>
                        </div>
                    </div>
                    <?php
                    }
                }
                foreach($f as $file){
                    if($file['type']!='folder' && array_key_exists($file['extension'], $exts)){
                        $i++;
                        $c = (($i%2) == 0)?'fd_fonce':'fd_clair';
                    ?>
                    <div class="file <?php echo $c; ?>" rel="file">
                        <?php if($file['type'] == 'img' && $file['extension'] != "ico"){
                            $marginLeft = ($file['dim']['l'] < 203) ? -($file['dim']['l']) - 17 : -220;
                            $marginTop = ($file['dim']['h'] < 500) ? -($file['dim']['h'])/2 + 7 : -247;
                            $marginTop2 = -((203*$file['dim']['h'])/$file['dim']['l'])/2 + 7;
                            $marginTop = ($marginTop < $marginTop2) ? $marginTop2 : $marginTop;

                            $style = 'margin: '.$marginTop.'px 0 0 '.$marginLeft.'px;';

                            echo '<img class="file-apercu" src="'.$d.$file['name'].'" alt="'.$file['name'].'" style="'.$style.'" />';
                        } ?>
                        <a href="#check_file" class="file_check ico" title="Ajouter/retirer ce fichier de la sélection" rel="infobulle">Ajouter/retirer ce fichier de la sélection</a>
                        <span class="file_name">
                            <a href="javascript:void();" class="di file_link <?php echo $file['type']; ?>" rel="<?php echo $d.$file['name']; ?>" ext="<?php echo $file['extension']; ?>" >
                                <span class="ico <?php echo $file['type']; ?>">icone</span>
                                <span class="label"><?php echo $file['name']; ?></span>
                            </a>
                            <input type="text" name="file_name" class="dn" maxlength="30" value="<?php echo str_replace('.'.$file['extension'], '', $file['name']); ?>" />
                            <img src="<?php echo DESIGN_DIR; ?>images/loading_mini.gif" alt="loading" class="dn fr" />
                        </span>
                        <span class="file_dim"><?php if($file['type'] == 'img'){ echo $file['dim']['l'].' x '.$file['dim']['h'].' px'; } ?></span>
                        <span class="file_size"><?php echo $file['size']; ?></span>
                        <div class="file_actions">
                            <span class="ico arrow-down">Arrow down</span>
                            <ul class="actions">
                                <?php if($file['type'] == 'code'){ ?>
                                <li class="actions-edit action-first-elem"><a href="#selection-edit"><span class="ico selection-edit">icone</span>Editer</a></li>
                                <?php } ?>
                                <li class="actions-rename <?php if($file['type'] != 'code'){ echo "action-first-elem"; } ?>"><a href="#file-rename"><span class="ico selection-rename">icone</span>Renommer</a></li>
                                <li class="actions-del"><a href="#selection-del"><span class="ico selection-del">icone</span>Supprimer</a></li>
                            </ul>
                        </div>
                    </div>
                    <?php
                    }
                }
                ?>
                <span class="empty-message" style="display: none;">- Ce dossier est vide -</span>
            </div>
            <?php
            }
            ?>
        </div>
        <div id="to_parent" class="dn">
            <div class="file fd_fonce to_parent" rel="folder">
                <span class="file_name">
                    <a href="javascript:void();" class="di folder_link" rel="<?php echo substr(preg_replace('#([a-zA-Z0-9._-]+\/)$#', '', $d), 0, -1); ?>" title="Aller au dossier parent">
                        <span class="ico folder">icone</span>
                        <span class="label">..</span>
                    </a>
                </span>
            </div>
        </div>
        <div id="dossierVide" class="dn">
            <div class="file" rel="folder">
                <a href="#check_file" class="file_check ico" title="Ajouter ce fichier à la sélection">Ajouter ce fichier à la sélection</a>
                <span class="file_name">
                    <a href="javascript:void();" class="di folder_link" rel="">
                        <span class="ico folder">icone</span>
                        <span class="label"></span>
                    </a>
                    <input type="text" name="file_name" class="dn" maxlength="30" value="" />
                    <img src="<?php echo DESIGN_DIR; ?>images/loading_mini.gif" alt="loading" class="dn fr" />
                </span>
                <span class="file_dim"></span>
                <span class="file_size"></span>
                <div class="file_actions">
                    <span class="ico arrow-down">Arrow down</span>
                    <ul class="actions">
                        <li class="actions-rename action-first-elem"><a href="#folder-rename"><span class="ico selection-rename">icone</span>Renommer</a></li>
                        <li class="actions-del"><a href="#selection-del"><span class="ico selection-del">icone</span>Supprimer</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="newFile" class="dn">
            <div class="file" rel="file">
                <a href="#check_file" class="file_check ico" title="Ajouter ce fichier à la sélection">Ajouter ce fichier à la sélection</a>
                <span class="file_name">
                    <a href="javascript:void();" class="di file_link code" rel="" ext="css">
                        <span class="ico code">icone</span>
                        <span class="label"></span>
                    </a>
                    <input type="text" name="file_name" class="dn" maxlength="30" value="" />
                    <img src="<?php echo DESIGN_DIR; ?>images/loading_mini.gif" alt="loading" class="dn fr" />
                </span>
                <span class="file_dim"></span>
                <span class="file_size">0 ko</span>
                <div class="file_actions">
                    <span class="ico arrow-down">Arrow down</span>
                    <ul class="actions">
                        <li class="actions-edit action-first-elem"><a href="#selection-edit"><span class="ico selection-edit">icone</span>Editer</a></li>
                        <li class="actions-rename"><a href="#file-rename"><span class="ico selection-rename">icone</span>Renommer</a></li>
                        <li class="actions-del"><a href="#selection-del"><span class="ico selection-del">icone</span>Supprimer</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<hr class="clear" />

<div id="uploader">
    <form action="./ajax/uploader.php" method="post" target="uploadFrame" enctype="multipart/form-data" style="text-align: center;">
        <br/>
        <input type="file" id="uploadFile" name="uploadFile" /><br/><br/>
        <input type="checkbox" id="uploadEcrase" name="uploadEcrase" value="1" /> <label for="uploadEcrase">Ecraser si existant</label><br/>
        <input type="hidden" name="chemin" value="<?php echo ROOT_ABS; ?>" id="cheminUpload" />
        <br/>
        <input type="submit" value="Envoyer" />
    </form>
    <span id="uploadStatut"></span>
    <iframe src="" id="uploadFrame" name="uploadFrame"></iframe>
    <div id="uploadEnd" class="dn"></div>
</div>

<div id="upload_apercu">
    <form action="./mes_zdesigns-<?php echo $design['id']; ?>-uploadapercu.html" method="post" enctype="multipart/form-data" style="text-align: center;">
        <br/>
        <input type="file" id="uploadFile" name="uploadFile" /><br/><br/>
        <br/>
        <input type="submit" value="Envoyer" />
    </form>
</div>

<div id="big_loader">
    <span class="loading" style="display: block; opacity: 0.8;">
        <img src="<?php echo DESIGN_DIR; ?>images/loading.gif" alt="Publication en cours..." /><br/>
        <span class="label">Publication en cours...</span>
    </span>
    <div class="aplat" style="opacity: 0.7;"></div>
</div>

<div id="aide">
    Le zExplorer est tellement cool qu'il n'y a pas besoin d'aide !<br/>
    <br/>
    Plus d'infos sur le Site du Zéro : <a href="http://www.siteduzero.com/tutoriel-3-302496-introduction-aux-designs-distants.html">Initiation aux designs distants</a>.
</div>

<div id="cat_select">
    <form action="./ajax/design.php" method="post">
        Catégorie actuelle : <em id="cat_design"><?php echo $cats[$design['id_cat']]; ?></em><br/><br/>
        <label for="cat">Catégorie : </label>
        <select name="cat" id="cat">
            <?php 
            $req_cat = $BDD->query("SELECT id, nom, ordre
                                    FROM cat
                                    ORDER BY ordre");
            while($c = mysql_fetch_array($req_cat)) {
                echo '<option value="'.$c['id'].'">'.$c['nom'].'</option>';
            }
            ?>
        </select>
        <br/>
        <input type="submit" value="Changer de catégorie" />
    </form>
</div>

<div id="suppr_design">
    <form action="./mes_zdesigns-<?php echo $idd; ?>-supprimer.html" method="post">
        <input type="hidden" name="suppr" value="oui" />
        <input type="submit" value="Supprimer ce design" />
        <input type="button" value="Annuler" onclick="$('#suppr_design').fadeOut(200); return false;" />
    </form>
</div>

<div id="editeur_css">
    <div id="editeur_css_in">
        <ul id="fichiers_ouverts">
        </ul>
        <ul id="outils_editeur">
            <li style="float: left;"><a href="javascript:void();" rel="editeur-save"><span class="ico save"></span>Enregistrer</a><img src="<?php echo DESIGN_DIR; ?>images/loading_mini.gif" alt="loading" class="dn fr" style="margin-left: 5px; opacity: 0.6;" /></li>
            <li><a href="javascript:void();" rel="editeur-indent">Indenter la selection/code</a></li>
            <li><a href="javascript:void();" rel="editeur-remplace">Rechercher et remplacer</a></li>
            <li><a href="javascript:void();" rel="editeur-rechercher">Rechercher</a></li>
            <li><a href="javascript:void();" rel="editeur-jump">Aller à la ligne X</a></li>
            <li><a href="javascript:void();" rel="editeur-redo" title="Refaire"><span class="ico redo"></span></a></li>
            <li><a href="javascript:void();" rel="editeur-undo" title="Annuler"><span class="ico undo"></span></a></li>
        </ul>
        <textarea id="code_css" cols="120" rows="30">
/**************************************************
* zDesigns.fr
*   zExplorer
* Créé par Alex-D - Tous droits réservés
*   Site web : http://alex-d.c.la/
*   E-mail : demodealex[arobase]gmail[point]com
***************************************************/
@import url("design.css");

body {
  margin: 0;
  padding: 3em 6em;
  font-family: tahoma, arial, sans-serif;
  color: #000;
}

#navigation a {
  font-weight: bold;
  text-decoration: none !important;
}

h1 {
  font-size: 2.5em;
}

h2 {
  font-size: 1.7em;
}

h1:before, h2:before {
  content: "::";
}

code {
  font-family: courier, monospace;
  font-size: 80%;
  color: #418A8A;
}
        </textarea>
    </div>
</div>

<script type="text/javascript">
    var $$ = $('#uploader');
    $$.popup({titre:'Envoyer un Fichier', height:150, width:400, zIndexMin:80});
    $('form', $$).submit(function(){
        $(this).find('input[type=submit]').blur();
        $(this).loader('Envoi en cours...');
    });

    $('#upload_apercu').popup({titre:'Changer l\'aperçu', height:150, width:400, zIndexMin:80});

    $('#aide').popup({
        titre:'Aide',
        height:450,
        width:740,
        zIndexMin:80,
        autoClean: false,
        minimizable: true,
        maximizable: true
    });
    
    $('#cat_select').popup({
        titre:'Catégorie du design',
        zIndexMin:80,
        autoClean: false
    });

    $('#suppr_design').popup({
        titre:'Supprimer le design',
        zIndexMin:80,
        autoClean: false
    });

    
    $$ = $('#editeur_css');
    var editeur = null;
    $$.popup({
        titre:'Editeur de feuilles de style CSS',
        height:150,
        width:800,
        zIndexMin:85,
        autoClean: false,
        minimizable: true,
        maximizable: true},
    function(){
        editeur = CodeMirror.fromTextArea('code_css', {
            height: "100%",
            parserfile: "parsecss.js",
            stylesheet: "./js/codemirror/css/csscolors.css",
            path: "./js/codemirror/js/",
            tabMode: 'shift',
            textWrapping: false,
            lineNumbers: "on",
            reindentOnLoad: true,
            autoMatchParens: true,
            indentUnit: 3,
            saveFunction: function(){
                $('a[rel=editeur-save]', $$).trigger('click');
            },
            onChange: function(){
                $('li.active a span', $('#fichiers_ouverts')).show();
            },
            onLoad: function(){
                $('a[rel=editeur-jump]').click(function() {
                    var line = prompt("Aller à la ligne :", "");
                    if (line && !isNaN(Number(line)))
                        editeur.jumpToLine(Number(line));
                    editeur.focus();
                });
                $('a[rel=editeur-remplace]').click(function(){
                    // This is a replace-all, but it is possible to implement a
                    // prompting replace.
                    var from = prompt("Chaine recherchée :", ""), to;
                    if (from) to = prompt("Remplacer cette chaine par :", "");
                        if (to == null) return;

                    var cursor = editeur.getSearchCursor(from, false);
                    while (cursor.findNext())
                        cursor.replace(to);
                    editeur.focus();
                });
                $('a[rel=editeur-rechercher]').click(function(){
                    var text = prompt("Chaine recherchée :", "");
                    if (!text) return;

                    var first = true;
                    do {
                        var cursor = editeur.getSearchCursor(text, first);
                        first = false;
                        while (cursor.findNext()) {
                            cursor.select();
                            if (!confirm("Aller au suivant ?"))
                                return;
                        }
                    } while (confirm("Fin du document atteinte. Retourner au début et continuer ?"));
                    editeur.focus();
                });
                $('a[rel=editeur-indent]').click(function(){
                    var pos = editeur.cursorLine();
                    if(editeur.selection() != ''){
                        editeur.reindentSelection();
                    } else {
                        editeur.reindent();
                    }
                    editeur.jumpToLine(pos);
                    editeur.focus();
                });
                $('a[rel=editeur-undo]').click(function(){
                    editeur.undo();
                    editeur.focus();
                });
                $('a[rel=editeur-redo]').click(function(){
                    editeur.redo();
                    editeur.focus();
                });


                $('#arbre').zexplorer({
                    etendre: '#folder-root',
                    editeur: editeur
                });
            }
        });
    });
</script>

<?php
include('./inc/pied.php');
include('./inc/barre_bas.php');
include('./inc/end.php');
?>