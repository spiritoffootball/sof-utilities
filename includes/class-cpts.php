<?php
/**
 * Custom Post Types Class.
 *
 * Handles SOF-specific Custom Post Types.
 *
 * @since 0.1
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * SOF Custom Post Types Class.
 *
 * A class that encapsulates SOF-specific Custom Post Types.
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

		/*
		// Register hooks.
		$this->register_hooks();
		*/

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// Activation and deactivation.
		add_action( 'sof_utilities/activated', [ $this, 'activate' ] );
		add_action( 'sof_utilities/deactivated', [ $this, 'deactivate' ] );

		// Always create post types.
		add_action( 'init', [ $this, 'create_post_types' ] );

	}


	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 0.1
	 */
	public function activate() {

		// Pass through.
		$this->create_post_types();

		// Go ahead and flush.
		flush_rewrite_rules();

	}

	/**
	 * Actions to perform on plugin deactivation. (NOT deletion)
	 *
	 * @since 0.1
	 */
	public function deactivate() {

		// Flush rules to reset.
		flush_rewrite_rules();

	}

	// -------------------------------------------------------------------------

	/**
	 * Create our Custom Post Types.
	 *
	 * @since 0.1
	 */
	public function create_post_types() {

	}

}
