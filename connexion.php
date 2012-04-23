<?php
$co_form = true;
$errors = array();
require_once("./inc/core.php");
require_once("./inc/functions.php");

if(isset($_POST) && isset($_POST['json'])){
    $co_form = false;
    $r = array(
        'erreur_login' => '',
        'erreur_pass'  => ''
    );

    if(isset($_POST['pseudo']) && !empty($_POST['pseudo'])){  // S'il a posté un login et qu'il n'est pas vide
        if(isset($_POST['pass']) && !empty($_POST['pass'])){ // S'il a posté un mot de pass et qu'il n'est pas vide

        $pseudo = mysql_real_escape_string(htmlentities($_POST['pseudo'],ENT_QUOTES)); //on sécurise

        $password = $_POST['pass']; //on sécurise
        if($_POST['json'] != 'cookies'){
            $pass1 = md5($password);
            $pass2 = sha1($password);
            $pass = $pass1.$pass2;
        } else {
            $pass = $password;
        }

        $nombrepseudo = mysql_result($BDD->query("SELECT COUNT(*) FROM membres WHERE pseudo = '".$pseudo."'"), 0);

            if($nombrepseudo == 1){  //Le pseudo existe
                $r['erreur_login'] = 'ok';
                $requete = $BDD->query('SELECT id, mdp, pseudo, rang, tchat FROM membres WHERE pseudo = "'.$pseudo.'" ');
                $donnees = mysql_fetch_assoc($requete);

                if($donnees['mdp'] == $pass){ //Accès ok, on peut se connecter
                    $r['erreur_pass'] = 'ok';

                    if(isset($_POST['connexion_auto']) && $_POST['connexion_auto'] == 'on'){
                        $expire = time() + 3600 * 24 * 365; //Temps d'expiration des cookies (1 an).
                        setcookie('pseudo', $pseudo, $expire, null, null, false, true); // On écrit un cookie
                        setcookie('pass', $pass, $expire, null, null, false, true);
                    }

                    $_SESSION['pseudo'] = stripslashes($donnees['pseudo']); // On enl?ve juste les anti-slashes () automatiques
                    $_SESSION['rang'] = $donnees['rang'];
                    $_SESSION['id'] = $donnees['id'];
                    $_SESSION['tchat'] = $donnees['tchat'];

                    $nb_tchat = 0;
                    if(droit_dacces(10)){
                        $retour = $BDD->query("SELECT DISTINCT id_membre FROM tchat");
                        while($donnees = mysql_fetch_array($retour)){

                            $retour2 = $BDD->query("SELECT  membres.rang, tchat.ignorer, tchat.id_membre
                                                    FROM tchat
                                                    INNER JOIN membres
                                                    ON(tchat.id_auteur = membres.id)
                                                    AND tchat.id_membre = '".$donnees['id_membre']."' 
                                                    ORDER BY tchat.id DESC
                                                    LIMIT 1") OR DIE (mysql_error());
                            while($don = mysql_fetch_array($retour2))
                                if($don['rang']  != 10 AND $don['ignorer'] != 1)
                                    $nb_tchat++;
                        }
                    } else {
                        $req_tchat = $BDD->query("SELECT id
                                                  FROM tchat
                                                  WHERE lu = '0'
                                                    AND ignorer = '0'
                                                    AND id_membre = '".$_SESSION['id']."'");
                        $nb_tchat = mysql_num_rows($req_tchat);
                    }
                    $tchat_phrase = pluralize($nb_tchat, '{Pas de|Un|{#}} nouveau{x} message{s}');

                    $url_avatar = './images/avatars/'.$_SESSION['id'].'.png';
                    $_SESSION['avatar'] = (is_file($url_avatar)) ? $url_avatar : DESIGN_DIR.'images/no_avatar.jpg';
                    $time = time();

                    $BDD->query('UPDATE membres SET time_derniere_visite="' . $time . '" WHERE pseudo="' . $pseudo.'" ');
                } else {
                    $r['message'] = "Le mot de passe ne correspond pas";
                }
            } else {
                $r['message'] = "Pseudo inexistant";
            }
        } else {
            $r['message'] = "Mot de passe vide";
        }
    } else {
        $r['message'] = "Pseudo vide";
    }

    if($r['erreur_login'] == 'ok' && $r['erreur_pass'] == 'ok'){
        $r['logbox'] = '
            <img id="avatarLogbox" class="fl" src="'.$_SESSION['avatar'].'" alt="Votre avatar" />
            <span id="pseudoLogbox">'.$pseudo.'</span>
            <a href="'.ROOT.'tchat.html" id="lienTchat">Tchat Admins<span>'.$tchat_phrase.'</span></a>';

        if($_POST['json'] != 'cookies'){
            ob_start();
            include('./inc/barre_bas.php');
            $r['barre_bas'] = ob_get_contents();
            ob_end_clean();
        }
    }
    if($_POST['json'] != 'cookies'){
        echo json_encode($r);
        exit();
    }
} elseif(isset($_POST['pseudo'])){
    if(isset($_POST['pseudo']) && !empty($_POST['pseudo'])){  // S'il a posté un login et qu'il n'est pas vide
        if(isset($_POST['pass']) && !empty($_POST['pass'])){ // S'il a posté un mot de pass et qu'il n'est pas vide

        $pseudo = mysql_real_escape_string(htmlentities($_POST['pseudo'],ENT_QUOTES)); //on sécurise

        $password = $_POST['pass']; //on sécurise
        $pass1 = md5($password);
        $pass2 = sha1($password);
        $pass = $pass1.$pass2;

        $nombrepseudo = mysql_result($BDD->query("SELECT COUNT(*) FROM membres WHERE pseudo = '".$pseudo."'"), 0);

            if($nombrepseudo == 1){  //Le pseudo existe
                $r['erreur_login'] = 'ok';
                $requete = $BDD->query('SELECT id, mdp, pseudo, rang, tchat FROM membres WHERE pseudo = "'.$pseudo.'" ');
                $donnees = mysql_fetch_assoc($requete);

                if($donnees['mdp'] == $pass){ //Accès ok, on peut se connecter
                    $r['erreur_pass'] = 'ok';

                    if(isset($_POST['connexion_auto']) && $_POST['connexion_auto'] == 'on'){
                        $expire = time() + 3600 * 24 * 365; //Temps d'expiration des cookies (1 an).
                        setcookie('pseudo', $pseudo, $expire, null, null, false, true); // On écrit un cookie
                        setcookie('pass', $pass, $expire, null, null, false, true);
                    }

                    $_SESSION['pseudo'] = stripslashes($donnees['pseudo']); // On enl?ve juste les anti-slashes () automatiques
                    $_SESSION['rang'] = $donnees['rang'];
                    $_SESSION['id'] = $donnees['id'];
                    $_SESSION['tchat'] = $donnees['tchat'];


                    $_SESSION['message']['info'] = "Bienvenue ".$_SESSION['pseudo']." !";
                    header('Location: ./');
                    exit();
                } else {
                    $errors['pass'] = "Mauvais mot de passe";
                }
            } else {
                $errors['pseudo'] = "Ce pseudo n'existe pas";
            }
        } else {
            $errors['pass'] = "Ce champ est vide";
        }
    } else {
        $errors['pseudo'] = "Ce champ est vide";
    }
}




if($co_form){

$titre = 'Connexion';

require_once('./classes/form.php');
include('./inc/head.php');
?>

<div id="arianne">
<a href="<?php echo ROOT; ?>">Connexion</a>
</div>

<br/>
<h1>Connexion</h1>

<div class="news">
    <div class="news_gauche">
        <div class="news_droite">
            <span class="news_titre">Connexion</span>

            <div id="inscri_form" class="news_texte">
                <?php
                $connexion = new form('connexion');
                $connexion->setErrors($errors);
                if(isset($_POST)){
                    $connexion->setValues($_POST);
                }

                $connexion->start('post', './connexion.html');
                    $connexion->input('pseudo', 'Pseudo', 'text');  br();
                    $connexion->input('pass', 'Mot de passe', 'password');  br(2);

                    $connexion->submit("Me connecter !");
                $connexion->end();
                ?>
            </div>
        </div>
    </div>
</div>

<?php
include('./inc/pied.php');
include('./inc/barre_bas.php');
include('./inc/end.php');

}
?>
