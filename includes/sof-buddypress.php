<?php
/**
 * BuddyPress Class.
 *
 * Handles general BuddyPress modifications.
 *
 * @package Spirit_Of_Football_Utilities
 * @since 0.2.3
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;



/**
 * SOF BuddyPress Class.
 *
 * A class that encapsulates SOF-specific BuddyPress manipulation.
 *
 * @since 0.2.3
 *
 * @package WordPress
 * @subpackage SOF
 */
class Spirit_Of_Football_BuddyPress {

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
	 * @since 0.2.3
	 */
	public function register_hooks() {

		// Check if SOF CIC can handle this.
		$sofcic = false;
		if ( 'sofcic' != sof_get_site() AND function_exists( 'bp_core_get_user_domain' ) ) {
			$sofcic = true;
		}

		// Include only on SOF eV and maybe on SOF CIC.
		if ( 'sofev' == sof_get_site() OR $sofcic === true ) {

			// Redirect to calling page after login.
			add_filter( 'login_redirect', [ $this, 'login_redirect' ], 20, 3 );

			// Add link to password recovery page.
			add_action( 'bp_login_widget_form', [ $this, 'login_password_link' ], 20 );

		}

	}



	// #########################################################################



	/**
	 * Redirect user after successful login.
	 *
	 * @since 0.2.3
	 *
	 * @param string $redirect_to URL to redirect to.
	 * @param string $request URL the user is coming from.
	 * @param object $user Logged-in user's data.
	 * @return string $redirect_to Modified URL to redirect to.
	 */
	public function login_redirect( $redirect_to, $request, $user ) {

		// Bail if no user.
		if ( ! $user instanceof WP_User ) {
			return $redirect_to;
		}

		/*
		// Bail if super admin.
		if ( is_super_admin( $user->ID ) ) {
			return $redirect_to;
		}

		// Bail if not main site and user is site administrator.
		if ( ! is_main_site() AND user_can( $user, 'manage_options' ) ) {
			return $redirect_to;
		}

		// Is our hidden input set?
		if ( isset( $_REQUEST['pcp-current-page'] ) AND ! empty( $_REQUEST['pcp-current-page'] ) ) {
			$redirect_to = $_REQUEST['pcp-current-page'];
		}
		*/

		// Is this user held in moderation queue?
		if (
			function_exists( 'bp_registration_get_moderation_status' ) AND
			bp_registration_get_moderation_status( $user->ID )
		) {

			// Redirect to home page.
			$redirect_to = home_url( '/' );

		} else {

			// Redirect to member home.
			$redirect_to = trailingslashit( bp_core_get_user_domain( $user->ID ) );

		}

		// Return to request URL.
		return $redirect_to;

	}



	/**
	 * Add a link to the password recovery page to the BuddyPress login widget.
	 *
	 * @since 0.2
	 */
	public function login_password_link() {

		// Get current URL.
		$url = wp_lostpassword_url();

		// Add link to password recovery page.
		echo '<span class="bp-login-widget-password-link">';
		echo '<a href="' . $url . '">' . __( 'Lost your password?' ) . '</a>';
		echo '</span>';

	}



} // Class ends.



