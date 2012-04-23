<?php
$urlLog = '../inc/log.php';
$urlFileLog = '../log.txt';
require_once('../classes/rapport.php');
require_once('../classes/bdd.php');
$BDD = new BDD();
require_once('../inc/core.php');
require_once('../inc/functions.php');


$designs_restaure = $BDD->query("SELECT id, id_membre, titre, active, complet, date_suppr
                                 FROM designs
                                 WHERE active = '3' AND (complet > '35' OR complet = '0')");

while($d = mysql_fetch_assoc($designs_restaure)){
    $BDD->query("UPDATE designs
                 SET active = '0', date_suppr = '0'
                 WHERE id = '".$d['id']."'");
    
    $design_dir = '../designs/'.$d['id_membre'].'/'.$d['id'].'_del/';
    
    $action = ' ';
    
    if(is_dir($design_dir)){
        $rename = rename($design_dir, str_replace('_del', '_dev', $design_dir));
        $action .= ':: Renommé';
    } else {
        $action .= ':: Dossier Inexistant';
    }
    
    echo $d['id_membre'].'/'.$d['id'].' :: '.$d['titre'].$action.'<br />';
}
?>
