<?php

class FediEmbedi_PeerTube extends WP_Widget {

	/**
	 * Sets up a new FediEmbedi widget instance.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'peertube_widget',
			'description' => __( 'Display a profile timeline', 'fediembedi' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'peertube', _x( 'PeerTube', 'Title','fediembedi' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current PeerTube widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current PeerTube widget instance.
	 */
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		// create unique transient name from widget options 
		$widget_instance = md5( serialize( $instance ) );
		if ( false === ( $status = get_transient( "peertube_$widget_instance" ) ) ) {
		
			$fedi_instance = !empty($instance['peertube'] ) ? esc_url_raw( $instance['peertube'] ) : '';
			$actor =  !empty( $instance['actor'] ) ? $instance['actor'] : '';
			$is_channel = !empty( $instance['channel'] ) ? boolval( $instance['channel'] ) : null;//radio channel or account
			$show_header = !empty( $instance['show_header'] ) ? boolval( $instance['show_header'] ) : null;
			$count    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
			$nsfw    = isset( $instance['nsfw'] ) ? boolval( $instance['nsfw'] ) : null;
			$height    = isset( $instance['height'] ) ? sanitize_text_field( $instance['height'] ) : '100%';
			$cache_time    = isset( $instance['cache'] ) ? sanitize_text_field( $instance['cache'] ) : 2 * HOUR_IN_SECONDS;

			if ( !$fedi_instance || !$actor ) {
				return;
			}
			$client = new \FediClient( $fedi_instance );
			if ( !$client ) {
				return;
			}
			echo $args['before_widget'];
			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			};

			//getVideos from remote instance
			$status = $client->getVideos( $actor, $is_channel, $count, $nsfw );
			set_transient( "peertube_$widget_instance", $status, $cache_time );
		}

		if( !is_null( $is_channel ) ) {
			$account = $status->data[0]->channel;
		} else {
			$account = $status->data[0]->account;
		}
    	include( plugin_dir_path(__FILE__) . 'templates/peertube.tpl.php' );

		echo $args['after_widget'];
	}

	/**
	 * Outputs the settings form for the PeerTube widget.
	 *
	 * @since 2.8.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
        //Radio inputs : https://wordpress.stackexchange.com/a/276659/87622

		$peertube =  !empty( $instance['peertube'] ) ? esc_url_raw( $instance['peertube'] ) : NULL;
		$actor = !empty( $instance['actor'] ) ? $instance['actor'] : NULL;
		$is_channel = !empty( $instance['channel'] ) ? $instance['channel'] : NULL;
		$show_header = !empty( $instance['show_header'] ) ? $instance['show_header'] : NULL;
		$number    = isset( $instance['number'] ) ? $instance['number'] : 5;
		$nsfw    = isset( $instance['nsfw'] ) ? $instance['nsfw'] : false;
		$height    = isset( $instance['height'] ) ? $instance['height'] : '';
		$cache_time    = isset( $instance['cache'] ) ? $instance['cache'] : 2 * HOUR_IN_SECONDS;

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'fediembedi' ); ?>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'peertube' ); ?>"><?php _e('PeerTube instance:', 'fediembedi'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('peertube'); ?>" name="<?php echo $this->get_field_name('peertube'); ?>" type="text" value="<?php echo esc_url_raw( $peertube, 'https' ); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('actor'); ?>"><?php _e('User or Channel name:', 'fediembedi'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('actor'); ?>" name="<?php echo $this->get_field_name('actor'); ?>" type="text" value="<?php echo $actor; ?>" />
			</label>
		</p>
		<p>
			<label>
				<input
					type="checkbox"
					<?php checked( $instance[ 'channel' ], '1' ); ?>
					id="<?php echo $this->get_field_id( 'channel' ); ?>"
					name="<?php echo $this->get_field_name('channel'); ?>"
					value="1"
				/><?php _e( 'This account is a Channel (not a user)', 'fediembedi' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input
					type="checkbox"
					<?php checked( $instance[ 'show_header' ], '1' ); ?>
					id="<?php echo $this->get_field_id( 'show_header' ); ?>"
					name="<?php echo $this->get_field_name('show_header'); ?>"
					value="1"
				/><?php _e( 'Show header', 'fediembedi' ); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to display:', 'fediembedi' ); ?><br>
				<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
				<small>Max: 20</small>
			</label>
		</p>
		<p>
			<label>
				<input
					type="checkbox"
					<?php checked( $instance[ 'nsfw' ], '0' ); ?>
					id="<?php echo $this->get_field_id( 'nsfw' ); ?>"
					name="<?php echo $this->get_field_name('nsfw'); ?>"
					value="0"
				/><?php _e( 'Show NSFW videos?', 'fediembedi' ); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Widget height:', 'fediembedi' ); ?><br>
				<input class="" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo $height; ?>" placeholder="500px" size="5" />
				<small><?php _e( 'Default: 100%', 'fediembedi' ); ?></small>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'cache' ); ?>"><?php _e( 'Cache duration:', 'fediembedi' ); ?><br>
				<input class="" id="<?php echo $this->get_field_id( 'cache' ); ?>" name="<?php echo $this->get_field_name( 'cache' ); ?>" type="text" value="<?php echo esc_attr( $cache_time ); ?>" placeholder="2 * HOUR_IN_SECONDS" size="5" />
				<small><?php _e( 'Default: 2 * HOUR_IN_SECONDS', 'fediembedi' ); ?></small>
				<details><summary><?php _e( 'Time constants', 'fediembedi' ); ?></summary>
					MINUTE_IN_SECONDS
					HOUR_IN_SECONDS
					DAY_IN_SECONDS
				</details>
			</label>
		</p>
		<?php
	}

	/**
	 * Handles updating settings for the current PeerTube widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$new_instance      = wp_parse_args( (array) $new_instance, array( 'title' => '' ) );
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['peertube'] = esc_url_raw($new_instance['peertube']);
		$instance['actor'] = sanitize_key($new_instance['actor']);
		$instance['channel'] = boolval( $new_instance['channel'] );
		$instance['number']    = (int) $new_instance['number'];
		$instance['nsfw']    = boolval( $new_instance['nsfw'] );
		$instance['height']     = sanitize_text_field( $new_instance['height'] );
		$instance['cache']   = sanitize_text_field( $new_instance['cache'] );
		return $instance;
	}
}
