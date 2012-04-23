<?php
/*
if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['nb_php_auth'])) $_SESSION['nb_php_auth'] = 0;
if(!isset($_SESSION['see_php_auth'])) $_SESSION['see_php_auth'] = false;

function protection($username, $password) {
    if (!isset($_SERVER['PHP_AUTH_USER'])
    || $username != $_SERVER['PHP_AUTH_USER']
    || $password != $_SERVER['PHP_AUTH_PW']) {
        if($_SESSION['nb_php_auth'] < 3){
            header('WWW-Authenticate: Basic realm="Private"');
            header('HTTP/1.0 401 Unauthorized', false);
            $_SESSION['nb_php_auth']++;
            exit();
        } else {
            header('Location: http://zdesigns.fr/v2/');
            exit();
        }
        return false;
    }
    return true;
}

if (protection('zdesigns', 'bientotv2') && !$_SESSION['see_php_auth']){
    $_SESSION['message']['info'] = "Accès Autorisé";
    $_SESSION['see_php_auth'] = true;
}
 * 
 */
?>
