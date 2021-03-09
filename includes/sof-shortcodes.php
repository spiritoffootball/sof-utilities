<?php
/**
 * Shortcodes Class.
 *
 * Handles SOF-specific Shortcodes.
 *
 * @package Spirit_Of_Football_Utilities
 * @since 0.1
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;



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
	 * Plugin (calling) object.
	 *
	 * @since 0.3
	 * @access public
	 * @var object $plugin The plugin object.
	 */
	public $plugin;



	/**
	 * Constructor.
	 *
	 * @since 0.2.3
	 *
	 * @param object $plugin The plugin object.
	 */
	public function __construct( $plugin ) {

		// Store reference to plugin.
		$this->plugin = $plugin;

		// Init when this plugin is loaded.
		add_action( 'sof_utilities/loaded', [ $this, 'initialise' ] );

	}



	/**
	 * Initialise this object.
	 *
	 * @since 0.3
	 */
	public function initialise() {

		// Register hooks.
		$this->register_hooks();

	}



	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// Register shortcodes.
		add_shortcode( 'team', [ $this, 'team_render' ] );

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
		$defaults = [
			'include' => '7, 3, 2, 8, 4, 5', // Default set of team members.
		];

		// Parse attributes.
		$shortcode_atts = shortcode_atts( $defaults, $attr, 'team' );

		// Build include users array from attribute.
		$include_users = explode( ',', $shortcode_atts['include'] );

		// Sanitise array items.
		array_walk( $include_users, function( &$item ) {
			$item = absint( trim( $item ) );
		});

		// Define args (users in order)
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



