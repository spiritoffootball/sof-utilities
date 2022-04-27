<?php
/**
 * SOF Child Pages Widget.
 *
 * A widget that display a list of child pages for a given page. The given page
 * is the one that the widget is displayed on.
 *
 * @since 0.3
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement a "Child Pages" widget.
 *
 * @since 0.3
 *
 * @see WP_Widget
 */
class SOF_Widget_Child_Pages extends WP_Widget {

	/**
	 * Constructor registers widget with WordPress.
	 *
	 * @since 0.3
	 */
	public function __construct() {

		// Use the class `widget_recent_entries` to inherit WP Recent Posts widget styling.
		$widget_ops = [
			'classname' => 'widget_child_pages',
			'description' => __( 'Display a list of child pages for a given page.', 'sof-utilities' ),
		];

		parent::__construct(
			'widget_child_pages',
			_x( 'SOF Child Pages Widget', 'widget name', 'sof-utilities' ),
			$widget_ops
		);

	}

	/**
	 * Outputs the content for the current Featured Page widget instance.
	 *
	 * @since 0.3
	 *
	 * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Featured Page widget instance.
	 */
	public function widget( $args, $instance ) {

		// Show widget prefix.
		echo ( isset( $args['before_widget'] ) ? $args['before_widget'] : '' );

		/**
		 * Get filtered title.
		 *
		 * @since 0.3
		 *
		 * @param string The instance title.
		 * @param array $instance Settings for the current widget instance.
		 * @param string The ID of the widget.
		 */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		// Show title if there is one.
		if ( ! empty( $title ) ) {
			echo isset( $args['before_title'] ) ? $args['before_title'] : '';
			echo $title;
			echo isset( $args['after_title'] ) ? $args['after_title'] : '';
		}

		// Get the queried object.
		$page = get_queried_object();

		// Bail if something's wrong.
		if ( ! ( $page instanceof WP_Post ) ) {
			return;
		}

		// Bail if it's not a Page.
		if ( 'page' !== $page->post_type ) {
			return;
		}

		// Use the Page ID by default.
		$page_id = $page->ID;

		// If this Page has a parent, use that.
		if ( ! empty( $page->post_parent ) ) {
			$page_id = $page->post_parent;
		}

		// Build args for the menu.
		$query = [
			'child_of' => $page_id,
		];

		// Show it.
		wp_page_menu( $query );

		// Show widget suffix.
		echo ( isset( $args['after_widget'] ) ? $args['after_widget'] : '' );

	}

	/**
	 * Outputs the settings form for the Child Pages widget.
	 *
	 * @since 0.3
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {

		// Get Title.
		$title = isset( $instance['title'] ) ? wp_strip_all_tags( $instance['title'] ) : '';

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'sof-utilities' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<?php

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @since 0.3
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 * @return array $instance Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		// Never lose a value.
		$instance = wp_parse_args( $new_instance, $old_instance );

		// --<
		return $instance;

	}

}
