jQuery('document').ready( function(){
    
    if( jQuery("#tecauto_import").prop( "checked" ) ){
        jQuery('.tecauto_cat').show(); 
    }
    if( jQuery("#emauto_import").prop( "checked" ) ){
        jQuery('.emauto_cat').show(); 
    }

    jQuery("#tecauto_import").on("change", function(){
        if ( this.checked ) { 
            jQuery('.tecauto_cat').show(); 
        } else { 
            jQuery('.tecauto_cat').hide();
        }
    });

    jQuery("#emauto_import").on("change", function(){
        if ( this.checked ) { 
            jQuery('.emauto_cat').show(); 
        } else { 
            jQuery('.emauto_cat').hide();
        }
    });
    
});