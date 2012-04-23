<?php
header('Content-Type: text/html; charset=utf-8');
require_once('../classes/bdd.php');
$BDD = new BDD();
$urlLog = '../inc/log.php';
$urlFileLog = '../log.txt';
require_once('../inc/core.php');
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Uploader</title>
        <?php
        require_once('../inc/functions.php');

        $nbFichiers = null;
        $error = null;
        $filename = null;

        if(isset($_FILES['uploadFile']) && $_FILES['uploadFile']['error'] == 0) {
            $filename = uniforme($_FILES['uploadFile']['name']);
            $extension = strtolower(end(explode(".", $filename)));
            $valid = (array_key_exists($extension, $exts)) ? true : false;

            if($valid){
                $urlCible = $_POST['chemin'].$filename;

                $prevSizeDesign = sizethis(str_replace(ROOT_ABS, '../', $_POST['chemin']));

                @$log->addData('[zUploader] Envoi vers '.$urlCible, 'none', 'zexplorer');
                $up = true;
                $maj = 'false';
                $newSize = $_FILES['uploadFile']['size']+$prevSizeDesign;
                if($_POST['uploadEcrase'] != 1 && file_exists(str_replace(ROOT_ABS, '../', $urlCible))){
                    $up = false;
                } elseif($_POST['uploadEcrase'] == 1 && file_exists(str_replace(ROOT_ABS, '../', $urlCible))){
                    $maj = 'true';
                    $newSize = $_FILES['uploadFile']['size'] + $prevSizeDesign - filesize(str_replace(ROOT_ABS, '../', $urlCible));
                }
                if($up){
                    if($newSize < SIZE_MAX){
                        if(@move_uploaded_file($_FILES['uploadFile']['tmp_name'], str_replace(ROOT_ABS, '../', $urlCible))) {
                            $error = 'true';
                            $type = (isset($exts[$extension])) ? $exts[$extension] : 'undifined';

                            if($type == 'img'){
                                $dims = getimagesize($urlCible);
                                $dim = array(
                                    'l' => $dims[0],
                                    'h' => $dims[1]
                                );
                            }

                            $size = filesize(str_replace(ROOT_ABS, '../', $urlCible));
                            $size = ($size > 1024) ? round($size/1024).' ko' : $size.' o';

                            $file = array(
                                'name' => $filename,
                                'type' => $type,
                                'dim' => $dim,
                                'size' => $size,
                                'extension' => $extension
                            );
                        } else {
                            $error = 'Envoi échoué';
                        }
                    } else {
                        $error = 'Il n\'y a plus assez d\'espace disponible';
                    }
                } else {
                    $error = 'Ce fichier existe déjà';
                }
            } else {
                $error = 'Extension de fichier interdite';
            }
        } else {
            $error = 'Aucun fichier séléctionné';
        }
        unset($_FILES);
        unset($_POST);
        ?>
    </head>
    <body>
        <?php if($error == 'true'){
            $urlCible = str_replace(ROOT_ABS, './', $urlCible);
        ?>
        <div id="body">
            <div id="upload-message"><?php echo 'Le fichier "'.$filename.'" a bien été envoyé'; ?></div>
            <div id="upload-maj"><?php echo $maj; ?></div>
            <div id="upload-elem">
                <div class="file" rel="file">
                    <?php if($file['type'] == 'img' && $file['extension'] != "ico"){
                        $marginLeft = ($file['dim']['l'] < 203) ? -($file['dim']['l']) - 17 : -220;
                        $marginTop = ($file['dim']['h'] < 500) ? -($file['dim']['h'])/2 + 7 : -247;
                        $marginTop2 = -((203*$file['dim']['h'])/$file['dim']['l'])/2 + 7;
                        $marginTop = ($marginTop < $marginTop2) ? $marginTop2 : $marginTop;

                        $style = 'margin: '.$marginTop.'px 0 0 '.$marginLeft.'px;';

                        echo '<img class="file-apercu" src="'.$urlCible.'" alt="'.$file['name'].'" style="'.$style.'" />';
                    } ?>
                    <a href="#check_file" class="file_check ico" title="Ajouter/retirer ce fichier de la sélection" rel="infobulle">Ajouter/retirer ce fichier de la sélection</a>
                    <span class="file_name">
                        <a href="javascript:void();" class="di file_link <?php echo $file['type']; ?>" rel="<?php echo $urlCible; ?>" ext="<?php echo $file['extension']; ?>" >
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
            </div>
            <div id="upload-size">
                <?php
                $sizeDesign = $prevSizeDesign + $_FILES['uploadFile']['size'];
                $pourcent = round(100 * ($sizeDesign/SIZE_MAX));
                $pourcent = ($pourcent > 100) ? 100 : $pourcent;
                $sizeDesign = ($sizeDesign > 1024) ? round($sizeDesign/1024).' ko' : $sizeDesign.' o';
                ?>
                <span class="pourcent"><?php echo $pourcent; ?></span>
                <span class="sizeDesign"><?php echo $sizeDesign; ?></span>
            </div>
        </div>

        <script type="text/javascript">
            window.top.window.finUpload("<?php echo $error; ?>",
                                        document.getElementById('body').innerHTML);
        </script>
        <?php } else { ?>
        <script type="text/javascript">
            window.top.window.finUpload("<?php echo $error; ?>", null);
        </script>
        <?php } ?>
    </body>
</html>