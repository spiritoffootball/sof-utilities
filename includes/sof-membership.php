<?php

/**
 * SOF Membership Class.
 *
 * A class that encapsulates SOF-specific Membership procedures.
 *
 * @since 0.2
 *
 * @package WordPress
 * @subpackage SOF
 */
class Spirit_Of_Football_Membership {

	/**
	 * Membership Group ID (group called "Teamer")
	 *
	 * @since 0.2.1
	 * @access public
	 * @var int $group_id The numeric ID of the BuddyPress "Teamer" group.
	 */
	public $group_id = 9;



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
	 * @since 0.2
	 */
	public function register_hooks() {

		// Include only on SOF eV for now.
		if ( 'sofev' != sof_get_site() ) return;

		// Hook into "add capability".
		add_action( 'civi_wp_member_sync_add_cap', array( $this, 'cap_add' ), 20, 2 );

		// Hook into "remove capability".
		add_action( 'civi_wp_member_sync_remove_cap', array( $this, 'cap_remove' ), 20, 2 );

		// Filter access to custom elements on events.
		add_filter( 'civicrm_eo_pl_access', array( $this, 'permissions_pl' ), 11, 2 );

	}




	// #########################################################################



	/**
	 * Actions to perform when a capability is added via CiviCRM Membership.
	 *
	 * @since 0.2
	 *
	 * @param object $user The WordPress user object.
	 * @param string $capability Capability name.
	 */
	public function cap_add( $user, $capability ) {

		// Kick out if we don't receive a valid user.
		if ( ! ( $user instanceof WP_User ) ) return;

		// Bail if not the "Mitglied" capability.
		if ( $capability != 'civimember_1' ) return;

		// Check existing membership.
		$is_member = groups_is_user_member( $user->ID, $this->group_id );

		// Skip creation if user is already a member.
		if ( $is_member ) return true;

		// Use BuddyPress function.
		$success = groups_join_group( $this->group_id, $user->ID );

		// Log an error on failure.
		if ( $success === false ) {

			error_log( print_r( array(
				'method' => __METHOD__,
				'procedure' => __( 'Could not add user to group', 'sof-utilities' ),
				'user' => $user,
				'group_id' => $this->group_id,
			), true ) );

		}

	}



	/**
	 * Actions to perform when a capability is removed via CiviCRM Membership.
	 *
	 * @since 0.2
	 *
	 * @param object $user The WordPress user object.
	 * @param string $capability Capability name.
	 */
	public function cap_remove( $user, $capability ) {

		// Kick out if we don't receive a valid user.
		if ( ! ( $user instanceof WP_User ) ) return;

		// Bail if not the "Mitglied" capability.
		if ( $capability != 'civimember_1' ) return;

		// Bail if user is not a member.
		if ( ! groups_is_user_member( $user->ID, $this->group_id ) ) return false;

		// Use BuddyPress function.
		$success = groups_leave_group( $this->group_id, $user->ID );

		// Log an error on failure.
		if ( $success === false ) {

			error_log( print_r( array(
				'method' => __METHOD__,
				'procedure' => __( 'Could not remove user from group', 'sof-utilities' ),
				'user' => $user,
				'group_id' => $this->group_id,
			), true ) );

		}

	}



	/**
	 * Filter access to Participant Listings.
	 *
	 * @since 0.2.1
	 *
	 * @param bool $granted False by default - assumes access not granted.
	 * @param int $post_id The numeric ID of the WP post.
	 * @return bool $granted True if access granted, false otherwise.
	 */
	public function permissions_pl( $granted, $post_id = null ) {

		// Always deny if not logged in.
		if ( ! is_user_logged_in() ) return false;

		// Get current user.
		$current_user = wp_get_current_user();

		// Allow if user is a member of our membership group.
		if ( groups_is_user_member( $current_user->ID, $this->group_id ) ) {
			$granted = true;
		} else {
			$granted = false;
		}

		// --<
		return $granted;

	}



} // Class ends.



