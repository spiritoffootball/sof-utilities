<?php
/**
 * SOF Featured Page Widget.
 *
 * A widget that display a page title, excerpt and featured image..
 *
 * @since 0.3
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement a "Featured Page" widget.
 *
 * @since 0.3
 *
 * @see WP_Widget
 */
class SOF_Widget_Featured_Page extends WP_Widget {

	/**
	 * Translatable list of link text options.
	 *
	 * @since 0.3
	 * @access public
	 * @var array $links The translatable list of link text options.
	 */
	public $links;

	/**
	 * Constructor registers widget with WordPress.
	 *
	 * @since 0.3
	 */
	public function __construct() {

		// Define the translatable list of link text options here.
		$this->links = [
			0 => __( 'Do not show a Link', 'sof-utilities' ),
			1 => __( 'Read More', 'sof-utilities' ),
			2 => __( 'Find Out More', 'sof-utilities' ),
			3 => __( 'Continue Reading', 'sof-utilities' ),
		];

		// Use the class `widget_recent_entries` to inherit WP Recent Posts widget styling.
		$widget_ops = [
			'classname' => 'widget_featured_page',
			'description' => __( 'Display a page title, excerpt and featured image.', 'sof-utilities' ),
		];

		parent::__construct(
			'widget_featured_page',
			_x( 'SOF Page Widget', 'widget name', 'sof-utilities' ),
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

		// Get Page ID.
		$page_id = ! empty( $instance['page-id'] ) ? (int) $instance['page-id'] : -1;

		// Sanity check.
		if ( empty( $page_id ) || $page_id == -1 ) {
			return;
		}

		// Do we want to show the Page Title?
		$show_title = ! empty( $instance['show-title'] ) ? $instance['show-title'] : 'yes';

		// Get selected Link Text ID.
		$link_text_id = ! empty( $instance['link-text-id'] ) ? $instance['link-text-id'] : 0;

		// If we've chosen a link.
		if ( $link_text_id !== 0 ) {

			// Get the translatable text.
			$link_text = $this->links[ $link_text_id ];

			/**
			 * Filter the default link text.
			 *
			 * @since 0.3
			 *
			 * @param string $link_text The default link text.
			 * @param array $instance Settings for the current widget instance.
			 * @param array $args The display arguments.
			 * @return string $link_text The modified default link text.
			 */
			$link_text = apply_filters( 'sof_utilities_featured_page_link_text', $link_text, $instance, $args );

		}

		// Get desired Featured Image size from widget setting.
		$image_size = ! empty( $instance['image-size'] ) ? wp_strip_all_tags( $instance['image-size'] ) : 'thumbnail';

		// Define args for query.
		$query_args = [
			'page_id' => $page_id,
		];

		// Do query.
		$query = new WP_Query( $query_args );

		// Did we get any results?
		if ( $query->have_posts() ) :

			while ( $query->have_posts() ) :
				$query->the_post();

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

				// Get the Featured Video.
				$featured_video = false;
				if ( defined( 'ACF' ) ) {
					$featured_video = get_field( 'featured_video' );
				}

				// Init.
				$has_feature_image = false;
				$feature_image_class = '';

				// Don't show Feature Image if we have a Video.
				if ( $featured_video ) {
					$feature_image_class = ' has_feature_video';
				} else {

					// Do we have a Feature Image?
					if ( $image_size != 'no-thumbnail' && has_post_thumbnail() ) {
						$has_feature_image = true;
						$feature_image_class = ' has_feature_image';
					}

				}

				// Maybe add Page Title class.
				if ( $show_title === 'yes' ) {
					$feature_image_class .= ' has_page_title';
				}

				// Include template.
				include SOF_UTILITIES_PATH . 'assets/templates/sof-featured-page-template.php';

			endwhile;

			// Show widget suffix.
			echo ( isset( $args['after_widget'] ) ? $args['after_widget'] : '' );

			// Reset the post globals as this query will have stomped on it.
			wp_reset_postdata();

		// End check for posts.
		endif;

	}

	/**
	 * Outputs the settings form for the Featured Page widget.
	 *
	 * @since 0.3
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {

		// Get Title, Page ID, Feature Image Size and Link Text ID.
		$title = isset( $instance['title'] ) ? wp_strip_all_tags( $instance['title'] ) : '';
		$page_id = isset( $instance['page-id'] ) ? (int) $instance['page-id'] : 0;
		$image_size = isset( $instance['image-size'] ) ? wp_strip_all_tags( $instance['image-size'] ) : 'thumbnail';
		$show_title = isset( $instance['show-title'] ) ? $instance['show-title'] : 'yes';
		$link_text_id = ! empty( $instance['link-text-id'] ) ? $instance['link-text-id'] : 0;

		// Define page query.
		$args = [
			'depth' => 0,
			'child_of' => 0,
			'selected' => $page_id,
			'name' => $this->get_field_name( 'page-id' ),
			'id' => $this->get_field_id( 'page-id' ),
			'show_option_none' => '',
			'show_option_no_change' => '',
			'option_none_value' => '',
		];

		// Get all relevant Pages.
		$pages = get_pages( $args );

		// Get all image sizes.
		$image_sizes = wp_get_registered_image_subsizes();

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'sof-utilities' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'page-id' ); ?>"><?php esc_html_e( 'Page:', 'sof-utilities' ); ?></label>
			<?php if ( ! empty( $pages ) ) : ?>
				<select class="widefat" name="<?php echo $this->get_field_name( 'page-id' ); ?>" id="<?php echo $this->get_field_id( 'page-id' ); ?>">
					<option value="-1"><?php esc_html_e( 'Choose a page', 'sof-utilities' ); ?></option>
					<?php echo walk_page_dropdown_tree( $pages, $args['depth'], $args ); ?>
				</select>
			<?php endif; ?>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'image-size' ); ?>"><?php esc_html_e( 'Feature Image size:', 'sof-utilities' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'image-size' ); ?>" name="<?php echo $this->get_field_name( 'image-size' ); ?>">
				<option value="no-thumbnail" <?php selected( $image_size, 'no-thumbnail' ); ?>><?php esc_html_e( 'Do not use a Feature Image', 'sof-utilities' ); ?></option>
				<?php foreach ( $image_sizes as $key => $value ) : ?>
					<option value="<?php echo $key; ?>" <?php selected( $image_size, $key ); ?>><?php echo $key; ?> (<?php printf( __( '%1$dx%2$d', 'sof-utilities' ), $value['width'], $value['height'] ); ?>)</option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'show-title' ); ?>"><?php esc_html_e( 'Show Page Title:', 'sof-utilities' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'show-title' ); ?>" name="<?php echo $this->get_field_name( 'show-title' ); ?>">
				<option value="yes" <?php selected( $show_title, 'yes' ); ?>><?php esc_html_e( 'Yes', 'sof-utilities' ); ?></option>
				<option value="no" <?php selected( $show_title, 'no' ); ?>><?php esc_html_e( 'No', 'sof-utilities' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'link-text-id' ); ?>"><?php esc_html_e( 'Link text:', 'sof-utilities' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'link-text-id' ); ?>" name="<?php echo $this->get_field_name( 'link-text-id' ); ?>">
				<?php foreach ( $this->links as $key => $value ) : ?>
					<option value="<?php echo $key; ?>" <?php selected( $link_text_id, $key ); ?>><?php echo $value; ?></option>
				<?php endforeach; ?>
			</select>
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
