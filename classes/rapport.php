<?php
class Rapport {
    private $racine;
    private $retour = array(
        'dossiers' => array(),
        'fichiers' => array()
    );
    private $pourcent = '--';
    private $exts = array();
    private $nb_elems = 0;

    /*
     * Rapport
     * @param array $ext Tableau des extensions de fichier autorisées
     */
    function __construct($exts = array()) {
        $this->exts = $exts;
    }

    /*
     * compare
     * @param string $d1 URL du dossier du design
     * @param string $d2 URL du dossier de référence (original)
     * @return array
     */
    public function compare($d1, $d2){
        $this->nb_elems = 0;
        $this->racine = $d1;
        $this->comp($d2, $d1);
        $this->pourcent = round((($this->nb_elems - (count($this->getFichiers())+count($this->getDossiers())))*100)/$this->nb_elems);
    }

    private function comp($d1, $d2){
        $dossier = opendir($d1);
        while($f = readdir($dossier)){
            if($f != '.' && $f != ".."){
                if(is_file($d1.$f)){
                    $extension = strtolower(end(explode(".", $d1.$f)));
                    if(array_key_exists($extension, $this->exts)){
                        if(!is_file($d2.$f)){
                            $this->retour['fichiers'][] = array(
                                'nom' => '/'.str_replace($this->racine, '', $d2.$f),
                                'url_from' => $d1.$f,
                                'url_to' => $d2.$f
                            );
                        }
                        $this->nb_elems++;
                    }
                }
                if(is_dir($d1.$f.'/')){
                    if(!is_dir($d2.$f.'/')){
                        $this->retour['dossiers'][] = array(
                            'nom' => '/'.str_replace($this->racine, '', $d2.$f).'/',
                            'url_from' => $d1.$f.'/',
                            'url_to' => $d2.$f.'/'
                        );
                    }
                    $this->comp($d1.$f.'/', $d2.$f.'/');
                    $this->nb_elems++;
                }
            }
        }
    }

    public function getAll(){
        return $this->retour;
    }
    public function getDossiers(){
        return $this->retour['dossiers'];
    }
    public function getFichiers(){
        return $this->retour['fichiers'];
    }
    public function getPourcent(){
        return $this->pourcent;
    }
}
?>
