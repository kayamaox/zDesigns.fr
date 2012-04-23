<?php
require_once('./inc/core.php');
require_once('./inc/functions.php');
require_once('./classes/unzip.php');

if(isset($_SESSION['pseudo'])){
    if(isset($_POST['upload']) && isset($_FILES['fichier'])){
        $tmp_file = $_FILES['fichier']['tmp_name'];
        if(is_uploaded_file($tmp_file)){
            $extension = end(explode(".", $_FILES['fichier']['name']));
            if(strtolower($extension) == 'zip'){
                if($_FILES['fichier']['size'] < SIZE_MAX){
                    if(!is_dir('./designs/'.$_SESSION['id'].'/')){
                        mkdir('./designs/'.$_SESSION['id'], 0777);
                    }
                    $name_file = str_replace('.zip', '', $_FILES['fichier']['name']);
                    $name_file = str_replace('zip', '', $name_file);
                    $BDD->query("INSERT INTO designs (id_membre, titre, active, vu, date, complet, note)
                                               VALUES('".$_SESSION['id']."', '".$name_file."', '0', '0', '".time()."', '0', '0')");
                    $idd = mysql_insert_id();
                    $name = $idd.'.zip';

                    if(move_uploaded_file($tmp_file, './designs/'.$_SESSION['id'].'/'.$name)){
                        @mkdir('./designs/'.$_SESSION['id'].'/'.$idd, 0777);
                        @mkdir('./designs/'.$_SESSION['id'].'/'.$idd.'/css', 0777);
                        @mkdir('./designs/'.$_SESSION['id'].'/'.$idd.'/images', 0777);
                        @unzip('./designs/'.$_SESSION['id'].'/'.$idd.'/', './designs/'.$_SESSION['id'].'/'.$name, true);
                        if(!droit_dacces(10)){
                            $message = "Salut Cyril !\n\r
                                       ".$_SESSION['pseudo']." vient d'uploader un pack_design s'appelant ".$name_file."\n\r
                                       Voici son lien :\n\r
                                       http://www.zdesigns.fr/mes_zdesigns-".$idd.".html";
                            mail("heilmann.cyril@free.fr", "Nouveau zDesign !", $message);
                        }
                        $_SESSION['message']['info'] = "Voici votre nouveau design";
                        header('Location: ./mes_zdesigns-'.$idd.'.html');
                        exit();
                    } else {
                        $_SESSION['message']['alert'] = "Le zip n'a pu être copier";
                    }
                } else {
                    $_SESSION['message']['alert'] = "Le zip est trop lourd";
                }
            } else {
                $_SESSION['message']['alert'] = "Ce fichier n'est pas un zip";
            }
        } else {
            $_SESSION['message']['alert'] = "Fichier introuvable";
        }

        if(isset($_SESSION['message']['alert'])){
            header('Location: ./zuploader.html');
            exit();
        }
    }
} else {
    $_SESSION['message']['alert'] = "Vous devez vous connecter";
    header('Location: '.ROOT);
    exit();
}


$titre = "zUploader";
include('./inc/head.php');
?>

<div id="arianne">
    <a href="<?php echo ROOT; ?>mes_zdesigns.html">Mes zDesigns</a> > <a href="<?php echo ROOT; ?>zuploader.html">zUploader</a>
</div>
<h1>zUploader</h1>
<br/><br/>
<form action="" enctype="multipart/form-data" method="post" id="zuploader_form">
    <img src="<?php echo DESIGN_DIR ?>images/zupload.jpg" alt="zUploader" />
    <p>
        Le zUploader accepte uniquement les archive zippées (*.zip) et de <?php echo SIZE_MAX/1024;?> Ko maximum.<br /><br />
        <label for="fichier_zupload">Sélectionnez le fichier : </label>
        <input id="fichier_zupload" type="file" size="50" name="fichier" /><br/><br />
        <input name="upload" type="submit" value="Envoyer !" />
    </p>
</form>

<?php
include('./inc/pied.php');
include('./inc/barre_bas.php');
include('./inc/end.php');
?>