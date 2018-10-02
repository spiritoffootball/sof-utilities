<?php

/**
 * SOF CiviCRM Class.
 *
 * A class that encapsulates SOF-specific CiviCRM manipulation.
 *
 * @since 0.3
 *
 * @package WordPress
 * @subpackage SOF
 */
class Spirit_Of_Football_CiviCRM {



	/**
	 * Constructor.
	 *
	 * @since 0.3
	 */
	public function __construct() {

		// nothing

	}



	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.3
	 */
	public function register_hooks() {

		// maybe add our style overrides
		add_action( 'civicrm_admin_utilities_admin_overridden', array( $this, 'enqueue_admin_css' ), 10 );

	}



	// #########################################################################



	/**
	 * CiviCRM Admin Utilities is prettifying CiviCRM - add our style overrides.
	 *
	 * @since 0.3
	 */
	public function enqueue_admin_css() {

		// add stylesheet
		wp_enqueue_style(
			'sof_utilities_civicrm_admin',
			plugins_url( 'assets/css/sof-civicrm.css', SOF_UTILITIES_FILE ),
			array( 'civicrm_admin_utilities_admin_override' ),
			CIVICRM_ADMIN_UTILITIES_VERSION, // version
			'all' // media
		);

	}



} // class Spirit_Of_Football_CiviCRM ends



