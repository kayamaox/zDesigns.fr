<?php session_start();
include_once 'class.zcode.php';
include_once 'class.unzcode.php';
include_once 'geshi/geshi.php';

$code = $_POST['zcode'];

$zcode = new zcode;
$zcode->load($code);

$codeP = $zcode->parse();

if($codeP) {
	$unzcode = new unzcode;
	$unzcode->load($codeP);
	$codePU = $unzcode->parse();

	echo '<h3>Aperçu final : </h3><div class="apercu_code">'.$codeP.'</div>';
	//echo '<h3 style="margin-top:50px">Code dé-parsé</h3>';
	//echo '<div class="apercu_code"><pre style="font-size:12px; width:100%; overflow:auto;">';
	//echo htmlentities($codePU, ENT_QUOTES, "UTF-8");
	//echo '</pre></div>';
}
else {
	echo '<div class="apercu_code">'.$zcode->displayError().'</div>';
}