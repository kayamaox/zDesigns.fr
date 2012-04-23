<?php
session_start();
session_destroy();
session_start();

if(isset($_COOKIE['pseudo'])){
    setcookie("pseudo", false, time() - 3600);
    setcookie("pass", false, time() - 3600);
    unset($_COOKIE["pseudo"]);
    unset($_COOKIE["pass"]);
}

$_SESSION['message']['info'] = "Vous êtes déconnecté";
header('Location: ./');
?>