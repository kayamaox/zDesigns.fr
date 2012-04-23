/*
*   Document    : zexplorer
*   Créé le     : 24 10 2010, 16:38:14
*   Auteur      : Alex-D
*   Client      : http://www.zdesigns.fr
*   Description :
*       Gestion de toutes les actions du zExplorer
*
*   Tous droits réservés
*   Copie partielle ou totale interdite
*   Pour toute demande d'utilisation : demodealex[a]gmail[pt]com
*/

(function($){

$.fn.zexplorer = function(params){
    var options = {
        etendre: {},
        editeur: null
    }
    $.extend(options, params);
    var msgErrServ = "Le serveur de répond pas";

    return this.each(function(){
        /* Variables générales */
        var $$ = $(this);
        var files = $('#files');
        var root = $('.root > span > a', $$).attr('href');
        var root_abs = $('.root > span > a', $$).attr('rel')+root.replace('./', '');

        /* Checkbox */
        var nbChecked = 0;
        var nbElems = $('.file', $('#root')).size();
        var all_ckeck = false;

        /* 1 seule valid' après double clic (entrer ou clickOut) */
        var lastValid = true;

        /* Renommage */
        var elemParentArbre = null;
        var urlParent = null;
        var tmpId = null;
        var elemURL = null;
        var elemArbre = null;
        var elemID = null;
        var name = null;
        var newName = null;
        var oldElemURL = null;

        var editor = options.editeur;
        var editeur = $('#editeur_css');
        var filesOpened = [];

        /******************************************************************************
         * Initialisation :: Ajout de classes, sous-dossiers masqués
         ******************************************************************************/
        // $('li::has(ul):not(.dossier)', $$).addClass('dossier');
        // $('.dossier > span > a', $$).addClass('label');
        // $('.dossier > ul', $$).hide();
        // $('.root').addClass('explore');
        $('.dossier').each(function(){
            var dossier = $(this);
            if($('ul', dossier).size() > 0){
                $('span:first', dossier).prepend('<div class="plus ico">Plus</div>');
            }
        });



        /******************************************************************************
         * Déploie et rétracte les dossiers
         ******************************************************************************/
        $('.dossier > span > div.plus', $$).click(function(e){
            $('html').trigger('click');
            var elem = $(this).parent().parent();
            $('ul:first', elem).slideToggle();
            elem.toggleClass('ouvert');
            e.stopPropagation();
            return false;
        });
        $(options.etendre, $$).each(function(){
            if($(this).hasClass('dossier')){
                $('ul:first', this).show();
                $(this).addClass('ouvert');
                $(this).parents('.dossier ul').show();
                $(this).parents('.dossier').addClass('ouvert');
            }
        });



        /******************************************************************************
         * Ouvre un dossier dans l'explorer
         ******************************************************************************/
        $('.dossier', $$).click(function(e){ // Depuis l'arbre
            var elem = $(this);
            $('.explore', $$).each(function(){
                $(this).removeClass('explore');
            });
            elem.addClass('explore');

            explore(elem);

            e.stopPropagation();
            return false;
        });

        $('.folder_link').single_double_click(function(){ // Depuis l'explorer
            openFolder($(this)); //--------------> Ouvre le dossier
        }, function(){
            if($(this).parent().parent().hasClass('to_parent')){
                openFolder($(this)); //--------------> Remonte au parent
            } else {
                renameDossier($(this).parent()); //--> Renomme le dossier
            }
        });

        $('#root_link').click(function(e){ // Depuis le fil d'Ariane (pour la racine)
            var elem = $('.root', $$);
            $('.explore', $$).each(function(){
                $(this).removeClass('explore');
            });
            elem.addClass('explore');

            explore(elem);

            e.stopPropagation();
            return false;
        });

        function openFolder(thisLink){ // Depuis l'explorer OU depuis le fil d'Ariane
            $('.explore', $$).each(function(){
                $(this).removeClass('explore');
            });

            var url = thisLink.attr('rel');
            var elem_explorer = url.replace(root, '');
            elem_explorer = elem_explorer.replace(/\//g, '-');

            var elem = $('#arbre_'+elem_explorer).parent().parent();
            if(url+'/' == root){
                elem = $('#folder-root');
            }

            explore(elem);
        }

        // Ouvre le dossier en fonction de la cible
        function explore(elem){
            $('html').trigger('click');
            if(elem.is(':hidden') || $('span:first .plus', elem).size() > 0){
                // Ouvre le dossier
                elem.addClass('ouvert');
                elem.find('ul:first').slideDown();
                // Ouvre les dossiers parents
                elem.parents('.dossier').addClass('ouvert');
                elem.parents('.dossier ul').slideDown();
            }
            
            $('.explore', $$).each(function(){
                $(this).removeClass('explore');
            });

            elem.addClass('explore');
            var elem_explorer = null;
            var arianne = null;

            if($(elem).hasClass('root')){
                elem_explorer = '#root';
                arianne = 'root';
            } else {
                var url = $('.explore > span > a.label').attr('href');
                elem_explorer = url.replace(root, '');
                elem_explorer = elem_explorer.replace(/\//g, '-');
                elem_explorer = elem_explorer.substring(0, elem_explorer.length-1);
                arianne = elem_explorer;
                elem_explorer = '#'+elem_explorer;
            }

            if($(elem_explorer).is(':hidden') || $(elem_explorer).size() == 0){
                all_ckeck = false;
                nbChecked = 0;
                $('a[href=#check_all_files]').removeClass('checked');
                $('.files-folder:visible .file').removeClass('selected');
                if($('.files-folder:visible', files).size() > 0){
                    $('.files-folder:visible', files).fadeOut(250, function(){
                        if($(elem_explorer).size() == 0){
                            newEmptyDossier(arianne);
                            nbChecked = 0;
                            nbElems = 0;
                        }
                        $(elem_explorer).slideDown(250, function(){
                            nbChecked = 0;
                            nbElems = $('.file', $('.files-folder:visible')).not('.to_parent').size();
                        });
                    });
                } else {
                    $('.files-folder:visible', files).each(function(){
                        $(this).hide();
                    });
                    if($(elem_explorer).size() == 0){
                        newEmptyDossier(arianne);
                        nbChecked = 0;
                        nbElems = 0;
                    }
                    $(elem_explorer).slideDown(250, function(){
                        nbChecked = 0;
                        nbElems = $('.file', $('.files-folder:visible')).not('.to_parent').size();
                    });
                }


                // Gère le fil d'Ariane
                if(arianne != 'root'){
                    var urlParent = null;
                    var labelParent = null;
                    var arianneOut = '';
                    $('#arbre_'+arianne).parents('li.dossier').each(function(){
                        if(!$(this).hasClass('root')){
                            urlParent = $(this).find('a.label:first').attr('href');
                            urlParent = urlParent.substring(0, urlParent.length-1);
                            labelParent = $(this).find('a.label:first').text().replace(/ /g, '');
                            arianneOut = ' > <a rel="'+urlParent+'" href="javascript:void();">'+labelParent+'</a>'+arianneOut;
                        }
                    });
                    $('#dyn_arianne').empty().append(arianneOut);

                    $('a', $('#dyn_arianne')).click(function(e){
                        openFolder($(this));
                        e.stopPropagation();
                        return false;
                    });
                } else {
                    $('#dyn_arianne').empty();
                }
                nbCheckedChange();
            }
        }




        /******************************************************************************
         * Barre d'outils
         ******************************************************************************/
         var tb = $('#barre_outils'); // toolsBar

         $('a[href=#upload-file]', tb).click(function(){
             $('html').trigger('click');
             $('#cheminUpload').attr('value', $('a.label:first', '#arbre .explore').attr('href').replace(root, root_abs));
             $('#uploader h1').empty().append('Envoyer un fichier');
             $('#uploader').fadeIn();
             $('#uploadEnd').bind('click', function(){
                 if($('#uploadEnd #upload-maj').text() == 'false'){
                     $('.files-folder:visible .file:last', files).after($('#uploadEnd #upload-elem').html());
                     coloFdFiles();
                     var newElem = $('.files-folder:visible .file:last', files);
                     $('a[href=#check_file]', newElem).click(function(){
                         $('html').trigger('click');
                         checkElem($(this));
                         return false;
                     });
                     $('.file_link', newElem).single_double_click(function(){ // Depuis l'explorer
                         if($(this).attr('class').replace('di file_link ', '') == 'img'){
                             $.zoombox.open($(this).attr('rel')); //--------------> Ouvre l'image dans zoombox
                         } else if($(this).attr('class').replace('di file_link ', '') == 'code'){
                             edit($(this).attr('rel'), $('.label', $(this)).text());
                         }
                     }, function(){
                         renameFile($(this).parent()); //--> Renomme le fichier
                     });
                     $('a[href=#selection-edit]', newElem).click(function(){
                         $('html').trigger('click');
                         var elem = $(this).parent().parent().parent().parent();
                         elem = $('.file_name a', elem);
                         edit(elem.attr('rel'), $('.label', elem).text());
                         return false;
                     });
                     $('a[href=#file-rename]', newElem).click(function(){
                         $('html').trigger("click");
                         renameFile($(this).parent().parent().parent().parent().find('.file_name'));
                         return false;
                     });
                     $('a[href=#selection-del]', newElem).click(function(){
                         $('html').trigger('click');
                         supprElem($(this).parent().parent().parent().parent());
                         return false;
                     });
                 } else {
                     var elemMaj = $('.files-folder:visible .file a[rel='+$('#uploadEnd #upload-elem .file_name a.file_link').attr('rel')+']', files).parent().parent();
                     $('.file_dim', elemMaj).empty().append($('#uploadEnd #upload-elem .file_dim').text());
                     $('.file_size', elemMaj).empty().append($('#uploadEnd #upload-elem .file_size').text());
                 }
                 changeSize($('#uploadEnd #upload-size .pourcent').text(), $('#uploadEnd #upload-size .sizeDesign').text());
             });
             return false;
         });
         /*
         $('a[href=#upload-zip]', tb).click(function(){
             $('html').trigger('click');
             $('#cheminUpload').attr('value', $('a.label:first', '#arbre .explore').attr('href').replace(root, root_abs));
             $('#uploader h1').empty().append('Envoyer une archive Zip');
             $('#uploader').fadeIn();
             return false;
         });
         */
         $('a[href=#new-css]', tb).click(function(){
             $('html').trigger('click');
             newCSS();
             return false;
         });
         $('a[href=#new-folder]', tb).click(function(){
             $('html').trigger('click');
             newDossier();
             return false;
         });

         // Actions
         $('a[href=#selection-edit]', tb).click(function(){
             $('html').trigger('click');
             var elem = $('.file_name a', $('.file.selected', files.has(':visible')));
             edit(elem.attr('rel'), $('.label', elem).text());
             return false;
         });
         $('a[href=#selection-rename]', tb).click(function(){
             $('html').trigger('click');
             var elem = $('.selected', files.has(':visible'));
             if(elem.attr('rel') == 'file'){
                 renameFile($('.file_name', elem));
             } else {
                 renameDossier($('.file_name', elem));
             }
             return false;
         });
         $('a[href=#selection-del]', tb).click(function(){
             $('html').trigger('click');
             if(nbChecked == 1){
                 var elem = $('.selected', files.has(':visible'));
                 supprElem(elem);
             } else if(nbChecked > 1){
                 supprMultiple();
             }
             return false;
         });

         $('a[href^=#publier]').click(function(){
             showBigLoader('Publication en cours ...');
             var idd = $('#id_design', div_edit).text();
             $.post('./ajax/design.php', {action: 'publier', id: idd}, function(data){
                 if(data != null && data['erreur'] == 'true'){
                     addMessage('info', 'Design publié');
                 } else {
                     var erreur = "Erreur";
                     if(data == null){erreur = msgErrServ} else {erreur = data['erreur'];}
                     addMessage('error', erreur);
                 }
                 hideBigLoader();
             }, 'json');
             return false;
         });
         $('a[href^=#publier_zds]').click(function(){
             showBigLoader('Publication en cours ...');
             $.post('./ajax/design.php', {action: 'publier_zds'}, function(data){
                 if(data != null && data['erreur'] == 'true'){
                     addMessage('info', 'Design publié');
                 } else {
                     var erreur = "Erreur";
                     if(data == null){erreur = msgErrServ} else {erreur = data['erreur'];}
                     addMessage('error', erreur);
                 }
                 hideBigLoader();
             }, 'json');
             return false;
         });
         $('a[href^=#aide]').click(function(){
             $('#aide').fadeIn(200, function(){
                 redim();
             });
             return false;
         });



         /******************************************************************************
         * Actions sur les éléments de l'explorer
         ******************************************************************************/
         $('.file_link').single_double_click(function(){ // Depuis l'explorer
             if($(this).attr('class').replace('di file_link ', '') == 'img'){
                $.zoombox.open($(this).attr('rel')); //--------------> Ouvre l'image dans zoombox
             } else if($(this).attr('class').replace('di file_link ', '') == 'code'){
                 edit($(this).attr('rel'), $('.label', $(this)).text());
             }
         }, function(){
             renameFile($(this).parent()); //--> Renomme le dossier
         });
         $('a[href=#selection-edit]', files).click(function(){
             $('html').trigger('click');
             var elem = $(this).parent().parent().parent().parent();
             elem = $('.file_name a', elem);
             edit(elem.attr('rel'), $('.label', elem).text());
             return false;
         });
         $('a[href=#file-rename]', files).click(function(){
             $('html').trigger("click");
             renameFile($(this).parent().parent().parent().parent().find('.file_name'));
             return false;
         });
         $('a[href=#folder-rename]', files).click(function(){
             $('html').trigger("click");
             renameDossier($(this).parent().parent().parent().parent());
             return false;
         });
         $('a[href=#selection-del]', files).click(function(){
             $('html').trigger('click');
             supprElem($(this).parent().parent().parent().parent());
             return false;
         });



        /******************************************************************************
         * Gestion selection
         ******************************************************************************/
        $('a[href=#check_file]').click(function(){
            $('html').trigger('click');
            checkElem($(this));
            return false;
        });
        function checkElem(elem){
            elem.parent().toggleClass('selected');
            if(elem.parent().hasClass('selected')){
                nbChecked += 1;
            } else {
                nbChecked -= 1;
            }

            if(nbChecked == nbElems){
                $('a[href=#check_all_files]').addClass('checked');
                all_ckeck = true;
            } else {
                $('a[href=#check_all_files]').removeClass('checked');
                all_ckeck = false;
            }
            nbCheckedChange();
        }

        $('a[href=#check_all_files]').click(function(){
            $('html').trigger('click');
            $('.files-folder:visible .file').removeClass('selected');
            if(all_ckeck){
                $(this).removeClass('checked');
                all_ckeck = false;
                nbChecked = 0;
            } else {
                $('.files-folder:visible .file').not('.to_parent').toggleClass('selected');
                $(this).addClass('checked');
                all_ckeck = true;
                nbChecked = nbElems;
            }
            nbCheckedChange();
            return false;
        });

        function nbCheckedChange(){
            nbElems = $('.file', $('.files-folder:visible')).not('.to_parent').size();
            switch(nbChecked){
                case 0:  // Aucune selection, on désactuve le menu "Pour la selection"
                    $('#barre_outils .for_selection li').each(function(){
                        $(this).hide();
                    });
                    $('#barre_outils .for_selection').addClass('inactive');

                    break;

                case 1: // Un objet selectionné, on active tous les éléments du menu "Pour la selection"
                    $('#barre_outils .for_selection li').each(function(){
                        $(this).show();
                    });
                    if($('.file.selected', $('.files-folder:visible')).attr('rel') != 'file' ||
                       $('.file.selected .file_name .file_link', $('.files-folder:visible')).attr('class').replace('di file_link ', '') != 'code'){
                        $('#barre_outils .for_selection li.actions-edit').hide();
                    }
                    $('#barre_outils .for_selection').removeClass('inactive');
                    break;

                default: // Plus d'un objet est selectionné, on active seulement la suppression (on masque les autres)
                    $('#barre_outils .for_selection li').each(function(){
                        $(this).hide();
                    });
                    $('#barre_outils .for_selection li.actions-del').show();
                    $('#barre_outils .for_selection').removeClass('inactive');
                    break;
            }
        }



        /******************************************************************************
         * Supprimer / Renommer / Nouveau Dossier / Upload Fichier
         ******************************************************************************/
        function supprElem(elem){
            var type = (elem.attr('rel') == 'folder') ? 'dossier' : 'fichier';
            addMessage('question', 'Supprimer ce '+type+' ?', 'Oui', 'Non', function(){
                var action = (type != null && type == 'dossier') ? 'supprDir' : 'supprFile';
                var urlFile = $('.file_name a', elem).attr('rel');

                $.post('./ajax/arbre.php', {action: action, url: urlFile}, function(data){
                    if(data != null && data['erreur'] == 'true'){
                        elem.slideUp(250, function(){
                            if(elem.hasClass('selected')){
                                nbChecked--;
                                nbCheckedChange();
                            }
                            elem.remove();
                            coloFdFiles();
                        });
                        if(type == 'dossier'){
                            var elemArbre = $('.file_name a', elem).attr('rel');
                            elemArbre = elemArbre.replace(root, '');
                            elemArbre = elemArbre.replace(/\//g, '-');
                            $('#'+elemArbre).remove();
                            elemArbre = $('#arbre_'+elemArbre).parent().parent();
                            elemParentArbre = elemArbre.parent().parent();
                            elemArbre.slideUp(200, function(){
                                $(this).remove();
                                if($('li', $('ul', elemParentArbre)).size() == 0){
                                    $('ul', elemParentArbre).remove();
                                    $('span:first .plus', elemParentArbre).remove();
                                    elemParentArbre.removeClass('ouvert');
                                }
                            });
                        }
                        changeSize(data['pourcent'], data['sizeDesign']);
                        if(data['rapport'] != null){
                            $('#rapport').empty().append(data['rapport']);
                            $('#rapport_pourcent').empty().append(data['pourcent_complet']);
                            $('a.zoombox').zoombox();
                            $('a[href^=#all_maj], a[href^=#mass_dir_maj], a[href^=#dir_maj], a[href^=#file_maj]').click(function(){
                                rapport_maj($(this));
                                return false;
                            });
                        }
                    } else {
                        var erreur = "Erreur";
                        if(data == null) {erreur = msgErrServ} else {erreur = data['erreur']}
                        addMessage('error', erreur);
                    }
                }, 'json');
            });
        }
        function supprMultiple(){
            var elems = $('.selected', files.has(':visible').not('.to_parent'));
            var nbElems = elems.size();
            addMessage('question', 'Supprimer ces '+nbElems+' elements ?', 'Oui', 'Non', function(){
                var urlFile = null;
                var type = null;
                var files = new Array();

                elems.each(function(){
                    type = ($(this).attr('rel') == 'folder') ? 'dossier' : 'fichier';
                    urlFile = $('.file_name a', $(this)).attr('rel');
                    files.push({0:type, 1:urlFile})
                });

                $.post('./ajax/arbre.php', {action: 'supprMultiple', files: files}, function(data){
                    if(data != null && data['erreur'] == 'true'){
                        elems.each(function(){
                            $(this).slideUp(250, function(){
                                $(this).remove();
                            });
                            type = ($(this).attr('rel') == 'folder') ? 'dossier' : 'fichier';
                            if(type == 'dossier'){
                                var elemArbre = $('.file_name a', $(this)).attr('rel');
                                elemArbre = elemArbre.replace(root, '');
                                elemArbre = elemArbre.replace(/\//g, '-');
                                $('#'+elemArbre).remove();
                                elemArbre = $('#arbre_'+elemArbre).parent().parent();
                                elemParentArbre = elemArbre.parent().parent();
                                elemArbre.slideUp(200, function(){
                                    $(this).remove();
                                    if($('li', $('ul', elemParentArbre)).size() == 0){
                                        $('ul', elemParentArbre).remove();
                                        $('span:first .plus', elemParentArbre).remove();
                                        elemParentArbre.removeClass('ouvert');
                                    }
                                });
                            }
                        });
                        setTimeout(function(){
                            coloFdFiles();
                        }, 270);
                        $('a[href=#check_all_files]').removeClass('checked');
                        all_ckeck = false;
                        nbChecked = 0;
                        nbCheckedChange();
                        changeSize(data['pourcent'], data['sizeDesign']);
                        if(data['rapport'] != null){
                            $('#rapport').empty().append(data['rapport']);
                            $('#rapport_pourcent').empty().append(data['pourcent_complet']);
                            $('a.zoombox').zoombox();
                            $('a[href^=#all_maj], a[href^=#mass_dir_maj], a[href^=#dir_maj], a[href^=#file_maj]').click(function(){
                                rapport_maj($(this));
                                return false;
                            });
                        }
                    } else {
                        var erreur = "Erreur";
                        if(data == null) {erreur = msgErrServ} else {erreur = data['erreur']}
                        addMessage('error', erreur);
                    }
                }, 'json');
            });
        }
        function renameFile(elem){
            lastValid = false;
            $('a .label', elem).hide();
            $('input', elem).show().select();

            $('input', elem).click(function(e){
                e.stopPropagation();
            });

            $('html').click(function(){
                if(!lastValid) onValid(elem);
            });

            $('html').keypress(function(e){
                var touche = (e.keyCode) ? e.keyCode : e.which;

                if(touche == 13){
                    if(!lastValid) onValid(elem);
                }
            });

            function onValid(elem){
                lastValid = true;
                $('input', elem).hide();
                $('a .label', elem).show();
                $('html').unbind();
                
                name = $('.label', elem).text().replace(/\ /g, '');
                newName = $('input', elem).val().replace(/\ /g, '_') + '.' + $('a', elem).attr('ext');
                
                tmpId = elem.parent().parent('.files-folder').attr('id');
                if(tmpId != 'root'){
                    elemParentArbre = $('#arbre_'+tmpId, $$);
                    urlParent = elemParentArbre.attr('href');
                } else {
                    elemParentArbre = $('.root', $$);
                    urlParent = root;
                }

                if(newName != '' && newName != '' && newName != null && newName != name){
                    elem.find('img').fadeIn(300);
                    $.post('./ajax/arbre.php', {action: 'rename', oldUrl: urlParent+name, newUrl: urlParent+newName, newName:newName, type:'file'}, function(data){
                        if(data != null && data['erreur'] == 'true'){
                            newName = data['newName'];
                            elem.parent().flashValid();
                            
                            $('a .label', elem).empty().append(newName);
                            $('a .label', elem).parent('a').attr('rel', urlParent+newName);

                            $('input', elem).empty().val(newName.replace('.'+$('a', elem).attr('ext'), ''));
                        } else {
                            var erreur = "Erreur";
                            if(data == null) {erreur = msgErrServ} else {erreur = data['erreur']}
                            addMessage('error', erreur);
                            $('input', elem).empty().val(name.replace('.'+$('a', elem).attr('ext'), ''));
                        }
                        elem.find('img').fadeOut(750);
                    }, 'json');
                }
            }
        }

        // Dossiers
        function renameDossier(elem){
            lastValid = false;
            $('a .label', elem).hide();
            $('input', elem).show().select();

            $('input', elem).click(function(e){
                e.stopPropagation();
            });

            $('html').click(function(){
                if(!lastValid) onValid(elem);
            });

            $('html').keypress(function(e){
                var touche = (e.keyCode) ? e.keyCode : e.which;

                if(touche == 13){
                    if(!lastValid) onValid(elem);
                }
            });

            function onValid(elem){
                lastValid = true;
                $('input', elem).hide();
                $('a .label', elem).show();
                $('html').unbind();

                tmpId = elem.parent().parent('.files-folder').attr('id');
                if(tmpId != 'root'){
                    elemParentArbre = $('#arbre_'+tmpId, $$);
                    urlParent = elemParentArbre.attr('href');
                } else {
                    elemParentArbre = $('.root', $$);
                    urlParent = root;
                }

                elemURL = $('.label', elem).parent().attr('rel');
                elemURL = elemURL.replace(root, '');
                elemURL = elemURL.replace(/\//g, '-');
                elemArbre = $('#arbre_'+elemURL);
                
                name = $('.label', elem).text().replace(/\ /g, '');
                newName = $('input', elem).val().replace(/\ /g, '_');
                
                if(newName != '' && newName != '' && newName != null && newName != name){
                    elem.find('img').fadeIn(300);
                    
                    $.post('./ajax/arbre.php', {action: 'rename', oldUrl: urlParent+name+'/', newUrl: urlParent+newName+'/', newName:newName}, function(data){
                        if(data != null && data['erreur'] == 'true'){
                            newName = data['newName'];
                            elem.parent().flashValid();
                            
                            // Changement des noms des dossiers
                            elemArbre.empty().append(newName);
                            $('.label', elem).empty().append(newName);

                            // Changement de l'URL du label
                            elemURL = urlParent.replace(root, '');
                            elemURL = elemURL.replace(/\//g, '-');
                            elemURL = elemURL.substring(0, elemURL.length-1);
                            $('.label', elem).parent().attr('rel', urlParent+newName);

                            // Changement des id
                            elemID = $('.label', elem).parent().attr('rel');
                            elemID = elemID.replace(root, '');
                            elemID = elemID.replace(/\//g, '-');
                            elemID = 'arbre_'+elemID;
                            elemArbre.attr('id', elemID);
                            
                            // Changement des URL des sous-dossiers et fichiers + id
                            $('a.label', elemArbre.parent().parent()).each(function(){
                                // On récupère le files-folder dans l'explorer
                                elemURL = $(this).attr('href');
                                elemURL = elemURL.replace(root, '');
                                elemURL = elemURL.replace(/\//g, '-');
                                elemURL = elemURL.substring(0, elemURL.length-1);
                                oldElemURL = $('#'+elemURL);

                                $('.file-apercu', oldElemURL).each(function(){
                                    $(this).attr('src', $(this).attr('src').replace(urlParent+name, urlParent+newName));
                                });
                                $('.file_name a', oldElemURL).each(function(){
                                    $(this).attr('rel', $(this).attr('rel').replace(urlParent+name, urlParent+newName));
                                });

                                // On met l'url à jour
                                $(this).attr('href', $(this).attr('href').replace(urlParent+name, urlParent+newName));

                                elemURL = $(this).attr('href');
                                elemURL = elemURL.replace(root, '');
                                elemURL = elemURL.replace(/\//g, '-');
                                elemURL = elemURL.substring(0, elemURL.length-1);
                                $(this).attr('id', 'arbre_'+elemURL);
                                oldElemURL.attr('id', elemURL);
                            });
                            $('input', elem).empty().val(newName);
                        } else {
                            var erreur = "Erreur";
                            if(data == null) {erreur = msgErrServ} else {erreur = data['erreur']}
                            addMessage('error', erreur);
                            $('input', elem).empty().val(name);
                        }
                        elem.find('img').fadeOut(750);
                    }, 'json');
                }
            }

            return false;
        }
        function newDossier(){
            var elem = null;
            if($('.files-folder:visible .file[rel=folder]', files).size() > 0){
                $('.files-folder:visible .file[rel=folder]:last', files).after($('#dossierVide').html());
                elem = $('.files-folder:visible .file[rel=folder]:last', files).addClass('fd_clair');
            } else if($('.files-folder:visible .file', files).size() > 0){
                $('.files-folder:visible .file:first', files).after($('#dossierVide').html());
                elem = $('.files-folder:visible .file:first', files).addClass('fd_clair');
            } else {
                $('.files-folder:visible', files).append($('#dossierVide').html());
                elem = $('.files-folder:visible .file:first', files).addClass('fd_clair');
                $('.files-folder:visible .empty-message', files).slideUp();
            }
            coloFdFiles();
            scrollGoto(elem, -170);

            lastValid = true;
            
            $('a .label', elem).hide();
            $('input', elem).show().select();

            $('input', elem).click(function(e){
                e.stopPropagation();
            });

            $('html').click(function(){
                if(!lastValid) onValid(elem);
            });

            $('html').keypress(function(e){
                var touche = (e.keyCode) ? e.keyCode : e.which;

                if(touche == 13){
                    if(!lastValid) onValid(elem);
                }
            });

            function onValid(elem){
                lastValid = true;
                name = $('input', elem).val();
                $('input', elem).hide();
                $('html').unbind();
            
                if(name != '' && name != '' && name != null){
                    $('a .label', elem).empty().append(name).fadeTo(200, 0.5);
                    elem.find('img').fadeIn(300);
                    
                    var id = '';
                    tmpId = elem.parent('.files-folder').attr('id');
                    if(tmpId != 'root'){
                        elemParentArbre = $('#arbre_'+tmpId, $$);
                        urlParent = elemParentArbre.attr('href');
                        elemParentArbre = elemParentArbre.parent().parent();
                        id = tmpId+'-';
                    } else {
                        elemParentArbre = $('.root', $$);
                        urlParent = root;
                    }
                    var urlDir = urlParent + name;

                    $.post('./ajax/arbre.php', {action: 'newDir', name: name, url: urlDir}, function(data){
                        if(data != null && data['erreur'] == 'true'){
                            elem.flashValid();

                            // Arbre
                            if($('ul', elemParentArbre).size() == 0){
                                elemParentArbre.append('<ul></ul>');
                                $('span:first', elemParentArbre).append('<div class="plus ico">Plus</div>');
                                $('span:first div.plus', elemParentArbre).click(function(e){
                                    $('html').trigger('click');
                                    var elem = $(this).parent().parent();
                                    $('ul:first', elem).slideToggle();
                                    elem.toggleClass('ouvert');
                                    e.stopPropagation();
                                    return false;
                                });
                                explore(elemParentArbre);
                            }
                            $('ul:first', elemParentArbre).slideDown(200);
                            $('ul:first', elemParentArbre).append('<li class="dossier lastAdded" style="display: none;"><span><a href="'+data['url']+'/" id="arbre_'+id+data['name']+'" class="label">'+data['name']+'</a></span>');
                            $('ul:first .lastAdded', elemParentArbre).click(function(e){ // Depuis l'arbre
                                var elem = $(this);
                                $('.explore', $$).each(function(){
                                    $(this).removeClass('explore');
                                });
                                elem.addClass('explore');

                                explore(elem);

                                e.stopPropagation();
                                return false;
                            });
                            $('ul:first .lastAdded', elemParentArbre).slideDown(250).removeClass('lastAdded');

                            
                            // Explorer
                            $('a .label', elem).empty().append(data['name']).fadeTo(200, 1);
                            $('input', elem).val(data['name']);
                            $('.folder_link', elem).attr('rel', data['url']);
                            $('a[href=#check_file]', elem).click(function(){
                                checkElem($(this));
                                return false;
                            });
                            $('.folder_link').single_double_click(function(){ // Depuis l'explorer
                                openFolder($(this)); //--------------> Ouvre le dossier
                            }, function(){
                                if($(this).parent().parent().hasClass('to_parent')){
                                    openFolder($(this)); //--------------> Remonte au parent
                                } else {
                                    renameDossier($(this).parent()); //--> Renomme le dossier
                                }
                            });
                            $('a[href=#folder-rename]', elem).click(function(){
                                $('html').trigger("click");
                                renameDossier($(this).parent().parent().parent().parent());
                                return false;
                            });
                            $('a[href=#selection-del]', elem).click(function(){
                                supprElem($(this).parent().parent().parent().parent());
                                return false;
                            });

                            if(all_ckeck){
                                all_ckeck = false;
                                $('a[href=#check_all_files]').trigger('click');
                            }
                            nbCheckedChange();
                        } else {
                            var erreur = "Erreur";
                            if(data == null) {erreur = msgErrServ} else {erreur = data['erreur']}
                            addMessage('error', erreur);
                            elem.slideUp(250, function(){$(this).remove();});
                        }
                        elem.find('img').fadeOut(750);
                    }, 'json');
                } else {
                    elem.remove();
                    coloFdFiles();
                }
            }
            
            lastValid = false;
            return false;
        }
        function newEmptyDossier(id){
            $('#files').append('<div id="'+id+'" class="files-folder empty-folder"><span class="empty-message">- Ce dossier est vide -</span></div>');
            $('#'+id).prepend($('#to_parent').html());
            var url = $('#arbre_'+id).parent().parent().parent().parent().find('span:first .label').attr('href');
            url = url.substring(0, url.length-1);
            $('.to_parent .folder_link', $('#'+id)).attr('rel', url);
            $('.to_parent .folder_link', $('#'+id)).click(function(){
                openFolder($(this));
            });
            coloFdFiles();
        }

        function newCSS(){
            $('.files-folder:visible .file:last', files).after($('#newFile').html());
            var elem = $('.files-folder:visible .file:last', files).addClass('fd_clair');
            coloFdFiles();
            scrollGoto(elem, -170);

            lastValid = true;

            $('a .label', elem).hide();
            $('input', elem).show().select();

            $('input', elem).click(function(e){
                e.stopPropagation();
            });

            $('html').click(function(){
                if(!lastValid) onValid(elem);
            });

            $('html').keypress(function(e){
                var touche = (e.keyCode) ? e.keyCode : e.which;

                if(touche == 13){
                    if(!lastValid) onValid(elem);
                }
            });

            function onValid(elem){
                lastValid = true;
                name = $('input', elem).val();
                $('input', elem).hide();
                $('html').unbind();

                if(name != '' && name != '' && name != null){
                    $('a .label', elem).empty().append(name).fadeTo(200, 0.5);
                    elem.find('img').fadeIn(300);
                    
                    tmpId = elem.parent('.files-folder').attr('id');
                    if(tmpId != 'root'){
                        elemParentArbre = $('#arbre_'+tmpId, $$);
                        urlParent = elemParentArbre.attr('href');
                        elemParentArbre = elemParentArbre.parent().parent();
                    } else {
                        elemParentArbre = $('.root', $$);
                        urlParent = root;
                    }
                    var urlFile = urlParent + name;

                    $.post('./ajax/arbre.php', {action: 'newCss', name: name, url: urlFile}, function(data){
                        if(data != null && data['erreur'] == 'true'){
                            elem.flashValid();

                            // Explorer
                            $('a .label', elem).empty().append(data['name']+'.css').fadeTo(200, 1);
                            $('input', elem).val(data['name']);
                            $('.file_link', elem).attr('rel', data['url']+'.css');
                            $('a[href=#check_file]', elem).click(function(){
                                checkElem($(this));
                                return false;
                            });
                            $('.file_link', elem).single_double_click(function(){ // Depuis l'explorer
                                edit($(this).attr('rel'), $('.label', $(this)).text());
                            }, function(){
                                renameFile($(this).parent()); //--> Renomme le fichier
                            });
                            $('a[href=#selection-edit]', elem).click(function(){
                                $('html').trigger('click');
                                var link_elem = $(this).parent().parent().parent().parent();
                                link_elem = $('.file_name a', elem);
                                edit(link_elem.attr('rel'), $('.label', link_elem).text());
                                return false;
                            });
                            $('a[href=#file-rename]', elem).click(function(){
                                $('html').trigger("click");
                                renameDossier($(this).parent().parent().parent().parent());
                                return false;
                            });
                            $('a[href=#selection-del]', elem).click(function(){
                                supprElem($(this).parent().parent().parent().parent());
                                return false;
                            });
                        } else {
                            var erreur = "Erreur";
                            if(data == null) {erreur = msgErrServ} else {erreur = data['erreur']}
                            addMessage('error', erreur);
                            elem.slideUp(250, function(){$(this).remove();});
                        }
                        elem.find('img').fadeOut(750);
                    }, 'json');
                } else {
                    elem.remove();
                    coloFdFiles();
                }
            }

            lastValid = false;
            return false;
        }


        $('a[href^=#all_maj], a[href^=#mass_dir_maj], a[href^=#dir_maj], a[href^=#file_maj]').click(function(){
            rapport_maj($(this));
            return false;
        });
        function rapport_maj(elem){
            showBigLoader('Mise à jour en cours...');
            var type = $(elem).attr('href').replace('_maj', '').replace('#', '');
            var url_from = '';
            var url_to = root;
            if(type == 'dir' || type == 'file'){
                url_from = $(elem).parent().find('.elem_name').attr('href');
                url_to = $(elem).parent().find('.elem_name').attr('rel');
            }
            $.post('./ajax/arbre.php', {action: 'maj', type: type, urlFrom: url_from, urlTo: url_to}, function(data){
                if(data != null && data['erreur'] == 'true'){
                    changeSize(data['pourcent'], data['sizeDesign']);
                    // Rafraichissement du rapport
                    if(data['rapport'] != null){
                        $('#rapport').empty().append(data['rapport']);
                        $('#rapport_pourcent').empty().append(data['pourcent_complet']);
                        $('a.zoombox').zoombox();
                        $('a[href^=#all_maj], a[href^=#mass_dir_maj], a[href^=#dir_maj], a[href^=#file_maj]').click(function(){
                            rapport_maj($(this));
                            return false;
                        });
                    }
                    // Ajout du/des élément(s) de la MAJ
                    if(data['to_create'] != null){
                        var el = null;
                        var elemToExplore = null;
                        for(var i = 0; i < (data['to_create']).length; i++){
                            el = data['to_create'][i];
                            if(el['type'] == 'folder'){
                                $('.files-folder', files).each(function(){
                                    $(this).hide();
                                });
                                var addRel = false;
                                if($('#'+el['parent'], files).size() == 0){
                                    addRel = true;
                                    $('#files').append('<div id="'+el['parent']+'" class="files-folder empty-folder dn"><span class="empty-message">- Ce dossier est vide -</span></div>');
                                    $('#'+el['parent']).prepend($('#to_parent').html());
                                }

                                if($('#'+el['parent']+' .file[rel=folder]', files).size() > 0){
                                    $('#'+el['parent']+' .file[rel=folder]:last', files).after($('#dossierVide').html());
                                    elem = $('#'+el['parent']+' .file[rel=folder]:last', files).addClass('fd_clair');
                                } else if($('#'+el['parent']+' .file', files).size() > 0){
                                    $('#'+el['parent']+' .file:first', files).after($('#dossierVide').html());
                                    elem = $('#'+el['parent']+' .file:first', files).addClass('fd_clair');
                                } else {
                                    $('#'+el['parent'], files).append($('#dossierVide').html());
                                    elem = $('#'+el['parent']+' .file:first', files).addClass('fd_clair');
                                    $('#'+el['parent']+' .empty-message', files).slideUp();
                                }
                                coloFdFiles();

                                // Arbre
                                if(el['parent'] == 'root'){
                                    elemParentArbre = $('#folder-root');
                                } else {
                                    elemParentArbre = $('#arbre_'+el['parent']).parent().parent();
                                }
                                if($('ul', elemParentArbre).size() == 0){
                                    elemParentArbre.append('<ul></ul>');
                                    $('span:first', elemParentArbre).append('<div class="plus ico">Plus</div>');
                                    $('span:first div.plus', elemParentArbre).click(function(e){
                                        $('html').trigger('click');
                                        var elem = $(this).parent().parent();
                                        $('ul:first', elem).slideToggle();
                                        elem.toggleClass('ouvert');
                                        e.stopPropagation();
                                        return false;
                                    });
                                }
                                explore(elemParentArbre);
                                $('ul:first', elemParentArbre).slideDown(200);
                                if(el['parent'] != 'root'){
                                    elemToExplore = '#arbre_'+el['parent']+'-'+el['name'];
                                    $('ul:first', elemParentArbre).append('<li class="dossier lastAdded" style="display: none;"><span><a href="'+el['url']+'/" id="arbre_'+el['parent']+'-'+el['name']+'" class="label">'+el['name']+'</a></span>');
                                } else {
                                    elemToExplore = '#arbre_'+el['name'];
                                    $('ul:first', elemParentArbre).append('<li class="dossier lastAdded" style="display: none;"><span><a href="'+el['url']+'/" id="arbre_'+el['name']+'" class="label">'+el['name']+'</a></span>');
                                }
                                $('ul:first .lastAdded', elemParentArbre).click(function(e){ // Depuis l'arbre
                                    var elemArbre = $(this);
                                    $('.explore', $$).each(function(){
                                        $(this).removeClass('explore');
                                    });
                                    elemArbre.addClass('explore');
                                    explore(elemArbre);

                                    e.stopPropagation();
                                    return false;
                                });
                                $('ul:first .lastAdded', elemParentArbre).slideDown(250).removeClass('lastAdded');

                                if(addRel){
                                    var url = $('#arbre_'+el['parent']).parent().parent().parent().parent().find('span:first .label').attr('href');
                                    url = url.substring(0, url.length-1);
                                    $('.to_parent .folder_link', $('#'+el['parent'])).attr('rel', url);
                                    $('.to_parent .folder_link', $('#'+el['parent'])).click(function(){
                                        openFolder($(this));
                                    });
                                    coloFdFiles();
                                }

                                // Explorer
                                $('a .label', elem).empty().append(el['name']).fadeTo(200, 1);
                                $('input', elem).val(el['name']);
                                $('.folder_link', elem).attr('rel', el['url']);
                                $('.file_size', elem).empty().append(el['size']);
                                $('a[href=#check_file]', elem).click(function(){
                                    checkElem($(this));
                                    return false;
                                });
                                $('.folder_link', elem).single_double_click(function(){ // Depuis l'explorer
                                    openFolder($(this)); //--------------> Ouvre le dossier
                                }, function(){
                                    if($(this).parent().parent().hasClass('to_parent')){
                                        openFolder($(this)); //--------------> Remonte au parent
                                    } else {
                                        renameDossier($(this).parent()); //--> Renomme le dossier
                                    }
                                });
                                $('a[href=#folder-rename]', elem).click(function(){
                                    $('html').trigger("click");
                                    renameDossier($(this).parent().parent().parent().parent());
                                    return false;
                                });
                                $('a[href=#selection-del]', elem).click(function(){
                                    supprElem($(this).parent().parent().parent().parent());
                                    return false;
                                });

                                if(all_ckeck){
                                    all_ckeck = false;
                                    $('a[href=#check_all_files]').trigger('click');
                                }
                                nbCheckedChange();
                            } else {
                                setTimeout(function(){
                                    $('#'+el['parent']).append($('#newFile').html());
                                    elemToExplore = '#arbre_'+el['parent'];
                                    coloFdFiles();
                                    var elem = $('#'+el['parent']+' .file:last');

                                    // Explorer
                                    $('.file_apercu', elem).attr('src', el['url']);
                                    $('a .label', elem).empty().append(el['name']+'.'+el['extension']).fadeTo(200, 1);
                                    $('input', elem).val(el['name']);
                                    $('.file_link', elem).attr('rel', el['url']);
                                    $('.file_link', elem).attr('ext', el['extension']);
                                    $('.file_link', elem).removeClass('code').addClass(el['type']);
                                    $('.file_link span.ico', elem).removeClass('code').addClass(el['type']);
                                    if(el['type'] != 'code'){
                                        $('.actions-edit', elem).remove();
                                        $('.actions-rename', elem).addClass('action-first-elem');
                                    } else {
                                        $('a[href=#selection-edit]', elem).click(function(){
                                            $('html').trigger('click');
                                            var link_elem = $('.file_name a', elem);
                                            edit(link_elem.attr('rel'), $('.label', link_elem).text());
                                            return false;
                                        });
                                    }
                                    $('.file_size', elem).empty().append(el['size']);
                                    $('a[href=#check_file]', elem).click(function(){
                                        checkElem($(this));
                                        return false;
                                    });
                                    $('.file_link', elem).single_double_click(function(){ // Depuis l'explorer
                                        if($(this).attr('class').replace('di file_link ', '') == 'img'){
                                            $.zoombox.open($(this).attr('rel')); //--------------> Ouvre l'image dans zoombox
                                        } else if($(this).attr('class').replace('di file_link ', '') == 'code'){
                                            edit($(this).attr('rel'), $('.label', $(this)).text());
                                        }
                                    }, function(){
                                        renameFile($(this).parent()); //--> Renomme le fichier
                                    });
                                    $('a[href=#file-rename]', elem).click(function(){
                                        $('html').trigger("click");
                                        renameDossier($(this).parent().parent().parent().parent());
                                        return false;
                                    });
                                    $('a[href=#selection-del]', elem).click(function(){
                                        supprElem($(this).parent().parent().parent().parent());
                                        return false;
                                    });
                                }, 10);
                            }
                        }
                        if(elemToExplore != null){
                            $('.files-folder', files).each(function(){
                                $(this).hide();
                            });
                            $(elemToExplore).trigger('click');
                            elemToExplore = null;
                        }
                    }
                    hideBigLoader();
                } else if(data != null && data['erreur'] == 'refresh'){
                    javascript:location.reload();
                } else {
                    hideBigLoader();
                    var erreur = "Erreur";
                    if(data == null) {erreur = msgErrServ} else {erreur = data['erreur']}
                    addMessage('error', erreur);
                }
            }, 'json');
        }


        function coloFdFiles(){
            var fd = 'fd_fonce';
            var folder = $('.files-folder:visible', files);
            $('.file', folder).each(function(){
                $(this).removeClass('fd_clair').removeClass('fd_fonce').addClass(fd);
                fd = (fd == 'fd_fonce') ? 'fd_clair' : 'fd_fonce';
            });

            if($('.file', folder).size() > 1){
                $('.empty-message', folder).hide();
            } else {
                if($('#root').is(':visible')){
                    $('.empty-message', folder).hide();
                } else {
                    $('.empty-message', folder).show();
                }
            }
        }

        function edit(url, filename){
            $('a[rel=editeur-save]', editeur).unbind();
            $('a[rel=editeur-save]', editeur).click(function(){
                saveFile();
            });

            var lastActive = ($('li.active').size() == 1) ? $('li.active a').attr('href') : null;
            if(lastActive != null){
                filesOpened[lastActive].code = editor.getCode();
                filesOpened[lastActive].history = editor.getHistory();
            }

            $('li', $('#fichiers_ouverts')).each(function(){
                $(this).removeClass('active');
                $('a', $(this)).each(function(){
                    $(this).unbind();
                });
            });
            
            if(filesOpened[url] == undefined){
                filesOpened[url] = {
                    code: null,
                    history: null
                };
                $('#fichiers_ouverts').append('<li class="active"><a href="'+url+'" class="fl" rel="editeur-file">'+filename+'<span class="dn">*</span></a><a href="javascript:void();" class="closeFile" title="Fermer ce fichier" style="float: left;"></a></li>');
                $('.CodeMirror-wrapping').loader('Chargement du fichier...');

                $.post('./ajax/arbre.php', {action: 'loadFile', url: url}, function(data){
                    $('.CodeMirror-wrapping').unload();
                    if(data != null && data['erreur'] == 'true'){
                        editor.setCode(data['fichier']);
                        editor.reindent();
                        setTimeout(function(){
                            $('li.active a span', $('#fichiers_ouverts')).hide();
                        }, 800);
                    } else {
                        var erreur = "Erreur";
                        if(data == null){erreur = msgErrServ} else {erreur = data['erreur'];}
                        editor.setCode('/* '+erreur+' */');
                    }
                    filesOpened[url].code = editor.getCode();
                }, 'json');
            } else {
                $('a[href='+url+']', $('#fichiers_ouverts')).parent('li').addClass('active');
                editor.setCode(filesOpened[url].code);
                editor.setHistory(filesOpened[url].history);
            }

            if(editeur.hasClass('minimized')){
                $('.maximize', editeur).trigger('click');
            } else {
                editeur.fadeIn(200, function(){
                    $(window).trigger('resize');
                });
            }

            $('a[rel=editeur-file]', $('#fichiers_ouverts')).click(function(){
                edit($(this).attr('href'), $(this).text());
                return false;
            });
            $('.closeFile', $('#fichiers_ouverts')).click(function(){
                delete filesOpened[$('a[rel=editeur-file]', $(this).parent()).attr('href')];
                if($('li', $('#fichiers_ouverts')).size() == 1){
                    editor.setCode('');
                    $('.close', editeur).trigger('click');
                    $(this).parent().remove();
                } else {
                    if($(this).parent('li').hasClass('active')){
                        $(this).parent().remove();
                        edit($('li:last a:first', $('#fichiers_ouverts')).attr('href'), '');
                    } else {
                        $(this).parent().remove();
                    }
                }
                return false;
            });
        }

        function saveFile(){
            if($('li.active', $('#fichiers_ouverts')).size() == 1){
                $('a[rel=editeur-save]', editeur).next('img').fadeIn(100);
                var elem = $('li.active a', $('#fichiers_ouverts'));
                var url = elem.attr('href');
                filesOpened[url].code = editor.getCode();
                var file = filesOpened[url].code;
                $.post('./ajax/arbre.php', {action: 'saveFile', url: url, fichier: file}, function(data){
                    if(data != null && data['erreur'] == 'true'){
                        $('a[rel^="'+url+'"]', files).parent().parent().find('.file_size').empty().append(data['size']);
                        $('li.active a span', $('#fichiers_ouverts')).hide();
                        $('a[rel=editeur-save] .ico', editeur).removeClass('save').addClass('for-selection');
                        $('#outils_editeur li:first', editeur).flashValid();
                        setTimeout(function(){
                            $('a[rel=editeur-save] .ico', editeur).addClass('save').removeClass('for-selection');
                        }, 750);
                    } else {
                        $('a[rel=editeur-save] .ico', editeur).removeClass('save').addClass('selection-del');
                        $('#outils_editeur li:first', editeur).flashValid(false);
                        setTimeout(function(){
                            $('a[rel=editeur-save] .ico', editeur).addClass('save').removeClass('selection-del');
                        }, 750);
                        $('.minimize', editeur).trigger('click');

                        var erreur = "Erreur";
                        if(data == null){erreur = msgErrServ} else {erreur = data['erreur'];}
                        addMessage('error', erreur);
                    }
                    $('a[rel=editeur-save]', editeur).next('img').fadeOut(300);
                }, 'json');
            }
        }
        coloFdFiles();




        /******************************************************************************
         * Edition titre et description
         ******************************************************************************/
        var div_edit = $('#proprietes_design .description');

        $('a[href^=#edit-titre]').click(function(){
            if($('#annul_edit', div_edit).is(':visible')){
                $('#annul_edit', div_edit).trigger('click');
            }
            $('b', div_edit).hide();
            $('input', div_edit).show();
            $('#valid_edit', div_edit).show();
            $('#annul_edit', div_edit).show();

            $('input', div_edit).click(function(e){
                e.stopPropagation();
            });

            $('html').click(function(){
                $('#valid_edit', div_edit).trigger('click');
            });

            $('html').keypress(function(e){
                var touche = (e.keyCode) ? e.keyCode : e.which;

                if(touche == 13){
                    $('#valid_edit', div_edit).trigger('click');
                }
            });

            $('#valid_edit', div_edit).click(function(){
                $('html').unbind();
                $('#valid_edit', div_edit).unbind();
                $('#annul_edit', div_edit).unbind().hide();
                $('#valid_edit', div_edit).css('width', '95px');
                $('#valid_edit img', div_edit).show();

                var newName = $('input', div_edit).val();
                var idd = $('#id_design', div_edit).text();

                $.post('./ajax/design.php', {action: 'changer_nom', titre: newName, id: idd}, function(data){
                    if(data != null && data['erreur'] == 'true'){
                        $('.titre_design_'+idd).each(function(){
                            $(this).empty().append(data['titre']);
                        });
                        document.title = 'zExplorer, '+data['titre']+' :: zDesigns.fr';
                    } else {
                        $('input', div_edit).val($('b', div_edit).text());
                        var erreur = "Erreur";
                        if(data == null){erreur = msgErrServ} else {erreur = data['erreur'];}
                        addMessage('error', erreur);
                    }
                    $('#valid_edit img', div_edit).hide();
                    $('#valid_edit', div_edit).css('width', '');
                    desactiveEdit();
                }, 'json');

                return false;
            });
            $('#annul_edit', div_edit).click(function(){
                desactiveEdit();
                $('input', div_edit).val($('b', div_edit).text());
                return false;
            });
            return false;
        });
        $('a[href^=#edit-description]').click(function(){
            if($('#annul_edit', div_edit).is(':visible')){
                $('#annul_edit', div_edit).trigger('click');
            }
            $('p', div_edit).hide();
            $('textarea', div_edit).show();
            $('#valid_edit', div_edit).show();
            $('#annul_edit', div_edit).show();

            $('#valid_edit', div_edit).click(function(){
                $('#valid_edit', div_edit).unbind();
                $('#annul_edit', div_edit).unbind().hide();
                $('#valid_edit', div_edit).css('width', '95px');
                $('#valid_edit img', div_edit).show();

                var newDescription = $('textarea', div_edit).val();
                var idd = $('#id_design', div_edit).text();

                $.post('./ajax/design.php', {action: 'changer_description', description: newDescription, id: idd}, function(data){
                    if(data != null && data['erreur'] == 'true'){
                        $('p', div_edit).empty().append(data['description']);
                    } else {
                        $('textarea', div_edit).val($('p', div_edit).text());
                        var erreur = "Erreur";
                        if(data == null){erreur = msgErrServ} else {erreur = data['erreur'];}
                        addMessage('error', erreur);
                    }
                    $('#valid_edit img', div_edit).hide();
                    $('#valid_edit', div_edit).css('width', '');
                    desactiveEdit();
                }, 'json');

                return false;
            });
            $('#annul_edit', div_edit).click(function(){
                desactiveEdit();
                $('textarea', div_edit).val($('p', div_edit).text());
                return false;
            });
            return false;
        });
        function desactiveEdit(){
            $('b', div_edit).show();
            $('input', div_edit).hide();

            $('p', div_edit).show();
            $('textarea', div_edit).hide();

            $('#valid_edit', div_edit).hide();
            $('#annul_edit', div_edit).hide();

            $('#valid_edit', div_edit).unbind();
            $('#annul_edit', div_edit).unbind();
        }


        $('a[href^=#changer-cat]').click(function(){
            $('#cat_select').fadeIn(200);
            return false;
        });
        $('#cat_select form').submit(function(){
            var idd = $('#id_design', div_edit).text();
            var cat = $('#cat_select form select').val();
            $.post('./ajax/design.php', {action: 'changer_cat', cat: cat, id: idd}, function(data){
                $('#cat_select .aplat').trigger('click');
                if(data != null && data['erreur'] == 'true'){
                    $('#cat_design, #cat_design_actuel').empty().append(data['cat']);
                } else {
                    var erreur = "Erreur";
                    if(data == null){erreur = msgErrServ} else {erreur = data['erreur'];}
                    addMessage('error', erreur);
                }
            }, 'json');
            return false;
        });


        $('a[href^=#uploadapercu]').click(function(){
            $('#upload_apercu').fadeIn(200);
            return false;
        });
    });
}



$(window).bind('resize', function(){redim();});
function redim(){
    $('.toPopup.resizable').each(function(){
        var elem = $(this);
        if(elem.is(':visible')){
            if(elem.hasClass('normal')){
                $('.aplat', elem).css('display', 'block');
                $('.boxPopUp', elem).css({
                    'position': 'fixed',
                    'min-height': 'inherit',
                    height: ($('.aplat', elem).height()-180)+'px',
                    width: ($('.aplat', elem).width()-100)+'px',
                    left: '50%',
                    'top': '200px',
                    'margin-left': '-'+($('.aplat', elem).width()-100)/2+'px',
                    'margin-top': '-75px'
                });
                if(elem.attr('id') == 'editeur_css'){
                    $('#editeur_css_in', elem).css({height: ($('.aplat', elem).height()-267)+'px'});
                }
            } else if(elem.hasClass('minimized')){
                $('.aplat', elem).css('display', 'none');
                $('.boxPopUp', elem).css({
                    'position': 'fixed',
                    'min-height': 'inherit',
                    top: 'inherit',
                    height: '20px',
                    width: '280px',
                    bottom: '35px',
                    left: '7px',
                    'margin-left': '0'
                });
            } else if(elem.hasClass('maximized')){
                $('.aplat', elem).css('display', 'block');
                $('.boxPopUp', elem).css({
                    'position': 'fixed',
                    'min-height': 'inherit',
                    height: '100%',
                    width: '100%',
                    top: '0',
                    left: '50%',
                    'margin-top': '0',
                    'margin-left': '-50%'
                });
                if(elem.attr('id') == 'editeur_css'){
                    $('#editeur_css_in', elem).css({height: ($('.aplat', elem).height()-90)+'px'});
                }
            }
        }
    });
}
})(jQuery);

function changeSize(pourcent, size){
    animJauge(pourcent);
    $('#size_design').empty().append(size);
}
// Anime la jauge en fonction d'un pourcentage
function animJauge(pourcentage, duration){
    duration = (duration == null) ? 1000 : duration;
    pourcentage = (pourcentage > 100) ? 100 : pourcentage;
    var largeur = $('#jauge_design').css('width').replace('px', '');
    var width = (largeur/100)*pourcentage;
    var color = colJauge(pourcentage);

    $('#jauge_design_in').animate({
        'width': width,
        'background-color': color
    }, duration, 'easeInOutBack', function(){
        $('#jauge_design_in').css('background-color', color);
    });

    if(pourcentage >= 90){
        $('#jauge_design .infos').addClass('maxSize');
    } else {
        $('#jauge_design .infos').removeClass('maxSize');
    }
    $('#jauge_design .fr').empty().append(pourcentage+'%');
}

// Fonction équivalente à celle en PHP pour
// renvoyer la couleur de la barre en fonction
// du pourcentage
function colJauge(pourcent){
    var colors = new Array();
    colors[0] = '#8aff00';
    colors[10] = '#8aff00';
    colors[20] = '#a2ff00';
    colors[30] = '#b4ff00';
    colors[40] = '#ccff00';
    colors[50] = '#e4ff00';
    colors[60] = '#fff600';
    colors[70] = '#ffde00';
    colors[80] = '#ff9600';
    colors[90] = '#ff6000';
    colors[100] = '#ff0000';

    var col = colors[10];
    col = colors[Math.round(pourcent / 10) * 10];
    return col;
}

function showBigLoader(label){
    $('#big_loader .label').empty().append(label);
    $('#big_loader').fadeIn(200);
}
function hideBigLoader(){
    $('#big_loader').fadeOut(200);
}