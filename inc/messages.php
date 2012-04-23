<?php
if(isset($messageError) || isset($_SESSION['message']['error'])) {$type="error";}
elseif(isset($messageInfo) || isset($_SESSION['message']['info'])) {$type="info";}
elseif(isset($messageAlert) || isset($_SESSION['message']['alert'])) {$type="alert";}
else{$type="";}

if($type != ""){
    echo "<div class='".$type."'>";
    if(isset(${'message'.ucfirst($type)})) {
        $message = ${'message'.ucfirst($type)};
        echo $message;
    } else {
        $message = $_SESSION['message'][$type];
        echo $message;
        unset($_SESSION['message'][$type]);
    }
    echo "</div>";

    require_once('./inc/functions.php');
    $pseudo = (isset($_SESSION['pseudo'])) ? $_SESSION['pseudo'] : 'Visiteur';
    @$log->addData('[Message d\''.$type.' > '.$pseudo.'] '.removeAccents($message), 'none', true);
} else {
    echo '<div class="dn"></div>';
}
?>