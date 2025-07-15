jQuery(document).ready(function ($) {
    const watchVideoBtn = $('#iee-watch-video-btn');
    const videoPopup    = $('#iee-wizard-video-popup');
    const videoFrame    = $('#iee-wizard-video-frame');
    const closePopup    = $('#iee-wizard-close-popup');

    // YouTube Video URL - replace with your own
    const videoURL = "https://www.youtube.com/embed/pbtcEcy4J4o?si=iHSv-EtnECWKLL36&autoplay=1";

    // Open the popup and set video source
    watchVideoBtn.on('click', function () {
        videoFrame.attr('src', videoURL);
        videoPopup.css('display', 'flex');
    });

    // Close popup on close button click
    closePopup.on('click', function () {
        videoFrame.attr('src', '');
        videoPopup.css('display', 'none');
    });

    // Close popup when clicking outside the video frame
    videoPopup.on('click', function (e) {
        if (e.target === this) {
            videoFrame.attr('src', '');
            videoPopup.css('display', 'none');
        }
    });
});