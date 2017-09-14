(function( $ ) {
	'use strict';

	jQuery(document).ready(function(){
		jQuery('.xt_datepicker').datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});
	});
	
	jQuery(document).ready(function(){
		jQuery('#eventbrite_import_by').on('change', function(){

			if( jQuery(this).val() == 'event_id' ){
				jQuery('.import_type_wrapper').hide();
				jQuery('.eventbrite_organizer_id').hide();
				jQuery('.eventbrite_organizer_id .iee_organizer_id').removeAttr( 'required' );
				jQuery('.eventbrite_event_id').show();
				jQuery('.eventbrite_event_id .iee_eventbrite_id').attr('required', 'required');

			} else if( jQuery(this).val() == 'your_events' ){
				jQuery('.import_type_wrapper').show();
				jQuery('.eventbrite_organizer_id').hide();
				jQuery('.eventbrite_organizer_id .iee_organizer_id').removeAttr( 'required' );
				jQuery('.eventbrite_event_id').hide();
				jQuery('.eventbrite_event_id .iee_eventbrite_id').removeAttr( 'required' );

			} else if( jQuery(this).val() == 'organizer_id' ){
				jQuery('.import_type_wrapper').show();
				jQuery('.eventbrite_organizer_id').show();
				jQuery('.eventbrite_organizer_id .iee_organizer_id').attr('required', 'required');
				jQuery('.eventbrite_event_id').hide();
				jQuery('.eventbrite_event_id .iee_eventbrite_id').removeAttr( 'required' );
			
			}

		});

		jQuery('#import_type').on('change', function(){
			if( jQuery(this).val() != 'onetime' ){
				jQuery('.hide_frequency .import_frequency').show();
			}else{
				jQuery('.hide_frequency .import_frequency').hide();
			}
		});

		jQuery("#import_type").trigger('change');
		jQuery("#eventbrite_import_by").trigger('change');
	});	

	// Render Dynamic Terms.
	jQuery(document).ready(function() {
	    jQuery('.eventbrite_event_plugin').on( 'change', function() {

	    	var event_plugin = jQuery(this).val();
	    	var data = {
	            'action': 'iee_render_terms_by_plugin',
	            'event_plugin': event_plugin
	        };

	        var terms_space = jQuery('.event_taxo_terms_wraper');
	        terms_space.html('<span class="spinner is-active" style="float: none;"></span>');
	        // send ajax request.
	        jQuery.post(ajaxurl, data, function(response) {
	            if( response != '' ){
	            	terms_space.html( response );
	            }else{
	            	terms_space.html( '' );
	            }	            
	        });    
	    });
	    jQuery(".eventbrite_event_plugin").trigger('change');                  
	});

})( jQuery );


