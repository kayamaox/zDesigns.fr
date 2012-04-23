<?php
require_once('./inc/core.php');
require_once('./inc/functions.php');

require_once('./zform/class.zcode.php');
require_once('./zform/class.unzcode.php');
require_once('./zform/geshi/geshi.php');

if(!droit_dacces(10)){
    $_SESSION['message']['alert'] = "Vous n'êtes pas Administrateur";
    header('Location: ./');
    exit();
}

$cat = (isset($_GET['cat'])) ? $_GET['cat'] : null;
$action = (isset($_GET['action'])) ? $_GET['action'] : null;

switch($cat){
    case 'news':
        switch($action){
            case 'poster':
                $titre_news = '';
                if(isset($_POST['texte'])){
                    if(!empty($_POST['titre'])){
                        if(strlen($_POST['titre']) > 7){
                            if(!empty($_POST['texte'])){
                                if(strlen($_POST['texte']) > 50){
                                    $titre = mysql_real_escape_string(stripslashes($_POST['titre']));

                                    $bbcode = $_POST['texte'];

                                    $zcode = new zcode();
                                    $zcode->load($bbcode);
                                    $zcode->chemin('./zform/');
                                    $html = $zcode->parse();

                                    if($html && !empty($html)){
                                        $bbcode = mysql_real_escape_string(stripslashes($bbcode));
                                        $html = mysql_real_escape_string(stripslashes($html));

                                        $BDD->query("INSERT INTO news (titre, bbcode, html, timestamp, id_pseudo)
                                                               VALUES ('".$titre."', '".$bbcode."', '".$html."', '".time()."', '".$_SESSION['id']."')");

                                        $_SESSION['message']['info'] = "La nouvelle a été postée";
                                        header('Location: ./news.html');
                                        exit();
                                    } else {
                                        $erreur = true;
                                    }
                                } else {
                                    $messageAlert = "La news est trop courte";
                                }
                            } else {
                                $messageAlert = "La news est vide";
                            }
                        } else {
                            $messageAlert = "Titre trop court";
                        }
                    } else {
                        $messageAlert = "Le titre est vide";
                    }
                    
                    $titre_news = $_POST['titre'];
                }
                $titre = 'Poster une news';
                $active_zform = true;
                include('./inc/head.php');
                ?>
                <div id="arianne">
                    <a href="<?php echo ROOT; ?>admin.html">Administration</a> > 
                    <a href="<?php echo ROOT; ?>admin-news.html">News</a> >
                    <a href="<?php echo ROOT; ?>admin-news-poster.html">Poster</a>
                </div>
                <br />
                <h1>Poster une news</h1>
                <?php include('./inc/sidebar.php'); ?>

                <div id="admin_content">
                    <div class="bloc_admin">
                        <h3>Rédiger la nouvelle</h3>
                        <div class="bloc_admin_content">
                            <?php
                            if(isset($erreur)){
                                echo '<div class="zcode">';
                                    $zcode->displayError();
                                echo '</div>';
                            }
                            ?>
                            <form action="./admin-news-poster.html" method="post">
                                <label for="titre_news">Titre</label><input type="texte" id="titre_news" name="titre" value="<?php echo $titre_news; ?>" />
                                <?php
                                include('./inc/zediteur.php');
                                ?>
                                <input type="submit" value="Poster la news" />
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                include('./inc/pied.php');
                include('./inc/barre_bas.php');
                include('./inc/end.php');
                break;


            case 'editer':
                if(isset($_GET['id'])){
                    $id = (int) $_GET['id'];

                    $req_news = $BDD->query("SELECT id, titre, bbcode FROM news WHERE id='".$id."'");

                    if(mysql_num_rows($req_news) != 1){
                        $_SESSION['message']['alert'] = "Cette news n'existe pas";
                        header('Location: ./admin-news.html');
                        exit();
                    }
                    $news = array();
                    while($new = mysql_fetch_assoc($req_news)){
                        $news[] = $new;
                    }
                    $news = $news[0];
                } else {
                    $_SESSION['message']['alert'] = "Cette news n'existe pas";
                    header('Location: ./admin-news.html');
                    exit();
                }
                
                if(isset($_POST['texte'])){
                    if(!empty($_POST['titre'])){
                        if(strlen($_POST['titre']) > 7){
                            if(!empty($_POST['texte'])){
                                if(strlen($_POST['texte']) > 50){
                                    $titre = mysql_real_escape_string(stripslashes($_POST['titre']));

                                    $bbcode = $_POST['texte'];

                                    $zcode = new zcode();
                                    $zcode->load($bbcode);
                                    $zcode->chemin('./zform/');
                                    $html = $zcode->parse();

                                    if($html && !empty($html)){
                                        $bbcode = mysql_real_escape_string(stripslashes($bbcode));
                                        $html = mysql_real_escape_string(stripslashes($html));

                                        $BDD->query("UPDATE news
                                                     SET titre = '".$titre."', bbcode = '".$bbcode."', html = '".$html."', timestamp_modif = '".time()."', id_pseudo_modif = '".$_SESSION['id']."'
                                                     WHERE id = '".$id."'");

                                        $_SESSION['message']['info'] = "La nouvelle a été postée";
                                        header('Location: ./news.html');
                                        exit();
                                    } else {
                                        $erreur = true;
                                    }
                                } else {
                                    $messageAlert = "La news est trop courte";
                                }
                            } else {
                                $messageAlert = "La news est vide";
                            }
                        } else {
                            $messageAlert = "Titre trop court";
                        }
                    } else {
                        $messageAlert = "Le titre est vide";
                    }

                    $titre_news = $_POST['titre'];
                }
                $titre = 'Poster une news';
                $active_zform = true;
                include('./inc/head.php');
                ?>
                <div id="arianne">
                    <a href="<?php echo ROOT; ?>admin.html">Administration</a> >
                    <a href="<?php echo ROOT; ?>admin-news.html">News</a> >
                    <a href="<?php echo ROOT; ?>admin-news-poster.html">Editer</a>
                </div>
                <br />
                <h1>Editer une news</h1>
                <?php include('./inc/sidebar.php'); ?>

                <div id="admin_content">
                    <div class="bloc_admin">
                        <h3>Edition de la nouvelle</h3>
                        <div class="bloc_admin_content">
                            <?php
                            if(isset($erreur)){
                                echo '<div class="zcode">';
                                    $zcode->displayError();
                                echo '</div>';
                            }
                            ?>
                            <form action="./admin-news-editer-<?php echo $news['id']; ?>.html" method="post">
                                <label for="titre_news">Titre</label><input type="texte" id="titre_news" name="titre" value="<?php echo ifdecode($news['titre']); ?>" />
                                <?php
                                $content = ifdecode($news['bbcode']);
                                include('./inc/zediteur.php');
                                ?>
                                <input type="submit" value="Poster la news" />
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                include('./inc/pied.php');
                include('./inc/barre_bas.php');
                include('./inc/end.php');
                break;


            default:
                $titre = 'Admin des news';
                include('./inc/head.php');
                ?>
                <div id="arianne">
                    <a href="<?php echo ROOT; ?>admin.html">Administration</a> >
                    <a href="<?php echo ROOT; ?>admin-news.html">News</a>
                </div>
                <br />
                <h1>Les News</h1>
                <?php include('./inc/sidebar.php'); ?>

                <div id="admin_content">
                    <div class="bloc_admin">
                        <h3>Toutes les nouvelles</h3>
                        <div class="bloc_admin_content">
                            <?php
                            $news = $BDD->query("SELECT id, titre, timestamp, id_pseudo, timestamp_modif, id_pseudo_modif, visible
                                                 FROM news
                                                 WHERE visible < 3");
                            while($n = mysql_fetch_assoc($news)){
                                ?>
                                <div class="news_ligne">
                                    
                                </div>
                                <?php
                            }
                            ?>
                            <table cellspacing="0">
                                <tr>
                                    <th class="col_news_titre">Titre</th>
                                    <th class="col_news_auteur">Auteur</th>
                                    <th class="col_news_date">Date d'ajout</th>
                                    <th class="col_news_modif">Modification</th>
                                    <th class="col_news_actions">Actions</th>
                                </tr>
                                <?php
                                $news = $BDD->query("SELECT news.id, news.titre, membres.pseudo, news.timestamp, news.id_pseudo, news.id_pseudo_modif, news.timestamp_modif, news.visible
                                                     FROM news
                                                     INNER JOIN membres
                                                         ON membres.id = news.id_pseudo
                                                     WHERE visible < 3
                                                     ORDER BY news.id DESC");
                                $i = 0;
                                while($n = mysql_fetch_assoc($news)){
                                    $i++;
                                    $c = (($i%2) == 0)?'fd_fonce':'fd_clair';
                                    
                                    echo '<tr class="'.$c.'">';
                                        echo '<td class="col_news_titre">'.$n['titre'].'</td>';
                                        echo '<td class="col_news_auteur">'.$n['pseudo'].'</td>';
                                        echo '<td class="col_news_date">Le '.date('d/m/y \à H\hi', $n['timestamp']).'</td>';
                                        if($n['timestamp_modif'] != 0){
                                            $ret = $BDD->query("SELECT pseudo 
                                                                FROM membres
                                                                WHERE id = '".$n['id_pseudo_modif']."'");
                                            $don = mysql_fetch_array($ret);
                                            echo '<td class="col_news_modif">Par '.$don['pseudo'];
                                            echo ' le '.date('d/m/y \à H\hi', $n['timestamp_modif']).'</td>';
                                        }
                                        else
                                            echo '<td class="col_news_modif">Non modifiée</td>';
                                        echo '<td class="col_news_actions"><a href="./admin-news-editer-'.$n['id'].'.html">Editer</a> | ';
                                        echo '<a href="./admin-news-supprimer-'.$n['id'].'.html">Supprimer</a></td>';
                                    echo '</tr>';
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
                include('./inc/pied.php');
                include('./inc/barre_bas.php');
                include('./inc/end.php');
                break;
        }
        break;


    case 'zdesigns':
        require_once('./inc/core.php');
        
        $cats_req = $BDD->query("SELECT id, nom FROM cat");
        $cats = array(
            0 => "Aucune"
        );
        while($cat = mysql_fetch_assoc($cats_req)){
            $cats[(int)$cat['id']] = $cat['nom'];
        }

        $titre = 'Admin des zDesigns';
        $js[] = "zoombox/zoombox";
        $js[] = "popup";

        include('./inc/head.php');
        ?>
        <div id="arianne">
            <a href="<?php echo ROOT; ?>admin.html">Administration</a> >
            <a href="<?php echo ROOT; ?>admin-zdesigns.html">zDesigns</a>
        </div>
        <br />
        <?php
        $page = (isset($_GET['page'])) ? (int) $_GET['page'] : 1;
        $designs_par_page = 10;
        $start = ($page-1)*$designs_par_page;

        $filtre = (isset($_GET['filtre'])) ? (int) $_GET['filtre'] : 0;
        $filtres = array(
            0 => 'Tous',
            1 => 'En ligne',
            2 => 'En Validation',
            3 => 'Hors Ligne',
            4 => 'À Supprimer'
        );
        $corres = array(
            1 => 2,
            2 => 1,
            3 => 0,
            4 => 3
        );
        $f = ($filtre != 0) ? '-f'.$filtre.'-'.strtolower(str_replace('_', '-', uniforme($filtres[$filtre]))) : '';
        $cond = ($filtre != 0) ? " WHERE designs.active = '".$corres[$filtre]."' " : '';



        $designs = $BDD->query("SELECT designs.id AS idd, designs.id_membre AS idm, designs.titre AS titre, designs.description AS description, designs.imprim AS imprim,
                                       designs.mini_imprim AS mini_imprim, designs.active AS active, designs.complet AS complet, designs.id_cat AS cat, designs.note AS note,
                                       designs.date AS date, membres.id, membres.pseudo AS pseudo
                                FROM designs
                                INNER JOIN membres
                                    ON designs.id_membre = membres.id
                                ".$cond."
                                ORDER BY active DESC
                                LIMIT ".$start.", ".$designs_par_page);

        $nb_designs = $BDD->query("SELECT id, active FROM designs");
        $nb_aff_designs = $BDD->query("SELECT id, active FROM designs".$cond);

        $nb_zds = mysql_num_rows($nb_designs);
        $nb_aff_zds = mysql_num_rows($nb_aff_designs);
        ?>
        <h1><?php echo pluralize($nb_zds, "{Aucun|Un|{#}} zDesign{s}"). " - ".($start+1)." à ".($start+$designs_par_page)." - Filtre : ".$filtres[$filtre]." (".$nb_aff_zds.")"; ?></h1>
        <?php include('./inc/sidebar.php'); ?>

        <div id="admin_content">
            <div id="mes_zdesigns">
                <div class="pagination filtres" style="height: 35px;">
                    <?php 
                    for($i = 0; $i < count($filtres); $i++){
                        if($i == $filtre){
                            echo '<span class="page current" style="margin: 0 6px 6px 0;">'.$filtres[$i].'</span>';
                        } else {
                            echo '<a href="'.ROOT.'admin-zdesigns-f'.$i.'-'.strtolower(str_replace('_', '-', uniforme($filtres[$i]))).'.html" class="page" style="margin: 0 6px 6px 0;">'.$filtres[$i].'</a>';
                        }
                    }
                    ?>
                </div>
                <?php
                while($d = mysql_fetch_assoc($designs)){
                    $nb_coms = mysql_num_rows($BDD->query("SELECT id_design, visible
                                                           FROM com_zdesigns
                                                           WHERE id_design = '".$d['id']."'
                                                               AND visible = '1'"));
                    ?>
                    <div class="design">
                        <h3><em>"<?php echo $d['titre']; ?>"</em> par <?php echo $d['pseudo']; ?></h3>
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
                                <?php echo str_replace('\n', '', $d['description']); ?>
                                <?php if($d['active'] < 3){ ?>
                                    <a href="./mes_zdesigns-<?php echo $d['idd']; ?>.html" class="btn_editer">Editer avec le zExplorer <span class="fleche"></span></a>
                                <?php } ?>
                            </div>
                            <div class="options">
                                <?php if($d['active'] == 2){
                                note($d['note']); ?><br />
                                <span><?php echo pluralize($nb_coms, '{Aucun|Un|{#}} commentaire{s}'); ?></span>
                                <?php } else { echo "Créé le ".date('d/m/y \à H\hi', $d['date']); } ?><br />
                                <span>Complet à <?php echo $d['complet']; ?>%</span><br />
                                <span>Catégorie : <?php echo $cats[(int)$d['cat']];  ?></span><br />
                                <span>Etat :
                                    <b style="color: <?php echo $etats[(int)$d['active']]['couleur']; ?>">
                                        <?php echo $etats[(int)$d['active']]['etat']; ?>
                                    </b>
                                </span><br />
                                <?php
                                if($d['active'] == 1){
                                ?>
                                <a href="./mes_zdesigns-<?php echo $d['idd']; ?>-valider.html">Valider</a> |
                                <a href="./mes_zdesigns-<?php echo $d['idd']; ?>-devalider.html">Refuser</a> ce design<br/>
                                <?php } elseif($d['active'] != 2){ br(1); } ?>
                                <hr />
                                L'Essayer sur le SdZ : <a href="http://www.siteduzero.com/designs.html?design=<?php echo ROOT_ABS.'designs/'.$d['idm'].'/'.$d['idd'].'_dev/'; ?>">Dev</a>
                                <?php if($d['active'] == 2){ ?> | <a href="http://www.siteduzero.com/designs.html?design=<?php echo ROOT_ABS.'designs/'.$d['idm'].'/'.$d['idd'].'/'; ?>">Public</a><?php } ?><br/>
                                <a href="./mes_zdesigns-<?php echo $d['idd']; ?>-telecharger.html">Télécharger les sources</a><br/>
                                <a href="javascript:void(0);" rel="./mes_zdesigns-<?php echo $d['idd']; ?>-supprimer.html"
                                   onclick="$('#suppr_design').fadeIn(200); $('#suppr_design form').attr('action', $(this).attr('rel')); return false;">
                                    Supprimer <em><span class="titre_design_<?php echo $d['idd']?>"><?php echo $d['titre']; ?></span></em>
                                </a><br/><br />
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php 
            if($nb_aff_zds > 0){
                echo pagination(ROOT.'admin-zdesigns'.$f.'-p{p}.html', $page, null, $nb_aff_zds, $designs_par_page); 
            }
            ?>
            <div class="pagination filtres">
                <?php 
                for($i = 0; $i < count($filtres); $i++){
                    if($i == $filtre){
                        echo '<span class="page current">'.$filtres[$i].'</span>';
                    } else {
                        echo '<a href="'.ROOT.'admin-zdesigns-f'.$i.'-'.strtolower(str_replace('_', '-', uniforme($filtres[$i]))).'.html" class="page">'.$filtres[$i].'</a>';
                    }
                }
                ?>
            </div>
        </div>


        <div id="suppr_design">
            <form action="./mes_zdesigns-<?php echo $d['idd']; ?>-supprimer.html?from=admin-zdesigns" method="post">
                <input type="hidden" name="suppr" value="oui" />
                <input type="submit" value="Supprimer ce design" />
                <input type="button" value="Annuler" onclick="$('#suppr_design').fadeOut(200); return false;" />
            </form>
        </div>
        <script type="text/javascript">
            $('#suppr_design').popup({
                titre:'Supprimer ce design',
                zIndexMin:80,
                autoClean: false
            });
        </script>
        <?php
        include('./inc/pied.php');
        include('./inc/barre_bas.php');
        include('./inc/end.php');
        break;


    default:
        $titre = 'Administration';
        $js[] = "flot/flot";
        $js[] = "flot/selection";

        include('./inc/head.php');
        ?>
        <div id="arianne">
            <a href="<?php echo ROOT; ?>admin.html">Administration</a>
        </div>
        <br />
        <h1>Administration</h1>
        <?php include('./inc/sidebar.php'); ?>

        <div id="admin_content">
            <div class="bloc_admin">
                <h3>Activité récente sur le site</h3>
                <div class="bloc_admin_content">
                    À faire
                </div>
            </div>
            <!--
            <div class="bloc_admin">
                <h3>Statistiques</h3>
                <div id="bloc_stats" class="bloc_admin_content">
                    <div class="loading">
                        <img src="./design/2/images/loading.gif" alt="Chargement..." /><br/>
                        Chargement...
                    </div>                    
                   
                    <div id="col_graph1" class="dn">
                        <b>Utilisateurs</b>
                        <div id="graph1"></div>
                        <div id="graph1_nav"></div>
                    </div>
                    
                    <div id="col_graph2" class="dn">
                        <b>Pages chargées avec un zDesigns</b>
                        <div id="graph2"></div>
                        <div id="graph2_nav"></div>
                    </div>
                    
                    <hr class="clear" />
                    <div id="stats_text"></div>
                    
                    <script id="source" type="text/javascript">
                        function load_stats(){
                            $.post('./ajax/stats.php', {id:'all'}, function(data){
                                if(data != null && data['erreur'] == 'true'){
                                    $('#stats_text').empty().html(data['stats_text']);
                                    
                                    $('#bloc_stats .loading').hide();
                                    $('#col_graph1').show();
                                    $('#col_graph2').show();
                                                                        
                                    var d = data['d1'];
                                    
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

                                    var d2 = data['d2'];

                                    // helper for returning the weekends in a period
                                    function weekendAreas2(axes) {
                                        var markings2 = [];
                                        var d2 = new Date(axes.xaxis.min);
                                        // go to the first Saturday
                                        d2.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
                                        d2.setUTCSeconds(0);
                                        d2.setUTCMinutes(0);
                                        d2.setUTCHours(0);
                                        var i2 = d2.getTime();
                                        do {
                                            // when we don't set yaxis, the rectangle automatically
                                            // extends to infinity upwards and downwards
                                            markings2.push({ xaxis: { from: i2, to: i2 + 2 * 24 * 60 * 60 * 1000 } });
                                            i2 += 7 * 24 * 60 * 60 * 1000;
                                        } while (i2 < axes.xaxis.max);

                                        return markings2;
                                    }

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
                                } else {
                                    var erreur = 'Erreur';
                                    if(data['erreur'] == null) {erreur = 'Le serveur de répond pas'} else {erreur = data['erreur']}
                                    addMessage('error', erreur);
                                }
                            }, 'json');                            
                        }
                        
                        $(function(){
                            $('#bloc_stats .loading').show();
                            setTimeout("load_stats();", 500);
                        });
                    </script>
                </div>
            </div>
            -->
            <div class="bloc_admin">
                <h3>Tâches récement ajoutées</h3>
                <div class="bloc_admin_content">
                    <table cellspacing="0">
                        <tr>
                            <th class="col_todo_statut">Statut</th>
                            <th class="col_todo_admin">Admin</th>
                            <th class="col_todo_intitule">Intitulé</th>
                            <th class="col_todo_ordre">Ordre</th>
                            <th class="col_todo_date">Date d'ajout</th>
                            <th class="col_todo_actions">Actions</th>
                        </tr>
                        <?php
                        $req_todo = $BDD->query("SELECT todolist.id, todolist.nom AS nom, todolist.statut AS statut, todolist.ordre AS ordre,
                                                        todolist.membre_concerne AS mbr, todolist.date_ajout AS date,
                                                        membres.id AS idm, membres.pseudo AS pseudo
                                                 FROM todolist
                                                 INNER JOIN membres
                                                    ON membres.id = todolist.membre_concerne
                                                 ORDER BY date_ajout DESC, ordre ASC
                                                 LIMIT 0, 10");
                        $i = 0;
                        while($t = mysql_fetch_assoc($req_todo)){
                            $i++;
                            $c = (($i%2) == 0)?'fd_fonce':'fd_clair';

                            $if_bold = ($_SESSION['id'] == $t['idm']) ? 'style="font-weight: bold;"' : '';

                            echo '<tr class="'.$c.'">';
                                echo '<td class="col_todo_statut" style="color: '.$statut[$t['statut']-1]['couleur'].';">'.$statut[$t['statut']-1]['etat'].'</td>';
                                echo '<td class="col_todo_admin" '.$if_bold.'>'.$t['pseudo'].'</td>';
                                echo '<td class="col_todo_intitule">'.$t['nom'].'</td>';
                                echo '<td class="col_todo_ordre">'.$t['ordre'].'</td>';
                                echo '<td class="col_todo_date">'.parse_date($t['date'], true).'</td>';
                                echo '<td class="col_todo_actions"><a href="./admin-todo-modifier-'.$t['id'].'.html">Modifier</a> | ';
                                echo '<a href="./admin-todo-supprimer-'.$t['id'].'.html">Supprimer</a></td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>

        <?php
        include('./inc/pied.php');
        include('./inc/barre_bas.php');
        include('./inc/end.php');
        break;
}
?>