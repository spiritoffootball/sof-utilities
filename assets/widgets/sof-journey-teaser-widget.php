<?php
/**
 * SOF Journey Teaser Widget.
 *
 * A widget that shows a teaser for the current Ball Journey.
 *
 * @since 0.2.2
 *
 * @package WordPress
 * @subpackage SOF
 */



// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



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
		$widget_ops = array(
			'classname' => 'widget_journey_teaser',
			'description' => __( 'Displays a "Journey Teaser" to guide visitors to the current blog.', 'sof-utilities' ),
		);

		parent::__construct(
			'widget_journey_teaser',
			_x( 'Journey Teaser', 'widget name', 'sof-utilities' ),
			$widget_ops
		);

	}



	/**
	 * Outputs the content for the current Recent Docs widget instance.
	 *
	 * @since 0.2.2
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Docs widget instance.
	 */
	public function widget( $args, $instance ) {

		/*
		$e = new Exception;
		$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'args' => $args,
			'instance' => $instance,
			//'backtrace' => $trace,
		), true ) );
		*/

		// get target site
		$target_site_id = ! empty( $instance['target_site'] ) ? $instance['target_site'] : '';

		// sanity check
		if ( empty( $target_site_id ) ) return;

		// switch to the site to get posts
		switch_to_blog( $target_site_id );

		// define args for query
		$query_args = array(
			'post_type' => 'post',
			'no_found_rows' => true,
			'post_status' => 'publish',
			'posts_per_page' => 1,
		);

		// do query
		$posts = new WP_Query( $query_args );

		// did we get any results?
		if ( $posts->have_posts() ) :

			// get filtered title
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			// show widget prefix
			echo ( isset( $args['before_widget'] ) ? $args['before_widget'] : '' );

			// show title if there is one
			if ( ! empty( $title ) ) {
				echo ( isset( $args['before_title'] ) ? $args['before_title'] : '' );
				echo $title;
				echo ( isset( $args['after_title'] ) ? $args['after_title'] : '' );
			}

			while ( $posts->have_posts() ) : $posts->the_post(); ?>

				<div class="post latest_ball_post">

					<?php

					// init
					$has_feature_image = false;
					$feature_image_class = '';

					// do we have a feature image?
					if ( has_post_thumbnail() ) {
						$has_feature_image = true;
						$feature_image_class = ' has_feature_image';
					}

					?>

					<div class="post_header<?php echo $feature_image_class; ?>">

						<div class="post_header_inner">

							<?php

							// show feature image when we have one
							if ( $has_feature_image ) {
								echo get_the_post_thumbnail( get_the_ID(), 'medium-640' );
							}

							?>

							<div class="post_header_text">

								<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>

							</div><!-- /post_header_text -->

						</div><!-- /post_header_inner -->

					</div><!-- /post_header -->

					<?php the_excerpt(); ?>

					<p><a class="button" href="<?php the_permalink() ?>"><?php _e( 'Find Out More', 'sof-utilities' ); ?></a></p>

				</div><!-- /latest_ball_post -->

			<?php endwhile;

			// show widget suffix
			echo ( isset( $args['after_widget'] ) ? $args['after_widget'] : '' );

			// reset the post globals as this query will have stomped on it
			wp_reset_postdata();

			// unswitch the site
			restore_current_blog();

		// end check for posts
		endif;

	}



	/**
	 * Outputs the settings form for the Recent Docs widget.
	 *
	 * @since 0.2.2
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {

		// get title
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'Follow the Journey', 'commentpress-sof-de' );
		}

		// get target site
		$target_site = ! empty( $instance['target_site'] ) ? $instance['target_site'] : '';

		// init query args
		$site_args = array(
			'archived' => 0,
			'spam' => 0,
			'deleted' => 0,
			'public' => 1,
		);

		// get sites
		$sites = get_sites( $site_args );

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'sof-utilities' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'target_site' ); ?>" class="site_label"><?php _e( 'Latest Ball Journey Site', 'sof-utilities' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'target_site' ); ?>" name="<?php echo $this->get_field_name( 'target_site' ); ?>" class="widefat">
				<option value="" <?php selected( $target_site, '' ); ?>><?php _e( 'None', 'sof-utilities' ); ?></option>
				<?php foreach( $sites as $site ) { ?>
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

		// never lose a value
		$instance = wp_parse_args( $new_instance, $old_instance );

		// --<
		return $instance;

	}



}



