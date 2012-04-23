/*
 * Copyright (c) 2010 Fabien Calluaud  (smurf1.free.fr, tm-ladder.com)
 * Licensed under the MIT license.
 * http://smurf1.free.fr/sdz/licence.txt
*/

$(window).load(function () { init() } ); // appel de la fonction d'init en fin de chargement de page
var txtareaHeight = 200; // hauteur par défaut du textarea

function init() {
	$('.spoiler_hidden a').live('click',  function() { switch_spoiler_hidden(this); return false; } ); // click sur les secrets
	$('#btn_apercu').live('click', charge_zcode );  // Demande de chargement de l'aperçu
	$('#btn_txt_test').live('click', charge_texte_exemple ); // ajout du texte d'exemple dans le textarea
	
	//gestion de la hauteur du textarea
	$('#txtarea_hauteur_plus').live('click', function() { txtareaHeight=txtareaHeight+50; $("#zcodearea").css("height", txtareaHeight) } ); 
	$('#txtarea_hauteur_moins').live('click', function() { if(txtareaHeight>249) {txtareaHeight=txtareaHeight-50; $("#zcodearea").css("height", txtareaHeight)} } ); 
	
	// click sur un élément de la liste des sources
	$('#voir_sources a').live('click', afficher_source );
}

// Masque / Démasque les zones secret
function switch_spoiler_hidden(el) {
	$(el).parent(".spoiler_hidden").next('div').children().toggle();
} 

// Appel AJAX pour le chargement de l'aperçu
function charge_zcode() {
	$.post('zform/apercu.php', { 'zcode':$("#zcodearea").attr('value') }, function(data) { $("#apercu").html(data);  });
}


// Chargement AJAX du code source du fichier sélectionné
function afficher_source () {
	$("#voir_sources a").removeClass('active');
	$(this).addClass('active');
	$('#source').load("tab_source.php?file="+$(this).attr('id'));
	return false;
}

// Insertion d'une balise simple dans le textarea
function balise(a,b) {
	$('#zcodearea').wrapSelection(a,b);
}

// Insertion d'une liste dans le textarea
function add_liste() {
	var c="";
	while(tmp=prompt("Saisir le contenu d'une puce (si vous voulez arrêter ici, cliquez sur annuler)")) {
		c+="<puce>"+tmp+"</puce>\n";
	}
	balise("<liste>\n"+c,"</liste>");
}

// Insertion d'une balise avec paramètre dans le textarea
function add_bal(a,b,c) {
	if($("#zcodearea").getSelection().length) {
		$('#zcodearea').wrapSelection("<"+a+" "+b+"=\""+$("#"+c).val()+"\">","</"+a+">");
	}	else {
		$('#zcodearea').insertAtCaretPos2("<"+a+" "+b+"=\""+$("#"+c).val()+"\"></"+a+">");
	}
	$("#"+c+" .opt_titre").attr("selected","selected");
}

// Insertion d'une balise de type citation, lien, email dans le textarea (demande des infos complémentaires)
function add_bal2(a,b) {
	if(a=="citation") {
	var c = prompt("Veuillez renseigner l'auteur de la citation","");
	if((!c)||($.trim(c)=="")) 
		$('#zcodearea').wrapSelection("<"+a+">","</"+a+">");
	else $('#zcodearea').wrapSelection("<"+a+" "+b+"=\""+c+"\">","</"+a+">");
	}
	
	if(a=="lien") {
		if($("#zcodearea").getSelection().length) {
			var txt = $("#zcodearea").getSelection().text;
			if(txt.indexOf("http://")==0||txt.indexOf("https://")==0||txt.indexOf("ftp://")==0||txt.indexOf("apt://")==0) {
				var c = prompt("Veuillez indiquer le texte du lien","");
				$('#zcodearea').wrapSelection("<"+a+" "+b+"=\"","\">"+c+"</"+a+">");			
			}
			else {
				var c = prompt("Veuillez indiquer l'url du lien","");
				if(!(txt.indexOf("http://")==0||txt.indexOf("https://")==0||txt.indexOf("ftp://")==0||txt.indexOf("apt://")==0)) { c = "http://"+c; }
				$('#zcodearea').wrapSelection("<"+a+" "+b+"=\""+c+"\">","</"+a+">");
			}
		}
		else {
			var c = prompt("Veuillez indiquer l'url du lien","");
			var d = prompt("Veuillez indiquer le texte du lien","");
			if(!(c.indexOf("http://")==0||c.indexOf("https://")==0||c.indexOf("ftp://")==0||c.indexOf("apt://")==0)) { c = "http://"+c; }
			if($.trim(d)!="") $('#zcodearea').insertAtCaretPos2("<"+a+" "+b+"=\""+c+"\">"+d+"</"+a+">");
			else $('#zcodearea').insertAtCaretPos2("<"+a+" "+b+"=\""+c+"\">"+c+"</"+a+">");
		}
	}
	else if(a=="email") {
		if($("#zcodearea").getSelection().length) {
			var patternMail = /^[0-9a-zA-Z\._-]+@[0-9a-zA-Z\._-]+\.[0-9a-zA-Z]+$/
			var txt = $("#zcodearea").getSelection().text;
			if(txt.match(patternMail)) {
				var c = prompt("Veuillez indiquer le nom (facultatif)","");
				if($.trim(c)!="") { $('#zcodearea').wrapSelection("<"+a+" "+b+"=\"","\">"+c+"</"+a+">");	}
				else { $('#zcodearea').wrapSelection("<"+a+">","</"+a+">"); }
			}
			else {
			var c = prompt("Veuillez indiquer l'email","");
			if($.trim(c)!="") { $('#zcodearea').wrapSelection("<"+a+" "+b+"=\""+c+"\">","</"+a+">");	}
			}
		}
		else {
			var c = prompt("Veuillez indiquer l'email","");
			var d = prompt("Veuillez indiquer le nom (facultatif)","");
			if($.trim(d)=="") $("#zcodearea").insertAtCaretPos("<"+a+">"+c+"</"+a+">");
			else $("#zcodearea").insertAtCaretPos2("<"+a+" "+b+"=\""+c+"\">"+d+"</"+a+">");
		}
		
	}
}

// Switch sur les smilies
function toggle_smilies() {
	$('#tb_smile1').toggle();
	$('#tb_smile2').toggle();
	$('#btn_toggle_smilies_1').toggle();
	$('#btn_toggle_smilies_2').toggle();
}