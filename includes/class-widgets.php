<?php
/**
 * Widgets Class.
 *
 * Handles SOF-specific Widgets.
 *
 * @since 0.2.1
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * SOF Widgets Class.
 *
 * A class that encapsulates initialisation of SOF-specific Widgets.
 *
 * @since 0.2.1
 */
class Spirit_Of_Football_Widgets {

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
	 * @since 0.2.1
	 */
	private function register_hooks() {

		// Register widgets.
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );

	}


	// -----------------------------------------------------------------------------------

	/**
	 * Register widgets for this component.
	 *
	 * @since 0.2.1
	 */
	public function register_widgets() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Include widgets.
		require SOF_UTILITIES_PATH . 'assets/widgets/class-widget-journey-teaser.php';
		require SOF_UTILITIES_PATH . 'assets/widgets/class-widget-featured-page.php';
		require SOF_UTILITIES_PATH . 'assets/widgets/class-widget-child-pages.php';

		// Register widgets.
		register_widget( 'SOF_Widget_Journey_Teaser' );
		register_widget( 'SOF_Widget_Featured_Page' );
		register_widget( 'SOF_Widget_Child_Pages' );

		// Optionally register BuddyPress Docs widget.
		if ( defined( 'BP_DOCS_VERSION' ) ) {
			require SOF_UTILITIES_PATH . 'assets/widgets/class-widget-docs.php';
			register_widget( 'SOF_Docs_Widget_Recent_Docs' );
		}

		// We're done.
		$done = true;

	}

}
