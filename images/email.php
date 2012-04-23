<?php session_start();
header ('Content-type: image/png');

$longueur = 8*strlen($_SESSION['mail_image']) + 11;
$image = imagecreate($longueur,18);

$bleu = imagecolorallocate($image, 181, 219, 236);
$noir = imagecolorallocate($image, 0, 0, 0);

imagestring($image, 4, 5, 1, $_SESSION['mail_image'], $noir);

imagepng($image);


?>