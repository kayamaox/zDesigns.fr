<?php
/* 
 * Met à jour le pourcentage de complétition des designs publiés / en validation
 */
$urlLog = '../inc/log.php';
$urlFileLog = '../log.txt';
require_once('../classes/rapport.php');
require_once('../classes/bdd.php');
$BDD = new BDD();
require_once('../inc/core.php');
require_once('../inc/functions.php');
require_once('../classes/rapport.php');

if(@dossier_vide('.'.DESIGN_ORIGINAL)){
    exit();
}

if(isset($_GET['all'])){
    $designs = $BDD->query("SELECT id AS idd, id_membre AS idm, active
                            FROM designs
                            WHERE active < 3");
} else {
    $designs = $BDD->query("SELECT id AS idd, id_membre AS idm, active
                            FROM designs
                            WHERE active = '1' OR active = '2'");
}

$i = 0;
while($d = mysql_fetch_assoc($designs)){
    $i++;
    echo $i.' :: ';
    if($d['active'] == 2){
        $design_dir = '../designs/'.$d['idm'].'/'.$d['idd'].'/';
    } else {
        $design_dir = '../designs/'.$d['idm'].'/'.$d['idd'].'_dev/';
        if(!is_dir($design_dir)){
            $design_dir = '../designs/'.$d['idm'].'/'.$d['idd'].'/';
        }
    }
    ${'rapport_'.$d['idd']} = new Rapport($exts);
    ${'rapport_'.$d['idd']}->compare($design_dir, '.'.DESIGN_ORIGINAL);

    $BDD->query("UPDATE designs
                 SET complet = '".${'rapport_'.$d['idd']}->getPourcent()."'
                 WHERE id = '".$d['idd']."'");
    echo $design_dir.' :: '.${'rapport_'.$d['idd']}->getPourcent().'%<br />';
    
    sleep(1);
}
?>
