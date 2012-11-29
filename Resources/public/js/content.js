/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$( document ).ready(function() {

    $( '#index_slider' ).nivoSlider({
        effect: 'fold',
        slices:15,
        boxCols:8,
        boxRows:8,
        animSpeed:500, 
        pauseTime:5000,
        directionNav:false,
        directionNavHide:false,
        controlNav:true,
        captionOpacity:1			
    });
    $("#playPauseButton").click(function (e) {
        console.log($(this).data("link"));
        var slider = $($(this).data("link"));
        e.preventDefault();  
        var $button = $(this);
        if ($button.hasClass("show")) { 
            slider.data('nivoslider').stop();
            $button.toggleClass("show", false);     
            $(".slider-bg").animate({height:"30px"});  
            $(".hide-text").animate({opacity:"0"}); 
            $(".show-text").animate({opacity:"1"});    
        }else {       
            slider.data('nivoslider').start();  
            $button.toggleClass("show", true); 
            $(".slider-bg").animate({height:"530px"}); 
            $(".hide-text").animate({opacity:"1"});   
            $(".show-text").animate({opacity:"0"});              
        }   
    });   
});