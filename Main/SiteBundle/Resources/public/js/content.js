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
            autoplay_slideshow: false,
            theme: /*'dark_rounded',*/'pp_default',
            social_tools: false
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
$(".portfolio_filter").live("click", function(){
    var obj = $(this);
    var filter = obj.data("filter-param");
    var filter_value = obj.data("filter-value");
    $("#gallery").find(obj.data("link-object")).each(function(){
        if ($(this).data(filter) != filter_value )
                $(this).fadeOut("slow");
        else if ($(this).data(filter) == filter_value )
                $(this).fadeIn("slow");
        else if (filter_value == 'all')
                $(this).fadeIn("slow");
    });
});
$(".portfolio_filter_keywors").live("click", function(){
    var obj = $(this);
    var filter = obj.data("filter-param");
    var filter_value = obj.data("filter-value");    
    var arr = [];
    $("#gallery").find(obj.data("link-object")).each(function(){
        arr = $(this).data(filter).toString().split(",");
        if ($.inArray(String(filter_value), arr) == -1 ){
                $(this).fadeOut("slow");
        }else
                $(this).fadeIn("slow");            
    });
    
});
$(".obslug").live("click", function(){
    var link = $("#hidden_story");
    if ( link.is(":hidden") )
           link.slideDown(1000);
       else
           link.slideUp(500);    
    var objClass = "open";
    if ($(".changeCss").hasClass(objClass))
        $(".changeCss").removeClass(objClass);
    else
        $(".changeCss").addClass(objClass);
});/*
$(".show_target_toggle").live("click", function(){
    var link = $($(this).data("link"));
    if ( link.is(":hidden") )
        link.slideDown(1000);
    else
        link.slideUp(500);
});
$(".changeCss").live("click", function(){
    var objClass = $(this).data("class");
    //console.log(objClass);
    if ($(this).hasClass(objClass))
        $(this).removeClass(objClass);
    else
        $(this).addClass(objClass);
});
$(".trigger_click").live("click", function(){
    var link = $($(this).data("trigger-link"));
    link.trigger("click");
});
/*
$(".portfolio_filter").live("click", function(){
    var obj = $(this);
    var filter = obj.data("filter-param");
    var filter_value = obj.data("filter-value");
    var i = 0;
    var key = 0;
    var arr_obj;
    
    $("#gallery").find(obj.data("link-object")).each(function(){
        if ($(this).data(filter) != filter_value ){
            $(this).addClass("transparent");
            if( pClick == 0)
                $(this).animate({"opacity": 0 });
        }
        else if ($(this).data(filter) == filter_value ){
            $(this).show();
            $(this).removeClass("transparent");
        }
        if ( pClick != 0){            
            $(this).animate({"opacity": 0});
            if ($(this).is(":hidden"))
                $(this).show();                
        }
    });
    var count = $("#gallery").find(obj.data("link-object")+".transparent").length;
    $("#gallery").find(obj.data("link-object")).delay(300).each(function(){
        arr_obj = $(this);
        if (arr_obj.data(filter) == filter_value ){
            arr_obj.removeClass("transparent");
            arr_obj.animate({"opacity": 1 });
            arr_obj.show("slow");
        }
    });  
   $("#gallery").find(obj.data("link-object")).delay(500).each(function(){
        arr_obj = $(this);        
        var hide_obj = $('#gallery '+obj.data('link-object')+".transparent:lt("+(key)+")");
        if (arr_obj.data(filter) == filter_value ){
                var link_obj = $('#gallery '+obj.data('link-object')+".transparent:eq("+(i)+")");
                if (key != i && key != 0 ){
                arr_obj.effect("transfer", { 
                        className: "fly-border",
                        to: $('#gallery '+obj.data('link-object')+":eq("+(i)+")") }, 
                        500
                        );}
                i++;
          }
        key++;
    });
    $("#gallery").find(obj.data("link-object")).delay(650).each(function(){
        if ($(this).data(filter) == filter_value ){
            $(this).hide(10);
        }
    });
    pClick++;

});
$(".portfolio_filter_keywors").live("click", function(){    
    var obj = $(this);
    var filter = obj.data("filter-param");
    var filter_value = obj.data("filter-value");
    var arr = [];
    var i = 0;
    var key = 0;
    
    $("#gallery").find(obj.data("link-object")).each(function(){
        arr = $(this).data(filter).toString().split(",");
        if ($.inArray(String(filter_value), arr) == -1 ){
            $(this).addClass("transparent");
            if( pClick == 0)
                $(this).animate({"opacity": 0 });
        }
        else if ($.inArray(String(filter_value), arr) >= 0 ){
            $(this).show();
            $(this).removeClass("transparent");
        }
        if ( pClick != 0){            
            $(this).animate({"opacity": 0});
            if ($(this).is(":hidden"))
                $(this).show();                
        }
    });
    var count = $("#gallery").find(obj.data("link-object")+".transparent").length;
    $("#gallery").find(obj.data("link-object")).delay(300).each(function(){
        arr_obj = $(this);
        arr = arr_obj.data(filter).toString().split(",");
        if ($.inArray(String(filter_value), arr) >= 0 ){
            arr_obj.removeClass("transparent");
            arr_obj.animate({"opacity": 1 });
            arr_obj.show("slow");
        }
    });  
   $("#gallery").find(obj.data("link-object")).delay(500).each(function(){
        var arr_obj = $(this);        
        arr = arr_obj.data(filter).toString().split(",");        
        var hide_obj = $('#gallery '+obj.data('link-object')+".transparent:lt("+(key)+")");
        if ($.inArray(String(filter_value), arr) >= 0 ){
                var link_obj = $('#gallery '+obj.data('link-object')+".transparent:eq("+(i)+")");
                if (key != i && key != 0 ){
                arr_obj.effect("transfer", { 
                        className: "fly-border",
                        to: $('#gallery '+obj.data('link-object')+":eq("+(i)+")") }, 
                        500
                        );}
                i++;
          }
        key++;
    });
    $("#gallery").find(obj.data("link-object")).delay(650).each(function(){
        var arr_obj = $(this);        
        arr = arr_obj.data(filter).toString().split(",");        
        if ($.inArray(String(filter_value), arr) == -1 ){
            $(this).hide(10);
        }
    });
    pClick++;
});
*/
