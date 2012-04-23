<?php
require_once('./inc/core.php');
require_once('./inc/functions.php');

if(isset($_POST['message'])){
    $message = mysql_real_escape_string(htmlspecialchars(stripslashes($_POST['message'])));
    $sess_id = '6';
    
    if(isset($_SESSION['pseudo'])){
        $pseudo = '';
        $sess_id = $_SESSION['id'];
    } elseif(isset($_POST['pseudo'])) {
        $pseudo = mysql_real_escape_string(htmlspecialchars(stripslashes($_POST['pseudo'])));
    } else {
        header('Location: ./livredor.html');
        exit();
    }

    // Appel du webservice Askimet avec les champs de mon formulaire de commentaire.
    require_once('./classes/akismet.php');

    $akismet = new Akismet(ROOT_ABS, AKISMET_KEY);
    $akismet->setCommentAuthor($pseudo); // nom de l'auteur
    $akismet->setCommentContent($message); // texte du commentaire
    $akismet->setPermalink(ROOT.'livredor.php'); // URL de l'article

    if($akismet->isCommentSpam()) {
        $visible = 0;
        $_SESSION['message']['alert'] = "Votre message est du SPAM";
    } else {
        $visible = 1;
        $_SESSION['message']['info'] = "Merci d'avoir laissé votre griffe !";
    }

    $BDD->query("INSERT INTO livreor (id_membre, pseudo, message, date, visible)
                 VALUES ('".$sess_id."', '".$pseudo."', '".$message."', '".time()."', '".$visible."')");
    unset($_POST);

    header('Location: ./livredor.html');
    exit();
}

if(isset($_GET['action']) && droit_dacces(10)){
    $idm = (int) $_GET['id_com'];
    switch($_GET['action']){
        case 'supprimer':
            $BDD->query("UPDATE livreor
                         SET visible = '0'
                         WHERE id = '".$_GET['id_com']."'");
            $_SESSION['message']['info'] = 'Commentaire supprimé';
            header('Location: '.ROOT.'livredor.html');
            exit();
            break;

        case 'editer':
            $_SESSION['message']['alert'] = 'Fonction indisponible';
            header('Location: '.ROOT.'livredor.html');
            exit();
            break;
    }
}

$titre = 'Livre d\'Or';
include('./inc/head.php');
?>
<div id="arianne">
    <a href="<?php echo ROOT; ?>livredor.php">Livre d'Or</a>
</div>
<br/>
<h1>Le Livre d'Or</h1>
<div class="coms_in">
    <div class="form_com">
        <?php
        if(isset($_SESSION['pseudo'])){
            $pseudo = $_SESSION['pseudo'].'<input type="hidden" name="pseudo" value="'.$_SESSION['pseudo'].'" />';
        } else {
            $pseudo = '<label for="pseudo_livredor">Pseudo : </label><input type="text" name="pseudo" id="pseudo_livredor" value="" />';
        }
        $rang = (isset($_SESSION['rang'])) ? $_SESSION['rang'] : 0;
        $rang = $rangs[$rang];
        
        $droite = (droit_dacces(10)) ? ' droite' : '';
        ?>
        <div class="com<?php echo $droite; ?>">
            <form action="./livredor.html" method="post">
                <span class="auteur"><?php echo $pseudo; ?><span class="rang"><?php echo $rang; ?></span></span>
                <div class="com_content">
                    <label for="message_com">Message : </label>
                    <textarea name="message" id="message_com" cols="50" rows="10"></textarea><br />
                    <input type="submit" value="Poster" />
                </div>
            </form>
        </div>
        <hr class="clear" />
        <br/><br/>
    </div>
    <?php
    $coms = $BDD->query("SELECT livreor.id AS id, livreor.id_membre AS idm, livreor.pseudo AS com_pseudo,
                                livreor.message AS com, livreor.date AS date,
                                membres.pseudo AS membre_pseudo, membres.id AS idm, membres.rang AS membre_rang
                         FROM livreor
                         INNER JOIN membres
                            ON membres.id = livreor.id_membre
                         WHERE livreor.visible = '1'
                         ORDER BY livreor.date DESC");
    
    while($com = mysql_fetch_array($coms)){
        if($com['membre_pseudo'] == 'membre'){
            $pseudo = ($com['com_pseudo'] != '') ? $com['com_pseudo'] : 'Anonyme';
            $rang = $rangs[0];
            $droite = '';
        } else {
            $pseudo = $com['membre_pseudo'];
            $rang = $com['membre_rang'];
            $droite = ((int)$rang == 10) ? ' droite' : '';
            $rang = $rangs[$rang];
        }
        ?>
        <div class="com<?php echo $droite; ?>">
            <span class="auteur"><?php echo $pseudo; ?><span class="rang"><?php echo $rang; ?></span></span>
            <p class="commentaire">
                <?php echo nl2br($com['com']); ?>
            </p>
            <span class="date">
                <?php if(droit_dacces(10)){ ?>
                <span class="actions_com">
                    <a href="<?php echo ROOT.'livredor-m'.$com['id'].'-editer.html'; ?>">Editer</a> |
                    <a href="<?php echo ROOT.'livredor-m'.$com['id'].'-supprimer.html'; ?>">Supprimer</a> |
                </span>
                <?php } ?>
                Posté <?php echo parse_date($com['date'], false, 'le'); ?>
            </span>
        </div>
        <hr class="clear" />
        <?php
    }
    ?>
</div>
<?php
include('./inc/pied.php');
include('./inc/barre_bas.php');
include('./inc/end.php');
?>