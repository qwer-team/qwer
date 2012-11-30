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
        controlNav:false,
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
	$(window).scroll(function () {
		if (jQuery(this).scrollTop() > 100) {
			jQuery('#back-top').fadeIn();
		} else {
			jQuery('#back-top').fadeOut();
		}
	});
    $( '#slider-code' ).jcarousel({
            scroll: 1, 
            wrap: 'circular', 
            buttonNextHTML: ".nextBtn", 
            buttonPrevHTML: ".prevBtn", 
            animation: "slow" }
    );
     $("#showcase").awShowcase(
     {
            content_width:			702,
            content_height:			340,
            fit_to_parent:			false,
            auto:					false,
            interval:				3000,
            continuous:				false,
            loading:				true,
            tooltip_width:			200,
            tooltip_icon_width:		32,
            tooltip_icon_height:	32,
            tooltip_offsetx:		18,
            tooltip_offsety:		0,
            arrows:					false,
            buttons:				true,
            btn_numbers:			true,
            keybord_keys:			true,
            mousetrace:				false, /* Trace x and y coordinates for the mouse */
            pauseonover:			true,
            stoponclick:			true,
            transition:				'vslide', /* hslide/vslide/fade */
            transition_delay:		300,
            transition_speed:		500,
            show_caption:			'show', /* onload/onhover/show */
            thumbnails:				true,
            thumbnails_position:	'outside-last', /* outside-last/outside-first/inside-last/inside-first */
            thumbnails_direction:	'vertical', /* vertical/horizontal */
            thumbnails_slidex:		0, /* 0 = auto / 1 = slide one thumbnail / 2 = slide two thumbnails / etc. */
            dynamic_height:			false, /* For dynamic height to work in webkit you need to set the width and height of images in the source. Usually works to only set the dimension of the first slide in the showcase. */
            speed_change:			true, /* Set to true to prevent users from swithing more then one slide at once. */
            viewline:				false /* If set to true content_width, thumbnails, transition and dynamic_height will be disabled. As for dynamic height you need to set the width and height of images in the source. */
     });


});

$('#back-top a').click(function () {
        $('body,html').stop(false, false).animate({
                scrollTop: 0
        }, 800);
        return false;
});

