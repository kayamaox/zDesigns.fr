<?php
session_start();
if(isset($_GET['e'])){
    $id = (int) $_GET['e'];
    switch($id){
        case 404:
            redir("Erreur 404 : Page introuvable");
            break;
    }
}
redir("Une erreur est survenue");

function redir($message){
    $_SESSION['message']['_error'] = $message;
    header('Location: http://www.zdesigns.fr/?e=true');
    exit();
}
?>
