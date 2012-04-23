$(function(){
    /* Slider */
    $('a#slideGauche').click(function(){
        prevSlide();
        return false;
    });
    $('a#slideDroite').click(function(){
        nextSlide();
        return false;
    });
    $('#navbar div a').click(function(){
        gotoSlide($(this).attr('rel'));
        return false;
    });
});

/* Slider Accueil */
var stopDuration = 12000;
var animDuration = 1000;
var timerSlider = setTimeout("nextSlide()", stopDuration);
var slide = 1;
function nextSlide(){
    $('#navbar div a').removeClass('active');
    if(slide < 3){
        $('#slides').animate({marginLeft: '-=100%'}, animDuration, 'easeInOutQuad');
        slide++;
    } else {
        $('#slides').animate({marginLeft: '0'}, animDuration, 'easeInOutQuad');
        slide = 1;
    }
    $('#btn_slide'+slide).addClass('active');

    clearTimeout(timerSlider);
    timerSlider = setTimeout("nextSlide()", stopDuration);
}
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
function gotoSlide(goSlide){
    if(goSlide == slide){
        return false;
    } else {
        $('#navbar div a').removeClass('active');

        goPercent = (goSlide-1)*(-100);
        if(goPercent != 0) goPercent += '%';
        $('#slides').animate({marginLeft: goPercent}, animDuration, 'easeInOutQuad');
        slide = goSlide;

        $('#btn_slide'+slide).addClass('active');
        clearTimeout(timerSlider);
        timerSlider = setTimeout("nextSlide()", stopDuration);
    }
    return false;
}