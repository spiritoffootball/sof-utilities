<?php
/**
 * SOF Journey Teaser Widget.
 *
 * A widget that shows a teaser for the current Ball Journey.
 *
 * @since 0.2.2
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement a "Journey Teaser" widget.
 *
 * @since 0.2.2
 *
 * @see WP_Widget
 */
class SOF_Widget_Journey_Teaser extends WP_Widget {

	/**
	 * Constructor registers widget with WordPress.
	 *
	 * @since 0.2.2
	 */
	public function __construct() {

		// Use the class `widget_recent_entries` to inherit WP Recent Posts widget styling.
		$widget_ops = [
			'classname' => 'widget_journey_teaser',
			'description' => __( 'Displays a "Journey Teaser" to guide visitors to the current blog.', 'sof-utilities' ),
		];

		parent::__construct(
			'widget_journey_teaser',
			_x( 'Journey Teaser', 'widget name', 'sof-utilities' ),
			$widget_ops
		);

	}

	/**
	 * Outputs the content for the current Journey Teaser widget instance.
	 *
	 * @since 0.2.2
	 *
	 * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Journey Teaser widget instance.
	 */
	public function widget( $args, $instance ) {

		// Get target site.
		$target_site_id = ! empty( $instance['target_site'] ) ? $instance['target_site'] : '';

		// Sanity check.
		if ( empty( $target_site_id ) ) {
			return;
		}

		// Switch to the site to get posts.
		switch_to_blog( $target_site_id );

		// Define args for query.
		$query_args = [
			'post_type' => 'post',
			'no_found_rows' => true,
			'post_status' => 'publish',
			'posts_per_page' => 1,
		];

		// Do query.
		$posts = new WP_Query( $query_args );

		// Did we get any results?
		if ( $posts->have_posts() ) :

			// Get filtered title.
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			// Show widget prefix.
			echo ( isset( $args['before_widget'] ) ? $args['before_widget'] : '' );

			// Show title if there is one.
			if ( ! empty( $title ) ) {
				echo ( isset( $args['before_title'] ) ? $args['before_title'] : '' );
				echo $title;
				echo ( isset( $args['after_title'] ) ? $args['after_title'] : '' );
			}

			while ( $posts->have_posts() ) :
				$posts->the_post();

				?>

				<div class="post latest_ball_post">

					<?php

					// Init.
					$has_feature_image = false;
					$feature_image_class = '';

					// Do we have a feature image?
					if ( has_post_thumbnail() ) {
						$has_feature_image = true;
						$feature_image_class = ' has_feature_image';
					}

					?>

					<style>
					.cp-homepage-widgets .post_header_inner,
					#activity_sidebar.sidebar_container .post_header_inner {
						position: relative;
						line-height: 1;
					}
					.cp-homepage-widgets .post_header.has_feature_image img,
					#activity_sidebar.sidebar_container .post_header.has_feature_image img {
						width: 100%;
						height: auto;
						line-height: 1;
					}
					.cp-homepage-widgets .post_header.has_feature_image img {
						min-height: 200px;
					}
					#activity_sidebar.sidebar_container .post_header.has_feature_image img {
						min-height: 150px;
					}
					.cp-homepage-widgets .post_header.has_feature_image .post_header_text,
					#activity_sidebar.sidebar_container .post_header.has_feature_image .post_header_text {
						position: absolute;
						bottom: 2px;
						width: 100%;
						padding: 0;
						background: #000;
						background: rgba( 0, 0, 0, 0.46);
						text-shadow: 0 0 3px #0A0933;
					}
					.cp-homepage-widgets .post_header_inner h2,
					#activity_sidebar.sidebar_container .post_header_inner h2 {
						margin: 0;
						padding: 0.6em 0.8em;
						line-height: 1.3;
					}
					.cp-homepage-widgets .post_header_inner h2 a,
					#activity_sidebar.sidebar_container .post_header_inner h2 a {
						color: #fff;
						text-decoration: none;
					}
					.cp-homepage-widgets .post_excerpt, .post_explore,
					#activity_sidebar.sidebar_container .post_excerpt, .post_explore {
						padding: 0 15px;
						line-height: 1.5;
					}
					</style>

					<div class="post_header<?php echo $feature_image_class; ?>">

						<div class="post_header_inner">

							<?php

							// Show feature image when we have one.
							if ( $has_feature_image ) {
								echo get_the_post_thumbnail( get_the_ID(), 'medium-640' );
							}

							?>

							<div class="post_header_text">

								<h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>

							</div><!-- /post_header_text -->

						</div><!-- /post_header_inner -->

					</div><!-- /post_header -->

					<div class="post_excerpt">
						<?php the_excerpt(); ?>
					</div><!-- /post_excerpt -->

					<div class="post_explore">
						<p><a class="button" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'sof-utilities' ); ?></a> <a class="button" href="<?php echo get_site_url( null, '/blog/' ); ?>"><?php esc_html_e( 'Go to the blog', 'sof-utilities' ); ?></a></p>
					</div><!-- /post_excerpt -->

				</div><!-- /latest_ball_post -->

				<?php

			endwhile;

			// Show widget suffix.
			echo ( isset( $args['after_widget'] ) ? $args['after_widget'] : '' );

			// Reset the post globals as this query will have stomped on it.
			wp_reset_postdata();

			// Unswitch the site.
			restore_current_blog();

		// End check for posts.
		endif;

	}

	/**
	 * Outputs the settings form for the Journey Teaser widget.
	 *
	 * @since 0.2.2
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {

		// Get title.
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'Follow the Journey', 'sof-utilities' );
		}

		// Get target site.
		$target_site = ! empty( $instance['target_site'] ) ? $instance['target_site'] : '';

		// Init query args.
		$site_args = [
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'public' => 1,
		];

		// Get sites.
		$sites = get_sites( $site_args );

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'sof-utilities' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'target_site' ); ?>" class="site_label"><?php esc_html_e( 'Latest Ball Journey Site', 'sof-utilities' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'target_site' ); ?>" name="<?php echo $this->get_field_name( 'target_site' ); ?>" class="widefat">
				<option value="" <?php selected( $target_site, '' ); ?>><?php esc_html_e( 'None', 'sof-utilities' ); ?></option>
				<?php foreach ( $sites as $site ) { ?>
					<option id="<?php echo $site->blog_id; ?>" value="<?php echo $site->blog_id; ?>" <?php selected( $target_site, $site->blog_id ); ?>><?php echo esc_html( get_blog_details( $site->blog_id )->blogname ); ?></option>
				<?php } ?>
			</select>
		</p>

		<?php

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @since 0.2.2
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
