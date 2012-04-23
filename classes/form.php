<?php
class form {    
    public $name;
    private $data;
    private $errors;
    private $doReturn;
    private $lang;
    private $mois_num = array(1=>'01', 2=>'02', 3=>'03', 4=>'04', 5=>'05', 6=>'06', 7=>'07', 8=>'08', 9=>'09', 10=>'10', 11=>'11', 12=>'12');
    static $inc_lang = false;
    
    
    
    /**
     * Constructeur
     *
     * @param $name, nom du formulaire
     * @param $doReturn, définie si les fonction return ou echo
     */
    function __construct($name = "formulaire", $doReturn = false){
        $this->name = $name;
        $this->doReturn = $doReturn;
        
        if(!isset($_SESSION)){
            session_start();
        }
        if(isset($_SESSION['lang'])){
            $lang = $_SESSION['lang'];
        } else {
            $lang = 'fr';
        }
        if(!self::$inc_lang){
            include("./inc/lang/".$lang.".php");
            self::$inc_lang = true;
        }
        $this->lang = new lang();
    }
    
    
    
    /**
     * Débute le formulaire
     * 
     * @param $method, 
     * @param $action, page de traitement du formulaire
     * @param $attr, array optionnel permettant d'ajouter des attributs
     */
    function start($method, $action, $attr = array()){
        $r = '<form method="'.strtolower($method).'" action="'.$action.'" name="'.$this->name.'"';
        foreach($attr as $k=>$v){
            $r .= ' '.$k.'="'.$v.'"';
        }
        
        if($this->doReturn){
            return $r.' >';
        } else {
            echo $r.' >';
        }
    }
    function end(){
        if($this->doReturn){
            return "</form>";
        } else {
            echo "</form>";
        }
    }
    
    
    /**
     * Crée un champ input
     * 
     * @param $champ, nom du champ
     * @param $label, si défini crée un label associé à l'input
     * @param $type, définie le type de l'input : text, password
     * @param $attr, array optionnel permettant d'ajouter des attributs
     */
    function input($champ, $label = null, $type = "text", $attr = array()){
        $r = '';
        if($label != null){
            $r .= '<label for="'.$this->name.'_'.$champ.'">'.$label.'</label>';
        }
        $r .= '<input type="'.$type.'" name="'.$champ.'" id="'.$this->name.'_'.$champ.'"';
        foreach($attr as $k=>$v){
            $r .= ' '.$k.'="'.$v.'"';
        }
        if(isset($this->data[$champ])){
            $r .= ' value="'.$this->data[$champ].'"';
        }
        $r .= ' />';
        
        $r .= $this->getErrors($champ);
        
        if($this->doReturn){
            return $r;
        } else {
            echo $r;
        }
    }
    
    
    /**
     * Crée un textarea
     * 
     * @param $champ, nom du champ
     * @param $value, valeur par défaut
     * @param $attr, array optionnel permettant d'ajouter des attributs (cols, rows, ...)
     */
    function textarea($champ, $value, $label = null, $attr = array()){
        $r = '';
        if($label != null){
            $r .= '<label for="'.$this->name.'_'.$champ.'">'.$label.'</label><br/>';
        }
        $r .= '<textarea id="'.$this->name.'_'.$champ.'" name="'.$champ.'"';
        foreach($attr as $k=>$v){
            $r .= ' '.$k.'="'.$v.'"';
        }
        $r .= ' >';
        
        if(isset($this->data[$champ])){
            $r .= $this->data[$champ];
        } else {
            $r .= $value;
        }
        $r .= '</textarea>';
        if($this->doReturn){
            return $r;
        } else {
            echo $r;
        }
    }
    
    
    /**
     * Crée un champ input de type hidden
     * 
     * @param $champ, nom du champ
     * @param $value, valeur associée
     */
    function hidden($champ, $value){
        $r = '<input type="hidden" name="'.$champ.'" value="'.$value.'" />';
        
        if($this->doReturn){
            return $r;
        } else {
            echo $r;
        }
    }
    
    
    /**
     * Crée un champ input de type submit
     * 
     * @param $value, texte du bouton
     * @param $attr, array optionnel permettant d'ajouter des attributs
     */
    function submit($value = "Envoyer", $attr = array(), $type = "submit"){
        $r = '<input type="'.$type.'" value="'.$value.'"';
        foreach($attr as $k=>$v){
            $r .= ' '.$k.'="'.$v.'"';
        }
        $r .=  ' />';
        
        if($this->doReturn){
            return $r;
        } else {
            echo $r;
        }
    }
    
    
    /**
     * Crée un champ input de type file :: "Parcourir"
     * 
     * @param $name, texte du bouton
     * @param $attr, array optionnel permettant d'ajouter des attributs
     */
    function file($champ, $label=null, $attr = array()){
        $r = '';
        if($label != null){
            $r .= '<label for="'.$this->name.'_'.$champ.'">'.$label.'</label>';
        }
        $r .= '<input type="file" name="'.$champ.'" id="'.$this->name.'_'.$champ.'"';
        foreach($attr as $k=>$v){
            $r .= ' '.$k.'="'.$v.'"';
        }
        $r .=  ' />';
        
        $r .= $this->getErrors($champ);
        if($this->doReturn){
            return $r;
        } else {
            echo $r;
        }
    }
    
    
    /**
     * Crée un champ select
     * 
     * @param $name, valeur de name
     * @param $options, array associant value=>label
     * @param $label, valeur du label
     * @param $attr, array optionnel permettant d'ajouter des attributs
     * @param $gestErrors, définit si il gere l'affichage des erreurs
     */
    function select($champ, $options, $label = null, $attr = array(), $gestErrors=true){
        $r = '';
        if($label != null){
            $r .= '<span>'.$label.'</span>';
        }
        $r .= '<select name="'.$champ.'"';
        foreach($attr as $k=>$v){
            $r .= ' '.$k.'="'.$v.'"';
        }
        $r .= ' >';
        
        foreach($options as $val=>$lab){
            $r .= '<option value="'.$val.'"';
            if(isset($this->data[$champ]) && $this->data[$champ] == $val){
                $r .= ' selected="selected"';
            }
            $r .= '>'.$lab.'</option>';
        }
        
        $r .= '</select>';
        
        if($gestErrors){
            $r .= $this->getErrors($champ);
        }
        
        if($this->doReturn){
            return $r;
        } else {
            echo $r;
        }
    }
    
    /**
     * Crée un/des champ(s) select pour une date
     * 
     * @param $jour, 0=>pas de jour | 1=>jours en lettres | 2=>jours en chiffres
     * @param $mois, 0=>pas de mois | 1=>mois en lettres | 2=>mois en 3 lettres | 3=>mois en chiffres
     * @param $an, 0=>pas d'année | 1=>années en chiffres
     * @param $heures, 0=>pas d'heures | 1=>heures en chiffres
     * @param $minutes, 0=>pas de minutes | 1=>minutes en chiffres
     */
    function selectDate($champ, $label = null, $jour = 0, $mois = 0, $an = 0, $heure = 0, $minute = 0){
        $doReturn_tmp = $this->doReturn;
        $this->doReturn = true;
        $r = '';
        if($label != null){
            $r .= '<span>'.$label.'</span>';
        }
        
        if($jour == 1){
            $r .= $this->select($champ.'_jour', $this->lang->date['jours'], null, array(), false);
        } else if($jour == 2){
            $jours = array();
            for($i=1; $i<=31; $i++){
                $jours[$i] = $i;
            }
            $r .= $this->select($champ.'_jour', $jours, null, array(), false, true);
        }
        if($mois != 0){
            switch($mois){
                case 1:
                    $r .= $this->select($champ.'_mois', $this->lang->date['mois'], null, array(), false);
                    break;
                case 2:
                    $r .= $this->select($champ.'_mois', $this->lang->date['mois_min'], null, array(), false);
                    break;
                case 3:
                    $r .= $this->select($champ.'_mois', $this->mois_num, null, array(), false);
                    break;
            }
        }
        if($an == 1){
            $ans = array();
            for($i=(date('Y')-90); $i<=date('Y'); $i++){
                $ans[$i] = $i;
            }
            $r .= $this->select($champ.'_an', $ans, null, array(), false);
        }
        
        if($heure == 1){
            $r .= " à ";
            $heures = array();
            for($i=0; $i<24; $i++){
                $heures[$i] = $i;
            }
            $r .= $this->select($champ.'_heure', $heures, null, array(), false);
            $r .= "h";
        }
        
        if($minute == 1){
            $minutes = array();
            for($i=0; $i<60; $i++){
                $minutes[$i] = $i;
            }
            $r .= $this->select($champ.'_minutes', $minutes, null, array(), false);
        }
        
        $r .= $this->getErrors($champ);
        $this->doReturn = $doReturn_tmp;
        if($this->doReturn){
            return $r;
        } else {
            echo $r;
        }
    }
    
    
    /**
     * Crée des champs radio
     *
     * @param string $champ, nom du champ
     * @param array $radios, associe value=>label
     * @param string $label, label général des radios
     * @param array $attr, optionnel permet d'ajouter des attributs 
     */
    function radio($champ, $radios, $label = null, $sep = "", $attr = array()){
        $r = '';
        if($label != null){
            $r .= '<span>'.$label.'</span><br/>';
        }
        
        $attrs = '';
        foreach($attr as $k=>$v){
            $attrs .= ' '.$k.'="'.$v.'"';
        }
        
        foreach($radios as $val=>$lab){
            $r .= '<label><input type="radio" value="'.$val.'" name="'.$this->name.'['.$champ.']"';
            if(isset($this->data[$champ]) && $this->data[$champ] == $val){
                $r .= ' checked="checked"';
            }
            $r .= $attrs.' > '.$lab.'</label>'.$sep;
        }
        
        $r = substr($r, 0, -strlen($sep));
        
        if($this->doReturn){
            return $r;
        } else {
            echo $r;
        }
    }
    
    
    /**
     * Crée un fieldset avec une legende
     * 
     * @param $legend, valeur de la legende
     * @param $attr, array optionnel permettant d'ajouter des attributs 
     */
    function fieldset_start($legend = null, $attr = array()){
        $r = '<fieldset';
        foreach($attr as $k=>$v){
            $r .= ' '.$k.'="'.$v.'"';
        }
        $r .= ' >';
        if($legend != null){
            $r .= '<legend>'.$legend.'</legend>';
        }
        
        if($this->doReturn){
            return $r;
        } else {
            echo $r;
        }
    }
    function fieldset_end(){
        $r = '</fieldset>';
        
        if($this->doReturn){
            return $r;
        } else {
            echo $r;
        }
    }
    
    
    /**
     * Getters et Setters
     *
     * setValues :: enregistre un array contenant les valeurs par défauts des champs
     * setErrors :: enregistre un array contenant les erreus associées aux champs
     * getErrors :: retourne un span de l'erreur associée au champ s'il y en a une
     */
    function setValues($data){
        $this->data = $data;
    }
    
    function setErrors($errors){
        $this->errors = $errors;
    }
    
    function getErrors($champ){
        if(isset($this->errors[$champ])){
            return '<span class="error" style="padding-left: 25px; padding-right: 7px;">'.$this->errors[$champ].'</span>';
        }
    }
}
?>