<?php
/**
 * CiviCRM Class.
 *
 * Handles general CiviCRM modifications.
 *
 * @package Spirit_Of_Football_Utilities
 * @since 0.2.3
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;



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
	 * @since 0.3
	 */
	public function register_hooks() {

		// Maybe add our style overrides.
		add_action( 'civicrm_admin_utilities_admin_overridden', [ $this, 'enqueue_admin_css' ], 10 );

	}



	// #########################################################################



	/**
	 * CiviCRM Admin Utilities is prettifying CiviCRM - add our style overrides.
	 *
	 * @since 0.3
	 */
	public function enqueue_admin_css() {

		// Add stylesheet.
		wp_enqueue_style(
			'sof_utilities_civicrm_admin',
			plugins_url( 'assets/css/sof-civicrm.css', SOF_UTILITIES_FILE ),
			[ 'civicrm_admin_utilities_admin_override' ],
			CIVICRM_ADMIN_UTILITIES_VERSION, // Version.
			'all' // Media.
		);

	}



} // Class ends.



