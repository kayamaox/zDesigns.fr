<?php
/*****************************
* supprDir
* ----------------------------
* Supprime un dossier avec son contenu
* ----------------------------
* Prend en paramètre le dossier a supprimer
* @param string $dir : l'adresse du dossier à supprimer
* @return error : En cas d'erreur, la fonction retourne l'erreur de rmdir, sinon, rien.
*****************************/
function supprDir($dir){
    $dossier = opendir($dir);
    while ($f = readdir($dossier)) {
        if($f != '.' && $f != ".."){
            if(is_file($dir.$f)){
                unlink($dir.$f);
            }
            if(is_dir($dir.$f)){
                supprDir($dir.$f.'/');
            }
        }
    }
    closedir($dossier);
    return rmdir($dir);
}

/*****************************
* sizethis
* ----------------------------
* Retourne le poids d'un dossier
* ----------------------------
* Prend en paramètre le dossier source
* @param string $src : l'adresse du dossier source
* @return int : taille en octets
*****************************/
function sizethis($src){
    $size = 0;
    $h = @opendir($src);
    while (($o = @readdir($h)) !== FALSE){
        if (($o != '.') and ($o != '..')){
            if (is_dir($src.'/'.$o))
                $size = $size + sizethis($src.'/'.$o);
            else
                $size = $size+filesize($src.'/'.$o);
        }
    }
    @closedir($h);
    return ($size);
}

/*****************************
* copy_folder
* ----------------------------
* Copie colle un dossier et son contenu
* ----------------------------
* Prend en paramètre le dossier source et celui de destination
* @param string $dir : l'adresse du dossier source
* @param string $dest : l'adresse du dossier de destination
*****************************/
function copy_folder($dir, $dest){
    if(!is_dir($dest)){
        @mkdir($dest.'/', 0777);
    }
    $dossier = @opendir($dir);
    while ($f = @readdir($dossier)) {
        if($f != '.' && $f != ".."){
            if(is_file($dir.$f)){
                @copy($dir.$f, $dest.$f);
            }
            if(is_dir($dir.$f)){
                @copy_folder($dir.$f.'/', $dest.$f.'/');
            }
        }
    }
    @closedir($dossier);
}



/*****************************
* dossier_vide
* ----------------------------
* Permet de savoir si un dossier est vide
* ----------------------------
* prend en paramètre le timestampt de la date à parser, retourne la date parser sous la forme 'Mecredi 6 Janvier 2011'
* @param string $dir : dossier à parcourir
* @return Boolean
*****************************/
function dossier_vide($dir){
    $dossier = @opendir($dir);
    while($elem = @readdir($dossier)){
        if($f != '.' && $f != ".."){
            if(is_file($dir.$elem) || is_dir($dir.$elem)){
                @closedir($dossier);
                return false;
            }
        }
    }
    @closedir($dossier);
    return true;
}



/*****************************
* date2
* ----------------------------
* Parse la date, simplement
* ----------------------------
* prend en paramètre le timestampt de la date à parser, retourne la date parser sous la forme 'Mecredi 6 Janvier 2011'
* @param int $timestamp : le timestamp à parser
* @return string : Retourne la date parsée sous forme de texte court
*****************************/
function date2($timestamp) {
    $jours = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
    $mois = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");

    $date = $jours[date('w', $timestamp)] . ' ' . date('j', $timestamp) . ' ' . $mois[date('m', $timestamp)-1] . ' ' . date('Y', $timestamp);

    return $date;
}

/*****************************
* parse_date
* ----------------------------
* Permet de parser la date en fonction de celle actuelle
* (Exemple : Il y a quelques secondes, hier à 14h02, le 28/12/2011.
* Necessite date2
* ----------------------------
* Prend en paramètres le timestamp à parser, si on veut mette une majuscle au debut (False par défaut), un préfixe (ex : 'le')
* @param int $timestamp : le timestamp à parser
* @param Boolean $maj : si on veut mettre la premiere lette en majuscule
* @return string : la date parsée sous forme de texte formaté
*****************************/
function parse_date($timestamp, $maj=false, $prefixe=''){
    $t = time() - $timestamp;
    $t2 = $timestamp - mktime(0, 0, 0);
    $r = '';
    if($t >= 0 && $t2 > -86400){ // Passé mais moins de deux jours
        if($t < 300){ // Moins de 5 minutes
            if($t < 60){ // Moins de une minute
                $r = ($maj)?'Il y a quelques secondes':'il y a quelques secondes';
            } else { // Quelques minutes
                $r = ($maj)?'Il y a quelques minutes':'il y a quelques minutes';
            }
        } elseif($t2 > 0 && $t2 < 86399) { // Aujourd'hui
            $r = ($maj)?'Aujourd\'hui à ':'aujourd\'hui à ';
            $r .= date('H\hi', $timestamp);
        } else {
            $r = ($maj)?'Hier à ':'hier à ';
            $r .= date('H\hi', $timestamp);
        }
    } else if($t < 0 && $t2 < 259200){ // À venir mais moins de deux jours
        if($t > -300){ // Dans moins de 5 minutes
            if($t > -60){ // Moins de une minute
                $r = ($maj)?'Dans quelques secondes':'dans quelques secondes';
            } else { // Quelques minutes
                $r = ($maj)?'Dans quelques minutes':'dans quelques minutes';
            }
        } elseif($t2 < 172800) { // Demain
            $r = ($maj)?'Demain à ':'demain à ';
            $r .= date('H\hi', $timestamp);
        } else {
            $r = ($maj)?'Après-demain à ':'après-demain à ';
            $r .= date('H\hi', $timestamp);
        }
    } else {
        $r = $prefixe.' '.date2($timestamp).' à '.date('H\hi', $timestamp);
    }

    $r = ($timestamp == 0) ? 'Indéfini' : $r;

    return $r;
}

/*****************************
* removeAccents
* ----------------------------
* Permet de remplacé les accents d'une chaine par leur équivalent désaccentué (é -> e)
* ----------------------------
* Prend en paramètre la chaine à désaccentuer
* @param string $string : La chaine à désaccentuer
* @return string : La chaine désaccentuée
*****************************/
function removeAccents($string){
    $Caracs = array("¥" => "Y", "µ" => "u", "À" => "A", "Á" => "A",
                    "Â" => "A", "Ã" => "A", "Ä" => "A", "Å" => "A",
                    "Æ" => "A", "Ç" => "C", "È" => "E", "É" => "E",
                    "Ê" => "E", "Ë" => "E", "Ì" => "I", "Í" => "I",
                    "Î" => "I", "Ï" => "I", "Ð" => "D", "Ñ" => "N",
                    "Ò" => "O", "Ó" => "O", "Ô" => "O", "Õ" => "O",
                    "Ö" => "O", "Ø" => "O", "Ù" => "U", "Ú" => "U",
                    "Û" => "U", "Ü" => "U", "Ý" => "Y", "ß" => "s",
                    "à" => "a", "á" => "a", "â" => "a", "ã" => "a",
                    "ä" => "a", "å" => "a", "æ" => "a", "ç" => "c",
                    "è" => "e", "é" => "e", "ê" => "e", "ë" => "e",
                    "ì" => "i", "í" => "i", "î" => "i", "ï" => "i",
                    "ð" => "o", "ñ" => "n", "ò" => "o", "ó" => "o",
                    "ô" => "o", "õ" => "o", "ö" => "o", "ø" => "o",
                    "ù" => "u", "ú" => "u", "û" => "u", "ü" => "u",
                    "ý" => "y", "ÿ" => "y");

    $string  = strtr($string, $Caracs);
    return $string;
}

/*****************************
* uniforme
* ----------------------------
* Supprime tous les caratères spéciaux et les transforme en "_". Necessite removeAccents
* ----------------------------
* Prend en paramètre la chaine à parser
* @param string $texte : chaine à parser
* @return string : la chaine parsée
*****************************/
function uniforme($texte){ //transforme les accent en lettre simple et les caractère sépciaux en '_'
    $texte = html_entity_decode($texte);
    $texte = removeAccents($texte);
    //$texte = preg_replace('#&#[0-9]+;#', '_', $texte); //je sais plus
    //$texte = preg_replace('#s#', '_', $texte); //idem
    $texte = preg_replace('#[^a-zA-Z0-9.-]#', '_', $texte); //tous les autres caractères en "_"
    $texte = preg_replace('#-#', '_', $texte); //les tirets en "_"
    $texte = preg_replace('#_+#', '_', $texte); //plus que 1 "_" sera transformé en un "_"
    $texte = preg_replace('#_$#', '', $texte); //le "_" de la fin sera viré
    $texte = preg_replace('#^_#', '', $texte); //celui du début aussi
    return $texte;
}

/*****************************
* droit_dacces
* ----------------------------
* Determine si le visiteur a suffisament de droits pour accéder à la zone protégée par cette fonction
* ----------------------------
* Prend le rang exigé pour accéder à la zone protégée par cette fonction
* @param int $rang_exige : rang exigé pour accéder à la zone protégée par cette fonction
* @return boolean : true si acces ok, false si acces pas ok
*****************************/
function droit_dacces($rang_exige)
{
	if(isset($_SESSION['rang']) && $_SESSION['rang'] >= $rang_exige)
		return true;
	else
		return false;
}

/*****************************
* colJauge
* ----------------------------
* Renvoi une couleur en fonction d'un pourcentage
* ----------------------------
* @param int $pourcent pourcentage du pack design
* @return string code héxadécimal de la couleur
*****************************/
function colJauge($pourcent){
    $colors = array(
        0 => '#8aff00',
        10 => '#8aff00',
        20 => '#a2ff00',
        30 => '#b4ff00',
        40 => '#ccff00',
        50 => '#e4ff00',
        60 => '#fff600',
        70 => '#ffde00',
        80 => '#ff9600',
        90 => '#ff6000',
        100 => '#ff0000'
    );

    $col = $colors[10];
    $col = $colors[ceil($pourcent / 10) * 10];
    return $col;
}


/*****************************
* addMessage
* ----------------------------
* Redirige le visiteur vers la
* page demandée et affiche un message
* ----------------------------
* @param string $type Type du message
* @param string $message Message à afficher
* @param string $url Url de redirection
*****************************/
function addMessage($type, $message, $url = './'){
    $_SESSION['message'][$type] = $message;
    header('Location: '.$url);
    exit();
}

/*****************************
* mess
* ----------------------------
* Permet de rediriger vers une page en affichant une bulle d'info en haut
* (Exemple : "Vous êtes connectés")
* ----------------------------
* Prend en paramètres le l'id du message à afficher (BDD) et le lien de redirection (défaut = index.html)
* @param int $id_mess : l'id BDD du message à afficher
* @param String $lien : lien de redirection (Défaut = index.html)
*****************************/
function mess($id_mess, $lien='forum.html')
{
    $lien = str_replace('./', '', $lien);

    $ret = mysql_query("SELECT * FROM messages WHERE id = '".$id_mess."' ") OR DIE(mysql_error());
    $donnees = mysql_fetch_array($ret);

    if($donnees['type'])
    {
        $_SESSION['message']['error'] = $donnees['message'];
        header('Location: '.ROOT.$lien);
        exit();
    }
    else
    {
        $_SESSION['message']['info'] = $donnees['message'];
        header('Location: '.ROOT.$lien);
        exit();
    }
}


/*****************************
* pluralize
* ----------------------------
* Gestion simple des pluriels
* ----------------------------
* @param int $n Nombre qui défini le singulier/pluriel. Insérable via {#}
* @param string $string Chaine à traiter
* @param array $values Tableau de valeurs insérables via %d dans string pour un nombre par exemple
* @return string
*****************************/
function pluralize($n = 1, $string = '', $values = array()) {
    // remplace {#} par le chiffre
    $string = str_replace("{#}", $n, $string);
    // cherche toutes les occurences de {...}
    preg_match_all("/\{(.*?)\}/", $string, $matches);
    foreach($matches[1] as $k=>$v) {
        // on coupe l'occurence à |
        $part = explode("|", $v);
        // si aucun
        if ($n == 0) {
            $mod = (count($part) == 1) ? "" : $part[0];
        // si singulier
        } else if ($n == 1) {
            $mod = (count($part) == 1) ? "" : $part[1];
        // sinon pluriel
        } else {
            $mod = (count($part) == 1) ? $part[0] : ((count($part) == 2) ? $part[1] : $part[2]);
        }
        // je remplace les occurences trouvées par le bon résultat.
        $string = str_replace($matches[0][$k], $mod , $string);
    }
    // retourne le résultat en y incluant éventuellement les valeurs passées
    return vsprintf($string, $values);
}



/*****************************
* scanFolderToZip
* ----------------------------
* Retourne un tableau utilisé par la fonction zipFolder
* ----------------------------
* @param string $folder Adresse du dossier à parcourir
* @return array
*****************************/
function scanFolderToZip($folder) {
    $files = array();
    $dh = opendir($folder);
    // je parcours le dossier dans lequel je me trouve
    // et j'analyse ce que je trouve...
    while (($file = readdir($dh)) !== false) {
        $path = $folder."/".$file;
        // si c'est un fichier, j'en récupère
        // le nom et le contenu
        if (is_file($path)) {
            $file = array();
            $fp = fopen($path, "r");
            $file["name"] = $path;
            $file["content"] = fread($fp, filesize($path));
            $files[] = $file;
            fclose($fp);
        // si c'est un dossier qui n'est pas . ou ..
        // je relance un scan sur son contenu.
        } else if ($file != "." && $file != "..") {
           $files = array_merge($files, scanFolderToZip($path));
        }
    }
    closedir($dh);
    return $files;
}



/*****************************
* create_dir
* ----------------------------
* Crée un dossier et ses parents si nécéssaire
* ----------------------------
* @param string $url Adresse du dossier à créer
* @param boolean $write Envoyer un retour json ?
* @param array $r dossiers créés pour arriver au dossier à créer
* @return string
*****************************/
function create_dir($url, $write = false, $r = array()){
    if(substr($url, -1) == '/'){
        $urlTestDir = substr(str_replace(end(explode('/', substr($url,  0, -1))), '', $url), 0, -1);
    } else {
        $urlTestDir = str_replace(end(explode('/', $url)), '', $url);
    }
    if(!is_dir($urlTestDir)){
        $r[] = $urlTestDir;
        create_dir($urlTestDir, $write, $r);
        mkdir($urlTestDir, 0777);
    } else if($write) {
        echo json_encode($r);
    }
}



/*****************************
* note
* ----------------------------
* Dessine les étoiles d'une note
* ----------------------------
* @param int $note Note comprise entre 0 et 5
* @return string
*****************************/
function note($note){
    echo '<span class="note">';
    $note = round($note);
    for($i = 1; $i <= 5; $i++){
        if($note >= $i){
            echo '<span class="ico etoile">'.$i.'</span>';
        } else {
            echo '<span class="ico etoile2">'.$i.'</span>';
        }
    }
    echo '</span>';
}


/*****************************
* br
* ----------------------------
* Saute le nombre de lignes indiqué
* ----------------------------
* @param int $nb_br Nombre de lignes à sauter
* @return string
*****************************/
function br($nb_br = 1){
    for($i=0; $i<$nb_br; $i++){
        echo '<br />';
    }
}



/*****************************
* ifdecode
* ----------------------------
* Décode en UTF8 si besoin
* ----------------------------
* @param string $string Chaine à traiter
* @return string
*****************************/
function ifdecode($string){
    if(substr_count($string, 'Ã') != 0){
        $string = utf8_decode($string);
    }
    
    return $string;
}



/*****************************
* mail_all
* ----------------------------
* Envoi un email aux admins
* ----------------------------
* @param string $sujet Sujet du mail
* @param string $message Contenu de l'email
*****************************/
function mail_all($sujet, $message){
    $adresses = array(
        'demodealex@gmail.com',
        'heilmann.cyril@free.fr',
        'be_someone@live.fr'
    );
    
    $headers ='From: "Contact - zDesigns.fr"<notification@zdesigns.fr>'."\n";
	$headers .='Content-Type: text/html; charset="utf-8"'."\n";
    $headers .='Content-Transfer-Encoding: 8bit'; 

    foreach($adresses as $adr){
        mail($adr, $sujet, $message, $headers);
    }
}



/*****************************
* get_time_hour
* ----------------------------
* Retourne le timestamp arrondi à l'heure
* ----------------------------
* @param timestamp $t Heure de l'évènement
* @return timestamp
*****************************/
function get_time_hour($t){
    $time = mktime(date('H', $t), 0, 0, date('m', $t), date('d', $t), date('Y', $t));
    return $time;
}



/*****************************
* url_zdesigns
* ----------------------------
* Formate les URL pour la galerie des zDesigns
* ----------------------------
* @param int $id_cat ID de la catégorie
* @param int $id_tri ID de l'ordre tri
* @param int $id_design ID du design
* @param string $titre A mettre à la fin de l'url avant .html
* @return string
*****************************/
function url_zdesigns($id_cat = 0, $id_tri = 0, $id_design = 0, $titre = ''){
    $url = ROOT.'zdesigns';
    
    $url .= ($id_cat != 0) ? '-c'.$id_cat : '';
    $url .= ($id_tri != 0) ? '-t'.$id_tri : '';
    $url .= ($id_design != 0) ? '-'.$id_design : '';
    
    $url .= ($titre != '') ? '-'.str_replace('_', '-', uniforme($titre)) : '';
    return strtolower($url).'.html';
}



/*****************************
* pagination
* ----------------------------
* Retourne une pagination HTML
* ----------------------------
* @param string $url_format Format de l'URL, {p} représente le numéro de la page
* @param int $current_page Page courante
* @param int $nb_pages Nombre de pages au total
* @param int $nb_elems Nombre d'éléments au total
* @param int $elem_par_page Nombre d'éléments par page
* @return string
*****************************/
function pagination($url_format = null, $current_page = 1, $nb_pages = null, $nb_elems = null, $elem_par_page = 10){
    $r = '';
    if($url_format != null && !empty($url_format)){
        if($nb_pages == null){
            if($nb_elems != null && $nb_elems > 0){
                $nb_pages = ceil($nb_elems/$elem_par_page);
            } else {
                echo "ERREUR dans l'utilisation de pagination() : un argument est manquant";
            }
        }
        
        if($nb_pages != null){
            $r .= '<div class="pagination">';
                for($i = 1; $i <= $nb_pages; $i++){
                    if($i == $current_page){
                        $r .= '<span class="page current">'.$i.'</span>';
                    } else {
                        $r .= '<a href="'.str_replace('{p}', $i, $url_format).'" class="page">'.$i.'</a>';
                    }
                }
            $r .= '</div>';
        }
    }
    return $r;
}
?>