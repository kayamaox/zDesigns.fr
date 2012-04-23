<?php
if(isset($_GET['idm']) && isset($_GET['idd'])){
    header('Content-type: text/css');
    
    ob_start('ob_gzhandler');
    
    $idm = (int) $_GET['idm'];
    $idd = (int) $_GET['idd'];
    if(isset($_GET['dev'])){
        $dir_css = './'.$idm.'/'.$idd.'_dev/css/';
    } else {
        $dir_css = './'.$idm.'/'.$idd.'/css/';
    }

    $urlLog = '../inc/log.php';
    $urlFileLog = '../log.txt';
    require_once('../classes/bdd.php');
    $BDD = new BDD();
    require_once('../inc/core.php');

    
    /*****************************************
     * Détection Mobile + Gestion CSS
     ****************************************/
    require_once('../inc/mobile_device_detect.php');

    if(!isset($_SESSION['mobile'])){
        $_SESSION['mobile'] = mobile_device_detect(true, false, true, true, true, true, true, false, false);
    }

    if($_SESSION['mobile'] && is_file($dir_css.'design.mobile.css') && filesize($dir_css.'design.mobile.css') > 10){
        include($dir_css.'design.mobile.css');
    } else {
        include($dir_css.'design.css');
    }
    
    
    
    /*****************************************
     * Statistiques
     ****************************************/
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    //Requête    
    $BDD->query("INSERT INTO stats_designs (id_design, ip, time)
                        VALUES('".$idd."', '".$ip."', '".time()."')");
}
?>