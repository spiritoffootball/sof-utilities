<?php
/**
 * SOF Featured Page Widget Template.
 *
 * Displays a page title, excerpt and featured image.
 *
 * @package Spirit_Of_Football_Utilities
 * @since 0.3.1
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Define title attributes.
$title_atts = [
	'before' => __( 'Permanent Link to: ', 'sof-utilities' ),
	'after'  => '',
];

?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="post_inner<?php echo esc_attr( $feature_image_class ); ?>">

		<div class="post_header">

			<?php if ( $has_feature_image ) : ?>
				<?php the_post_thumbnail( $image_size ); ?>
			<?php endif; ?>

			<?php if ( 'yes' === $show_title && ! $featured_video ) : ?>
				<div class="post_title">
					<h2>
						<?php if ( 0 !== $link_text_id ) : ?>
							<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute( $title_atts ); ?>">
						<?php endif; ?>
						<?php the_title(); ?>
						<?php if ( 0 !== $link_text_id ) : ?>
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

		<?php if ( 'yes' === $show_title && $featured_video ) : ?>
			<div class="post_title">
				<h2>
					<?php if ( 0 !== $link_text_id ) : ?>
						<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute( $title_atts ); ?>">
					<?php endif; ?>
					<?php the_title(); ?>
					<?php if ( 0 !== $link_text_id ) : ?>
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

	<?php if ( 0 !== $link_text_id ) : ?>
		<div class="post_explore">
			<p><a class="button" href="<?php the_permalink(); ?>"><?php echo esc_html( $link_text ); ?></a></p>
		</div><!-- /post_explore -->
	<?php endif; ?>

	<?php edit_post_link( __( 'Edit this content', 'sof-utilities' ), '<p class="edit_link">', '</p>' ); ?>

</div><!-- /#post -->
