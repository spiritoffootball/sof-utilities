<?php /*
--------------------------------------------------------------------------------
Plugin Name: SOF Utilities
Plugin URI: http://spiritoffootball.com
Description: Network-wide Utilities for the SOF sites.
Author: Christian Wach
Version: 0.3
Author URI: http://haystack.co.uk
Text Domain: sof-utilities
--------------------------------------------------------------------------------
*/



// Set our version here.
define( 'SOF_UTILITIES_VERSION', '0.3' );

// Store reference to this file.
if ( ! defined( 'SOF_UTILITIES_FILE' ) ) {
	define( 'SOF_UTILITIES_FILE', __FILE__ );
}

// Store URL to this plugin's directory.
if ( ! defined( 'SOF_UTILITIES_URL' ) ) {
	define( 'SOF_UTILITIES_URL', plugin_dir_url( SOF_UTILITIES_FILE ) );
}

// Store PATH to this plugin's directory.
if ( ! defined( 'SOF_UTILITIES_PATH' ) ) {
	define( 'SOF_UTILITIES_PATH', plugin_dir_path( SOF_UTILITIES_FILE ) );
}



/**
 * SOF Utilities Class.
 *
 * A class that encapsulates network-wide utilities.
 *
 * @since 0.1
 *
 * @package WordPress
 * @subpackage SOF
 */
class Spirit_Of_Football_Utilities {

	/**
	 * Pseudo-maintenance mode.
	 *
	 * @since 0.3
	 * @access private
	 * @var bool $maintenance_mode True sets WordPress into pseudo-maintenance mode.
	 */
	private $maintenance_mode = false;

	/**
	 * BuddyPress object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $buddypress The BuddyPress object.
	 */
	public $buddypress;

	/**
	 * CiviCRM object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $civicrm The CiviCRM object.
	 */
	public $civicrm;

	/**
	 * Custom Post Types object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $cpts The Custom Post Types object.
	 */
	public $cpts;

	/**
	 * Metaboxes object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $metaboxes The Metaboxes object.
	 */
	public $metaboxes;

	/**
	 * Menus object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $menus The Menus object.
	 */
	public $menus;

	/**
	 * Membership object.
	 *
	 * @since 0.2
	 * @access public
	 * @var object $membership The Membership object.
	 */
	public $membership;

	/**
	 * Mirror object.
	 *
	 * @since 0.2.1
	 * @access public
	 * @var object $mirror The Mirror object.
	 */
	public $mirror;

	/**
	 * Widgets object.
	 *
	 * @since 0.2.1
	 * @access public
	 * @var object $widgets The Widgets object.
	 */
	public $widgets;



	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// Include files.
		$this->include_files();

		// Setup globals.
		$this->setup_globals();

		// Register hooks.
		$this->register_hooks();

	}



	/**
	 * Include files.
	 *
	 * @since 0.1
	 */
	public function include_files() {

		// Include BuddyPress class.
		include_once SOF_UTILITIES_PATH . 'includes/sof-buddypress.php';

		// Include CiviCRM class.
		include_once SOF_UTILITIES_PATH . 'includes/sof-civicrm.php';

		// Include CPT class.
		include_once SOF_UTILITIES_PATH . 'includes/sof-cpts.php';

		// Include Metaboxes class.
		include_once SOF_UTILITIES_PATH . 'includes/sof-metaboxes.php';

		// Include Menus class.
		include_once SOF_UTILITIES_PATH . 'includes/sof-menus.php';

		// Include Membership class.
		include_once SOF_UTILITIES_PATH . 'includes/sof-membership.php';

		// Include Mirror class.
		include_once SOF_UTILITIES_PATH . 'includes/sof-mirror.php';

		// Include Shortcodes class.
		include_once SOF_UTILITIES_PATH . 'includes/sof-shortcodes.php';

		// Include Widgets class.
		include_once SOF_UTILITIES_PATH . 'includes/sof-widgets.php';

	}



	/**
	 * Set up objects.
	 *
	 * @since 0.1
	 */
	public function setup_globals() {

		// Init BuddyPress object.
		$this->buddypress = new Spirit_Of_Football_BuddyPress;

		// Init CiviCRM object.
		$this->civicrm = new Spirit_Of_Football_CiviCRM;

		// Init CPT object.
		$this->cpts = new Spirit_Of_Football_CPTs;

		// Init Metaboxes object.
		$this->metaboxes = new Spirit_Of_Football_Metaboxes;

		// Init Menus object.
		$this->menus = new Spirit_Of_Football_Menus;

		// Init Membership object.
		$this->membership = new Spirit_Of_Football_Membership;

		// Init Mirror object.
		$this->mirror = new Spirit_Of_Football_Mirror;

		// Init Shortcodes object.
		$this->shortcodes = new Spirit_Of_Football_Shortcodes;

		// Init Widgets object.
		$this->widgets = new Spirit_Of_Football_Widgets;

	}



	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// Use translation.
		add_action( 'plugins_loaded', array( $this, 'translation' ) );

		// Hooks that always need to be present.
		$this->buddypress->register_hooks();
		$this->civicrm->register_hooks();
		$this->cpts->register_hooks();
		$this->metaboxes->register_hooks();
		$this->menus->register_hooks();
		$this->membership->register_hooks();
		$this->mirror->register_hooks();
		$this->shortcodes->register_hooks();
		$this->widgets->register_hooks();

		// Maintenance mode.
		if ( $this->maintenance_mode ) {
			add_action( 'init', array( $this, 'maintenance_mode' ) );
		}

	}



	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 0.1
	 */
	public function activate() {

		// Pass through.
		$this->cpts->activate();

	}



	/**
	 * Actions to perform on plugin deactivation. (NOT deletion)
	 *
	 * @since 0.1
	 */
	public function deactivate() {

		// Pass through.
		$this->cpts->deactivate();

	}



	/**
	 * Load translations if present.
	 *
	 * @since 0.1
	 */
	public function translation() {

		// Load translations.
		load_plugin_textdomain(
			'sof-utilities', // Unique name.
			false, // Deprecated argument.
			dirname( plugin_basename( SOF_UTILITIES_FILE ) ) . '/languages/' // Relative path to directory
		);

	}



	/**
	 * Puts WordPress into pseudo-maintenance mode.
	 *
	 * @since 0.3
	 */
	public function maintenance_mode() {

		// Allow back-end and network admins access.
		if ( ! is_admin() AND ! current_user_can( 'manage_network_plugins' ) ) {

			// Invoke maintenance.
			if ( file_exists( WP_CONTENT_DIR . '/maintenance.php' ) ) {
				require_once( WP_CONTENT_DIR . '/maintenance.php' );
				die();
			}

		}

	}



} // Class ends.



// Instantiate the class.
global $sof_utilities_plugin;
$sof_utilities_plugin = new Spirit_Of_Football_Utilities();

// Activation.
register_activation_hook( __FILE__, array( $sof_utilities_plugin, 'activate' ) );

// Deactivation.
register_deactivation_hook( __FILE__, array( $sof_utilities_plugin, 'deactivate' ) );



