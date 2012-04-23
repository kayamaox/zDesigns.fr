<?php
$urlLog = '../inc/log.php';
$urlFileLog = '../log.txt';
require_once('../classes/rapport.php');
require_once('../classes/bdd.php');
$BDD = new BDD();
require_once('../inc/core.php');
require_once('../inc/functions.php');


$r = array();


$stats = array(
    'hier' => 0,
    'jour' => 0,
    'semaine' => 0,
    'mois' => 0,
    'an' => 0
);
$stats2 = array(
    'hier' => 0,
    'jour' => 0,
    'semaine' => 0,
    'mois' => 0,
    'an' => 0
);
$req_stats = $BDD->query("SELECT ip, id_design, time
                          FROM stats_designs
                          GROUP BY ip
                          ORDER BY time DESC");
$req_stats2 = $BDD->query("SELECT ip, id_design, time
                           FROM stats_designs
                           ORDER BY time DESC");
$r_stats = array();
$r_stats2 = array();

while($s = mysql_fetch_assoc($req_stats)){
    if($s['time'] >= mktime(0, 0, 0)-3600*24 && $s['time'] < mktime(0, 0, 0)){
        $stats['hier'] += 1;
    }
    if($s['time'] >= mktime(0, 0, 0)){
        $stats['jour'] += 1;
    }
    if($s['time'] >= (mktime(0, 0, 0)-3600*24*7)){
        $stats['semaine'] += 1;
    }
    if($s['time'] >= (mktime(0, 0, 0)-3600*24*7*4)){
        $stats['mois'] += 1;
    }
    if($s['time'] >= (mktime(0, 0, 0)-3600*24*7*4*12)){
        $stats['an'] += 1;
    }
}
while($s2 = mysql_fetch_assoc($req_stats2)){
    if($s2['time'] > (time()-3600*24*7)){
        $r_stats[get_time_hour($s2['time'])][$s2['ip']] = $s2;
        $r_stats2[] = $s2;
    }

    if($s2['time'] >= mktime(0, 0, 0)-3600*24 && $s2['time'] < mktime(0, 0, 0)){
        $stats2['hier'] += 1;
    }
    if($s2['time'] >= mktime(0, 0, 0)){
        $stats2['jour'] += 1;
    }
    if($s2['time'] >= (mktime(0, 0, 0)-3600*24*7)){
        $stats2['semaine'] += 1;
    }
    if($s2['time'] >= (mktime(0, 0, 0)-3600*24*7*4)){
        $stats2['mois'] += 1;
    }
    if($s2['time'] >= (mktime(0, 0, 0)-3600*24*7*4*12)){
        $stats2['an'] += 1;
    }
}

sort($r_stats2);

ob_start();
echo '<b>'.$stats['hier'].'</b> personnes ont utilisé un design distant hier<br />';
echo '<b>'.$stats['jour'].'</b> personnes ont utilisé un design distant aujourd\'hui<br />';
echo '<b>'.$stats['semaine'].'</b> personnes ont utilisé un design distant ces 7 derniers jours<br />';
echo '<b>'.$stats['mois'].'</b> personnes ont utilisé un design distant ces 30 derniers jours<br />';
echo '<b>'.$stats['an'].'</b> personnes ont utilisé un design distant ces 365 derniers jours<br />';
echo '<b>'.mysql_num_rows($req_stats).'</b> personnes ont déjà utilisé un design distant<br />';
echo '<b>'.$stats2['hier'].'</b> pages chargées sur le SdZ utilisant un design distant hier<br />';
echo '<b>'.$stats2['jour'].'</b> pages chargées sur le SdZ utilisant un design distant aujourd\'hui<br />';
echo '<b>'.$stats2['semaine'].'</b> pages chargées sur le SdZ utilisant un design distant ces 7 derniers jours<br />';
echo '<b>'.$stats2['mois'].'</b> pages chargées sur le SdZ utilisant un design distant ces 30 derniers jours<br />';
echo '<b>'.$stats2['an'].'</b> pages chargées sur le SdZ utilisant un design distant ces 365 derniers jours<br />';
echo '<b>'.mysql_num_rows($req_stats2).'</b> pages chargées sur le SdZ utilisant un design distant <br />';

$r['stats_text'] = ob_get_contents();
ob_end_clean();


$g1 = array();      $d1 = array();
$g2 = array();      $d2 = array();

// $first_time = get_time_hour($r_stats2[0]['time']);
$first_time = get_time_hour(time()-3600*24*7);
$hour = get_time_hour(time())-1;
$nb_hour = ($hour - $first_time)/3600;

for($i = 0; $i <= $nb_hour; $i++){
    $t = $first_time + 3600*$i;
    if($t != $hour){
        $g1[$t] = 0;
        $g2[$t] = 0;
    }
}

foreach($r_stats as $h=>$s){
    foreach($s as $k=>$v){
        if(!isset($g1[$h])){
            $g1[$h] = 0;
            $g2[$h] = 0;
        }
        if($h != $hour){
            $g1[$h] += 1;
        }
    }
}

foreach($g1 as $d=>$n){
    if(date('I', $d) == 1){
        $d += 3600;
    }
    $d1[] = array(($d+3600)*1000, $n);
}


foreach($r_stats2 as $s){
    $h = get_time_hour($s['time']);
    if(!isset($g2[$h])){
        $g2[$h] = 0;
    }
    if($h != $hour){
        $g2[$h] += 1;
    }
}

foreach($g2 as $d=>$n){
    if(date('I', $d) == 1){
        $d += 3600;
    }
    $d2[] = array(($d+3600)*1000, $n);
}


$r['d1'] = $d1;
$r['d2'] = $d2;

$r['erreur'] = 'true';
echo json_encode($r);
?>