<?php
if(!isset($_SESSION)){session_start();}
$dev = false;

define('ROOT', '/zDesigns/v2_dev/');
// define('ROOT', 'http://www.zdesigns.fr/');
define('ROOT_ABS', 'C:/wamp/www/zDesigns/v2_dev/');
// define('ROOT_ABS', 'http://www.zdesigns.fr/');
define('DESIGN', '2/');
define('DESIGN_DIR', ROOT.'design/'.DESIGN);
define('DESIGN_ORIGINAL', './designs/officiel/');

define('NO_SCREEN', DESIGN_DIR.'images/no_screen.jpg');

define('SIZE_MAX', 1536000); // 1,5 Mo
define('POURCENT_MIN', 70); // 70%

define('AKISMET_KEY', 'la_cle');

$exts = array(
    'jpg'  => 'img',
    'jpeg' => 'img',
    'png'  => 'img',
    'gif'  => 'img',
    
    'ico'  => 'undifined',

    'css'  => 'code',
    'scss' => 'code'
);

$completion = array(
    70  => "devrait être utilisable",
    80  => "est utilisaable",
    90  => "est presque à jour",
    100 => "est à jour"
);

$rangs = array(
    0 => 'Visiteur',
    1 => 'Membre',
    8 => 'Développeur',
    10 => 'Administrateur'
);

$etats = array(
    0 => array(
            'etat' => 'Hors ligne',
            'couleur' => 'grey'
        ),
    1 => array(
            'etat' => 'En Validation',
            'couleur' => '#a3773e'
        ),
    2 => array(
            'etat' => 'En ligne',
            'couleur' => 'green'
        ),
    3 => array(
            'etat' => 'À Supprimer',
            'couleur' => 'red'
        )
);

$statut = array(
    0 => array(
            'etat' => 'Nouveau',
            'couleur' => 'grey'
        ),
    1 => array(
            'etat' => 'En Cours',
            'couleur' => '#a3773e'
        ),
    2 => array(
            'etat' => 'Terminé',
            'couleur' => 'green'
        )
);


$urlLog = (!isset($urlLog)) ? './inc/log.php' : $urlLog;
$urlFileLog = (!isset($urlFileLog)) ? './log.txt' : $urlFileLog;
require_once($urlLog);
$log = new log($urlFileLog, $dev);

if(!isset($BDD)){
    require_once('./classes/bdd.php');
    $BDD = new BDD();
}
?>
