<?php
/**
 * Plugin Name: FediEmbedi
 * Plugin URI: https://git.feneas.org/mediaformat/fediembedi
 * Description: Widgets and shortcodes to show your Fediverse profile timeline
 * Version: 0.10.7
 * Author: mediaformat
 * Author URI: https://mediaformat.org
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain: fediembedi
 * Domain Path: /languages
 * Github Plugin URI: https://github.com/mediaformat/fediembedi
 */
namespace FediEmbedi;
require_once 'fediembedi-client.php';

class FediConfig
{
    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'init'));
        add_action('widgets_init', array($this, 'fediembedi_widget'));
        add_shortcode('mastodon', array($this, 'mastodon_shortcode'));
        add_shortcode('pixelfed', array($this, 'pixelfed_shortcode'));
        add_shortcode('peertube', array($this, 'peertube_shortcode'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_menu', array($this, 'configuration_page'));
        add_action('admin_notices', array($this, 'admin_notices'));
        add_filter('fedi_emoji', array($this, 'convert_emoji'), 10, 2);
        add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'fediembedi_add_plugin_page_settings_link'));

    }

    /**
     * Init
     *
     * Plugin initialization
     *
     * @return void
     */
    public function init()
    {
        $plugin_dir = basename(dirname(__FILE__));
        //load_plugin_textdomain('fediembedi', false, $plugin_dir . '/languages');

        if (isset($_GET['code'])) {
        //if (isset($_GET['code']) && isset($GET['instance_type'])) {

            $instance_type = $_REQUEST['instance_type'];
            $code = $_GET['code'];

            switch ($instance_type) {
              case 'mastodon':
                $client_id = get_option('fediembedi-mastodon-client-id');
                $client_secret = get_option('fediembedi-mastodon-client-secret');
                break;
              case 'pixelfed':
                $client_id = get_option('fediembedi-pixelfed-client-id');
                $client_secret = get_option('fediembedi-pixelfed-client-secret');
                break;
            }

            if (!empty($code) && !empty($client_id) && !empty($client_secret)) {
                //echo __('Authentification, please wait', 'fediembedi') . '...';

                switch ($instance_type) {
                  case 'mastodon':
                    update_option('fediembedi-mastodon-token', 'nada');
                    $instance = get_option('fediembedi-mastodon-instance');
                    $client = new \FediClient($instance);
                    $token = $client->get_bearer_token($client_id, $client_secret, $code, get_admin_url() . '?instance_type=' . $instance_type);
                    break;
                  case 'pixelfed':
                    update_option('fediembedi-pixelfed-token', 'nada');
                    $instance = get_option('fediembedi-pixelfed-instance');
                    $client = new \FediClient($instance);
                    $token = $client->get_bearer_token($client_id, $client_secret, $code, get_admin_url() . '?instance_type=' . $instance_type);
                    break;
                }

                if (isset($token->error)) {
                    //TODO: Proper error message
                    update_option(
                        'fediembedi-notice',
                        serialize(
                            array(
                                'message' => '<strong>FediEmbedi</strong> : ' . __("Can't log you in.", 'fediembedi') .
                                '<p><strong>' . __('Instance message', 'fediembedi') . '</strong> : ' . $token->error_description . '</p>',
                                'class' => 'error',
                            )
                        )
                    );
                    unset($token);
                    switch ($instance_type) {
                      case 'mastodon':
                        update_option('fediembedi-mastodon-token', '');
                        break;
                      case 'pixelfed':
                        update_option('fediembedi-pixelfed-token', '');
                        break;
                    }
                } else {
                    switch ($instance_type) {
                      case 'mastodon':
                        update_option('fediembedi-mastodon-client-id', $client_id);//
                        update_option('fediembedi-mastodon-client-secret', $client_secret);//
                        update_option('fediembedi-mastodon-token', $token->access_token);
                        break;
                      case 'pixelfed':
                        update_option('fediembedi-pixelfed-client-id', $client_id);//
                        update_option('fediembedi-pixelfed-client-secret', $client_secret);//
                        update_option('fediembedi-pixelfed-token', $token->access_token);
                        break;
                    }

                }
                $redirect_url = get_admin_url() . 'options-general.php?page=fediembedi';
            } else {
                //Probably hack or bad refresh, redirect to homepage
                $redirect_url = home_url();
            }

            wp_redirect($redirect_url);
            exit;
        }

        $mastodon_token = get_option('fediembedi-mastodon-token');
        $pixelfed_token = get_option('fediembedi-pixelfed-token');
    }

    /*
     *  Widget
     */
    public function fediembedi_widget() {
      //Mastodon
      include(plugin_dir_path(__FILE__) . 'fediembedi-mastodon-widget.php' );
      register_widget( 'FediEmbedi_Mastodon' );
      if(empty(get_option('fediembedi-mastodon-token'))){
        unregister_widget( 'FediEmbedi_Mastodon' );
      }

      //Pixelfed
      include(plugin_dir_path(__FILE__) . 'fediembedi-pixelfed-widget.php' );
      register_widget( 'FediEmbedi_Pixelfed' );
      if(empty(get_option('fediembedi-pixelfed-token'))){
        unregister_widget( 'FediEmbedi_Pixelfed' );
      }

      //PeerTube
      include(plugin_dir_path(__FILE__) . 'fediembedi-peertube-widget.php' );
    	register_widget( 'FediEmbedi_PeerTube' );
    }

    public function mastodon_shortcode($atts){
      //fedi instance
  		$fedi_instance = get_option('fediembedi-mastodon-instance');
  		$access_token = get_option('fediembedi-mastodon-token');
  		$client = new \FediClient($fedi_instance, $access_token);
  		$cred = $client->verify_credentials($access_token);

      $atts = shortcode_atts( array(
        'only_media' => false,
        'pinned' => false,
        'exclude_replies' => false,
        'max_id' => null,
        'since_id' => null,
        'min_id' => null,
        'limit' => 5,
        'exclude_reblogs' => false,
        'show_header' => true,
      ), $atts, 'mastodon' );

      //getStatus from remote instance
      $status = $client->getStatus($atts['only_media'], $atts['pinned'], $atts['exclude_replies'], null, null, null, $atts['limit'], $atts['exclude_reblogs']);
      //if(WP_DEBUG_DISPLAY === true): echo '<details><summary>Mastodon</summary><pre>'; var_dump($status); echo '</pre></details>'; endif;
      $show_header = $atts['show_header'];
      $account = $status[0]->account;
      ob_start();
      include(plugin_dir_path(__FILE__) . 'templates/mastodon.tpl.php' );
      return ob_get_clean();
    }

    public function pixelfed_shortcode($atts){
      //fedi instance
      $fedi_instance = get_option('fediembedi-pixelfed-instance');
      $access_token = get_option('fediembedi-pixelfed-token');
      $client = new \FediClient($fedi_instance, $access_token);
      $cred = $client->verify_credentials($access_token);

      $atts = shortcode_atts( array(
        'only_media' => false,
        'pinned' => false,
        'exclude_replies' => false,
        'max_id' => null,
        'since_id' => null,
        'min_id' => null,
        'limit' => 9,
        'exclude_reblogs' => false,
        'show_header' => true,
      ), $atts, 'pixelfed' );

      //getStatus from remote instance
      $status = $client->getStatus($atts['only_media'], $atts['pinned'], $atts['exclude_replies'], null, null, null, $atts['limit'], $atts['exclude_reblogs']);
      //if(WP_DEBUG_DISPLAY === true): echo '<details><summary>Mastodon</summary><pre>'; var_dump($client->getStatus($atts)); echo '</pre></details>'; endif;
      $show_header = $atts['show_header'];
      if($account = $status[0]->account){
        ob_start();
        include(plugin_dir_path(__FILE__) . 'templates/pixelfed.tpl.php' );
        return ob_get_clean();
      } else {
        return;
      }
    }

    public function peertube_shortcode($atts){

      $atts = shortcode_atts( array(
        'instance' => null,
        'actor' => null,
        'is_channel' => null,
        'limit' => 9,
        'show_header' => true,
      ), $atts, 'peertube' );

      $client = new \FediClient($atts['instance']);

      //getVideos from remote instance
      $status = $client->getVideos($atts['actor'], $atts['is_channel'], $atts['limit'], $atts['nsfw'] );
      if(!is_null($atts['is_channel'])){
        $account = $status->data[0]->channel;
      } else {
        $account = $status->data[0]->account;
      }

      //if(WP_DEBUG_DISPLAY === true): echo '<details><summary>PeerTube</summary><pre>'; var_dump($status); echo '</pre></details>'; endif;
      $show_header = $atts['show_header'];
      ob_start();
      include(plugin_dir_path(__FILE__) . 'templates/peertube.tpl.php' );
      return ob_get_clean();
    }

    /*
     * convert_emoji
     */
    public function convert_emoji($string, $emojis){
      if(is_null($emojis) || !is_array($emojis)){
        return $string;
      }
      foreach($emojis as $emoji){
           $match = '/:' . $emoji->shortcode .':/';
           $string = preg_replace($match, "<img draggable=\"false\" role=\"img\" class=\"emoji\" src=\"{$emoji->static_url}\">", $string);
      }
      return $string;
    }

    public function enqueue_styles($hook)
    {
        global $post;
        if( is_active_widget( false, false, 'mastodon') || is_active_widget( false, false, 'pixelfed') || ( is_a( $post, 'WP_Post' ) && ( has_shortcode( $post->post_content, 'mastodon') || has_shortcode( $post->post_content, 'pixelfed') ) ) ) {
            wp_enqueue_script( 'resize-sensor', plugin_dir_url( __FILE__ ) . 'assets/ResizeSensor.js', array(), 'css-element-queries-1.2.2' );
            wp_enqueue_script( 'element-queries', plugin_dir_url( __FILE__ ) . 'assets/ElementQueries.js', array('resize-sensor'), 'css-element-queries-1.2.2' );
        }
        if( is_active_widget( false, false, 'mastodon')  || ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'mastodon') ) ) {
            wp_enqueue_style( 'mastodon', plugin_dir_url( __FILE__ ) . 'assets/mastodon.css', array(), filemtime(plugin_dir_path( __FILE__ ) . 'assets/mastodon.css') );
        }
        if( is_active_widget( false, false, 'pixelfed')  || ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'pixelfed') )  ) {
            wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'assets/bootstrap/css/bootstrap.min.css', array(), '4.4.1' );
            wp_enqueue_style( 'pixelfed', plugin_dir_url( __FILE__ ) . 'assets/pixelfed.css', array(), filemtime(plugin_dir_path( __FILE__ ) . 'assets/pixelfed.css') );
        }
        if( is_active_widget( false, false, 'peertube')  || ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'peertube') ) ) {
            wp_enqueue_style( 'peertube', plugin_dir_url( __FILE__ ) . 'assets/peertube.css', array(), filemtime(plugin_dir_path( __FILE__ ) . 'assets/mastodon.css') );
        }
    }

    public function enqueue_scripts($hook)
    {
      global $pagenow;
      $infos = get_plugin_data(__FILE__);
      if ($pagenow == "options-general.php") {
          //We might be on settings page <-- Do you know a bette solution to get if we are in our own settings page?
          $plugin_url = plugin_dir_url(__FILE__);
          //wp_enqueue_script('settings_page', $plugin_url . 'assets/settings_page.js', array('jquery'), $infos['Version'], true);

      }
    }

    /**
     * Configuration_page
     *
     * Add the configuration page menu
     *
     * @return void
     */
    public function configuration_page()
    {
        add_options_page(
            'FediEmbedi',
            'FediEmbedi',
            'manage_options',
            'fediembedi',
            array($this, 'show_configuration_page')
        );
    }

    /**
     * Show_configuration_page
     *
     * Content of the configuration page
     *
     * @throws Exception The exception.
     * @return void
     */
    public function show_configuration_page()
    {

        wp_enqueue_style('fediembedi-configuration', plugin_dir_url(__FILE__) . 'style.css');

        if (isset($_GET['fediembedi-disconnect'])) {
          switch ($_GET['fediembedi-disconnect']) {
            case 'mastodon':
              update_option('fediembedi-mastodon-token', '');
              break;
            case 'pixelfed':
              update_option('fediembedi-pixelfed-token', '');
              break;
          }

        }
        $mastodon_instance = null;
        $mastodon_token = get_option('fediembedi-mastodon-token');
        $pixelfed_instance = null;
        $pixelfed_token = get_option('fediembedi-pixelfed-token');

        if (isset($_POST['save'])) {

            $is_valid_nonce = wp_verify_nonce($_POST['_wpnonce'], 'fediembedi-configuration');

            if ($is_valid_nonce) {

                $instance = esc_url($_POST['instance']);
                $instance_type = esc_attr($_POST['instance_type']);

                $client = new \FediClient($instance);
                $redirect_url = get_admin_url() . '?instance_type=' . $instance_type;
                $auth_url = $client->register_app($redirect_url);

                if ($auth_url == "ERROR") {
                    update_option(
                        'fediembedi-notice',
                        serialize(
                            array(
                                'message' => '<strong>FediEmbedi</strong> : ' . __('The given instance url belongs to an unrecognized service!', 'fediembedi'),
                                'class' => 'error',
                            )
                        )
                    );

                } else {
                    if (empty($instance)) {
                        update_option(
                            'fediembedi-notice',
                            serialize(
                                array(
                                    'message' => '<strong>FediEmbedi</strong> : ' . __('Please set your instance before you connect !', 'fediembedi'),
                                    'class' => 'error',
                                )
                            )
                        );
                    } else {

                        switch ($instance_type) {
                          case 'mastodon':
                            update_option('fediembedi-mastodon-client-id', $client->get_client_id());
                            update_option('fediembedi-mastodon-client-secret', $client->get_client_secret());
                            update_option('fediembedi-mastodon-instance', $instance);
                            $mastodon_account = $client->verify_credentials($mastodon_token);
                            $account = $mastodon_account;
                            break;
                          case 'pixelfed':
                            update_option('fediembedi-pixelfed-client-id', $client->get_client_id());
                            update_option('fediembedi-pixelfed-client-secret', $client->get_client_secret());
                            update_option('fediembedi-pixelfed-instance', $instance);
                            $pixelfed_account = $client->verify_credentials($pixelfed_token);
                            $account = $pixelfed_account;
                            break;
                        }

                        //$account = $client->verify_credentials($token);

                        if (is_null($account) || isset($account->error)) {
                            echo '<meta http-equiv="refresh" content="0; url=' . $auth_url . '" />';
                            echo __('Redirect to ', 'fediembedi') . $instance;
                            exit;
                        }

                        //Inform user that save was successfull
                        update_option(
                            'fediembedi-notice',
                            serialize(
                                array(
                                    'message' => '<strong>FediEmbedi</strong> : ' . __('Configuration successfully saved!', 'fediembedi'),
                                    'class' => 'success',
                                )
                            )
                        );

                    }
                }

                $this->admin_notices();
            }
        }

        if (!empty($mastodon_token)) {
            $mastodon_instance = get_option('fediembedi-mastodon-instance');
            $client = new \FediClient($mastodon_instance);
            $mastodon_account = $client->verify_credentials($mastodon_token);
        }
        if (!empty($pixelfed_token)) {
            $pixelfed_instance = get_option('fediembedi-pixelfed-instance');
            $client = new \FediClient($pixelfed_instance);
            $pixelfed_account = $client->verify_credentials($pixelfed_token);
        }

        include 'fediembedi-settings-form.tpl.php';
    }

    /**
     * Admin_notices
     * Show the notice (error or info)
     *
     * @return void
     */
    public function admin_notices()
    {

        $notice = unserialize(get_option('fediembedi-notice'));

        if (is_array($notice)) {
            echo '<div class="notice notice-' . sanitize_html_class($notice['class']) . ' is-dismissible"><p>' . $notice['message'] . '</p></div>';
            update_option('fediembedi-notice', null);
        }
    }

    /**
     * @param $links
     *
     * @return array
     */
    function fediembedi_add_plugin_page_settings_link( $links ) {
      $links[] = '<a href="' . admin_url( 'options-general.php?page=fediembedi' ) . '">' . __('Configuration', 'fediembedi') . '</a>';
      $links[] = '<a href="https://git.feneas.org/mediaformat/fediembedi/issues" target="_blank">' . __('Support', 'fediembedi') . '</a>';
    	return $links;
    }

}

$fediconfig = new FediConfig();
