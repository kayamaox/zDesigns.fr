<?php
$urlLog = '../inc/log.php';
$urlFileLog = '../log.txt';
require_once('../classes/rapport.php');
require_once('../classes/bdd.php');
$BDD = new BDD();
require_once('../inc/core.php');

$BDD->query("INSERT INTO is_down (status, time)
                    VALUES ('1', ".time().")")
?>
