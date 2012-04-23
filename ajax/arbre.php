<?php
if(isset($_POST['action'])){
    $urlLog = '../inc/log.php';
    $urlFileLog = '../log.txt';
    $BDD = 'null';
    require_once('../inc/core.php');
    require_once('../inc/functions.php');
    require_once('../classes/rapport.php');
    $design_dir ='./designs/'.$_SESSION['id'].'/';

    if(isset($_POST['url'])){
        $url = explode('/', $_POST['url']);
        $id_design = $url[3];
        $design_dir ='./designs/'.$url[2].'/';

        $_POST['url'] = '.'.$_POST['url'];
    }
    $r['erreur'] = null;
    
    switch($_POST['action']){
        case 'supprDir':
            if(substr_count($_POST['url'], $design_dir) == 1 || droit_dacces(10)){
                $r['erreur'] = (@supprDir($_POST['url'].'/'))?'true':'Erreur lors de la suppression de ce dossier';

                $erreur = ($r['erreur'] != 'true') ? ' || ERREUR :: '.$r['erreur'] : '';
                if($r['erreur'] == 'true'){
                    $sizeDesign = sizethis('.'.$design_dir.$id_design.'/');
                    $pourcent = round(100 * ($sizeDesign/SIZE_MAX));
                    $r['pourcent'] = ($pourcent > 100) ? 100 : $pourcent;
                    $r['sizeDesign'] = ($sizeDesign > 1024) ? round($sizeDesign/1024).' ko' : $sizeDesign.' o';

                    // Renvoi du rapport
                    ob_start();
                    $rapport = new Rapport($exts);
                    $rapport->compare('.'.$design_dir.$id_design.'/', '.'.DESIGN_ORIGINAL);
                    $nb_files = count($rapport->getFichiers());
                    $nb_dossiers = count($rapport->getDossiers());
                    $nb_tot = $nb_files + $nb_dossiers;
                    include('../inc/rapport.php');
                    $r['pourcent_complet'] = $rapport->getPourcent();
                    $r['rapport'] = ob_get_clean();
                }
                @$log->addData('[zExplorer] Supprimer dossier '.$_POST['url'].$erreur);
            } else {
                $r['erreur'] = 'Accès interdit';
            }
            echo json_encode($r);
            break;



        case 'supprFile':
            if(substr_count('.'.$_POST['url'].'/', $design_dir) == 1 || droit_dacces(10)){
                $r['erreur'] = (@unlink($_POST['url']))?'true':'Erreur lors de la suppression de ce fichier';

                $erreur = ($r['erreur'] != 'true') ? ' || ERREUR :: '.$r['erreur'] : '';
                if($r['erreur'] == 'true'){
                    $sizeDesign = sizethis('.'.$design_dir.$id_design.'/');
                    $pourcent = round(100 * ($sizeDesign/SIZE_MAX));
                    $r['pourcent'] = ($pourcent > 100) ? 100 : $pourcent;
                    $r['sizeDesign'] = ($sizeDesign > 1024) ? round($sizeDesign/1024).' ko' : $sizeDesign.' o';

                    // Renvoi du rapport
                    ob_start();
                    $rapport = new Rapport($exts);
                    $rapport->compare('.'.$design_dir.$id_design.'/', '.'.DESIGN_ORIGINAL);
                    $nb_files = count($rapport->getFichiers());
                    $nb_dossiers = count($rapport->getDossiers());
                    $nb_tot = $nb_files + $nb_dossiers;
                    include('../inc/rapport.php');
                    $r['pourcent_complet'] = $rapport->getPourcent();
                    $r['rapport'] = ob_get_clean();
                }
                @$log->addData('[zExplorer] Supprimer fichier '.$_POST['url'].$erreur);
            } else {
                $r['erreur'] = 'Accès interdit';
            }
            echo json_encode($r);
            break;



        case 'supprMultiple':
            $nbErreur = 0;
            $nom = '';
            foreach($_POST['files'] as $elem){
                if(substr_count('.'.$elem[1].'/', $design_dir) == 1 || droit_dacces(10)){
                    if($elem[0] == 'dossier'){
                        $nbErreur = @supprDir('.'.$elem[1].'/') ? $nbErreur : $nbErreur+1;
                    } else {
                        $nbErreur = @unlink('.'.$elem[1]) ? $nbErreur : $nbErreur+1;
                    }
                    @$log->addData('[zExplorer] Suppression Multiple '.$elem[1]);
                } else {
                    $nbErreur++;
                }
            }
            $r['erreur'] = ($nbErreur == 0) ? 'true' : $nbErreur.' éléments n\'ont pu être supprimés'.$nom;
            if($r['erreur'] == 'true'){
                $id_design = explode('/', $_POST['files'][0][1]);
                $design_dir = './designs/'.$id_design[2].'/';
                $id_design = $id_design[3];
                $sizeDesign = sizethis('.'.$design_dir.$id_design.'/');
                $pourcent = round(100 * ($sizeDesign/SIZE_MAX));
                $r['pourcent'] = ($pourcent > 100) ? 100 : $pourcent;
                $r['sizeDesign'] = ($sizeDesign > 1024) ? round($sizeDesign/1024).' ko' : $sizeDesign.' o';

                // Renvoi du rapport
                ob_start();
                $rapport = new Rapport($exts);
                $rapport->compare('.'.$design_dir.$id_design.'/', '.'.DESIGN_ORIGINAL);
                $nb_files = count($rapport->getFichiers());
                $nb_dossiers = count($rapport->getDossiers());
                $nb_tot = $nb_files + $nb_dossiers;
                include('../inc/rapport.php');
                $r['pourcent_complet'] = $rapport->getPourcent();
                $r['rapport'] = ob_get_clean();
            }
            echo json_encode($r);
            break;



        case 'newDir':
            $r['name'] = uniforme($_POST['name']);
            $newUrl = str_replace($_POST['name'], $r['name'], $_POST['url']);
            if(substr_count($newUrl.'/', $design_dir) == 1 || droit_dacces(10)){
                $r['erreur'] = (@mkdir($newUrl.'/', 0777))?'true':'Erreur lors de la création du dossier';
                $r['url'] = substr($newUrl, 1);
            } else {
                $r['erreur'] = 'URL invalide';
            }

            $erreur = ($r['erreur'] != 'true') ? ' || ERREUR :: '.$r['erreur'] : '';
            @$log->addData('[zExplorer] Nouveau dossier '.$r['name'].$erreur);
            echo json_encode($r);
            break;



        case 'rename':
            $r['newName'] = uniforme($_POST['newName']);
            @$log->addData('---------------------------------------------------------------------------------');
            @$log->addData('[zExplorer] [Requette] Renomme '.$type.' '.$_POST['oldUrl'].' en '.$_POST['newName'].' => '.$r['newName']);

            if(!empty($r['newName'])){
                if(isset($_POST['type']) && $_POST['type'] == 'file'){
                    $extension = end(explode(".", $r['newName']));
                    $valid = (array_key_exists($extension, $exts)) ? true : false;
                    $newUrl = str_replace($_POST['newName'], $r['newName'], $_POST['newUrl']);
                    if(substr_count($newUrl, $design_dir) == 1 || droit_dacces(10)){
                        $newUrl = str_replace($_POST['newName'], $r['newName'], $_POST['newUrl']);

                        if(file_exists('.'.$newUrl)){
                            $r['erreur'] = 'Ce fichier existe déjà';
                            $valid = false;
                        }
                    } else {
                        $r['erreur'] = 'URL invalide';
                        $valid = false;
                    }

                    $type = 'fichier';
                } else {
                    $newUrl = str_replace($_POST['newName'], $r['newName'], $_POST['newUrl']);
                    if(substr_count($design_dir, $newUrl) == 1 || droit_dacces(10)){
                        $valid = true;
                    } else {
                        $r['erreur'] = 'URL invalide';
                        $valid = false;
                    }
                    $type = 'dossier';
                }

                if($valid){
                    @$log->addData('[zExplorer] [Execution] Renomme '.$type.' '.$_POST['oldUrl'].' en '.$newUrl);
                    $r['erreur'] = (@rename('.'.$_POST['oldUrl'], '.'.$newUrl))?'true':'Erreur lors du renommage';
                } else {
                    if(!isset($r['erreur'])){
                        $r['erreur'] = 'Problème d\'extension, rafraichissez la page';
                    }
                }
            } else {
                $r['erreur'] = 'Nom invalide';
            }
            $erreur = ($r['erreur'] != 'true') ? ' || ERREUR :: '.$r['erreur'] : '';
            @$log->addData('[zExplorer] [Resultat] Renomme '.$type.' '.$_POST['oldUrl'].' en '.$newUrl.$erreur);
            @$log->addData('--- Fin du traitement --------------------------------------------------------------------');
            if(!$dev) @$log->addData('[zExplorer] Renomme '.$type.' '.$_POST['oldUrl'].' en '.$newUrl.$erreur, 'none', 'zexplorer');
            echo json_encode($r);
            break;



        case 'loadFile':
            $url = $_POST['url'];
            $r['fichier'] = null;
            if(substr_count($url, $design_dir) == 1 || droit_dacces(10)){
                if($r['fichier'] = utf8_encode(@file_get_contents($url))){
                    $r['erreur'] = 'true';
                } else {
                    $r['erreur'] = 'Une erreur est survenue lors de l\'ouverture du fichier';
                }
            } else {
                $r['erreur'] = 'URL invalide';
            }
            if($r['fichier'] == null || $r['fichier'] == ''){
                $r['erreur'] = 'true';
                $r['fichier'] = '/* Ce fichier est vide */';
            }
            echo json_encode($r);
            break;



        case 'saveFile':
            $url = $_POST['url'];
            if(substr_count($url, $design_dir) == 1 || droit_dacces(10)){
                if(@file_put_contents($url, stripslashes(utf8_decode($_POST['fichier'])))){
                    $r['erreur'] = 'true';
                    $size = @filesize($url);
                    $r['size'] = ($size > 1024) ? round($size/1024).' ko' : $size.' o';
                } else {
                    $r['erreur'] = 'Une erreur est survenue lors de l\'écriture du fichier';
                }
            } else {
                $r['erreur'] = 'URL invalide';
            }
            echo json_encode($r);
            break;



        case 'newCss':
            $r['name'] = uniforme(str_replace('.css', '', $_POST['name']));
            $newUrl = str_replace($_POST['name'], $r['name'], $_POST['url']);
            if(substr_count($newUrl, $design_dir) == 1 || droit_dacces(10)){
                if(!file_exists($newUrl.'.css')){
                    $dataIniCss = utf8_decode('/*
*   Document    : '.$r['name'].'
*   Créé le     : '.date('d m Y').', '.date('H:i:s').'
*   Auteur      : '.$_SESSION['pseudo'].'
*   Description :
*       Décrivez le contenu et l\'utilité de cette feuille de style
*/');

                    $r['erreur'] = (@file_put_contents($newUrl.'.css', $dataIniCss))?'true':'Erreur lors de la création du fichier';
                    $r['url'] = substr($newUrl, 1);
                } else {
                    $r['erreur'] = 'Un fichier du même nom exite déjà';
                }
            } else {
                $r['erreur'] = 'URL invalide';
            }

            $erreur = ($r['erreur'] != 'true') ? ' || ERREUR :: '.$r['erreur'] : '';
            @$log->addData('[zExplorer] Nouvelle CSS '.$r['name'].$erreur);
            echo json_encode($r);
            break;



        case 'maj':
            ob_start();
            $urlFrom = ($_POST['urlFrom'] != '') ? '.'.$_POST['urlFrom'] : '.'.DESIGN_ORIGINAL;
            $urlTo = '.'.$_POST['urlTo'];
            $url = explode('/', $urlTo);
            $design_dir ='./designs/'.$url[2].'/'.$url[3].'/';
            $type = $_POST['type'];

            $rapport = new Rapport($exts);
            if($type != 'file'){
                $rapport->compare($urlTo, $urlFrom);
            }

            switch($type){
                case 'all':
                    $elems = array_merge($rapport->getDossiers(), $rapport->getFichiers());
                    break;

                case 'mass_dir':
                    $elems = $rapport->getDossiers();
                    break;

                case 'dir':
                    $elems = array_merge($rapport->getDossiers(), $rapport->getFichiers());
                    create_dir($urlTo);
                    break;

                case 'file':
                    $elems = array(0 => array(
                        'nom' => end(explode('/', $urlTo)),
                        'url_from' => $urlFrom,
                        'url_to' => $urlTo
                    ));
                    break;
            }

            
            $content = '';
            foreach($elems as $elem){
                if(substr($elem['nom'], -1) == '/'){ // C'est un dossier
                    create_dir($elem['url_to']);
                    copy_folder($elem['url_from'], $elem['url_to']);
                } else { // C'est un fichier
                    $content = ob_get_contents();
                    ob_clean();
                    create_dir($elem['url_to'], true);
                    $to_create = ob_get_contents();
                    ob_clean();
                    $to_create = array_reverse(json_decode($to_create));
                    $to_create[] = $elem['url_to'];
                    copy($elem['url_from'], $elem['url_to']);
                }
            }
            $content .= ob_get_clean();
            if(!empty($content)){
                $r['erreur'] = "Une erreur est survenue";
            } else {
                if($type == 'file'){
                    $r['erreur'] = "true";

                    $toCreate = array();
                    for($i = 0; $i<count($to_create); $i++){
                        $toCreate[$i]['url'] = str_replace('.../', './', $to_create[$i]);
                        $toCreate[$i]['url'] = str_replace('../', './', $toCreate[$i]['url']);

                        if(substr($toCreate[$i]['url'], -1) == '/'){
                            $toCreate[$i]['name'] = end(explode("/", substr($toCreate[$i]['url'], 0, -1)));
                            $toCreate[$i]['type'] = 'folder';
                            $toCreate[$i]['extension'] = '';
                            $size = sizethis('.'.$toCreate[$i]['url']);
                            $toCreate[$i]['parent'] = substr(str_replace(end(explode('/', substr($toCreate[$i]['url'],  0, -1))), '', $toCreate[$i]['url']), 0, -1);
                            $toCreate[$i]['url'] = substr($toCreate[$i]['url'], 0, -1);
                        } else {
                            $toCreate[$i]['name'] = end(explode("/", $toCreate[$i]['url']));
                            $extension = strtolower(end(explode(".", $toCreate[$i]['name'])));
                            $toCreate[$i]['extension'] = $extension;
                            $toCreate[$i]['type'] = (isset($exts[$extension])) ? $exts[$extension] : 'undifined';

                            if($toCreate[$i]['type'] == 'img'){
                                $dims = getimagesize('.'.$toCreate[$i]['url']);
                                $toCreate[$i]['dim'] = $dims[0].' x '.$dims[1].' px';
                            }
                            $size = filesize('.'.$toCreate[$i]['url']);
                            $toCreate[$i]['name'] = str_replace('.'.$extension, '', $toCreate[$i]['name']);
                            $toCreate[$i]['parent'] = str_replace(end(explode('/', $toCreate[$i]['url'])), '', $toCreate[$i]['url']);
                        }
                        $toCreate[$i]['size'] = ($size > 1024) ? round($size/1024).' ko' : $size.' o';

                        $toCreate[$i]['parent'] = str_replace($design_dir, '', $toCreate[$i]['parent']);
                        $toCreate[$i]['parent'] = str_replace('/', '-', $toCreate[$i]['parent']);
                        if($toCreate[$i]['parent'] == ''){
                            $toCreate[$i]['parent'] = 'root';
                        } else {
                            $toCreate[$i]['parent'] = substr($toCreate[$i]['parent'], 0, -1);
                        }
                    }
                    $r['to_create'] = $toCreate;

                    // Taille du design + espace utilisé
                    $sizeDesign = sizethis('.'.$design_dir);
                    $pourcent = round(100 * ($sizeDesign/SIZE_MAX));
                    $r['pourcent'] = ($pourcent > 100) ? 100 : $pourcent;
                    $r['sizeDesign'] = ($sizeDesign > 1024) ? round($sizeDesign/1024).' ko' : $sizeDesign.' o';

                    // Renvoi du rapport
                    ob_start();
                    $rapport->compare('.'.$design_dir, '.'.DESIGN_ORIGINAL);
                    $nb_files = count($rapport->getFichiers());
                    $nb_dossiers = count($rapport->getDossiers());
                    $nb_tot = $nb_files + $nb_dossiers;
                    include('../inc/rapport.php');
                    $r['pourcent_complet'] = $rapport->getPourcent();
                    $r['rapport'] = ob_get_clean();
                } else {
                    $r['erreur'] = 'refresh';
                }
            }
            echo json_encode($r);
            break;
    }
    exit();
}
?>