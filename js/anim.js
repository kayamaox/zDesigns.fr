/*
*   Document    : anim
*   Créé le     : 14 11 2010, 21:03:58
*   Auteur      : Alex-D
*   Client      : http://www.zdesigns.fr
*   Description :
*       Gestion des animations générales du site
*       + Correction de la fonction jQuery animate
*         au niveau des couleurs en background
*
*   Tous droits réservés
*   Copie partielle ou totale interdite
*   Pour toute demande d'utilisation : demodealex[a]gmail[pt]com
*/

var timerMess = null;
if($('messageInfo div').attr('class') != ''){
    timerMess = setTimeout("masquerMessage()", 10000);
}
$(function(){
    // Scroll
    $('a[rel^=scroll]').click(function(){
            cible=$(this).attr('href');
            scrollGoto(cible);
            return false;
    });

    active_barre_bas();

    // Logbox
    $('#lienConnexion').click(function(){
        $('#logboxSlide').animate({
            'marginTop': '-75px'
        }, 1000, 'easeOutBounce')
        return false;
    });
    $('#retourLogbox').click(function(){
        $('#logboxSlide').animate({
            'marginTop': '0'
        }, 1000, 'easeOutBounce')
        return false;
    });
    //--> JSON formulaire connexion
    $("#login").submit(function() {
        var form = $(this);
        $('#logboxSlide').animate({opacity:0.2}, 250);
        $('#logboxIn').loader('Connexion en cours...');
        var pseudo = form.find("input[type=text]").val();
        var mdp = form.find("input[type=password]").val();
        var co_auto = form.find("input[type=checkbox]").val();
        var url = form.attr("action");
        $.post(url, {pseudo:pseudo, pass:mdp, connexion_auto: co_auto, json:'on'}, function(data) {
            if(data['erreur_login'] == "ok" && data['erreur_pass'] == "ok") {
                $("#logboxSlide").fadeOut(500, function(){
                    $("#logboxIn").empty();
                    $('#logboxIn').append(data['logbox']);

                    pseudo = $("#pseudoLogbox").text();
                    addMessage('info', 'Bienvenue '+pseudo);

                    $('body').append(data['barre_bas']);
                    $('body').removeClass('barre_bas_mobile');
                    active_barre_bas();
                });
            } else {
                addMessage('alert', data['message']);
                $('#logboxIn').unload();
                $('#logboxSlide').animate({opacity:1}, 250, "linear", function(){
                    if(data['erreur_login'] != "ok"){$("input[type=text]", form).move_alert(0);}else{$("input[type=text]", form).move_alert(1);}
                    if(data['erreur_pass'] != "ok"){$("input[type=password]", form).move_alert(0);}else{$("input[type=password]", form).move_alert(1);}
                });
            }
        }, "json");
        return false;
    });


    // Infobulles
    $('*[rel=infobulle]').mouseover(function(){
        if($(this).attr('title')=='') return false;
        $(this).infobulle();
        return false;
    });


    // Propriétés du design
    $('#proprietes_design_link').click(function(){
        if($('#proprietes_design').is(':visible')){
            $('#proprietes_design').slideUp();
            $('#proprietes_design_link').empty().append('Afficher les propriétés');
        } else {
            $('#proprietes_design').slideDown();
            $('#proprietes_design_link').empty().append('Masquer les propriétés');
        }
    });
    
    
    // Stats du design
    var stats = false;
    $('#stats_link').click(function(){
        if($('#stats').is(':visible')){
            $('#stats').slideUp();
            $('#stats_link .label').empty().append('Afficher les statistiques');
        } else {
            $('#stats').slideDown();
            $('#stats_link .label').empty().append('Masquer les statistiques');
            if(!stats){
                showStats();
                stats = true;
            }
        }
    });


    // Afficher/Masquer Rapport
    $('#rapport_link').click(function(){
        if($('#rapport').is(':visible')){
            $('#rapport').slideUp();
            $('#rapport_link .aff_mask').empty().append('Afficher');
        } else {
            $('#rapport').slideDown();
            $('#rapport_link .aff_mask').empty().append('Masquer');
        }
    });
});
function finUpload(erreur, body){
    $('#uploadEnd').empty().append(body);
    $('#uploadStatut').empty();
    $('#uploader input[type=file]').val('');
    if(erreur == 'true'){
        $('#uploadStatut').append($('#uploadEnd').find('#upload-message').text()).hide();
        $('#uploadEnd').trigger('click');
    } else {
        $('#uploadStatut').append(erreur).hide();
    }
    $('#uploadStatut').slideDown(500);
    $('#uploader').unload();
    $('#uploadFrame').remove();
    $('#uploader').append('<iframe src="" id="uploadFrame" name="uploadFrame"></iframe>');
}



(function($){
/* Permet de mettre/enlever un loader sur un élément */
$.fn.loader = function(text){
    text = (!text || text == null || text == '') ? 'Chargement...' : text;
    var $$ = $(this);
    if($('.loader', $$).size() == 0){
        $$.prepend('<div class="loader"><img src="./design/2/images/loading.gif" alt="Chargement..." />'+text+'</div>');
        $('.loader', $$).fadeTo(250, 0.8);
    }
}
$.fn.unload = function(){
    var $$ = $(this);
    $('.loader', $$).fadeOut(250, function(){
        $(this).remove();
    });
}


/* Fait un flash background */
$.fn.flashValid = function(valid, duration, col){
    duration = (duration == null) ? 200 : duration;
    var c = (valid || valid == null) ? '#eaffd7' : '#ffd7d7';
    c = (col == null) ? c : col;
    var $$ = $(this);
    var bgc = $$.css('background-color');
    $$.animate({backgroundColor: c}, duration, function(){
       $(this).animate({backgroundColor: bgc}, duration, function(){
           $(this).css('background', '');
       });
    });
}


/* Clignotement du fond */
$.fn.move_alert = function(bool){
    if(bool == 0){
        $(this).animate({"opacity": "0.4"},100,"easeInSine", function() {
            $(this).css({backgroundColor:"#ffd8d8", borderColor:"#CC0000"});
        });
        $(this).animate({"opacity": "1"},50,"easeOutSine");
        $(this).animate({"opacity": "0.4"},50,"easeInSine");
        $(this).animate({"opacity": "1"},50,"easeOutSine");
        $(this).animate({"opacity": "0.4"},50,"easeInSine");
        $(this).animate({"opacity": "1"},50,"easeOutSine");
    } else {
        $(this).css({backgroundColor:"", borderColor:""});
    }
}
})(jQuery);




// Barre Bas
function active_barre_bas(){
    $('#plus_admin').click(function(){
        var btn = $(this);
        var menu = $('#menu_plus_admin');
        menu.slideToggle(250, function(){
            btn.toggleClass('deroule');
        });
        return false;
    });
    $('#voir_designs').click(function(){
        var btn = $(this);
        var menu = $('#menu_voir_designs');
        menu.slideToggle(250, function(){
            btn.toggleClass('deroule');
        });
        return false;
    });
    $('#head, #ombre_g').click(function(){
        if($('#plus_admin').hasClass('deroule')){
            $('#menu_plus_admin').slideUp(250, function(){
                $('#plus_admin').removeClass('deroule');
            });
        }
        if($('#voir_designs').hasClass('deroule')){
            $('#menu_voir_designs').slideUp(250, function(){
                $('#voir_designs').removeClass('deroule');
            });
        }
    });
    $('#masquer_bar').click(function(){
        if($('#plus_admin').hasClass('deroule')){$('#menu_plus_admin').hide();}
        if($('#voir_designs').hasClass('deroule')){$('#menu_voir_designs').hide();}
        $('body').addClass('barre_bas_hidden');
        $('#barre_bas').slideUp(250, function(){
            $('#afficher_bar').slideDown();
        });
        $('#barre_bas_mobile').fadeIn(250);
        return false;
    });
    $('#afficher_bar').click(function(){
        $('#afficher_bar').slideUp(250, function(){
            $('#barre_bas').slideDown(500, function(){
                if($('#plus_admin').hasClass('deroule')){$('#menu_plus_admin').slideDown(250);}
                if($('#voir_designs').hasClass('deroule')){$('#menu_voir_designs').slideDown(250);}
            });
            $('body').removeClass('barre_bas_hidden');
        });
        $('#barre_bas_mobile').fadeOut(250);
        return false;
    });
}



/* Messages Header */
function masquerMessage(){
    $('#messageInfo div').slideUp(500);
    clearTimeout(timerMess);
}
function afficherMessage(type, fonctionAccepte, fonctionAnnule){
    $('#messageInfo div').slideDown(500);
    clearTimeout(timerMess);
    scrollGoto('#head');
    if(type != null && type != 'question') {
        timerMess = setTimeout("masquerMessage()", 10000);
    } else {
        var lastHauteur = $('body').scrollTop();
        $('a[href=#question-accepte]').click(function(){
            masquerMessage();
            $('html,body').animate({scrollTop:lastHauteur},1000,'easeOutQuint');
            if(fonctionAccepte != undefined) {fonctionAccepte.call();}
            $('a[href=#question-accepte]').unbind();
            return false;
        });
        $('a[href=#question-annule]').click(function(){
            masquerMessage();
            $('html,body').animate({scrollTop:lastHauteur},1000,'easeOutQuint');
            if(fonctionAnnule != undefined) {fonctionAnnule.call();}
            $('a[href=#question-annule]').unbind();
            return false;
        });
    }
}
function addMessage(type, message, accepte, annule, fonctionAccepte, fonctionAnnule){
    $('#messageInfo div').slideUp(500, function(){
        $('#messageInfo').empty().append('<div class="'+type+' dn">'+message+'</div>');
        if(type == 'question'){
            var labelOui = (accepte != null) ? accepte : 'Oui';
            var labelAnnule = (annule != null) ? annule : 'Annuler';
            $('#messageInfo .question').append('<br/><span class="reponse"><a href="#question-accepte">'+labelOui+'</a><a href="#question-annule">'+labelAnnule+'</a></span>');
        }
        afficherMessage(type, fonctionAccepte, fonctionAnnule);
    });
}



// Scroll jusqu'à un élément
function scrollGoto(cible, marge){
    marge = (marge != null || marge != undefined) ? marge : 0;
    if($(cible).length>=1){
        hauteur=$(cible).offset().top;
    } else {
        hauteur=$("a[name="+cible.substr(1,cible.length-1)+"]").offset().top;
    }
    $('html,body').animate({scrollTop:(hauteur+marge)},1000,'easeOutQuint');
    return false;
}

//  Remplace toutes les occurences d'une chaine
function replaceAll(str, search, repl) {
  while (str.indexOf(search) != -1)
    str = str.replace(search, repl);
  return str;
}




var jours = new Array('Dimanche', 'Lundi', 'Mardi', 'Mercredi',  'Jeudi', 'Vendredi', 'Samedi');

var mois = new Array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                     'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

function parse_date(timestamp, maj){
    maj = (maj != null) ? maj : false;
    
    var dt = new Date(timestamp*1000);
    var d1 = new Date();
    var d2 = new Date(d1.getFullYear(), d1.getMonth(), d1.getDate(), 0, 0, 0, 0);
    var time = d1.getTime()/1000;
    
    
    var minutes = dt.getMinutes();
    minutes = (parseInt(minutes) < 10) ? '0'+minutes : minutes;
    // var date = date(H\hi...)
    var date = dt.getHours()+'h'+minutes;
    // var date2 = Vendredi 15 Mai 2009
    var date2 = jours[dt.getDay()]+' '+dt.getDate()+' '+mois[dt.getMonth()]+' à '+date;
    
    var t = time - timestamp;
    var t2 = timestamp - (d2.getTime()/1000);
    var r = '';
    if(t >= 0 && t2 > -86400){ // Passé mais moins de deux jours
        if(t < 300){ // Moins de 5 minutes
            if(t < 60){ // Moins de une minute
                r = (maj)?'Il y a quelques secondes':'il y a quelques secondes';
            } else { // Quelques minutes
                r = (maj)?'Il y a quelques minutes':'il y a quelques minutes';
            }
        } else if(t2 > 0 && t2 < 86399) { // Aujourd'hui
            r = (maj)?'Aujourd\'hui à ':'aujourd\'hui à ';
            r += date;
        } else {
            r = (maj)?'Hier à ':'hier à ';
            r += date;
        }
    } else if(t < 0 && t2 < 259200){ // À venir mais moins de deux jours
        if(t > -300){ // Dans moins de 5 minutes
            if(t > -60){ // Moins de une minute
                r = (maj)?'Dans quelques secondes':'dans quelques secondes';
            } else { // Quelques minutes
                r = (maj)?'Dans quelques minutes':'dans quelques minutes';
            }
        } else if(t2 < 172800) { // Demain
            r = (maj)?'Demain à ':'demain à ';
            r += date;
        } else {
            r = (maj)?'Après-demain à ':'après-demain à ';
            r += date;
        }
    } else {
        r = date2+' à '+date;
    }

    r = (timestamp == 0) ? 'Indéfini' : r;

    return r;
}



// Gestion du simple et double clic sur un même objet.
(function($){
$.fn.single_double_click = function(single_click_callback, double_click_callback, timeout) {
    return this.each(function(){
        var clicks = 0, self = this;
        jQuery(this).click(function(event){
            clicks++;
            if (clicks == 1) {
                setTimeout(function(){
                    if(clicks == 1) {
                        single_click_callback.call(self, event);
                    } else {
                        double_click_callback.call(self, event);
                    }
                    clicks = 0;
                }, timeout || 250);
            }
        });
    });
}
})(jQuery);


(function($){
$.fn.infobulle = function(){
    var elem = $(this);
    var title = elem.attr('title');
    elem.attr({title: '', 'infob-titre': title});
    $('#corps').append('<span class="infobulle"></span>');
    var bulle = $('.infobulle');
    bulle.append(title);
    var posTop = elem.offset().top-elem.height()-13;
    var posLeft = elem.offset().left+elem.width()/2-bulle.width()/2;
    bulle.css({
        left: posLeft,
        top: posTop,
        opacity: 0.85
    });

    elem.mouseout(function(){
        bulle.remove();
        elem.attr({title: title, rel: 'infobulle'});
        elem.removeAttr('infob-titre');
    });
}
})(jQuery);


/* Gère les animations couleur */
(function(jQuery){
	// We override the animation for all of these color styles
	jQuery.each(['backgroundColor', 'borderBottomColor', 'borderLeftColor', 'borderRightColor', 'borderTopColor', 'color', 'outlineColor'], function(i,attr){
		jQuery.fx.step[attr] = function(fx){
			if ( fx.state == 0 ) {
				fx.start = getColor( fx.elem, attr );
				fx.end = getRGB( fx.end );
			}

			fx.elem.style[attr] = "rgb(" + [
				Math.max(Math.min( parseInt((fx.pos * (fx.end[0] - fx.start[0])) + fx.start[0]), 255), 0),
				Math.max(Math.min( parseInt((fx.pos * (fx.end[1] - fx.start[1])) + fx.start[1]), 255), 0),
				Math.max(Math.min( parseInt((fx.pos * (fx.end[2] - fx.start[2])) + fx.start[2]), 255), 0)
			].join(",") + ")";
		}
	});

	// Color Conversion functions from highlightFade
	// By Blair Mitchelmore
	// http://jquery.offput.ca/highlightFade/

	// Parse strings looking for color tuples [255,255,255]
	function getRGB(color) {
		var result;

		// Check if we're already dealing with an array of colors
		if ( color && color.constructor == Array && color.length == 3 )
			return color;

		// Look for rgb(num,num,num)
		if (result = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(color))
			return [parseInt(result[1]), parseInt(result[2]), parseInt(result[3])];

		// Look for rgb(num%,num%,num%)
		if (result = /rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(color))
			return [parseFloat(result[1])*2.55, parseFloat(result[2])*2.55, parseFloat(result[3])*2.55];

		// Look for #a0b1c2
		if (result = /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(color))
			return [parseInt(result[1],16), parseInt(result[2],16), parseInt(result[3],16)];

		// Look for #fff
		if (result = /#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(color))
			return [parseInt(result[1]+result[1],16), parseInt(result[2]+result[2],16), parseInt(result[3]+result[3],16)];

		// Otherwise, we're most likely dealing with a named color
		return colors[jQuery.trim(color).toLowerCase()];
	}

	function getColor(elem, attr) {
		var color;

		do {
			color = jQuery.curCSS(elem, attr);

			// Keep going until we find an element that has color, or we hit the body
			if ( color != '' && color != 'transparent' || jQuery.nodeName(elem, "body") )
				break;

			attr = "backgroundColor";
		} while ( elem = elem.parentNode );

		return getRGB(color);
	};

	// Some named colors to work with
	// From Interface by Stefan Petre
	// http://interface.eyecon.ro/

	var colors = {
		aqua:[0,255,255],
		azure:[240,255,255],
		beige:[245,245,220],
		black:[0,0,0],
		blue:[0,0,255],
		brown:[165,42,42],
		cyan:[0,255,255],
		darkblue:[0,0,139],
		darkcyan:[0,139,139],
		darkgrey:[169,169,169],
		darkgreen:[0,100,0],
		darkkhaki:[189,183,107],
		darkmagenta:[139,0,139],
		darkolivegreen:[85,107,47],
		darkorange:[255,140,0],
		darkorchid:[153,50,204],
		darkred:[139,0,0],
		darksalmon:[233,150,122],
		darkviolet:[148,0,211],
		fuchsia:[255,0,255],
		gold:[255,215,0],
		green:[0,128,0],
		indigo:[75,0,130],
		khaki:[240,230,140],
		lightblue:[173,216,230],
		lightcyan:[224,255,255],
		lightgreen:[144,238,144],
		lightgrey:[211,211,211],
		lightpink:[255,182,193],
		lightyellow:[255,255,224],
		lime:[0,255,0],
		magenta:[255,0,255],
		maroon:[128,0,0],
		navy:[0,0,128],
		olive:[128,128,0],
		orange:[255,165,0],
		pink:[255,192,203],
		purple:[128,0,128],
		violet:[128,0,128],
		red:[255,0,0],
		silver:[192,192,192],
		white:[255,255,255],
		yellow:[255,255,0]
	};

})(jQuery);