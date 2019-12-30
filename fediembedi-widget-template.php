<?php

//fedi instance
$fedi_instance = get_option('fediembedi-instance');
$access_token = get_option('fediembedi-token');
$client = new \Client($fedi_instance, $access_token);
$cred = $client->verify_credentials($access_token);
//$profile = $client->getAccount();

//widget options
$show_header = (!empty($instance['show_header'])) ? $instance['show_header'] : '';
$only_media = (!empty($instance['only_media'])) ? $instance['only_media'] : '';
$pinned = (!empty($instance['pinned'])) ? $instance['pinned'] : '';
$exclude_replies = (!empty($instance['exclude_replies'])) ? $instance['exclude_replies'] : '';
$exclude_reblogs = (!empty($instance['exclude_reblogs'])) ? $instance['exclude_reblogs'] : '';
$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
$height    = isset( $instance['height'] ) ? esc_attr( $instance['height'] ) : '100%';

//Default query
$query = http_build_query(array(
  'only_media' => $only_media,
  'pinned' => $pinned,
  'exclude_replies' => $exclude_replies,
  'limit' => 5,
  'exclude_reblogs' => $exclude_reblogs
));
$status = $client->getStatus($only_media, $pinned, $exclude_replies, null, null, null, $number, $exclude_reblogs);

$instance_type = get_option('fediembedi-instance-type');
  switch ($instance_type) {
      case 'Mastodon':
        include(plugin_dir_path(__FILE__) . 'fediembedi-mastodon.tpl.php' );
        break;
      case 'Pixelfed':
        include(plugin_dir_path(__FILE__) . 'fediembedi-pixelfed.tpl.php' );
        break;
      default:
        include(plugin_dir_path(__FILE__) . 'fediembedi-mastodon.tpl.php' );
        break;
    } ?>
