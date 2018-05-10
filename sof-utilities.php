<?php /*
--------------------------------------------------------------------------------
Plugin Name: SOF Utilities
Plugin URI: http://spiritoffootball.com
Description: Network-wide Utilities for the SOF sites
Author: Christian Wach
Version: 0.3
Author URI: http://haystack.co.uk
Text Domain: sof-utilities
--------------------------------------------------------------------------------
*/



// set our version here
define( 'SOF_UTILITIES_VERSION', '0.3' );

// store reference to this file
if ( ! defined( 'SOF_UTILITIES_FILE' ) ) {
	define( 'SOF_UTILITIES_FILE', __FILE__ );
}

// store URL to this plugin's directory
if ( ! defined( 'SOF_UTILITIES_URL' ) ) {
	define( 'SOF_UTILITIES_URL', plugin_dir_url( SOF_UTILITIES_FILE ) );
}

// store PATH to this plugin's directory
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
	 * BuddyPress object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $buddypress The BuddyPress object.
	 */
	public $buddypress;

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

		// include files
		$this->include_files();

		// setup globals
		$this->setup_globals();

		// register hooks
		$this->register_hooks();

	}



	/**
	 * Include files.
	 *
	 * @since 0.1
	 */
	public function include_files() {

		// include BuddyPress class
		include_once SOF_UTILITIES_PATH . 'includes/sof-buddypress.php';

		// include CPT class
		include_once SOF_UTILITIES_PATH . 'includes/sof-cpts.php';

		// include Metaboxes class
		include_once SOF_UTILITIES_PATH . 'includes/sof-metaboxes.php';

		// include Menus class
		include_once SOF_UTILITIES_PATH . 'includes/sof-menus.php';

		// include Membership class
		include_once SOF_UTILITIES_PATH . 'includes/sof-membership.php';

		// include Mirror class
		include_once SOF_UTILITIES_PATH . 'includes/sof-mirror.php';

		// include Shortcodes class
		include_once SOF_UTILITIES_PATH . 'includes/sof-shortcodes.php';

		// include Widgets class
		include_once SOF_UTILITIES_PATH . 'includes/sof-widgets.php';

	}



	/**
	 * Set up objects.
	 *
	 * @since 0.1
	 */
	public function setup_globals() {

		// init BuddyPress object
		$this->buddypress = new Spirit_Of_Football_BuddyPress;

		// init CPT object
		$this->cpts = new Spirit_Of_Football_CPTs;

		// init Metaboxes object
		$this->metaboxes = new Spirit_Of_Football_Metaboxes;

		// init Menus object
		$this->menus = new Spirit_Of_Football_Menus;

		// init Membership object
		$this->membership = new Spirit_Of_Football_Membership;

		// init Mirror object
		$this->mirror = new Spirit_Of_Football_Mirror;

		// init Shortcodes object
		$this->shortcodes = new Spirit_Of_Football_Shortcodes;

		// init Widgets object
		$this->widgets = new Spirit_Of_Football_Widgets;

	}



	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// use translation
		add_action( 'plugins_loaded', array( $this, 'translation' ) );

		// hooks that always need to be present
		$this->buddypress->register_hooks();
		$this->cpts->register_hooks();
		$this->metaboxes->register_hooks();
		$this->menus->register_hooks();
		$this->membership->register_hooks();
		$this->mirror->register_hooks();
		$this->shortcodes->register_hooks();
		$this->widgets->register_hooks();

	}



	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 0.1
	 */
	public function activate() {

		// pass through
		$this->cpts->activate();

	}



	/**
	 * Actions to perform on plugin deactivation. (NOT deletion)
	 *
	 * @since 0.1
	 */
	public function deactivate() {

		// pass through
		$this->cpts->deactivate();

	}



	/**
	 * Loads translation, if present.
	 *
	 * @since 0.1
	 */
	function translation() {

		// only use, if we have it...
		if ( function_exists( 'load_plugin_textdomain' ) ) {

			// not used, as there are no translations as yet
			load_plugin_textdomain(

				// unique name
				'sof-utilities',

				// deprecated argument
				false,

				// relative path to directory containing translation files
				dirname( plugin_basename( SOF_UTILITIES_FILE ) ) . '/languages/'

			);

		}

	}



} // class Spirit_Of_Football_Utilities ends



// Instantiate the class
global $sof_utilities_plugin;
$sof_utilities_plugin = new Spirit_Of_Football_Utilities();

// activation
register_activation_hook( __FILE__, array( $sof_utilities_plugin, 'activate' ) );

// deactivation
register_deactivation_hook( __FILE__, array( $sof_utilities_plugin, 'deactivate' ) );



