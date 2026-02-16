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
                }
            },
            complete: function(){
                $container.removeClass('iee-loading');
            }
        });

    });
});