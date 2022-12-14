jQuery(function($) {

    // Notices
    $( document ).on( 'click', '.notice-fediembedi .notice-dismiss', function () {
        $.ajax( ajaxurl, {
            type: 'POST',
            data: {
              action: 'dismissed_notice_handler',
            }
        } );
    } );
});
