<?php
/**
 * CiviCRM Class.
 *
 * Handles general CiviCRM modifications.
 *
 * @since 0.2.3
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * SOF CiviCRM Class.
 *
 * A class that encapsulates SOF-specific CiviCRM manipulation.
 *
 * @since 0.3
 */
class Spirit_Of_Football_CiviCRM {

	/**
	 * Plugin object.
	 *
	 * @since 0.3
	 * @access public
	 * @var Spirit_Of_Football_Utilities
	 */
	public $plugin;

	/**
	 * Constructor.
	 *
	 * @since 0.2.3
	 *
	 * @param Spirit_Of_Football_Utilities $plugin The plugin object.
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
	private function register_hooks() {

		// Maybe add our style overrides.
		add_action( 'civicrm_admin_utilities_admin_overridden', [ $this, 'enqueue_admin_css' ], 10 );

		// Maybe allow access to "Manage Groups" shortcut.
		add_action( 'civicrm_admin_utilities_manage_groups_menu_item', [ $this, 'manage_groups_menu_item' ], 20 );

	}

	// -----------------------------------------------------------------------------------

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

	/**
	 * Allow or deny access to the "Manage Groups" menu item.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $allow True by default, which allows access.
	 * @return bool $allow True allows access to the "Manage Groups" menu item, false does not.
	 */
	public function manage_groups_menu_item( $allow ) {

		// Network admins can see it.
		if ( is_super_admin() ) {
			return true;
		}

		// Pass for all others.
		return $allow;

	}

}
