<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

delete_option( 'fediembedi-client-id' );
delete_option( 'fediembedi-client-secret' );
delete_option( 'fediembedi-token' );
delete_option( 'fediembedi-instance' );
delete_option( 'fediembedi-instance-info' );
delete_option( 'fediembedi-notice' );
