<?php session_cache_limiter('private');    
session_regenerate_id();  
session_start();
header ('Content-type: image/png');
$largeur = 280;
$hauteur = 60;

$polices = array('polices/trebuc.ttf', 'polices/tahomabd.ttf', 'polices/verdanab.ttf');

$image = imagecreatetruecolor($largeur, $hauteur);
$fond  = imagecolorallocate($image, 217, 217, 217);
imagefill($image, 0, 0, $fond);


$hauteurs = array(
	0 => mt_rand(0, $hauteur),
	1 => mt_rand(0, $hauteur),
	2 => mt_rand(0, $hauteur)
);
$largeurs = array(
	0 => mt_rand(0, $largeur),
	1 => mt_rand(0, $largeur),
	2 => mt_rand(0, $largeur),
	3 => mt_rand(0, $hauteur)
);

imagesetthickness($image, 1);

$nb_caracteres = 6;
$lettres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
$lettres_melange = strlen($lettres);
$code = '';

$x = mt_rand(2, 20);
$y = ($hauteur / 2) + mt_rand(0, 10);
$array_images = array();

$j=0;
while($j < 35) //lignes de fond
{
	$couleur1 = mt_rand(0, 250);
	$couleur2 = mt_rand(0, 250);
	$couleur3 = mt_rand(0, 250);
	$couleur_ligne = imagecolorallocate($image, $couleur1, $couleur2, $couleur3);
	imageline($image, mt_rand(0, $largeur), mt_rand(0, $hauteur), mt_rand(0, $largeur), mt_rand(0, $hauteur), $couleur_ligne);
	$j++;
}

$i = 0;
while($i < $nb_caracteres) //lettres
{
	$array_image[$i] = $image;
	$lettre_a_ajouter = $lettres[mt_rand(0, $lettres_melange - 1)];
	$taille = mt_rand(25,35);
	$angle = mt_rand(-30, 25);
	
	$code .=  $lettre_a_ajouter;
	
	$couleur1 = mt_rand(0, 250);
	$couleur2 = mt_rand(0, 250);
	$couleur3 = mt_rand(0, 250);
	$couleur_lettre = imagecolorallocate($image, $couleur1, $couleur2, $couleur3);

	imagettftext($image, $taille, $angle, $x, $y, $couleur_lettre, realpath($polices[array_rand($polices)]), $lettre_a_ajouter);
	
	
	
	$x += $taille + mt_rand(2, 20);
	$y = ($hauteur / 2) + mt_rand(10, 15);
	
	$i++;
}

$_SESSION['code'] = $code;
imagepng($image);
?>