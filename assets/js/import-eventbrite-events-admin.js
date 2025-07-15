(function( $ ) {
	'use strict';

	jQuery(document).ready(function(){
		jQuery('.xt_datepicker').datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});
		jQuery(document).on("click", ".iee_datepicker", function(){
		    jQuery(this).datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat: 'yy-mm-dd',
				showOn:'focus'
			}).focus();
		});

		jQuery(document).on("click", ".vc_ui-panel .iee_datepicker input[type='text']", function(){
		    jQuery(this).datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat: 'yy-mm-dd',
				showOn:'focus'
			}).focus();
		});
	});
	
	jQuery(document).ready(function(){
		jQuery(document).on('change', '#eventbrite_import_by', function(){

			if( jQuery(this).val() == 'event_id' ){
				jQuery('.import_type_wrapper').hide();
				jQuery('.eventbrite_organizer_id').hide();
				jQuery('.eventbrite_collection_id').hide();
				jQuery('.eventbrite_organizer_id .iee_organizer_id').removeAttr( 'required' );
				jQuery('.eventbrite_event_id').show();
				jQuery('.eventbrite_event_id .iee_eventbrite_id').attr('required', 'required');
				jQuery('.eventbrite_collection_id .iee_collection_id').removeAttr( 'required' );
			
			} else if( jQuery(this).val() == 'organizer_id' ){
				jQuery('.import_type_wrapper').show();
				jQuery('.eventbrite_organizer_id').show();
				jQuery('.eventbrite_organizer_id .iee_organizer_id').attr('required', 'required');
				jQuery('.eventbrite_collection_id').hide();
				jQuery('.eventbrite_event_id').hide();
				jQuery('.eventbrite_event_id .iee_eventbrite_id').removeAttr( 'required' );
				jQuery('.eventbrite_collection_id .iee_collection_id').removeAttr( 'required' );

			} else if( jQuery(this).val() == 'collection_id' ){
				jQuery('.import_type_wrapper').show();
				jQuery('.eventbrite_collection_id').show();
				jQuery('.eventbrite_collection_id .iee_collection_id').attr('required', 'required');
				jQuery('.eventbrite_event_id').hide();
				jQuery('.eventbrite_organizer_id').hide();
				jQuery('.eventbrite_event_id .iee_eventbrite_id').removeAttr( 'required' );
				jQuery('.eventbrite_organizer_id .iee_organizer_id').removeAttr( 'required' );
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
	    	var taxo_cats = jQuery('#iee_taxo_cats').val();
	    	var taxo_tags = jQuery('#iee_taxo_tags').val();

	    	var data = {
	            'action': 'iee_render_terms_by_plugin',
	            'event_plugin': event_plugin,
	            'taxo_cats': taxo_cats,
	            'taxo_tags': taxo_tags
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

	jQuery(document).ready(function() {
	    jQuery('.enable_ticket_sec').on( 'change', function() {
			var ischecked= jQuery(this).is(':checked');
			if(ischecked){
				jQuery('.checkout_model_option').show();
			}else{
				jQuery('.checkout_model_option').hide();
			}
	    });
	    jQuery(".enable_ticket_sec").trigger('change');
	});

	// Color Picker
	jQuery(document).ready(function($){
		$('.iee_color_field').each(function(){
			$(this).wpColorPicker();
		});
	});

	//Shortcode Copy Text
	jQuery(document).ready(function($){
		$(document).on("click", ".iee-btn-copy-shortcode", function() { 
			var trigger = $(this);
			$(".iee-btn-copy-shortcode").removeClass("text-success");
			var $tempElement = $("<input>");
			$("body").append($tempElement);
			var copyType = $(this).data("value");
			$tempElement.val(copyType).select();
			document.execCommand("Copy");
			$tempElement.remove();
			$(trigger).addClass("text-success");
			var $this = $(this),
			oldText = $this.text();
			$this.attr("disabled", "disabled");
			$this.text("Copied!");
			setTimeout(function(){
				$this.text("Copy");
				$this.removeAttr("disabled");
			}, 800);
	  
		});

	});

})( jQuery );

jQuery(document).ready(function($){

	const iee_tab_link = document.querySelectorAll('.iee_tab_link');
	const iee_tabcontents = document.querySelectorAll('.iee_tab_content');

	iee_tab_link.forEach(function(link) {
		link.addEventListener('click', function() {
		const iee_tabId = this.dataset.tab;

			// Loop through all links to update classes
			iee_tab_link.forEach(function (link) {
				if (link === this) {
					link.classList.add('var-tab--active');
					link.classList.remove('var-tab--inactive');
				} else {
					link.classList.remove('var-tab--active');
					link.classList.add('var-tab--inactive');
				}
			}, this);

			// Loop through all tab contents to show/hide
			iee_tabcontents.forEach(function (content) {
				if (content.id === iee_tabId) {
					content.classList.add('var-tab--active');
				} else {
					content.classList.remove('var-tab--active');
				}
			});
		});
	});

	const iee_gm_apikey_input = document.querySelector('.iee_google_maps_api_key');
	if ( iee_gm_apikey_input ) { 
		iee_gm_apikey_input.addEventListener('input', function() { 
			const iee_check_key = document.querySelector('.iee_check_key'); 
			if (iee_gm_apikey_input.value.trim() !== '') { 
				iee_check_key.style.display = 'contents'; 
			} else { 
				iee_check_key.style.display = 'none'; 
			} 
		}); 
	}
  
	const iee_checkkeylink = document.querySelector('.iee_check_key a');
	if ( iee_checkkeylink ) { 
		iee_checkkeylink.addEventListener('click', function(event) { 
			event.preventDefault(); 
			const iee_gm_apikey = iee_gm_apikey_input.value.trim();
			if ( iee_gm_apikey !== '' ) { 
				iee_check_gmap_apikey(iee_gm_apikey); 
			} 
		}); 
	}

	function iee_check_gmap_apikey(iee_gm_apikey) {
		const iee_xhr = new XMLHttpRequest();
		iee_xhr.open('GET', 'https://www.google.com/maps/embed/v1/place?q=New+York&key=' + encodeURIComponent(iee_gm_apikey), true);
		const iee_loader = document.getElementById('iee_loader');
		iee_loader.style.display = 'inline-block';
		iee_xhr.onreadystatechange = function() {
			if ( iee_xhr.readyState === XMLHttpRequest.DONE ) {
				iee_loader.style.display = 'none';
				if (iee_xhr.status === 200) {
					const response = iee_xhr.responseText;
					var iee_gm_success_notice = jQuery("#iee_gmap_success_message");
						iee_gm_success_notice.html('<span class="iee_gmap_success_message">Valid Google Maps License Key</span>');
						setTimeout(function(){ iee_gm_success_notice.empty(); }, 2000);
				} else {
					var iee_gm_error_notice = jQuery("#iee_gmap_error_message");
					iee_gm_error_notice.html( '<span class="iee_gmap_error_message" >Inalid Google Maps License Key</span>' );
						setTimeout(function(){ iee_gm_error_notice.empty(); }, 2000);
				}
			}
		};

		iee_xhr.send();
	}

	jQuery(document).ready(function($) {
		var $slides = $('.iee-screenshot-slide');
		var index = 0;

		setInterval(function() {
			$slides.removeClass('active');
			index = (index + 1) % $slides.length;
			$slides.eq(index).addClass('active');
		}, 3000);
	});

});
