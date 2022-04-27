<?php
/**
 * Plugin Name: SOF Utilities
 * Plugin URI: https://github.com/spiritoffootball/sof-utilities
 * Description: Network-wide utilities for the SOF sites.
 * Author: Christian Wach
 * Version: 0.3.1
 * Author URI: https://haystack.co.uk
 * Text Domain: sof-utilities
 * Domain Path: /languages
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Set our version here.
define( 'SOF_UTILITIES_VERSION', '0.3.1' );

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

		// Initialise once plugins are loaded.
		add_action( 'plugins_loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialise.
	 *
	 * @since 0.1
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && $done === true ) {
			return;
		}

		// Init translation.
		$this->translation();

		// Include files.
		$this->include_files();

		// Setup globals.
		$this->setup_globals();

		// Register hooks.
		$this->register_hooks();

		/**
		 * Broadcast that this plugin is now loaded.
		 *
		 * @since 0.3
		 */
		do_action( 'sof_utilities/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Include files.
	 *
	 * @since 0.1
	 */
	public function include_files() {

		// Include class files.
		include_once SOF_UTILITIES_PATH . 'includes/sof-buddypress.php';
		include_once SOF_UTILITIES_PATH . 'includes/sof-civicrm.php';
		include_once SOF_UTILITIES_PATH . 'includes/sof-cpts.php';
		include_once SOF_UTILITIES_PATH . 'includes/sof-metaboxes.php';
		include_once SOF_UTILITIES_PATH . 'includes/sof-menus.php';
		include_once SOF_UTILITIES_PATH . 'includes/sof-membership.php';
		include_once SOF_UTILITIES_PATH . 'includes/sof-mirror.php';
		include_once SOF_UTILITIES_PATH . 'includes/sof-shortcodes.php';
		include_once SOF_UTILITIES_PATH . 'includes/sof-widgets.php';

	}

	/**
	 * Set up objects.
	 *
	 * @since 0.1
	 */
	public function setup_globals() {

		// Init objects.
		$this->buddypress = new Spirit_Of_Football_BuddyPress( $this );
		$this->civicrm = new Spirit_Of_Football_CiviCRM( $this );
		$this->cpts = new Spirit_Of_Football_CPTs( $this );
		$this->metaboxes = new Spirit_Of_Football_Metaboxes( $this );
		$this->menus = new Spirit_Of_Football_Menus( $this );
		$this->membership = new Spirit_Of_Football_Membership( $this );
		$this->mirror = new Spirit_Of_Football_Mirror( $this );
		$this->shortcodes = new Spirit_Of_Football_Shortcodes( $this );
		$this->widgets = new Spirit_Of_Football_Widgets( $this );

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// Maintenance mode.
		if ( $this->maintenance_mode ) {
			add_action( 'init', [ $this, 'maintenance_mode' ] );
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
		// phpcs:ignore WordPress.WP.DeprecatedParameters.Load_plugin_textdomainParam2Found
		load_plugin_textdomain(
			'sof-utilities', // Unique name.
			false, // Deprecated argument.
			dirname( plugin_basename( SOF_UTILITIES_FILE ) ) . '/languages/' // Relative path to directory.
		);

	}

	/**
	 * Puts WordPress into pseudo-maintenance mode.
	 *
	 * @since 0.3
	 */
	public function maintenance_mode() {

		// Allow back-end and network admins access.
		if ( ! is_admin() && ! current_user_can( 'manage_network_plugins' ) ) {

			// Invoke maintenance.
			if ( file_exists( WP_CONTENT_DIR . '/maintenance.php' ) ) {
				require_once WP_CONTENT_DIR . '/maintenance.php';
				die();
			}

		}

	}

}

/**
 * Utility to get a reference to this plugin.
 *
 * @since 0.3
 *
 * @return Spirit_Of_Football_Utilities $plugin The plugin reference.
 */
function spirit_of_football_utilities() {

	// Store instance in static variable.
	static $plugin = false;

	// Maybe return instance.
	if ( false === $plugin ) {
		$plugin = new Spirit_Of_Football_Utilities();
	}

	// --<
	return $plugin;

}

// Initialise plugin now.
spirit_of_football_utilities();

// Activation.
register_activation_hook( __FILE__, [ spirit_of_football_utilities(), 'activate' ] );

// Deactivation.
register_deactivation_hook( __FILE__, [ spirit_of_football_utilities(), 'deactivate' ] );
