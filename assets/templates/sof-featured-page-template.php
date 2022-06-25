<?php
/**
 * SOF Featured Page Widget Template.
 *
 * Displays a page title, excerpt and featured image.
 *
 * @since 0.3.1
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Define title attributes.
$title_atts = [
	'before' => __( 'Permanent Link to: ', 'sof-utilities' ),
	'after' => '',
];

?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="post_inner<?php echo $feature_image_class; ?>">

		<div class="post_header">

			<?php if ( $has_feature_image ) : ?>
				<?php the_post_thumbnail( $image_size ); ?>
			<?php endif; ?>

			<?php if ( $show_title === 'yes' && ! $featured_video ) : ?>
				<div class="post_title">
					<h2>
						<?php if ( $link_text_id !== 0 ) : ?>
							<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute( $title_atts ); ?>">
						<?php endif; ?>
						<?php the_title(); ?>
						<?php if ( $link_text_id !== 0 ) : ?>
							</a>
						<?php endif; ?>
					</h2>
				</div><!-- /post_title -->
			<?php endif; ?>

		</div><!-- /post_header -->

		<?php if ( $featured_video ) : ?>
			<div class="post_video">
				<?php the_field( 'featured_video' ); ?>
			</div><!-- /post_video -->
		<?php endif; ?>

		<?php if ( $show_title === 'yes' && $featured_video ) : ?>
			<div class="post_title">
				<h2>
					<?php if ( $link_text_id !== 0 ) : ?>
						<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute( $title_atts ); ?>">
					<?php endif; ?>
					<?php the_title(); ?>
					<?php if ( $link_text_id !== 0 ) : ?>
						</a>
					<?php endif; ?>
				</h2>
			</div><!-- /post_title -->
		<?php endif; ?>

		<div class="post_excerpt">
			<?php if ( defined( 'ACF' ) && get_field( 'featured_text' ) ) : ?>
				<?php the_field( 'featured_text' ); ?>
			<?php else : ?>
				<?php the_excerpt(); ?>
			<?php endif; ?>
		</div><!-- /post_excerpt -->

	</div><!-- /post_inner -->

	<?php if ( $link_text_id !== 0 ) : ?>
		<div class="post_explore">
			<p><a class="button" href="<?php the_permalink(); ?>"><?php echo $link_text; ?></a></p>
		</div><!-- /post_explore -->
	<?php endif; ?>

	<?php edit_post_link( __( 'Edit this content', 'sof-utilities' ), '<p class="edit_link">', '</p>' ); ?>

</div><!-- /post -->
