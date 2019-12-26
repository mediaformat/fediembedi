<?php

class WP_Widget_fediembedi extends WP_Widget {

	/**
	 * Sets up a new Search widget instance.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_fediembedi',
			'description' => __( 'Display a profile timeline', 'fediembedi' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'fediembedi', _x( 'FediEmbedi', 'fediembedi' ), $widget_ops );
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
		include(plugin_dir_path(__FILE__) . 'fediembedi-widget-template.php' );//fediembedi_widget_template
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
		$show_header = (!empty( $instance['show_header'])) ? $instance['show_header'] : NULL;
		$only_media = (!empty( $instance['only_media'])) ? $instance['only_media'] : NULL;
		$pinned = (!empty($instance['pinned'])) ? $instance['pinned'] : NULL;
		$exclude_replies = (!empty($instance['exclude_replies'])) ? $instance['exclude_replies'] : NULL;
		$exclude_reblogs = (!empty($instance['exclude_reblogs'])) ? $instance['exclude_reblogs'] : NULL;
		$remote_instance = get_option('fediembedi-instance');
		$client = new \Client($remote_instance);
		$instance_info = $client->getInstance();

		$pixelfed = '';
		if (strpos($instance_info->version, 'Pixelfed') !== false) {
		    $pixelfed = true;
		}
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
        <br>
        <label>
            <input
                type="checkbox"
                <?php checked( $instance[ 'only_media' ], '1' ); ?>
                id="<?php echo $this->get_field_id( '1' ); ?>"
                name="<?php echo $this->get_field_name('only_media'); ?>"
                value="1"
            /><?php _e( 'Only media', 'fediembedi' ); ?>
        </label>
        <br>
        <label>
            <input
                type="checkbox"
                <?php checked( $instance[ 'pinned' ], '1' ); ?>
                id="<?php echo $this->get_field_id( '1' ); ?>"
                name="<?php echo $this->get_field_name('pinned'); ?>"
                value="1"
            /><?php _e( 'Show pinned statuses', 'fediembedi' ); ?>
        </label>
        <br>
        <label>
            <input
                type="checkbox"
                <?php checked( $instance[ 'exclude_replies' ], '1' ); ?>
                id="<?php echo $this->get_field_id( '1' ); ?>"
                name="<?php echo $this->get_field_name('exclude_replies'); ?>"
                value="1"
            /><?php _e( 'Hide replies', 'fediembedi' ); ?>
        </label>
        <br>
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
		$instance['show_header'] = $new_instance['show_header'];
		$instance['only_media'] = $new_instance['only_media'];
		$instance['pinned'] = $new_instance['pinned'];
		$instance['exclude_replies'] = $new_instance['exclude_replies'];
		$instance['exclude_reblogs'] = $new_instance['exclude_reblogs'];
		return $instance;
	}

}
