/*
*   Document    : zexplorer
*   Créé le     : 31 01 2010, 17:50:28
*   Auteur      : Alex-D
*   Client      : http://www.zdesigns.fr
*   Description :
*       Gère toutes les actions de la galerie des zDesigns
*
*   Tous droits réservés
*   Copie partielle ou totale interdite
*   Pour toute demande d'utilisation : demodealex[a]gmail[pt]com
*/

var nb_cols = '3';
$(function(){
    $('a.details').click(function(){
        $('.details_design:visible .fermer').trigger('click');
        var id = $(this).attr('href');
        var regex = /\/zdesigns-([0-9]+)/g;
        id = regex.exec(id);
        id = id[1];
        if($('#details_design_'+id).is(':hidden')){
            var sep = null;
            if($(this).parent().parent().parent().hasClass('cell_design')){
                sep = $(this).parent().parent().parent().nextAll('hr.sep'+nb_cols+':first');
                sep.css({
                    'display': 'block',
                    'visibility': 'hidden',
                    'padding-top': '320px'
                });
            } else {
                sep = $(this).parent().parent().parent().parent().parent().parent().next('hr.clear');
                sep.css({
                    'display': 'block',
                    'visibility': 'hidden',
                    'padding-top': '20px'
                });
            }
            
            var elem = $('#details_design_'+id).remove().insertAfter(sep);
            elem.slideDown(750, function(){
                $(window).trigger('resize');
                scrollGoto(elem, parseInt(-$(window).height(), 10)/2 + 350);

                $('.detail_design_home a.details', elem).click(function(){
                    $('.content', elem).stop().animate({
                        'margin-left': '-100%'
                    }, 500, function(){
                        $('.coms .btns', elem).fadeIn();
                    });
                    return false;
                });
                $('.coms a.details', elem).click(function(){
                    $('.coms .btns', elem).hide();
                    $('.content', elem).stop().animate({
                        'margin-left': '0'
                    }, 500);
                    return false;
                });
                $('.link_go_form', elem).click(function(){
                    $('.form_com', elem).slideDown();
                    $('.form_com form', elem).submit(function(){
                        var message = $(".form_com form textarea").val();
                        if(message != '' && message != ' '){
                            var pseudo = $(".form_com form input[name=pseudo]").val();
                            var url = $('.form_com form', elem).attr('action');
                            var idd = $(".form_com form textarea").attr('id').replace('message_com_', '');
                            $(".form_com form textarea").blur();
                            $(".form_com form textarea").val('');
                            $.post(url, {action:'post_com', id: idd, pseudo: pseudo, message:message}, function(data){
                                if(data['erreur'] != null){
                                    if(data['erreur'] == 'true'){
                                        $('.form_com', elem).slideUp();
                                        $('.form_com', elem).after($('#empty_com').html());
                                        var newCom = $('.com.empty', elem).removeClass('empty').hide();
                                        $('.auteur', newCom).append(pseudo);
                                        $('.rang', newCom).append(nl2br($('.form_com .rang', elem).text()));
                                        $('.commentaire', newCom).append(message);
                                        if($('.form_com .com', elem).hasClass('droite')){
                                            newCom.addClass('droite');
                                        }
                                        newCom.slideDown();
                                    } else {
                                        addMessage('error', data['erreur']);
                                    }
                                } else {
                                    addMessage('error', 'Erreur serveur');
                                }
                            }, "json");
                        }
                        return false;
                    });
                    return false;
                });
                $('a[href^=#voter]', elem).click(function(){
                    $('.form_note', elem).fadeIn(200);
                    return false;
                });
                $('a[href^=#quit_voter]', elem).click(function(){
                    $('.form_note', elem).fadeOut(200);
                    return false;
                });


                /*
                 * Système de notation
                 */
                // On passe chaque note à l'état grisé par défaut
                $("ul.zone_etoiles li label", elem).removeClass("etoile");
                $("ul.zone_etoiles li label", elem).addClass("etoile2");
                // Au survol de chaque note à la souris
                $("ul.zone_etoiles li", elem).mouseover(function(){
                    // On passe les notes supérieures à l'état inactif (par défaut)
                    $(this).nextAll("li").find("label").removeClass('etoile').addClass("etoile2");
                    // On passe les notes inférieures à l'état actif
                    $(this).prevAll("li").find("label").removeClass('etoile2').addClass("etoile");
                    // On passe la note survolée à l'état actif (par défaut)
                    $(this).find("label").removeClass('etoile2').addClass("etoile");
                });
                // Lorsque l'on sort du sytème de notation à la souris
                $("ul.zone_etoiles", elem).mouseout(function() {
                    // On passe toutes les notes à l'état inactif
                    $(this).children("li").find("label").removeClass("etoile").addClass("etoile2");
                    // On simule (trigger) un mouseover sur la note cochée s'il y a lieu
                    $(this).find("li input:checked").parent("li").trigger("mouseover");
                });
                var vote_clicked = false;
                $("ul.zone_etoiles", elem).click(function(){
                    var this_el = $(this);
                    if(!vote_clicked){
                        vote_clicked = true;
                        window.setTimeout(function(){
                            var idd = this_el.attr('id').replace('note_', '');
                            var note = this_el.find('input:checked').attr('value');
                            if(note != undefined){
                                var url = './ajax/design.php';
                                $.post(url, {action:'noter', id: idd, note: note}, function(data){
                                    if(data['erreur'] != null){
                                        if(data['erreur'] == 'true'){
                                            this_el.parent().parent().find('.note').remove();
                                            this_el.parent().parent().find('.vote_link:first').before(data['note']);
                                            $('a[href^=#quit_voter]', elem).trigger('click');
                                            addMessage('info', 'Votre vote est enregistré');
                                        } else {
                                            addMessage('error', data['erreur']);
                                        }
                                    } else {
                                        addMessage('error', 'Erreur serveur');
                                    }
                                }, "json");
                            }
                        }, 50);
                    }
                    window.setTimeout(function(){
                        vote_clicked = false;
                    }, 1000);
                });
                

                $('.fermer', elem).click(function(){
                    $('.coms .btns', elem).hide();
                    elem.slideUp(500, function(){
                        $('.content', elem).css({
                            'margin-left': '0'
                        });
                        sep.hide();
                    });
                    return false;
                });
            });
        }
        return false;
    });


    $('.design_une').mouseover(function(){
        $(this).find('.infos').stop().animate({
            marginTop: '225px',
            opacity: '0.9'
        }, 300);
    })
    $('.design_une').mouseout(function(){
        $(this).find('.infos').stop().animate({
            marginTop: '253px',
            opacity: '0.8'
        }, 300);
    });
    $('.design_une').trigger('mouseout');

    $(window).trigger('resize');
});

function nl2br(str, is_xhtml){
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '' : '</br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

$(window).bind('resize', function(){
    var largeurCorps = parseInt($('#corps').css('width'), 10);
    if(largeurCorps < 1150){
        $('.cell_design').css('width', '33.3%');
        nb_cols = '3';
    } else {
        $('.cell_design').css('width', '25%');
        nb_cols = '4';
    }
});