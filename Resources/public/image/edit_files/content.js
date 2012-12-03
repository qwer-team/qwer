/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$( document ).ready(function() {
    
    $( '.new_window' ).live( 'click', function(){
        var url = $( this ).attr( 'href' );
        window.open( url, '', 
            'Width = 1100, Height = 660, Toolbar=0, '+
            'Status=1, Menubar=1, Scrollbars=1, '+
            'Resizable=0, left=screen.width/2-400, top=20'
        );
        return false;
    }) ;

    $('.pager_changes').live( 'change', function( e ){
        var link = $( this ).find( "option:selected" ).attr( 'href' ) ;
        document.location.href =  link ;
    });

    $(".menu_sys_delete").live('click', function(e){
        $( this ).parents( 'form' ).submit();
    });

    $(".checkbox_update_visible").live('change', function(e){
        $( this ).parents( 'form' ).ajaxSubmit();
    });
    $('#ms_edit_icon').live('change', function( e ) {
        $( '#ms_edit_icon').ajaxSubmit({
            success: function( data ){
                $( '.menu_sys_iconImage' ).attr( {'src':data.image} );
            }
        });
    });

    $( '.menu_sys_lang_tab' ).live( 'click', function(){
        $( '.menu_sys_lang_tab' ).removeClass( 'current' );
        $( this ).addClass( 'current' );
        var lang = $( this ).attr( 'lang' );
        $( '.menu_sys_lang_show' ).hide();
        //console.log( lang ) ;
        $( '.menu_sys_lang_show[lang="' + lang + '"]' ).show() ;
    }) ;

    $( '.menu_sys_link, .menu_sys_child_link' ).live( 'hover', function(){
        $(this).parent().find(".menu_sys_child_link").each(
            function(){
                $(this).toggle();
        });
    });
    /*
        $(".select2").select2(
            {"placeholder":"Select a value",
                "allowClear":false,
                "minimumInputLength":0,
                "width":"element"
            });*/
        $('.openPas').fancybox({
            'modal '        : true,
            'transitionIn'  : 'none',
            'transitionOut' : 'none'
        });

    $(".show_object").live("click", function(){
        $($(this).attr("href")).fadeIn();
    });
    $(".hide_object").live("click", function(){
        $($(this).attr("href")).fadeOut();
    });
    $('.update_date').live('click', function() {
        $($(this).attr("href")).ajaxSubmit({
            success: function( data ){
                $("#save_message").show();
                //$("#save_message").delay(500).fadeOut();
            }
        });
    });

    $(function(){
            $('#jcrop_target').Jcrop({
                    onChange: showCoords,
                    onSelect: showCoords,
                    aspectRatio: 1
            });
    });

    function showCoords(c)
    {
            $('.x').val(c.x);
            $('.y').val(c.y);
            $('.w').val(c.w);
            $('.h').val(c.h);
    };

    $(".submit_form").live('click', function(){
        $($(this).attr("href")).ajaxSubmit();
    })
    $(".delete_object").live('click', function(){
        var obj = $(this);
        if (obj.data("prev")) 
            if (!confirm("Удалить элементы и все закрепленные за ним товары?")) return;
        $(obj.data("form")).ajaxSubmit({
            success: function( data ){
                $(obj.data("link")).remove();
            }
        });
    });
    $(".change_position_object").live('click', function(){
        var obj = $(this);
        var action = obj.data("action");
        var kod = parseInt($(obj.data("form")).find("input[name='form[kod]']").val());
        if (action == 'up'){
            kod--;
            if (kod == 0) return;
        }else{
            kod++;        
        }
        $(obj.data("form")).find("input[name='form[kod]']").val(kod);
        $(obj.data("form")).ajaxSubmit({
            'datatype': 'xml',
            'success' : function( data ){
                $(obj.data("link")).replaceWith(data);
            }
        });
    });
   
    $(function() {
        function split( val ) {
            return val.split( /,\s*/ );
        }
        function extractLast( term ) {
            return split( term ).pop();
        }
        $( ".products_search" )
        // don't navigate away from the field on tab when selecting an item
        .bind( "keydown", function( event ) {
        if ( event.keyCode === $.ui.keyCode.TAB &&
        $( this ).data( "autocomplete" ).menu.active ) {
        event.preventDefault();
        }
        })
        .autocomplete({
            source: function( request, response ) {

                $.getJSON( "/itc/ru/product/ajax_member.json", {
                    term: extractLast( request.term )
                }, response );
            },
            search: function() {
                // custom minLength
                var term = extractLast( this.value );
                if ( term.length < 2 ) {
                    return false;
                }
            },
            focus: function() {
            // prevent value inserted on focus
                return false;
            },
            select: function( event, ui ) {
                var terms = split( this.value );
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push( ui.item.label );
                // add placeholder to get the comma-and-space at the end
                terms.push( "" );
                this.value = terms.join( ", " );
                $( '.relationsProduct' ).append( '<option value="'+ui.item.value+'" selected="selected">'+ui.item.label+'</option>')
                return false;
            }
        });
    });


});
