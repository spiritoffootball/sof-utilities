<?php
/**
 * Custom Post Types Class.
 *
 * Handles SOF-specific Custom Post Type functionality.
 *
 * @since 0.1
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * SOF Custom Post Type Loader Class.
 *
 * A class that encapsulates SOF-specific Custom Post Type functionality.
 *
 * @since 0.1
 */
class Spirit_Of_Football_CPTs {

	/**
	 * Plugin object.
	 *
	 * @since 0.3
	 * @access public
	 * @var Spirit_Of_Football_Utilities
	 */
	public $plugin;

	/**
	 * Terms object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_CPT_Terms
	 */
	public $terms;

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
	 * Initialises this object.
	 *
	 * @since 0.3
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Bootstrap class.
		$this->include_files();
		$this->setup_objects();
		$this->register_hooks();

		/**
		 * Fires when this class is loaded.
		 *
		 * @since 1.0
		 */
		do_action( 'sof_utilities/cpts/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Includes files.
	 *
	 * @since 1.0
	 */
	private function include_files() {

		require SOF_UTILITIES_PATH . 'includes/class-cpt-terms.php';

	}

	/**
	 * Instantiates objects.
	 *
	 * @since 1.0
	 */
	private function setup_objects() {

		$this->terms = new Spirit_Of_Football_CPT_Terms( $this );

	}

	/**
	 * Registers WordPress hook callbacks.
	 *
	 * @since 0.1
	 */
	private function register_hooks() {

	}

}
