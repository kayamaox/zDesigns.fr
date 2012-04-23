<?php
header ("Content-type: image/png"); // On indique au serveur que la page jauge.php est une image.
$image = imagecreate(125,10); // Taille de l'image
 
if(isset($_GET['valeur'])) // si get['valeur'] existe alors $valeur y est égal
{
 $valeur=$_GET['valeur'];
}
else // sinon $valeur est égal a 0
{
 $valeur=0;
}
$x=($valeur*123)/100; // on calcul un pourcent du chargement ( affichage divisé par cent  sans oublier le 1 px de chaque coté retiré)
 
$vide=imagecolorallocate($image, 255, 255, 255); //arierre plan
$ecriture=imagecolorallocate($image, 0, 0, 200); // l'ecriture
$bord=imagecolorallocate($image, 0, 0, 0); // et le bord

if($valeur <= 80) // si plus petit que 80 alors vert
{
 $interne=imagecolorallocate($image, 50, 180, 50);
} 
elseif($valeur <= 90) // si plus petit que 90 alors orange
{
 $interne=imagecolorallocate($image, 225, 251, 46);
}
elseif($valeur <=95) // sinonsi plus petit que 100 alors orange
{
 $interne=imagecolorallocate($image, 255, 127, 0);
}
else // sinon (reste plus que 100) alors rouge
{
 $interne=imagecolorallocate($image, 255, 0, 0);
}
 
imagerectangle($image, 0, 0, 124, 9, $bord);
ImageFilledRectangle($image, 1, 1, $x, 8, $interne);
imagestring($image, 1, 60, 1, $valeur." %", $ecriture); // on ecris
imagepng($image); // On définit les formes et l'affichage et enfin on affiche l'image
?>