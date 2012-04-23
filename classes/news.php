<?php
Class News {
    private $nombre;
    private $ordre;
    private $BDD;

    private $resultat;
    
    public function __construct($BDD=null, $nombre='0', $ordre='DESC'){
        if($BDD != null){
            $this->BDD = $BDD;
        } else {
            require_once('./classes/bdd.php');
            $this->BDD = new BDD();
        }
        $this->nombre = $nombre;
        $this->ordre = $ordre;

        $this->traiter();
    }


    public function traiter() {
        $req = 'SELECT news.id, news.titre, news.id_pseudo, membres.pseudo, news.html, news.timestamp
                FROM news
                INNER JOIN membres
                    ON news.id_pseudo = membres.id
                WHERE visible < 3
                ORDER BY news.id '.$this->ordre;
        if($this->nombre > 0)
            $req .= ' LIMIT 0, '. $this->nombre;
        $this->BDD->query($req);
        $this->resultat = $this->BDD->retourner();
    }


    public function recupere() {
        return $this->resultat;
    }


    public function __toString() {
        false;
    }
};
?>