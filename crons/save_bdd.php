<?php
$urlLog = '../inc/log.php';
$urlFileLog = '../log.txt';
require_once('../classes/rapport.php');
require_once('../classes/bdd.php');
$BDD = new BDD();
require_once('../inc/core.php');
require_once('../inc/functions.php');

// on va faire une requête pour rechercher toutes les tables de la bdd concernée
$result_table = mysql_query("SHOW TABLES");

// Date
$date = date2(time());

$content = "-- Généré le : ".$date."\n\n";

while ($donnees_table = mysql_fetch_array($result_table)){
    $table = $donnees_table[0];

        // on va créer une variable pour y mettre le texte concernant l'en-tête de la structure qui sera écrit dans le fichier .txt
    $content .= "-- \n";
    $content .= "-- Structure de la table ` ".$table." ` \n";
    $content .= "--  \n\n";

        // on va demander la "création" de la table
    $req_structure = "SHOW CREATE TABLE $table ";
    $result_structure = mysql_query($req_structure);
    $donnee_structure = mysql_fetch_array($result_structure);

    $content .= $donnee_structure[1] ;
    $content .= "; \n\n" ;

        // on crée une variable pour le titre du contenu de la table
    $content .= "-- \n";
    $content .= "-- Contenu de la table ` ".$table."` \n";
    $content .= "--  \n\n";

        // on va récupérer le nombre de champs présents dans la table
    $req_champ = "SHOW COLUMNS FROM $table";
    $result_champ = mysql_query($req_champ);
    $nbre_champ = mysql_num_rows($result_champ);

        // on va rechercher TOUS les enregistrements de la table concernée
    $req_tout = "SELECT * FROM $table ";
    $result_tout = mysql_query($req_tout);
    $contenu = "";

    // on va boucler pour sortir toutes les données
    while($donnees_tout = mysql_fetch_array($result_tout)){
        $contenu = "INSERT INTO " . $table . " VALUES (";

        $i = 0;
        // on va boucler tous les champs
        while ( $i < $nbre_champ ){
            // on remplace les apostrophes du contenu par deux apostrophes
            $donnees_tout[$i] = str_replace("'","''",$donnees_tout[$i]);
            $donnees_tout[$i] = str_replace("\\","\\\\",$donnees_tout[$i]);

            // et on affiche les résultats en fonction des champs et dans l'ordre des champs
            $contenu .= "'" . $donnees_tout[$i] . "',";
            $i++;
        }
        // on va enlever la dernière virgule
        $contenu = substr($contenu,0,-1);
        $contenu .= ");\n";
        $content .= $contenu;
    }
}

$url_save_db = '../../save_bdd/tmp.gzip';
if(file_exists($url_save_db)){
    unlink($url_save_db);
}

file_put_contents($url_save_db, $content);



/*  DROPBOX  */
include('../inc/DropboxUploader.php');
    
$fileName = '../../save_bdd/zdesigns--'.date('d-m-Y H\hi').'.gzip';
rename($url_save_db, $fileName);

$uploader = new DropboxUploader('email@truc.com', 'mot_de_passe');
$uploader->upload($fileName, '/BDD_save/zdesigns');

if(!isset($_GET['cron'])){
    $_SESSION['messageInfo'] = "Sauvegarde réussie";
} else {
    echo "ok";
}
?>
