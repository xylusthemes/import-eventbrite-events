jQuery(document).ready(function($){
    $(document).on('click', '.prev-next-posts a', function(e){
        var $link = $(this);
        var $container = $link.closest('.iee_archive'); // old container
        var atts = $container.data('shortcode');       // shortcode attributes
        var nextPage = parseInt($link.data('page')) || 1;

        if (!atts || atts.ajaxpagi !== 'yes') return true;

        e.preventDefault();
        $container.addClass('iee-loading');

        $.ajax({
            url: iee_ajax.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'iee_load_paged_events',
                atts: JSON.stringify(atts),
                page: nextPage
            },
            success: function(response){
                if(response.success){
                    // Replace old container with new HTML
                    $container.replaceWith(response.data);

                    // Update $container reference for next click
                    var $newContainer = $('.iee_archive').filter(function(){
                        return $(this).data('shortcode').ajaxpagi === 'yes';
                    }).first();

                    // Update pagination links dynamically
                    $newContainer.find('.iee-next-page').attr('data-page', nextPage + 1);
                    $newContainer.find('.iee-prev-page').attr('data-page', nextPage - 1);
                    
                    // Run live check on new events
                    if (typeof iee_run_live_sold_out_check === 'function') {
                        iee_run_live_sold_out_check($newContainer);
                    }
                }
            },
            complete: function(){
                $container.removeClass('iee-loading');
            }
        });

    });
});
function iee_run_live_sold_out_check($context) {
    if (typeof iee_ajax === 'undefined' || !iee_ajax.ajaxurl || iee_ajax.live_check_enabled !== 'yes') return;
    
    $context = $context || jQuery(document);
    var event_ids = [];
    $context.find('.archive-event, .iee_event_container, .iee_event_list, .iee6_event-card, article, [class*="post-"]').each(function() {
        var $el = jQuery(this);
        var classes = $el.attr('class');
        if (!classes) return;
        var match = classes.match(/\bpost-(\d+)\b/);
        if (match && match[1]) {
            var id = match[1];
            if (event_ids.indexOf(id) === -1) {
                event_ids.push(id);
                $el.attr('data-event-id', id);
            }
        }
    });

    if (event_ids.length > 0) {
        jQuery.ajax({
            url: iee_ajax.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'iee_check_live_sold_out',
                event_ids: event_ids
            },
            success: function(response){
                if(response.success && response.data && response.data.sold_out_ids) {
                    var sold_out_ids = response.data.sold_out_ids;
                    jQuery.each(sold_out_ids, function(index, id) {
                        var $event = $context.find('.post-' + id);
                        if ($event.length) {
                            var $imgContainer = $event.find('.img_placeholder, .iee_event_image, .event_img, .iee-event-img, .iee-style4-event-image-img, .iee5_event-image, .iee6_event-image');
                            var $wrapper = $imgContainer.find('.iee-badges-wrapper');
                            if ($wrapper.length === 0) {
                                $wrapper = jQuery('<div class="iee-badges-wrapper"></div>');
                                $imgContainer.append($wrapper);
                            }
                            if ($wrapper.find('span:contains("Sold Out")').length === 0) {
                                $wrapper.prepend('<span class="iee-badge">Sold Out</span>');
                            }
                        }
                    });
                }
            }
        });
    }
}

// Live Sold Out Check on Page Load
jQuery(document).ready(function($){
    iee_run_live_sold_out_check();
});
