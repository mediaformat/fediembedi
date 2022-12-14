<?php
/**
 * Plugin Name: FediEmbedi
* Plugin URI: https://codeberg.org/mediaformat/fediembedi
 * Gitea Plugin URI: https://codeberg.org/mediaformat/fediembedi
 * Description: Widgets and shortcodes to show your Fediverse profile timeline
 * Version: 0.12.0
 * Author: mediaformat
 * Author URI: https://mediaformat.org
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain: fediembedi
 * Domain Path: /languages
 */
namespace FediEmbedi;
require_once 'includes/class-client.php';

class FediConfig {
    public function __construct() {
      add_action( 'plugins_loaded', array( $this, 'init' ) ); // SAVE OPTIONS
      add_action( 'widgets_init', array( $this, 'fediembedi_widget' ) );
      add_shortcode( 'mastodon', array( $this, 'mastodon_shortcode' ) );
      add_shortcode( 'pixelfed', array( $this, 'pixelfed_shortcode' ) );
      add_shortcode( 'peertube', array( $this, 'peertube_shortcode' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
      add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
      add_action( 'admin_menu', array( $this, 'configuration_page' ) ); // show_configuration_page / registration
      add_action( 'admin_notices', array( $this, 'admin_notices' ) );
      add_action( 'wp_ajax_dismissed_notice_handler', array( $this, 'ajax_notice_dismisser' ) );
      add_filter( 'fedi_emoji', array($this, 'convert_emoji'), 10, 2 );
      add_filter( 'plugin_row_meta', array( $this, 'plugin_support_and_faq_links' ), 10, 2 );
      add_filter( 'plugin_action_links_'.plugin_basename( __FILE__ ), array( $this, 'fediembedi_add_plugin_page_settings_link') );

    }

    /**
     * Init
     *
     * Plugin initialization
     *
     * @return void
     */
    public function init() {
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
                //echo __('Authentication, please wait', 'fediembedi') . '...';

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

                if ( isset($token->error ) ) {
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
      include( plugin_dir_path( __FILE__ ) . 'includes/class-mastodon-widget.php' );
      register_widget( 'FediEmbedi_Mastodon' );
      if( empty( get_option( 'fediembedi-mastodon-token' ) ) ) {
        unregister_widget( 'FediEmbedi_Mastodon' );
      }

      //Pixelfed
      include( plugin_dir_path( __FILE__ ) . 'includes/class-pixelfed-widget.php' );
      register_widget( 'FediEmbedi_Pixelfed' );
      if( empty( get_option( 'fediembedi-pixelfed-token' ) ) ) {
        unregister_widget( 'FediEmbedi_Pixelfed' );
      }

      //PeerTube
      include( plugin_dir_path( __FILE__ ) . 'includes/class-peertube-widget.php' );
    	register_widget( 'FediEmbedi_PeerTube' );
    }

    public function mastodon_shortcode( $atts ) {
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
        'height' => '100%',
        'cache' => 2 * HOUR_IN_SECONDS,
      ), $atts, 'mastodon' );
      
      // create unique transient name from shortcode options 
      $shortcode_atts = md5( serialize( $atts ) );
      if ( false === ( $status = get_transient( "mastodon_$shortcode_atts" ) ) ) {
        $fedi_instance = get_option( 'fediembedi-mastodon-instance' );
        $access_token = get_option( 'fediembedi-mastodon-token' );
        $client = \FediEmbedi\FediConfig::fedi_client( $fedi_instance, 'mastodon' );
        $verify = $client->verify_credentials( $access_token );
        if ( is_wp_error( $verify ) ){
          return;
        }

        //getStatus from remote instance
        $status = $client->getStatus( $atts['only_media'], $atts['pinned'], $atts['exclude_replies'], null, null, null, $atts['limit'], $atts['exclude_reblogs'] );
        set_transient( "mastodon_$shortcode_atts", $status, $atts['cache'] );
		  }
      
      $show_header = $atts['show_header'];
      $account = $status[0]->account;
      ob_start();
      include( plugin_dir_path( __FILE__ ) . 'templates/mastodon.tpl.php' );
      return ob_get_clean();
    }

    public function pixelfed_shortcode( $atts ) {
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
        'height' => '100%',
        'cache' => 2 * HOUR_IN_SECONDS,
      ), $atts, 'pixelfed' );

      // create unique transient name from shortcode options 
      $shortcode_atts = md5( serialize( $atts ) );
      delete_transient( "pixelfed_$shortcode_atts" );
      if ( false === ( $status = get_transient( "pixelfed_$shortcode_atts" ) ) ) {
      //fedi instance
        $fedi_instance = get_option( 'fediembedi-pixelfed-instance' );
        $access_token = get_option( 'fediembedi-pixelfed-token' );
        $client = \FediEmbedi\FediConfig::fedi_client( $fedi_instance, 'pixelfed' );
        $verify = $client->verify_credentials( $access_token );
        if ( is_wp_error( $verify ) ){
          return;
        }

        //getStatus from remote instance
        $status = $client->getStatus( $atts['only_media'], $atts['pinned'], $atts['exclude_replies'], null, null, null, $atts['limit'], $atts['exclude_reblogs'] );
        set_transient( "pixelfed_$shortcode_atts", $status, $atts['cache'] );
		  }
      
      $show_header = $atts['show_header'];
      $account = $status[0]->account;
      ob_start();
      include(plugin_dir_path(__FILE__) . 'templates/pixelfed.tpl.php' );
      return ob_get_clean();
    }

    public function peertube_shortcode( $atts ) {
      $atts = shortcode_atts( array(
        'instance' => null,
        'actor' => null,
        'is_channel' => null,
        'limit' => 9,
        'nsfw' => null,
        'show_header' => true,
        'height' => '100%',
        'cache' => 2 * HOUR_IN_SECONDS,
      ), $atts, 'peertube' );

      $shortcode_atts = md5( serialize( $atts ) );
      if ( false === ( $status = get_transient( "peertube_$shortcode_atts" ) ) ) {
        $atts['instance'] = \esc_url_raw( $atts['instance'], 'https' );
        $client = \FediEmbedi\FediConfig::fedi_client( $atts['instance'] );
        $verify = $client->verify_credentials( $access_token );
        if ( is_wp_error( $verify ) ){
          return;
        }

        //getVideos from remote instance
        $status = $client->getVideos( $atts['actor'], $atts['is_channel'], $atts['limit'], $atts['nsfw'] );
        set_transient( "peertube_$shortcode_atts", $status, $atts['cache'] );
      }
      if(!is_null($atts['is_channel'])){
        $account = $status->data[0]->channel;
      } else {
        $account = $status->data[0]->account;
      }

      $show_header = $atts['show_header'];
      $height = $atts['height'];
      ob_start();
      include( plugin_dir_path( __FILE__ ) . 'templates/peertube.tpl.php' );
      return ob_get_clean();
    }

    /*
     * convert_emoji
     */
    public function convert_emoji( $string, $emojis ) {
      if(is_null($emojis) || !is_array($emojis)){
        return $string;
      }
      foreach( $emojis as $emoji ) {
           $match = '/:' . $emoji->shortcode .':/';
           $string = preg_replace($match, "<img draggable=\"false\" role=\"img\" class=\"emoji\" src=\"{$emoji->static_url}\">", $string);
      }
      return $string;
    }

    public function enqueue_styles($hook) {
        global $post;
        //if( is_active_widget( false, false, 'mastodon') || is_active_widget( false, false, 'pixelfed') || ( is_a( $post, 'WP_Post' ) && ( has_shortcode( $post->post_content, 'mastodon') || has_shortcode( $post->post_content, 'pixelfed') ) ) ) {
            wp_enqueue_script( 'resize-sensor', plugin_dir_url( __FILE__ ) . 'assets/ResizeSensor.js', array(), 'css-element-queries-1.2.2' );
            wp_enqueue_script( 'element-queries', plugin_dir_url( __FILE__ ) . 'assets/ElementQueries.js', array('resize-sensor'), 'css-element-queries-1.2.2' );
        //}
        //if( is_active_widget( false, false, 'mastodon')  || ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'mastodon') ) ) {
            wp_enqueue_style( 'mastodon', plugin_dir_url( __FILE__ ) . 'assets/mastodon.css', array(), filemtime(plugin_dir_path( __FILE__ ) . 'assets/mastodon.css') );
        //}
        //if( is_active_widget( false, false, 'pixelfed')  || ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'pixelfed') )  ) {
            wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'assets/bootstrap/css/bootstrap.min.css', array(), '4.4.1' );
            wp_enqueue_style( 'pixelfed', plugin_dir_url( __FILE__ ) . 'assets/pixelfed.css', array(), filemtime(plugin_dir_path( __FILE__ ) . 'assets/pixelfed.css') );
        //}
        //if( is_active_widget( false, false, 'peertube')  || ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'peertube') ) ) {
            wp_enqueue_style( 'peertube', plugin_dir_url( __FILE__ ) . 'assets/peertube.css', array(), filemtime(plugin_dir_path( __FILE__ ) . 'assets/mastodon.css') );
        //}
    }

    public function admin_enqueue_scripts( $hook ) {
      global $pagenow;
      $infos = get_plugin_data(__FILE__);
      ///if ($pagenow == "options-general.php") {
          $plugin_url = plugin_dir_url(__FILE__);
          wp_enqueue_script('settings_page', $plugin_url . 'assets/admin.js', array( 'jquery' ), $infos['Version'], true );
      //}
    }

    /**
     * Configuration_page
     *
     * Add the configuration page menu
     *
     * @return void
     */
    public function configuration_page() {
        add_options_page(
            'FediEmbedi',
            'FediEmbedi',
            'manage_options',
            'fediembedi',
            array( $this, 'show_configuration_page' )
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
    public function show_configuration_page() {

        wp_enqueue_style( 'fediembedi-configuration', plugin_dir_url( __FILE__ ) . 'style.css' );

        if ( isset($_GET['fediembedi-disconnect'] ) ) {
          switch ( $_GET['fediembedi-disconnect'] ) {
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

        if ( isset( $_POST['save'] ) ) {

            $is_valid_nonce = wp_verify_nonce( $_POST['_wpnonce'], 'fediembedi-configuration' );

            if ( $is_valid_nonce ) {

                $instance = esc_url( $_POST['instance'] );
                $instance_type = esc_attr( $_POST['instance_type'] );

                $client = new \FediClient( $instance );
                //$redirect_url = get_admin_url() . '?instance_type=' . $instance_type;
                $redirect_url = add_query_arg( array( 'instance_type' => $instance_type ), get_admin_url() );

                $auth_url = $client->register_app( $redirect_url );

                //if ( $auth_url == "ERROR" ) { // TODO convert to wp_error
                if ( is_wp_error( $auth_url ) ) { // TODO convert to wp_error
                    update_option(
                      'fediembedi-notice',
                      serialize(
                        array(
                          'message' => '<strong>FediEmbedi</strong> : ' . __( 'The given instance url belongs to an unrecognized service!', 'fediembedi' ),
                          'class' => 'error',
                        )
                      )
                    );
                } else {
                    if ( empty( $instance ) ) {
                        update_option(
                          'fediembedi-notice',
                          serialize(
                            array(
                              'message' => '<strong>FediEmbedi</strong> : ' . __( 'Please set your instance before you connect !', 'fediembedi' ),
                              'class' => 'error',
                            )
                          )
                        );
                    } else {
                        switch ( $instance_type ) {
                          case 'mastodon':
                            update_option( 'fediembedi-mastodon-client-id', $client->get_client_id() );
                            update_option( 'fediembedi-mastodon-client-secret', $client->get_client_secret() );
                            update_option( 'fediembedi-mastodon-instance', $instance );
                            $mastodon_account = $client->verify_credentials( $mastodon_token );
                            $account = $mastodon_account;
                            break;
                          case 'pixelfed':
                            update_option( 'fediembedi-pixelfed-client-id', $client->get_client_id() );
                            update_option( 'fediembedi-pixelfed-client-secret', $client->get_client_secret() );
                            update_option( 'fediembedi-pixelfed-instance', $instance );
                            $pixelfed_account = $client->verify_credentials( $pixelfed_token );
                            $account = $pixelfed_account;
                            break;
                        }

                        //$account = $client->verify_credentials($token);

                        if ( is_null( $account ) || isset( $account->error ) ) {
                            echo '<meta http-equiv="refresh" content="0; url=' . $auth_url . '" />';
                            echo __( 'Redirect to login: ', 'fediembedi' ) . $instance;
                            exit;
                        }

                        //Inform user that save was successfull
                        update_option(
                          'fediembedi-notice',
                          serialize(
                            array(
                              'message' => '<strong>FediEmbedi</strong> : ' . __( 'Configuration successfully saved!', 'fediembedi' ),
                              'class' => 'success',
                            )
                          )
                        );
                    }
                }
                $this->admin_notices();
            }
        }

        if ( !empty( $mastodon_token ) ) {
            $mastodon_instance = get_option( 'fediembedi-mastodon-instance' );
            $client = new \FediClient( $mastodon_instance );
            $mastodon_account = $client->verify_credentials( $mastodon_token );
        }
        if ( !empty( $pixelfed_token ) ) {
            $pixelfed_instance = get_option( 'fediembedi-pixelfed-instance' );
            $client = new \FediClient( $pixelfed_instance );
            $pixelfed_account = $client->verify_credentials( $pixelfed_token );
        }
        include 'includes/class-settings-form.tpl.php';
    }

    /**
     * get FediClient and verify_credentials
     */
    public static function fedi_client( $instance, $type = null ) {
      if ( $type ) {
        $token = get_option( "fediembedi-$type-token" );
        $client = new \FediClient( $instance, $token );
        $credentials = $client->verify_credentials($token);
        if ( isset( $credentials->error ) ) {
          update_option(
            'fediembedi-notice',
            serialize(
              array( // TODO fix error translation handling, and clean up strings
                'message' => '<strong>FediEmbedi</strong> : ' . _x( "$credentials->error.", 'fediembedi' ) .
                '<p>' . sprintf( wp_kses( __( "Please <a href='%s'>re-authorize</a> your " . ucfirst($type) . " account.", 'fediembedi' ), 
                  array(  'a' => array( 'href' => array() ) ) ), admin_url( 'options-general.php?page=fediembedi' ) ) . '</p>',
                'class' => 'error',
              )
            )
          );
          delete_option( "fediembedi-$type-token" );
          return null;
        } 
      } else {
        $client = new \FediClient( $instance );
      }
      return $client;
    }

    /**
     * Admin_notices
     * Show the notice (error or info)
     *
     * @return void
     */
    public function admin_notices() {
      global $pagenow;
      $admin_pages = array( 'index.php', 'admin.php', 'plugins.php', 'options-general.php' );
      if ( in_array( $pagenow, $admin_pages ) ) {
        $notice = unserialize( get_option( 'fediembedi-notice' ) );
        if ( is_array( $notice ) ) {
          printf( '<div class="notice notice-fediembedi is-dismissible notice-%1$s"><p>%2$s</p></div>', 
            esc_attr( $notice['class'] ),
            wp_kses_post( $notice['message'] ) 
          );
        }
      }
    }

    /**
     * AJAX handler to store the state of dismissible notices.
     */
    function ajax_notice_dismisser() {
      update_option( 'fediembedi-notice', null );
    }

    /**
     * @param $links
     *
     * @return array
     */
    function fediembedi_add_plugin_page_settings_link( $links ) {
      $links[] = '<a href="' . admin_url( 'options-general.php?page=fediembedi' ) . '">' . __('Configuration', 'fediembedi') . '</a>';
    	return $links;
    }

    /**
     * @param $plugin_meta
     *
     * @return array
     */
    function plugin_support_and_faq_links( $plugin_meta, $plugin_file ) {
      if ( strpos( $plugin_file, basename(__FILE__) ) ) {
        $plugin_meta[] = '<a href="https://codeberg.org/mediaformat/fediembedi/issues" target="_blank" rel="noopener">Bugs</a>';
        $plugin_meta[] = '<a href="https://paypal.me/mediaformat" target="_blank" rel="noopener">Support Development</a>';
      }
      return $plugin_meta;
    }

}

$fediconfig = new FediConfig();
