<?php
/**
 * Team Shortcode Class.
 *
 * Handles Team Shortcode.
 *
 * @since 1.0.1
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Team Shortcode Class.
 *
 * A class that encapsulates the Team Shortcode.
 *
 * @since 1.0.1
 */
class Spirit_Of_Football_Shortcode_Team {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.1
	 * @access public
	 * @var Spirit_Of_Football_Utilities
	 */
	public $plugin;

	/**
	 * Shortcodes loader object.
	 *
	 * @since 1.0.1
	 * @access public
	 * @var Spirit_Of_Football_Shortcodes
	 */
	public $shortcodes;

	/**
	 * Constructor.
	 *
	 * @since 1.0.1
	 *
	 * @param Spirit_Of_Football_Shortcodes $shortcodes The Shortcode Loader object.
	 */
	public function __construct( $shortcodes ) {

		// Store references to objects.
		$this->shortcodes = $shortcodes;
		$this->plugin     = $shortcodes->plugin;

		// Init when loader class is loaded.
		add_action( 'sof_utilities/shortcodes/loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialise this object.
	 *
	 * @since 1.0.1
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Register hooks.
		$this->register_hooks();

		// We're done.
		$done = true;

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 1.0.1
	 */
	private function register_hooks() {

		// Register Shortcode.
		add_shortcode( 'team', [ $this, 'shortcode_render' ] );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Adds the team to a page via a Shortcode.
	 *
	 * @since 1.0.1
	 *
	 * @param array $attr The saved shortcode attributes.
	 * @param str   $content The enclosed content of the shortcode.
	 * @return str $team The HTML markup for the shortcode.
	 */
	public function shortcode_render( $attr, $content = null ) {

		// Init return.
		$team = '';

		// Init defaults.
		$defaults = [
			'include' => '7, 3, 2, 8, 4, 5', // Default set of team members.
		];

		// Parse attributes.
		$shortcode_atts = shortcode_atts( $defaults, $attr, 'team' );

		// Build include users array from attribute.
		$include_users = explode( ',', $shortcode_atts['include'] );

		// Sanitise array items.
		array_walk(
			$include_users,
			function( &$item ) {
				$item = absint( trim( $item ) );
			}
		);

		// Define args (users in order).
		$args = [
			'include' => $include_users,
			'orderby' => 'include',
		];

		// Get the users by ID.
		$users = get_users( $args );

		// Did we get any?
		if ( count( $users ) > 0 ) {

			// Open div.
			$team .= '<div id="sof_team">' . "\n";

			// Loop through sorted users.
			foreach ( $users as $user ) {

				// Get data.
				$user_data = get_userdata( $user->ID );

				// Open internal wrapper.
				$team .= '<div class="sof_team_member clearfix">' . "\n";

				// Show display name.
				$team .= '<h2>' . esc_html( $user->display_name ) . '</h2>' . "\n";

				// Add gravatar.
				$team .= '<div class="author_avatar">' . get_avatar( $user_data->user_email, $size = '200' ) . '</div>' . "\n";

				// Open text wrapper.
				$team .= '<div class="sof_team_member_desc">' . "\n";

				// Show description.
				$team .= '<p>' . esc_html( nl2br( $user_data->description ) ) . '</p>' . "\n";

				// Show link to profile if we're a super admin.
				if ( is_multisite() && is_super_admin() ) {
					$url   = admin_url( 'user-edit.php?user_id=' . $user->ID );
					$team .= '<p><a class="post-edit-link" href="' . $url . '">' .
						esc_html__( 'Edit this profile', 'sof-utilities' ) .
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

}
