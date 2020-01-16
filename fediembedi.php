<?php
/**
 * Plugin Name: FediEmbedi
 * Plugin URI: https://git.feneas.org/mediaformat/fediembedi
 * Github Plugin URI: https://github.com/mediaformat/fediembedi
 * Description: A widget to show your Mastodon profile timeline
 * Version: 0.7.2
 * Author: mediaformat
 * Author URI: https://mediaformat.org
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain: fediembedi
 * Domain Path: /languages
 */
namespace FediEmbedi;
require_once 'fediembedi-client.php';

class FediConfig
{
    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'init'));
        add_action('widgets_init', array($this, 'fediembedi_widget'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_menu', array($this, 'configuration_page'));
        add_action('admin_notices', array($this, 'admin_notices'));
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
            $code = $_GET['code'];
            $client_id = get_option('fediembedi-client-id');
            $client_secret = get_option('fediembedi-client-secret');

            if (!empty($code) && !empty($client_id) && !empty($client_secret)) {
                //echo __('Authentification, please wait', 'fediembedi') . '...';

                update_option('fediembedi-token', 'nada');

                $instance = get_option('fediembedi-instance');
                $client = new \FediClient($instance);
                $token = $client->get_bearer_token($client_id, $client_secret, $code, get_admin_url());
                //$instance_info = $client->getInstance();

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
                    update_option('fediembedi-token', '');
                } else {
                    update_option('fediembedi-client-id', '');
                    update_option('fediembedi-client-secret', '');
                    update_option('fediembedi-token', $token->access_token);
                    //update_option('fediembedi-instance-type', $instance_type);

                }
                $redirect_url = get_admin_url() . 'options-general.php?page=fediembedi';
            } else {
                //Probably hack or bad refresh, redirect to homepage
                $redirect_url = home_url();
            }

            wp_redirect($redirect_url);
            exit;
        }

        $token = get_option('fediembedi-token');

    }

    /*
     *  Widget
     */
    public function fediembedi_widget() {
    	include(plugin_dir_path(__FILE__) . 'fediembedi-widget.php' );//
    	register_widget( 'WP_Widget_fediembedi' );
    }

    public function enqueue_styles($hook)
    {
        if( is_active_widget( false, false, 'fediembedi') ) {
          $instance_type = get_option('fediembedi-instance-type');
          switch ($instance_type) {
    		      case 'Mastodon':
    		        wp_enqueue_style( 'mastodon', plugin_dir_url( __FILE__ ) . 'assets/mastodon.css', array(), filemtime(plugin_dir_path( __FILE__ ) . 'assets/mastodon.css') );
    		        break;
    		      case 'Pixelfed':
                wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'assets/bootstrap/css/bootstrap.min.css', array(), '4.4.1' );
                wp_enqueue_style( 'pixelfed', plugin_dir_url( __FILE__ ) . 'assets/pixelfed.css', array(), filemtime(plugin_dir_path( __FILE__ ) . 'pixelfed/pixelfed.css') );
                //https://css-tricks.com/lozad-js-performant-lazy-loading-images/ lazyloading for background images
    		        break;
    		      default:
    		        wp_enqueue_style( 'fediembedi', plugin_dir_url( __FILE__ ) . 'assets/mastodon.css', array(), filemtime(plugin_dir_path( __FILE__ ) . 'assets/mastodon.css') );
    		        break;
    		    }
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

        if (isset($_GET['disconnect'])) {
            update_option('fediembedi-token', '');
        }

        $token = get_option('fediembedi-token');

        if (isset($_POST['save'])) {

            $is_valid_nonce = wp_verify_nonce($_POST['_wpnonce'], 'fediembedi-configuration');

            if ($is_valid_nonce) {
                $instance = esc_url($_POST['instance']);
                $instance_type = esc_attr($_POST['instance_type']);
                //TODO switch($instance_type) case() return $scopes

                $client = new \FediClient($instance);
                $redirect_url = get_admin_url();

                $instance_type = get_option('fediembedi-instance-type');
                switch ($instance_type) {
                    case 'Mastodon':
                      $auth_url = $client->register_app($redirect_url);
                      break;
                    case 'Pixelfed':
                      $auth_url = $client->register_app($redirect_url);
                      break;
                    case 'PeerTube':
                      $auth_url = $client->register_client($redirect_url, 'user');
                      break;
                }
                //$auth_url = $client->register_app($redirect_url, $scopes);

                if ($auth_url == "ERROR") {
                    //var_dump('$auth_url = ERROR'); //die;
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
                  //var_dump($auth_url); //die;
                    if (empty($instance)) {
                      //var_dump($instance); //die;
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
                        update_option('fediembedi-client-id', $client->get_client_id());
                        update_option('fediembedi-client-secret', $client->get_client_secret());
                        update_option('fediembedi-instance', $instance);
                        update_option('fediembedi-instance-type', $instance_type);

                        $account = $client->verify_credentials($token);

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

        $instance = get_option('fediembedi-instance');
        $instance_type = get_option('fediembedi-instance-type');

        if (!empty($token)) {
            $client = new \FediClient($instance);
            $account = $client->verify_credentials($token);
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
    	return $links;
    }

}

$fediconfig = new FediConfig();
