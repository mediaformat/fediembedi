<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

foreach ( wp_load_alloptions() as $option => $value ) {
    if ( strpos( $option, 'fediembedi-' ) === 0 ) {
        delete_option( $option );
    }
}
