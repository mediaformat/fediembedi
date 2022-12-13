<?php

class FediEmbedi_Pixelfed extends WP_Widget {

	/**
	 * Sets up a new FediEmbedi widget instance.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'pixelfed_widget',
			'description' => __( 'Display a profile timeline', 'fediembedi' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'pixelfed', _x( 'Pixelfed', 'fediembedi' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Pixelfed widget instance.
	 *
	 * @since 2.8.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Pixelfed widget instance.
	 */
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		//fedi instance
		$instance_url = get_option('fediembedi-pixelfed-instance');
		$access_token = get_option('fediembedi-pixelfed-token');
		$client = new \FediClient($instance_url, $access_token);
		$cred = $client->verify_credentials($access_token);
		if (!$cred){
			return;
		}

		//widget options
		$show_header = (!empty($instance['show_header'])) ? $instance['show_header'] : '';
		$only_media = (!empty($instance['only_media'])) ? $instance['only_media'] : '';
		$pinned = (!empty($instance['pinned'])) ? $instance['pinned'] : '';
		$exclude_replies = (!empty($instance['exclude_replies'])) ? $instance['exclude_replies'] : '';
		$exclude_reblogs = (!empty($instance['exclude_reblogs'])) ? $instance['exclude_reblogs'] : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$height    = isset( $instance['height'] ) ? esc_attr( $instance['height'] ) : '100%';

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		};

		$status = $client->getStatus($only_media, $pinned, $exclude_replies, null, null, null, $number, $exclude_reblogs);
		$account = $status[0]->account;
      	include(plugin_dir_path(__FILE__) . 'templates/pixelfed.tpl.php' );

		echo $args['after_widget'];
	}

	/**
	 * Outputs the settings form for the Pixelfed widget.
	 *
	 * @since 2.8.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
        //Radio inputs : https://wordpress.stackexchange.com/a/276659/87622
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
      <label>
          <input
              type="checkbox"
              <?php checked( $instance[ 'show_header' ], '1' ); ?>
              id="<?php echo $this->get_field_id( '1' ); ?>"
              name="<?php echo $this->get_field_name('show_header'); ?>"
              value="1"
          /><?php _e( 'Show header', 'fediembedi' ); ?>
      </label>
	</p>
	<p>
      <label>
          <input
              type="checkbox"
              <?php checked( $instance[ 'only_media' ], '1' ); ?>
              id="<?php echo $this->get_field_id( '1' ); ?>"
              name="<?php echo $this->get_field_name('only_media'); ?>"
              value="1"
          /><?php _e( 'Only show media', 'fediembedi' ); ?>
      </label>
	</p>
	<p>
      <label>
          <input
              type="checkbox"
              <?php checked( $instance[ 'pinned' ], '1' ); ?>
              id="<?php echo $this->get_field_id( '1' ); ?>"
              name="<?php echo $this->get_field_name('pinned'); ?>"
              value="1"
          /><?php _e( 'Only show pinned statuses', 'fediembedi' ); ?>
      </label>
	</p>
	<p>
      <label>
          <input
              type="checkbox"
              <?php checked( $instance[ 'exclude_replies' ], '1' ); ?>
              id="<?php echo $this->get_field_id( '1' ); ?>"
              name="<?php echo $this->get_field_name('exclude_replies'); ?>"
              value="1"
          /><?php _e( 'Hide replies', 'fediembedi' ); ?>
      </label>
	</p>
	<p>
      <label>
          <input
              type="checkbox"
              <?php checked( $instance[ 'exclude_reblogs' ], '1' ); ?>
              id="<?php echo $this->get_field_id( '1' ); ?>"
              name="<?php echo $this->get_field_name('exclude_reblogs'); ?>"
              value="1"
          /><?php _e( 'Hide reblogs', 'fediembedi' ); ?>
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
	 * Handles updating settings for the current Pixelfed widget instance.
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
		$instance['show_header'] = boolval( $new_instance['show_header']);
		$instance['only_media'] = boolval( $new_instance['only_media'] );
		$instance['pinned'] = boolval( $new_instance['pinned'] );
		$instance['exclude_replies'] = boolval( $new_instance['exclude_replies'] );
		$instance['exclude_reblogs'] = boolval( $new_instance['exclude_reblogs'] );
		$instance['number']    = (int) $new_instance['number'];
		$instance['height']     = sanitize_text_field( $new_instance['height'] );
		return $instance;
	}
}
