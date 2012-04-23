<?php
session_start();
if(!isset($_SESSION['pseudo'])){
    $_SESSION['message']['alert'] = "Vous devez être connecté";
    header('Location: ./');
    exit();
}


require_once('./inc/core.php');
require_once('./inc/functions.php');

if(!droit_dacces(10)){
    $_SESSION['message']['alert'] = "Vous ne pouvez pas voir ce dossier";
    header('Location: ./');
    exit();
}


$design_dir = './design/2_dev/';
if(!is_dir($design_dir)){
    copy_folder('./design/2/', $design_dir);
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
            header('Content-Disposition: inline; filename="zDs_'.date('H\hi_d-m-Y').'.zip"');
            echo $archive;
            exit();
            break;
    }
}


$titre = 'zExplorer, zDs';

$js[] = 'zexplorer';
$js[] = 'popup';
$js[] = 'codemirror/js/codemirror';
$js[] = 'zoombox/zoombox'; // Inclu automatiquement le CSS associé

include('./inc/head.php');

$sizeDesign = sizethis($design_dir);
$pourcent = round(100 * ($sizeDesign/SIZE_MAX));
$pourcent = ($pourcent > 100) ? 100 : $pourcent;
$sizeDesign = ($sizeDesign > 1024) ? round($sizeDesign/1024).' ko' : $sizeDesign.' o';
$maxSize = ($pourcent >= 90) ? 'maxSize': '';
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
    <a href="<?php echo ROOT; ?>admin.html">Administration</a> > zExplorer > <a href="<?php echo ROOT; ?>zexplorer_zds.html">zDesigns</a>
</div>
<br/>
<h1>zExplorer :: <span>zDesigns</span></h1>

<div id="jauge_design">
    <span class="infos <?php echo $maxSize; ?>">
        <?php
        echo 'Taille du zDesign : <span id="size_design">'.$sizeDesign.'</span>';
        ?>
    </span>
</div>

<div id="block_all"></div>

<div id="arbre_contener">
    <ul id="arbre">
        <li class="dossier root" id="folder-root"><span><a href="<?php echo $design_dir; ?>" rel="<?php echo ROOT_ABS; ?>">zDesigns</a></span>
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
        <li><a href="#publier_zds" title="Rendre publiques les modifs de la version dev" rel="infobulle"><span class="ico publish">icone</span>Publier</a></li>
        <li class="last_item"><a href="#aide"><span class="ico aide">icone</span>Aide</a></li>
    </ul>

   

    <div id="file_exporer">
         <h3>
            <a href="<?php echo substr($design_dir, 0, -1); ?>" id="root_link">zDesigns</a><span id="dyn_arianne"></span>
         </h3>

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
    <form action="./mes_zdesigns-zDs-uploadapercu.html" method="post" enctype="multipart/form-data" style="text-align: center;">
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