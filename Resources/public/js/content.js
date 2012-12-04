/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$( document ).ready(function() {
    /**
     * Развертывлка меню
     */
    $('ul.sf-menu').superfish({
        delay:       700, 		// one second delay on mouseout 
        animation:   {opacity:'show',height:'show'}, // fade-in and slide-down animation 
        speed:       'normal',  // faster animation speed 
        autoArrows:  false,   // generation of arrow mark-up (for submenu) 
        dropShadows: false   // drop shadows (for submenu)
    });
    
    $(".fancybox").fancybox({
        'modal '        : true,
        'transitionIn'  : 'none',
        'transitionOut' : 'none'
    });
    
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
            animation: "slow"}
    );
    $("a[rel^='prettyPhoto']").prettyPhoto({
            animation_speed:'normal',
            slideshow:5000,
            autoplay_slideshow: false
    });




});

$('#back-top a').click(function () {        
        $('body,html').stop(false, false).animate({
                scrollTop: 0
        }, 1000);
        return false;
});
function InputReset(input){
    input.val('');
};
