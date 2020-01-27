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
		parent::__construct( 'peertube', _x( 'PeerTube', 'fediembedi' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Search widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Search widget instance.
	 */
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		//fedi instance
		$fedi_instance = (!empty($instance['peertube'])) ? $instance['peertube'] : '';
		$actor = (!empty($instance['actor'])) ? $instance['actor'] : '';
		$is_channel = (!empty($instance['channel'])) ? $instance['channel'] : null;//radio channel or account

		$client = new \FediClient($fedi_instance);

		//widget options
		$show_header = (!empty($instance['show_header'])) ? $instance['show_header'] : null;
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$height    = isset( $instance['height'] ) ? esc_attr( $instance['height'] ) : '100%';

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		};

		//getVideos from remote instance
		$status = $client->getVideos($actor, $is_channel);
		if(!is_null($is_channel)){
			$account = $status->data[0]->channel;
		} else {
			$account = $status->data[0]->account;
		}
		if(WP_DEBUG_DISPLAY === true): echo '<details><summary>PeerTube</summary><pre>'; var_dump($status); echo '</pre></details>'; endif;
    include(plugin_dir_path(__FILE__) . 'templates/peertube.tpl.php' );

		echo $args['after_widget'];
	}

	/**
	 * Outputs the settings form for the Search widget.
	 *
	 * @since 2.8.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
        //Radio inputs : https://wordpress.stackexchange.com/a/276659/87622

		$peertube = (!empty($instance['peertube'])) ? $instance['peertube'] : NULL;
		$actor = (!empty($instance['actor'])) ? $instance['actor'] : NULL;
		$is_channel = (!empty($instance['channel'])) ? $instance['channel'] : NULL;

		$show_header = (!empty( $instance['show_header'])) ? $instance['show_header'] : NULL;
		$only_media = (!empty( $instance['only_media'])) ? $instance['only_media'] : NULL;
		$pinned = (!empty($instance['pinned'])) ? $instance['pinned'] : NULL;
		$exclude_replies = (!empty($instance['exclude_replies'])) ? $instance['exclude_replies'] : NULL;
		$exclude_reblogs = (!empty($instance['exclude_reblogs'])) ? $instance['exclude_reblogs'] : NULL;
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$height    = isset( $instance['height'] ) ? esc_attr( $instance['height'] ) : '';

		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'fediembedi'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('peertube'); ?>"><?php _e('PeerTube instance:', 'fediembedi'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('peertube'); ?>" name="<?php echo $this->get_field_name('peertube'); ?>" type="text" value="<?php echo esc_url($peertube, 'https'); ?>" />
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
              <?php checked( $instance[ 'show_header' ], '1' ); ?>
              id="<?php echo $this->get_field_id( 'show_header' ); ?>"
              name="<?php echo $this->get_field_name('show_header'); ?>"
              value="1"
          /><?php _e( 'Show header', 'fediembedi' ); ?>
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
          /><?php _e( 'Is this account a Channel?', 'fediembedi' ); ?>
      </label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to display:' ); ?><br>
				<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
				<small>Max: 20</small>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Widget height:' ); ?><br>
				<input class="" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo $height; ?>" placeholder="500px" size="5" />
				<small><?php _e( 'Default: 100%', 'fediembedi' ); ?></small>
			</label>
		</p>
		<?php
	}

	/**
	 * Handles updating settings for the current Search widget instance.
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
		$instance['peertube'] = esc_url($new_instance['peertube']);
		$instance['actor'] = sanitize_key($new_instance['actor']);
		$instance['channel'] = $new_instance['channel'];
		$instance['show_header'] = $new_instance['show_header'];
		$instance['only_media'] = $new_instance['only_media'];
		$instance['pinned'] = $new_instance['pinned'];
		$instance['exclude_replies'] = $new_instance['exclude_replies'];
		$instance['exclude_reblogs'] = $new_instance['exclude_reblogs'];
		$instance['number']    = (int) $new_instance['number'];
		$instance['height']     = sanitize_text_field( $new_instance['height'] );
		return $instance;
	}

}
