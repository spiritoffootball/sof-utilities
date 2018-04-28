<?php

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
	 * Constructor.
	 *
	 * @since 0.2.3
	 */
	public function __construct() {

		// nothing

	}



	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.2.3
	 */
	public function register_hooks() {

		// include only on SOF eV for now...
		if ( 'sofev' != sof_get_site() ) return;

		// redirect to calling page after login
		add_filter( 'login_redirect', array( $this, 'login_redirect' ), 20, 3 );

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

		// bail if no user
		if ( ! $user instanceof WP_User ) return $redirect_to;

		/*
		// bail if super admin
		if ( is_super_admin( $user->ID ) ) return $redirect_to;

		// bail if not main site and user is site administrator
		if ( ! is_main_site() AND user_can( $user, 'manage_options' ) ) return $redirect_to;

		// is our hidden input set?
		if ( isset( $_REQUEST['pcp-current-page'] ) AND ! empty( $_REQUEST['pcp-current-page'] ) ) {
			$redirect_to = $_REQUEST['pcp-current-page'];
		}
		*/

		// is this user held in moderation queue?
		if (
			function_exists( 'bp_registration_get_moderation_status' ) AND
			bp_registration_get_moderation_status( $user->ID )
		) {

			// redirect to home page
			$redirect_to = home_url( '/' );

		} else {

			// redirect to member home
			$redirect_to = trailingslashit( bp_core_get_user_domain( $user->ID ) );

		}

		// return to request URL
		return $redirect_to;

	}



} // class Spirit_Of_Football_BuddyPress ends



