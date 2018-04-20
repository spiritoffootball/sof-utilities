<?php
/**
 * SOF BuddyPress Docs Recent Docs Widget.
 *
 * A modified clone of BuddyPress Docs Recent Docs Widget overridden such that
 * all relevant BP Docs are shown on the SOF Member Homepage.
 *
 * @since 0.2.1
 *
 * @package WordPress
 * @subpackage SOF
 */



// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/**
 * Core class used to implement a Recent Docs widget.
 *
 * @since 0.2.1
 *
 * @see WP_Widget
 */
class SOF_Docs_Widget_Recent_Docs extends WP_Widget {

	/**
	 * Sets up a new Recent Docs widget instance.
	 *
	 * @since 0.2.1
	 *
	 * @access public
	 */
	public function __construct() {

		// Use the class `widget_recent_entries` to inherit WP Recent Posts widget styling.
		$widget_ops = array(
			'classname' => 'widget_recent_entries widget_recent_bp_docs widget_recent_sof_docs',
			'description' => __( 'Displays the most recent Docs that the visitor can read. Used on SOF Member Homepage.', 'sof-utilities' ),
		);

		parent::__construct(
			'widget_recent_sof_docs',
			_x( '(BuddyPress Docs) Recent SOF Docs', 'widget name', 'sof-utilities' ),
			$widget_ops
		);

		$this->alt_option_name = 'widget_recent_sof_docs';

	}



	/**
	 * Outputs the content for the current Recent Docs widget instance.
	 *
	 * @since 0.2.1
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Docs widget instance.
	 */
	public function widget( $args, $instance ) {

		$bp = buddypress();

		// Store the existing doc_query, so ours is made from scratch.
		$temp_doc_query = isset( $bp->bp_docs->doc_query ) ? $bp->bp_docs->doc_query : null;
		$bp->bp_docs->doc_query = null;

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Documents', 'sof-utilities' );

		/* This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

		$doc_args = array(
			'posts_per_page' => $number,
			'post_status'    => array( 'publish' ),
		);

		/*
		 * If this widget appears on a single user's profile, we want to
		 * limit the returned posts to those started by the displayed user.
		 * If viewing another user's profile, doc access will kick in.
		 */
		if ( bp_is_user() ) {
			$my_groups = groups_get_user_groups( bp_loggedin_user_id() );
			$d_group_id = ! empty( $my_groups['total'] ) ? $my_groups['groups'] : array( 0 );
			$doc_args['group_id'] = $d_group_id;
		}

		if ( bp_docs_has_docs( $doc_args ) ) :
			echo $args['before_widget'];
			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			} ?>
			<ul>
			<?php while ( bp_docs_has_docs() ) : bp_docs_the_doc(); ?>
				<li>
					<a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
				<?php if ( $show_date ) : ?>
					<span class="post-date"><?php echo get_the_date(); ?></span>
				<?php endif; ?>
				</li>
			<?php endwhile; ?>
		</ul>
		<?php echo $args['after_widget'];

		endif;

		// Restore the main doc_query; obliterate our secondary loop arguments.
		$bp->bp_docs->doc_query = $temp_doc_query;

	}



	/**
	 * Handles updating the settings for the current Recent Docs widget instance.
	 *
	 * @since 0.2.1
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance              = $old_instance;
		$instance['title']     = sanitize_text_field( $new_instance['title'] );
		$instance['number']    = (int) $new_instance['number'];
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;

		return $instance;

	}



	/**
	 * Outputs the settings form for the Recent Docs widget.
	 *
	 * @since 0.2.1
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {

		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;

		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'sof-utilities' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of docs to show:', 'sof-utilities' ); ?></label>
		<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?', 'sof-utilities' ); ?></label></p>
		<?php

	}



}



