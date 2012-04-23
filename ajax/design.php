<?php
if(isset($_POST['action'])){
    $urlLog = '../inc/log.php';
    $urlFileLog = '../log.txt';
    require_once('../classes/rapport.php');
    require_once('../classes/bdd.php');
    $BDD = new BDD();
    require_once('../inc/core.php');
    require_once('../inc/functions.php');

    switch($_POST['action']){
        case 'changer_nom':
            $idd = (int) $_POST['id'];
            $req_idm = $BDD->query("SELECT id, id_membre
                                    FROM designs
                                    WHERE id='".$idd."'");
            $req_idm = mysql_fetch_assoc($req_idm);
            $idm = $req_idm['id_membre'];

            if($_SESSION['id'] == $idm || droit_dacces(10)){
                $titre = mysql_real_escape_string(htmlspecialchars($_POST['titre']));
                if(!empty($titre)){
                    if(strlen($titre) <= 25){
                        $BDD->query("UPDATE designs SET titre='".$titre."' WHERE id='".$idd."' ");
                        $r['titre'] = stripslashes($titre);
                        $r['erreur'] = 'true';
                    } else {
                        $r['erreur'] = 'Nom trop long';
                    }
                } else {
                    $r['erreur'] = 'Nom vide';
                }
            } else {
                $r['erreur'] = "Ce n'est pas votre design";
            }
            echo json_encode($r);
            break;


        case 'changer_description':
            $idd = (int) $_POST['id'];
            $req_idm = $BDD->query("SELECT id, id_membre
                                    FROM designs
                                    WHERE id='".$idd."'");
            $req_idm = mysql_fetch_assoc($req_idm);
            $idm = $req_idm['id_membre'];

            if($_SESSION['id'] == $idm || droit_dacces(10)){
                $description = mysql_real_escape_string(htmlspecialchars($_POST['description']));
                if(!empty($description)){
                    if(strlen($description) <= 300){
                        $BDD->query("UPDATE designs SET description='".$description."' WHERE id='".$idd."' ");
                        $r['description'] = stripslashes(str_replace('\n', '', $description));
                        $r['erreur'] = 'true';
                    } else {
                        $r['erreur'] = 'Description trop longue';
                    }
                } else {
                    $r['erreur'] = 'Description vide';
                }
            } else {
                $r['erreur'] = "Ce n'est pas votre design";
            }
            echo json_encode($r);
            break;

            
        case 'changer_cat':
            $idd = (int) $_POST['id'];
            $req_idm = $BDD->query("SELECT id, id_membre
                                    FROM designs
                                    WHERE id='".$idd."'");
            $req_idm = mysql_fetch_assoc($req_idm);
            $idm = $req_idm['id_membre'];

            if($_SESSION['id'] == $idm || droit_dacces(10)){
                $id_cat = (int) mysql_real_escape_string(htmlspecialchars($_POST['cat']));
                $BDD->query("UPDATE designs SET id_cat='".$id_cat."' WHERE id='".$idd."'");
                $cats_design = $BDD->query("SELECT id, nom
                                            FROM cat
                                            WHERE id='".$id_cat."'");
                while($cat = mysql_fetch_assoc($cats_design)){
                    $cat_design[] = $cat;
                }
                $r['cat'] = $cat_design[0]['nom'];
                $r['erreur'] = 'true';
            } else {
                $r['erreur'] = "Ce n'est pas votre design";
            }
            echo json_encode($r);
            break;


        case 'publier':
            $idd = (int) $_POST['id'];
            $req_idm = $BDD->query("SELECT id, id_membre
                                    FROM designs
                                    WHERE id='".$idd."'");
            $req_idm = mysql_fetch_assoc($req_idm);
            $idm = $req_idm['id_membre'];

            if($_SESSION['id'] == $idm || droit_dacces(10)){
                // supprDir('../designs/'.$idm.'/'.$idd.'/');
                copy_folder('../designs/'.$idm.'/'.$idd.'_dev/', '../designs/'.$idm.'/'.$idd.'/');
                $rapport = new Rapport($exts);
                $rapport->compare('../designs/'.$idm.'/'.$idd.'/', '.'.DESIGN_ORIGINAL);
                $BDD->query("UPDATE designs
                             SET complet = '".$rapport->getPourcent()."'
                             WHERE id = '".$idd."'");
                $r['erreur'] = 'true';
            } else {
                $r['erreur'] = "Ce n'est pas votre design";
            }
            echo json_encode($r);
            break;


        case 'publier_zds':
            if(droit_dacces(10)){
                copy_folder('../design/2_dev/', '../design/2/');
                $r['erreur'] = 'true';
            } else {
                $r['erreur'] = "Vous êtes un méchant !";
            }
            echo json_encode($r);
            break;

            
        case 'post_com':
            $idd = (int) $_POST['id'];
            $idm = (isset($_SESSION['id'])) ? $_SESSION['id'] : 6;

            // Appel du webservice Askimet avec les champs de mon formulaire de commentaire.
            require_once('../classes/akismet.php');

            $pseudo = mysql_real_escape_string(htmlspecialchars($_POST['pseudo']));
            $message = mysql_real_escape_string(htmlspecialchars($_POST['message']));

            $akismet = new Akismet(ROOT_ABS, AKISMET_KEY);
            $akismet->setCommentAuthor($pseudo); // nom de l'auteur
            $akismet->setCommentContent($message); // texte du commentaire
            $akismet->setPermalink(ROOT.'zdesigns-'.$idd.'.php'); // URL de l'article

            if($akismet->isCommentSpam() && !droit_dacces(10)){
                $visible = 0;
                $r['erreur'] = "Votre message est du SPAM";
            } else {
                $visible = 1;
                $r['erreur'] = 'true';
            }

            $BDD->query("INSERT INTO com_zdesigns
                         SET id_design = '".$idd."',
                             id_membre = '".$idm."',
                             pseudo = '".$pseudo."',
                             texte = '".$message."',
                             timestamp = '".time()."',
                             visible = '".$visible."'");
            
            if(!isset($_GET['nojs'])){
                echo json_encode($r);
            } else {
                header('Location: '.$_POST['url']);
            }
            break;


        case 'noter':
            $idd = (int) $_POST['id'];
            $vote = (int) $_POST['note'];
            $ip = ip2long($_SERVER['REMOTE_ADDR']);

            $nb = $BDD->query("SELECT id_design, ip
                               FROM designs_votes
                               WHERE ip = '".$ip."' AND id_design = '".$idd."'");
            if(mysql_num_rows($nb) != 0){
                $BDD->query("UPDATE designs_votes
                             SET note = '".$vote."'
                             WHERE ip = '".$ip."' AND id_design = '".$idd."'");
            } else {
                $BDD->query("INSERT INTO designs_votes
                                (id_design, note, ip)
                             VALUES ('".$idd."', '".$vote."', '".$ip."')");
            }

            $note_req = $BDD->query("SELECT note, id_design
                                     FROM designs_votes
                                     WHERE id_design = '".$idd."'");
            $nb_notes = mysql_num_rows($note_req);
            $total_votes = 0;
            while($v = mysql_fetch_assoc($note_req)){
                $total_votes += (int) $v['note'];
            }
            $note = round(($total_votes/$nb_notes), 2);
            $BDD->query("UPDATE designs
                         SET note = '".$note."',
                             note_points = '".$total_votes."'
                         WHERE id = '".$idd."'");

            ob_start();
            note($note);
            $r['note'] = ob_get_contents();
            ob_clean();

            $r['erreur'] = 'true';
            echo json_encode($r);
            break;
    }
}
?>