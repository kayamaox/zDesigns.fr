/*
*   Document    : popup
*   Créé le     : 27 10 2010, 12:37:22
*   Auteur      : Alex-D
*   Client      : http://www.zdesigns.fr
*   Description :
*       Permet la création de pop-in dans la page de manière simplifié
*
*   Tous droits réservés
*   Copie partielle ou totale interdite
*   Pour toute demande d'utilisation : demodealex[a]gmail[pt]com
*/


(function($){
$.fn.popup = function(params, fonction){
    var options = {
        titre:'',
        height: 200,
        width: 400,
        autoClean: true,
        zIndexMin: 80,
        minimizable: false,
        maximizable: false
    }
    $.extend(options, params);

    var lastFormat = 'normal';

    var $$ = $(this);
    $$.addClass('toPopup');
    $$.hide();
    var originalContent = $$.html();
    $$.empty();
    $$.addClass('normal');

    var mini = (options.minimizable) ? '<a href="javascript:void();" class="minimize"></a>' : '';
    var maxi = (options.minimizable) ? '<a href="javascript:void();" class="maximize"></a>' : '';

    var content = '<div class="boxPopUp" style="min-height: '+options.height+'px; width: '+options.width+'px; margin-top: '+(-options.height/2)+'px; margin-left: '+(-options.width/2)+'px; z-index: '+(options.zIndexMin+1)+';"><div class="inboxPopUp" style="z-index: '+options.zIndexMin+';"><a href="javascript:void();" class="close"></a>'+maxi+mini+'<h1>'+options.titre+'</h1><div class="content">'+originalContent+'</div></div></div><div class="aplat" style="z-index: '+(options.zIndexMin-1)+';"></div>';
    $$.append(content);
    if(fonction != undefined){ fonction.call(); }

    if(options.minimizable){
        $('.minimize', $$).click(function(){
            $$.removeClass('normal');
            $$.removeClass('maximized');
            $$.addClass('minimized');
            $(window).trigger('resize');
        });
    }

    if(options.maximizable){
        $('.maximize', $$).click(function(){
            if($$.hasClass('minimized')){
                $$.removeClass('normal');
                $$.removeClass('minimized');
                $$.removeClass('maximized');
                $$.addClass(lastFormat);
            } else if($$.hasClass('maximized')){
                $$.removeClass('minimized');
                $$.removeClass('maximized');
                $$.addClass('normal');
                lastFormat = 'normal';
            } else {
                $$.removeClass('normal');
                $$.removeClass('minimized');
                $$.addClass('maximized');
                lastFormat = 'maximized';
            }
            $(window).trigger('resize');
        });
    }

    if(options.minimizable || options.maximizable){
        $$.addClass('resizable');
    }

    $('.close, .aplat', $$).click(function(){
        $$.fadeOut(250, function(){
            if(options.autoClean){
                $('.content', $$).empty().append(originalContent);
            }
        });
    });
}
})(jQuery);