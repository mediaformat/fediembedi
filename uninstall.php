<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

delete_option( 'fediembedi-notice' );

//original options < 0.8.0
delete_option( 'fediembedi-client-id' );
delete_option( 'fediembedi-client-secret' );
delete_option( 'fediembedi-token' );
delete_option( 'fediembedi-instance' );
delete_option( 'fediembedi-instance-info' );
delete_option( 'fediembedi-instance-type' );

//pixelfed
delete_option('fediembedi-pixelfed-client-id');
delete_option('fediembedi-pixelfed-client-secret');
delete_option('fediembedi-pixelfed-token');
delete_option('fediembedi-pixelfed-instance');
delete_option('fediembedi-pixelfed-token');

//mastodon
delete_option('fediembedi-mastodon-client-id');
delete_option('fediembedi-mastodon-client-secret');
delete_option('fediembedi-mastodon-token');
delete_option('fediembedi-mastodon-instance');
delete_option('fediembedi-mastodon-token');
