<?php
Class BDD {
    private $retour;

    // Ouvre la connexion
    public function __construct($request = null) {
        mysql_connect('localhost', 'root', '');
        mysql_select_db('zdesigns');
        mysql_query("SET NAMES 'utf8'");
        if($request != null){
            $this->query($request);
        }
    }

    // Execute une requette SQL et retourne le résultat
    public function query($sql){
        if(!isset($_SESSION['nb_requete'])) $_SESSION['nb_requete'] = 0;
        $_SESSION['nb_requete']++;
        $this->retour = mysql_query($sql) OR DIE (mysql_error());
        return $this->retourner();
    }

    // Retourne le résultat de la dernière requette
    public function retourner() {
        return $this->retour;
    }

    // Ferme la connexion à la fin des opérations
    public function  __destruct() {
        mysql_close();
    }
};
?>