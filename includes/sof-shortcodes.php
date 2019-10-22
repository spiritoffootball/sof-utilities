<?php

/**
 * SOF Custom Shortcodes Class.
 *
 * A class that encapsulates SOF-specific Shortcodes.
 *
 * @since 0.1
 *
 * @package WordPress
 * @subpackage SOF
 */
class Spirit_Of_Football_Shortcodes {



	/**
	 * Constructor.
	 *
	 * @since 0.2
	 */
	public function __construct() {

		// Nothing.

	}



	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// Register shortcodes.
		add_shortcode( 'team', array( $this, 'team_render' ) );

	}



	// #########################################################################



	/**
	 * Add the team to a page via a shortcode.
	 *
	 * @since 0.3 Moved here from the "spiritoffootball" theme.
	 *
	 * @param array $attr The saved shortcode attributes.
	 * @param str $content The enclosed content of the shortcode.
	 * @return str $team The HTML markup for the shortcode.
	 */
	public function team_render( $attr, $content = null ) {

		// Init return.
		$team = '';

		// Init defaults.
		$defaults = array(
			'include' => '7, 3, 2, 8, 4, 5', // Default set of team members.
		);

		// Parse attributes.
		$shortcode_atts = shortcode_atts( $defaults, $attr, 'team' );

		// Build include users array from attribute.
		$include_users = explode( ',', $shortcode_atts['include'] );

		// Sanitise array items.
		array_walk( $include_users, function( &$item ) {
			$item = absint( trim( $item ) );
		});

		// Define args (users in order)
		$args = array(
			'include' => $include_users,
			'orderby' => 'include',
		);

		// Get the users by ID.
		$users = get_users( $args );

		// Did we get any?
		if ( count( $users ) > 0 ) {

			// Open div.
			$team .= '<div id="sof_team">' . "\n";

			// Loop through sorted users.
			foreach( $users AS $user ) {

				// Get data.
				$user_data = get_userdata( $user->ID );

				// Open internal wrapper.
				$team .= '<div class="sof_team_member clearfix">' . "\n";

				// Show display name.
				$team .= '<h2>' . esc_html( $user->display_name ) . '</h2>' . "\n";

				// Add gravatar.
				$team .= '<div class="author_avatar">' . get_avatar( $user_data->user_email, $size='200' ) . '</div>' . "\n";

				// Open text wrapper.
				$team .= '<div class="sof_team_member_desc">' . "\n";

				// Show description.
				$team .= '<p>' . esc_html( nl2br( $user_data->description ) ) . '</p>' . "\n";

				// Show link to profile if we're a super admin.
				if ( is_multisite() AND is_super_admin() ) {
					$url = admin_url( 'user-edit.php?user_id=' . $user->ID );
					$team .= '<p><a class="post-edit-link" href="' . $url . '">' .
								__( 'Edit this profile', 'spiritoffootball' ) .
							 '</a></p>' . "\n";
				}

				// Close sof_team_desc.
				$team .= '</div>' . "\n";

				// Close sof_team_member.
				$team .= '</div>' . "\n";

			}

			// Close sof_team.
			$team .= '</div>' . "\n";

		}

		// --<
		return $team;

	}



} // Class ends.



