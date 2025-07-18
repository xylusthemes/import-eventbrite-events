<div class="iee-wizard-wrap" >
    <h3><?php esc_attr_e( 'Import Eventbrite Event', 'import-eventbrite-events' ); ?></h3>
    <div class="iee-wizard-starter-video" >
        <a id="iee-watch-video-btn" href="javascript:void(0)" class="iee-wizard-button-style">
            <svg xmlns="http://www.w3.org/2000/svg" width="44.098" height="33" viewBox="0 0 44.098 33">
                <path d="M24.4,9A90.306,90.306,0,0,0,8.3,10.2a5.55,5.55,0,0,0-4.5,4.3A65.024,65.024,0,0,0,3,25a54.425,54.425,0,0,0,.9,10.5,5.691,5.691,0,0,0,4.5,4.3A92.024,92.024,0,0,0,24.5,41a91.941,91.941,0,0,0,16.1-1.2,5.545,5.545,0,0,0,4.5-4.3,75.529,75.529,0,0,0,1-10.6,54.229,54.229,0,0,0-1-10.6A5.681,5.681,0,0,0,40.6,10,124.79,124.79,0,0,0,24.4,9Zm0,2a99.739,99.739,0,0,1,15.8,1.1,3.669,3.669,0,0,1,2.9,2.7,54.775,54.775,0,0,1,1,10.1,73.687,73.687,0,0,1-1,10.3c-.3,1.9-2.3,2.5-2.9,2.7a91.694,91.694,0,0,1-15.6,1.2c-6,0-12.1-.4-15.6-1.2a3.668,3.668,0,0,1-2.9-2.7A39.331,39.331,0,0,1,5,25a55.674,55.674,0,0,1,.8-10.1c.3-1.9,2.4-2.5,2.9-2.7A87.752,87.752,0,0,1,24.4,11ZM19,17V33l14-8Zm2,3.4L29,25l-8,4.6Z" transform="translate(-2.5 -8.5)" fill="#959da4" stroke="#fff" stroke-width="1"></path>
            </svg>
            <p><?php esc_attr_e( 'Getting Started Video', 'import-eventbrite-events' ); ?></p>
        </a>
    </div>

    <div class="iee-wizard-open-popup-box" >
        <button class="iee-wizard-open-popup add-event iee-wizard-button-style" onclick="window.open('<?php echo esc_url( 'https://www.eventbrite.com/myaccount/apps/' ); ?>', '_blank', 'noopener,noreferrer');">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="26" viewBox="0 0 32 26">
                <path d="M0,29H32V3H0Zm1-1V8H31V28ZM31,4V7H1V4ZM3,5H5V6H3ZM7,5H9V6H7Zm4,0h2V6H11ZM3,12H16.5v1H3Zm0,4H16.5v1H3Zm0,4H16.5v1H3Zm15.5,1H29V12H18.5Zm1-8H28v7H19.5Z" transform="translate(0 -3)" fill="#959da4"></path>
            </svg>
            <span><?php esc_attr_e( 'Create Eventbrite Token', 'import-eventbrite-events' ); ?></span>
        </button>
        <button class="iee-wizard-open-popup iee-settings iee-wizard-button-style" id="iee_wizard_setting_button" onclick="window.location.href='<?php echo esc_url( admin_url( 'admin.php?page=eventbrite_event&tab=settings' ) ); ?>';" >
            <svg xmlns="http://www.w3.org/2000/svg" width="32.002" height="32.002" viewBox="0 0 32.002 32.002">
                <path d="M26.563,10.125,29.688,7,25,2.312,21.875,5.437,19.5,4.624V0h-7V4.624l-2.375.813L7,2.312,2.312,7l3.125,3.125L4.624,12.5H0v7H4.624l.813,2.375L2.312,25,7,29.688l3.125-3.125,2.375.813V32h7V27.376l2.375-.813L25,29.688,29.688,25l-3.125-3.125.813-2.375H32v-7H27.376ZM31,18.5H26.625l-1.188,3.625L28.312,25,25,28.313l-2.875-2.875L18.5,26.626V31h-5V26.626L9.874,25.438,7,28.313,3.686,25l2.875-2.875L5.373,18.5H1v-5H5.373L6.561,9.875,3.686,7,7,3.687,9.874,6.562,13.5,5.374V1h5V5.374l3.625,1.188L25,3.687,28.312,7,25.437,9.875,26.625,13.5H31Zm-15-6A3.5,3.5,0,1,0,19.5,16,3.494,3.494,0,0,0,16,12.5Zm0,6A2.5,2.5,0,1,1,18.5,16,2.507,2.507,0,0,1,16,18.5Z" transform="translate(0.001 0.001)" fill="#959da4"></path>
            </svg>
            <span><?php esc_attr_e( 'Settings', 'import-eventbrite-events' ); ?></span>
        </button>
        <div style="clear:both" ></div>
    </div>

    <div class="iee-wizard-back-box" >
        <button class="iee-wizard-back-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
                <path d="M11.739,13.962a.437.437,0,0,1-.624,0L7.377,10.226a.442.442,0,0,1,0-.626l3.559-3.562a.442.442,0,0,1,.626.624L8.314,9.912l3.425,3.426a.442.442,0,0,1,0,.624M18.406,10A8.406,8.406,0,1,1,10,1.594,8.4,8.4,0,0,1,18.406,10m-.885,0A7.521,7.521,0,1,0,10,17.521,7.528,7.528,0,0,0,17.521,10" transform="translate(-1.594 -1.594)" fill="#959da4"></path>
            </svg>
            <span onclick="window.location.href='<?php echo esc_url( admin_url( 'admin.php?page=eventbrite_event&tab=dashboard' ) ); ?>';" ><?php esc_attr_e( 'Back to WordPress Dashboard', 'import-eventbrite-events' ); ?></span>
        </button>
    </div>
    <a href="admin.php?page=eventbrite_event&tab=dashboard" title="close" class="iee-wizard-close-button">
        <svg enable-background="new 0 0 256 256" id="Layer_1" version="1.1" viewBox="0 0 256 256" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <path d="M137.051,128l75.475-75.475c2.5-2.5,2.5-6.551,0-9.051s-6.551-2.5-9.051,0L128,118.949L52.525,43.475  c-2.5-2.5-6.551-2.5-9.051,0s-2.5,6.551,0,9.051L118.949,128l-75.475,75.475c-2.5,2.5-2.5,6.551,0,9.051  c1.25,1.25,2.888,1.875,4.525,1.875s3.275-0.625,4.525-1.875L128,137.051l75.475,75.475c1.25,1.25,2.888,1.875,4.525,1.875  s3.275-0.625,4.525-1.875c2.5-2.5,2.5-6.551,0-9.051L137.051,128z"></path>
        </svg>
    </a>
</div>
<div id="iee-wizard-video-popup" class="iee-popup-overlay">
    <div class="iee-popup-content">
        <span id="iee-wizard-close-popup" class="iee-close-btn">&times;</span>
        <iframe id="iee-wizard-video-frame" width="1350" height="700" src="" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
    </div>
</div>