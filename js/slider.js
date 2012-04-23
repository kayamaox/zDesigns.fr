/*
*   Document    : slider
*   Créé le     : 26 12 2010, 15:34:41
*   Auteur      : Alex-D
*   Client      : http://www.zdesigns.fr
*   Description :
*       Dynamicité du slider en accueil et animation "slice"
*
*   Tous droits réservés
*   Copie partielle ou totale interdite
*   Pour toute demande d'utilisation : demodealex[a]gmail[pt]com
*/


/* Centre les garnitures */
var largeurCorps = 0;
var largeurMin = 1000;
var rapport = 950/370;

var nbBandesVert = 15;
var largeurBande = parseInt(parseInt($('#apercuSlider').css('width'), 10)/nbBandesVert, 10)+1;
var idBande = 0;
var apercuSliderHeight = 0;
var apercuSliderWidth = 0;
var animSpeed = 500;
var timerBuff = 0;
var actualUrl = null;

var timerSlider = null;

$(function(){
    actualUrl = $('#Slide2 img').attr('src');

    /* Initialisation des transitions */
    for(var i=0; i<nbBandesVert; i++){
        $('#apercuSlideVert').append('<div class="slide-vert"><img src="" alt="" /></div>');
    }
    $('#apercuSlideVert .slide-vert img').attr('src', actualUrl);


    /* Initialisation des dimentions */
    function init(callback){
        largeurCorps = parseInt($('#corps').css('width'), 10);

        if(largeurCorps < largeurMin){
            var newLargeur = largeurCorps - 50;
            var marge = (largeurCorps-largeurMin+70);
            marge = (marge < 25) ? 25 : marge;
            $('#slider_conteneur').css('height', ((newLargeur/rapport)+marge)+'px');
            $('#slider').css({
                'width': newLargeur+'px',
                'height': newLargeur/rapport+'px',
                'margin-left': '-'+((newLargeur/2)+10)+'px'
            });
            $('#apercuSlider, #apercuSliderOmbre').css({
                'height': newLargeur/rapport+'px',
                'width': (newLargeur-237)+'px'
            });
            $('#volet_droit_slider').css('height', newLargeur/rapport+'px');
            $('#garnitureCentre').css('width', newLargeur);
            $('#garnituresIn').css('margin-left', '-'+((newLargeur+540)/2)+'px');
            $('#garnituresIn2').css('width', (newLargeur+540)+'px');
            $('#volet_droit_slider').addClass('smallScreen');
        } else {
            $('#slider_conteneur').css('height', '440px');
            $('#slider').css({
                'width': '950px',
                'height': '370px',
                'margin-left': '-485px'
            });
            $('#apercuSlider, #apercuSliderOmbre, #apercuSlideVertIn, #apercuSlideHorizIn').css({
                'height': '370px',
                'width': '713px'
            });
            $('#volet_droit_slider').css('height', '370px');
            $('#garnitureCentre').css('width', '950px');
            $('#garnituresIn').css('margin-left', '-745px');
            $('#garnituresIn2').css('width', '1490px');
            $('#volet_droit_slider.smallScreen').removeClass('smallScreen');
        }

        largeurBande = parseInt(parseInt($('#apercuSlider').css('width'), 10)/nbBandesVert, 10)+1;
        idBande = 0;
        apercuSliderHeight = parseInt($('#apercuSlider').css('height'), 10);
        apercuSliderWidth = parseInt($('#apercuSlider').css('width'), 10);
        $('#apercuSlider .slide-vert').each(function(){
            $(this).css({
                'height': '1px',
                'width': largeurBande,
                'opacity': '0.7'
            });
            $('img', $(this)).css({
                'width': apercuSliderWidth,
                'margin-left': '-'+(largeurBande*idBande)+'px'
            });
            idBande++;
        });

        $('#garnituresIn2').css('margin-left', largeurCorps/2);
        if(callback != undefined){ callback.call(); }
    }
    $(window).bind('resize', function(){ init(); });

    window.onload = function(){
        $('#sliderIn img.loadingSlider').fadeOut(200, function(){
            $('#sliderIn #activeSlide, #navbar').fadeIn(500, function(){
                timerSlider = setTimeout("nextSlide()", stopDuration);
                init(function(){
                    setTimeout(function(){
                        $('#garnitures').fadeIn(750);
                        window.clearTimeout();
                    }, 1100);

                    animer('sliceDown');
                });
            });
        });
    }

    
    /* Slider *//*
    $('a#slideGauche').click(function(){
        prevSlide();
        return false;
    });
    $('a#slideDroite').click(function(){
        nextSlide();
        return false;
    });
    */
    $('#navbar a').click(function(){
        gotoSlide($(this).attr('rel'));
        return false;
    });
});



var stopDuration = 12000;
var animDuration = 1000;
var slide = 2;
var goSlide = 2;
var positions = new Array(10, 278, 550);
function nextSlide(){
    if(slide < 3){
        goSlide++;
    } else {
        goSlide = 1;
    }
    gotoSlide(goSlide);
}
/*
function prevSlide(){
    $('#navbar div a').removeClass('active');
    if(slide > 1){
        $('#slides').animate({marginLeft: '+=100%'}, animDuration, 'easeInOutQuad');
        slide--;
    } else {
        $('#slides').animate({marginLeft: '-200%'}, animDuration, 'easeInOutQuad');
        slide = 3;
    }
    $('#btn_slide'+slide).addClass('active');
    clearTimeout(timerSlider);
    timerSlider = setTimeout("nextSlide()", stopDuration);
}
*/
function gotoSlide(goSlide){
    if(goSlide == slide){
        return false;
    } else {
        slide = goSlide;

        actualUrl = $('#Slide'+slide+' img').attr('src');
        $('#apercuSlideVert .slide-vert img').attr('src', actualUrl);
        animer('sliceDown');

        $('#Slide'+slide+' span').each(function(){
            $('#volet_droit_slider .'+$(this).attr('class')).empty().append($(this).html());
        });
        $('#Slide'+slide+' a').each(function(){
            $('#volet_droit_slider .'+$(this).attr('class')).attr('href', $(this).attr('href'));
        });
        $('#activeNavbar').animate({
            'margin-left': positions[slide-1]+'px'
        }, animDuration);
        
        clearTimeout(timerSlider);
        timerSlider = setTimeout("nextSlide()", stopDuration);
    }
    return false;
}



function animer(animName){
    switch(animName){
        case 'sliceDown':
            timerBuff = 0;
            $('#apercuSlideVert').show();
            $('#apercuSlider .slide-vert').each(function(){
                var elem = $(this);
                setTimeout(function(){
                    elem.animate({ height:apercuSliderHeight, opacity:'0.98' }, animSpeed);
                }, (timerBuff));
                timerBuff += 50;
            });
            window.setTimeout(function(){
                finAnim();
                $('#apercuSlider .slide-vert').css({
                    'height': '1px',
                    'opacity': '0.5'
                });
            }, (timerBuff+animSpeed));
            break;
    }

    function finAnim(){
        $('#apercuFond').attr('src', actualUrl).show();
        $('.transition').hide();
    }
}