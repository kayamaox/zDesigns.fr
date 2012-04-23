<?php
/*
 * Copyright (c) 2010 Fabien Calluaud  (smurf1.free.fr, tm-ladder.com)
 * Licensed under the MIT license.
 * http://smurf1.free.fr/sdz/licence.txt
*/

class unzcode {
	private $html;
	private $chemin;
	private $result;

	public function __construct($code='') {
		if($code!='') $this->load($code);
	}
	
	public function load($code) {
		$this->html = $code;
	}
	
	public function chemin($chemin) {
		$this->chemin = $chemin;
	}
	
	private function preParse() {
		$this->html = str_replace('&nbsp;', '&#160;', $this->html);
		
		$pattern = '#(<a href="([^"]*)">[^<]*)?<span class="citation">citation( : )?([^<]*)</span>[^<]*(</a>)?[^<]*<div class="citation2">#is';
		$this->html = preg_replace($pattern, '<div class="citation2" auteur="$4" lien="$2" >', $this->html);

		$pattern = '#(<a href="([^"]*)">[^<]*)?<span class="code">Code : ([^- ]*)( - )?([^<]*)</span>[^<]*(</a>)?[^<]*<div class="([^"]*)">#is';
		$this->html = preg_replace($pattern, '<div class="$7" type="$3" titre="$5" lien="$2" >', $this->html);
		
		$this->html = "<root>".$this->html."</root>";
	}
	
	private function postParse() {
		$this->result = str_replace(array("&lt;","&gt;"), array("<",">"), $this->result );
	}
	
	private function transform() {
		$xml = new domDocument();
		$xml -> loadXML($this->html);
		
		$xsl = new domDocument();
		$xsl -> load($this->chemin.'unzcode.xsl');
		
		$xslt = new XSLTProcessor();
		$xslt -> importStylesheet($xsl);
		
		$this->result = $xslt -> transformToXml($xml);
	}
	
	public function parse() {
		$this->preParse();
		$this->transform();
		$this->postParse();
		return $this->result;
	}
	
	
}