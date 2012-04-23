<?php
/* 
 * Supprime tous les designs supprimé (mais récupérables) depuis plus d'une semaine
 * ET 
 * Envoi à supprimer les designs donc le pourcentage de complétion est inférieur à 35%
 */
$urlLog = '../inc/log.php';
$urlFileLog = '../log.txt';
require_once('../classes/rapport.php');
require_once('../classes/bdd.php');
$BDD = new BDD();
require_once('../inc/core.php');
require_once('../inc/functions.php');

$nb_jours = 7; // Une semaine de délais
$date_max = time() - ($nb_jours * 86400);
/*
$designs_to_del = $BDD->query("SELECT id, id_membre, active, date_suppr
                               FROM designs
                               WHERE active = '3' AND date_suppr < '".$date_max."'");
*/
$designs_to_del = $BDD->query("SELECT id, id_membre, active, date_suppr
                               FROM designs
                               WHERE active = '3'");

while($d = mysql_fetch_assoc($designs_to_del)){
    $url = '../designs/'.$d['id_membre'].'/'.$d['id'].'_del/';
    $url2 = '../designs/'.$d['id_membre'].'/'.$d['id'].'/';
    if(is_dir($url) || is_dir($url2)){
        if(supprDir($url) || supprDir($url2)){
            $BDD->query("DELETE FROM designs
                         WHERE id = ".$d['id']);
        }
    } else {
        $BDD->query("DELETE FROM designs
                     WHERE id = ".$d['id']);
    }
}




$designs_to_del2 = $BDD->query("SELECT id, id_membre, titre, active, complet
                                FROM designs
                                WHERE complet < '35' AND active != '3'");
echo mysql_num_rows($designs_to_del2).'<br />';

while($d2 = mysql_fetch_assoc($designs_to_del2)){
    $BDD->query("UPDATE designs
                 SET active = '3', date_suppr = '".time()."'
                 WHERE id = '".$d2['id']."'");
    
    $design_dir = '../designs/'.$d2['id_membre'].'/'.$d2['id'].'_dev/';
    
    $action = ' ';
    
    if(is_dir($design_dir)){
        rename($design_dir, str_replace('_dev', '_del', $design_dir));
        supprDir(str_replace('_dev', '', $design_dir));
        $action .= ':: Renommé';
    } else {
        $action .= ':: Dossier Inexistant';
    }
    
    echo $d2['id_membre'].'/'.$d2['id'].' :: '.$d2['titre'].$action.'<br />';
}
?>
