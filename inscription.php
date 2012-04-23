<?php
require_once('./inc/core.php');

if(isset($_SESSION['pseudo'])){
    header('Location: ./');
    exit();
}

$form_visible = true;
$errors = array();

if(isset($_POST['pseudo'])){
    $form_visible = false;
    
    if(!empty($_POST['pseudo'])){
        if(strlen($_POST['pseudo']) > 3 && strlen($_POST['pseudo']) < 20){
            $pseudo = mysql_real_escape_string(htmlentities($_POST['pseudo'], ENT_QUOTES));
            $nombrepseudo = mysql_result($BDD->query("SELECT COUNT(*) FROM membres WHERE pseudo = '".$pseudo."'"), 0);

            if($nombrepseudo == 0){
                if(!empty($_POST['mdp'])){
                    if(strlen($_POST['mdp']) > 8 && strlen($_POST['mdp']) < 45){
                        if($_POST['mdp'] == $_POST['mdp2']){
                            if(!empty($_POST['email'])){
                                $mail = mysql_real_escape_string($_POST['email']);
                                if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $mail)){
                                    $nombremail = mysql_result($BDD->query("SELECT COUNT(*) FROM membres WHERE mail = '".$mail."'"), 0);

                                    if($nombremail == 0){
                                        if($_POST['email'] == $_POST['email2']){

                                            // Inscription possible
                                            $pass1 = md5($_POST['mdp']);
                                            $pass2 = sha1($_POST['mdp']);
                                            $pass = $pass1.$pass2;
                                            $rid = mysql_real_escape_string($_POST['rid']);
                                            $BDD->query("INSERT INTO membres (pseudo, rid, mdp, mail, rang, time_inscrit, time_derniere_visite)
                                                         VALUES ('".$pseudo."', '".$rid."', '".$pass."', '".$mail."', '1', '".time()."', '".time()."')");

                                        } else {
                                            $errors['email2'] = "Confirmation différente de l'e-mail";
                                        }
                                    } else {
                                        $errors['email'] = "Cet email est déjà associé à un compte";
                                    }
                                } else {
                                    $errors['email'] = "E-Mail invalide";
                                }
                            } else {
                                $errors['email'] = "Ce champ est vide";
                            }
                        } else {
                            $errors['mdp'] = "Confirmation différente du mot de passe";
                        }
                    } else {
                        $errors['mdp'] = "La longeur du mot de passe doit être comprise entre 8 et 45 caractères";
                    }
                } else {
                    $errors['mdp'] = "Ce champ est vide";
                }
            } else {
                $errors['pseudo'] = "Ce pseudo est déjà pris";
            }
        } else {
            $errors['pseudo'] = "La longeur du pseudo doit être comprise entre 3 et 20 caractères";
        }
    } else {
        $errors['pseudo'] = "Le champ est vide";
    }

    if(count($errors) > 0){
        $form_visible = true;
    } else {
        $_SESSION['message']['info'] = "Inscription terminée";
        header('Location: ./');
        exit();
    }
}


if(!$form_visible) exit();


$titre = "Inscription";
require_once('./classes/form.php');
include('./inc/head.php');
?>
<div id="arianne">
    <a href="<?php echo ROOT; ?>">Inscription</a>
</div>

<br/>
<h1>Inscription</h1>

<div class="news">
    <div class="news_gauche">
        <div class="news_droite">
            <span class="news_titre">Créer un compte</span>

            <div id="inscri_form" class="news_texte">
                <?php
                $inscription = new form('inscription');
                $inscription->setErrors($errors);
                if(isset($_POST)){
                    $inscription->setValues($_POST);
                }

                $inscription->start('post', './inscription.html');
                    $inscription->input('pseudo', 'Pseudo', 'text');  br(2);

                    $inscription->input('mdp', 'Mot de passe', 'password');  br();
                    $inscription->input('mdp2', 'Confirmation mot de passe', 'password');  br(2);

                    $inscription->input('rid', 'ID sur le <acronym title="Site du Zéro">SdZ</acronym>', 'text');  br(2);

                    $inscription->input('email', 'Adresse E-Mail', 'text');  br();
                    $inscription->input('email2', 'Confirmation e-mail', 'text');  br(2);

                    $inscription->submit("M'inscrire !");
                $inscription->end();
                ?>
            </div>
        </div>
    </div>
</div>

<?php
include('./inc/pied.php');
include('./inc/barre_bas.php');
include('./inc/end.php');
?>
