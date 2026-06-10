<?php
/**
 * Shortcodes Loader Class.
 *
 * Handles loading SOF-specific Shortcode classes.
 *
 * @since 0.1
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Shortcodes Loader Class.
 *
 * A class that encapsulates loading SOF-specific Shortcode classes.
 *
 * @since 0.1
 */
class Spirit_Of_Football_Shortcodes {

	/**
	 * Plugin object.
	 *
	 * @since 0.3
	 * @access public
	 * @var Spirit_Of_Football_Utilities
	 */
	public $plugin;

	/**
	 * Team Shortcode object.
	 *
	 * @since 1.0.1
	 * @access public
	 * @var Spirit_Of_Football_Shortcode_Team
	 */
	public $team;

	/**
	 * Logo Orbiter Shortcode object.
	 *
	 * @since 1.0.1
	 * @access public
	 * @var Spirit_Of_Football_Shortcode_Orbiter
	 */
	public $orbiter;

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

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Bootstrap class.
		$this->include_files();
		$this->setup_globals();
		$this->register_hooks();

		/**
		 * Broadcast that this class is now loaded.
		 *
		 * @since 1.0.1
		 */
		do_action( 'sof_utilities/shortcodes/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Include files.
	 *
	 * @since 1.0.1
	 */
	private function include_files() {

		// Include class files.
		require SOF_UTILITIES_PATH . 'includes/class-shortcode-team.php';
		require SOF_UTILITIES_PATH . 'includes/class-shortcode-orbiter.php';

	}

	/**
	 * Set up objects.
	 *
	 * @since 1.0.1
	 */
	private function setup_globals() {

		// Init objects.
		$this->team    = new Spirit_Of_Football_Shortcode_Team( $this );
		$this->orbiter = new Spirit_Of_Football_Shortcode_Orbiter( $this );

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	private function register_hooks() {

	}

}
