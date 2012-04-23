<?php
/*
 * Copyright (c) 2010 Fabien Calluaud  (smurf1.free.fr, tm-ladder.com)
 * Licensed under the MIT license.
 * http://smurf1.free.fr/sdz/licence.txt
*/

class zcode {

	private $zcode;
	private $chemin;
	private $result;
	private $erreur = array();
	
	# balises autorisées
	private $authorized = array(
		'titre1','titre2', 
		'tableau', 'ligne', 'entete', 'cellule', 'legende',
		'citation', 
		'liste', 'puce', 
		'taille', 
		'gras', 'italique', 'souligne', 'barre', 
		'indice', 'exposant',
		'image',
		'information', 'attention', 'erreur', 'question', 
		'flottant', 'position', 
		'code_contener', 
		'lien', 'email',
		'couleur', 
		'secret',
		'police',
		'acronyme',
		'br'
	);

	private $smilies = array(
		':)' => 'smile.png',
		':D' => 'heureux.png',
		';)' => 'clin.png',
		':pirate:' => 'pirate.png',
		':p' => 'langue.png',
		':lol:' => 'rire.gif',
		':euh:' => 'unsure.gif',
		':(' => 'triste.png',
		':o' => 'huh.png',
		':colere2:' => 'mechant.png',
		'o_O' => 'blink.gif',
		'^^' => 'hihi.png',
		':-°' => 'siffle.png',
		':ange:' => 'ange.png',
		':colere:' => 'angry.gif',
		':diable:' => 'diable.png',
		':magicien:' => 'magicien.png',
		':ninja:' => 'ninja.png',
		'>_<' => 'pinch.png',
		':\'(' => 'pleure.png',
		':honte:' => 'rouge.png',
		':soleil:' => 'soleil.png',
		':waw:' => 'waw.png',
		':zorro:' => 'zorro.png'
	);
	
	const BALISE_CODE = "code";
	const REMPLACEMENT_CODE = "code_contener";
	

	public function __construct($code='') {
		if($code!='') $this->load($code);
		$this->chemin = '';
	}
	
	public function load($code) {
		$this->zcode = $code;
	}
	
	public function chemin($chemin) {
		$this->chemin = $chemin;
	}
	
	private function prepare_parse() {
		$this->zcode = stripslashes($this->zcode);
		
	
		$this->zcode = str_replace("&","&amp;",$this->zcode);
		foreach($this->smilies as $key=>$smile) {
			$this->zcode = str_replace($key, '&_lt;smile image="'.$smile.'" &_gt;'.$key.'&_lt;/smile&_gt;', $this->zcode);
		}
		$this->preparse_balises_code();
		foreach($this->authorized as $i=>$a) {
			$this->authorized[$i] = '#<([\/]?'.$a.')( )*([^>]*)?>#i';
		}
				
		//Passage en minuscule des balises autorisées et de leurs nom d'attributs (en soit, ça ne sert pas à grand chose sauf à faire une petite correction d'erreur de XML)
		$this->zcode = preg_replace_callback($this->authorized, array(&$this, "balises_tolower"), $this->zcode); // <- passe les balises autorisées en minuscule et changes les < et > en &_lt; et &_gt;
		$this->zcode = preg_replace_callback('#&_lt;([a-zA-Z0-9]+) (.*)&_gt;#Us', array(&$this, "traite_attr"), $this->zcode); // <- mets les nom d'attributs des balises autorisées en minuscule


		$this->zcode = str_replace(array('<','>'), array('&lt;', '&gt;'), $this->zcode); // <- conversion de tous les < et > restants (balises non autorisées)
		$this->zcode = str_replace(array('&_lt;','&_gt;'), array('<', '>'), $this->zcode); // <- on remet les balises autorisées avec leurs < et >
		
		$this->zcode = nl2br($this->zcode);

		$this->zcode = '<?xml version="1.0" encoding="UTF-8"?><zcode>'.$this->zcode.'</zcode>';
	
	}
	
	private function preparse_balises_code() {
		$pattern = "#<".self::BALISE_CODE."([^>]*)>(.*)<\/".self::BALISE_CODE.">#is";
		$this->zcode = preg_replace_callback($pattern, array(&$this, "traiter_balises_code"), $this->zcode);


		$pattern = "#<".self::REMPLACEMENT_CODE."([^>]*)>(.*)</".self::REMPLACEMENT_CODE.">#isU";
		$this->zcode = preg_replace_callback($pattern, array(&$this, "traiter_entities_codes"), $this->zcode);
	}
	
	private function traiter_balises_code($m) {
		//Assertion arrière négative
		$pattern = "#<\/".self::BALISE_CODE.">((.(?<!<\/".self::BALISE_CODE."))*)<".self::BALISE_CODE."([^>]*)>#isU";
		$content = preg_replace($pattern, "</".self::REMPLACEMENT_CODE.">\\1<".self::REMPLACEMENT_CODE."\\3>", $m[2]);
		return "<".self::REMPLACEMENT_CODE."{$m[1]}>{$content}</".self::REMPLACEMENT_CODE.">";
	}

	private function traiter_entities_codes($m) {
		$content = str_replace(array('<', '>'), array('&lt;','&gt;'), $m[2]);
		return "<".self::REMPLACEMENT_CODE."{$m[1]}>{$content}</".self::REMPLACEMENT_CODE.">";
	}
	
	// Fonction de passage des noms de balise en minuscule et protection des < et >
	private function balises_tolower($matches) {
		return '&_lt;' . strtolower( $matches[1] ) . stripslashes( $matches[2] . $matches[3] ) . '&_gt;';
	}

	// Fonction de récupération des noms d'attributs de balises
	private function traite_attr($matches) {
		$attrs = $matches[2];
		$attrs = preg_replace_callback("#([^ =]*)=#", array(&$this, "attr_tolower"), $attrs);
		return '&_lt;'.$matches[1].' '.$attrs.'&_gt;';
	}

	// Fonction de mise en minuscule des nom d'attributs des balises
	private function attr_tolower( $matches ) {
		return( strtolower( $matches[1] ) )."=";
	}
	
	private function post_parse() {
		//Attention: expression peut être à changer (contactez moi si vous trouvez des url non détectées)
		$this->result = preg_replace('#([^"])((https?|ftp)://([a-zA-Z0-9\._\-/@:]+)(\?[a-zA-Z0-9\&amp;%_\./~\-=]+)?)#', '$1<a href="$2">$2</a>', $this->result );
	}
	


	private function transform() {
		$xslt = new XSLTProcessor();
		$xslt->registerPhpFunctions();

		libxml_use_internal_errors(true);
		$xml = new domDocument();
		if(!$xml -> loadXML($this->zcode)) {
			foreach (libxml_get_errors() as $error) {
				$this->erreur[] = array("level"=>$error->level, "code"=>$error->code, "column"=>$error->column , "message"=>$error->message, "file"=>$error->file, "line"=>$error->line );
			}
			libxml_clear_errors();
			return false;
		}

		$xsl = new domDocument();
		$xsl -> load($this->chemin.'zcode.xsl');

		$xslt -> importStylesheet($xsl);

		$this->result = $xslt -> transformToXml($xml);
		return true;
	}
	
	public static function geshi_code($code, $langage, $debut, $surligne) {
		$code = trim($code,"\n\r");
		$geshi = new GeSHi($code, $langage);
		$geshi->set_header_type( GESHI_HEADER_PRE_TABLE );
		$geshi->enable_line_numbers( GESHI_NORMAL_LINE_NUMBERS );
		
		if($debut) $geshi->start_line_numbers_at($debut);
		if($surligne) {
			$tLignes = explode(',',$surligne);
			foreach($tLignes as $i=>$t) { $tLignes[$i] = $t-$debut+1; }
			
			$geshi->highlight_lines_extra($tLignes);
		}

		return $geshi->parse_code();
	}
	
	public function displayError() {
		$return = '<h4>Il y a des erreurs dans le zCode : </h4><br/>';
		$return .= '<table class="tab_user" style="width:100%;"><tr><th>Niveau</th><th>Code</th><th>Colonne</th><th>Message</th><th>Fichier</th><th>Ligne</th></tr>';
		foreach($this->erreur as $e) {
			$return .= "<tr><td>{$e['level']}</td><td>{$e['code']}</td><td>{$e['column']}</td><td>{$e['message']}</td><td>{$e['file']}</td><td>{$e['line']}</td></tr>";
		}
		$return .= "</table>";
		return $return;
	}
	
	
	
	public function parse() {
		if(!empty($this->zcode)) 
		{
			$this->prepare_parse();
			if(!$this->transform()) {
				return false;
			} else {
				$this->post_parse();
				return $this->result;
			}
		}
		else
			return false;
	}
}