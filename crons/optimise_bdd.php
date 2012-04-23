<?php
$urlLog = '../inc/log.php';
$urlFileLog = '../log.txt';
require_once('../classes/rapport.php');
require_once('../classes/bdd.php');
$BDD = new BDD();
require_once('../inc/core.php');


// On initialise la requête d'optimisation
$sql_opt = "OPTIMIZE TABLE ";

// On recherche toutes les données des tables
$rep = mysql_query("SHOW TABLE STATUS");

while($t_status = mysql_fetch_assoc($rep)) {
    if($t_status["Data_free"] > 0) {
        // Data_free est supérieur à 0, on peut optimiser la table
        $sql_opt .= "`".$t_status["Name"]."`, ";
    }
}

$sql_opt = substr($sql_opt, 0, -2);
mysql_query($sql);
?>