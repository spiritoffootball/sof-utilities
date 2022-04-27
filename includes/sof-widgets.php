<?php
/**
 * Widgets Class.
 *
 * Handles SOF-specific Widgets.
 *
 * @package Spirit_Of_Football_Utilities
 * @since 0.2.1
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;



/**
 * SOF Widgets Class.
 *
 * A class that encapsulates initialisation of SOF-specific Widgets.
 *
 * @since 0.2.1
 *
 * @package WordPress
 * @subpackage SOF
 */
class Spirit_Of_Football_Widgets {

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
	 * @since 0.2.1
	 */
	public function register_hooks() {

		// Register widgets.
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );

	}




	// -------------------------------------------------------------------------



	/**
	 * Register widgets for this component.
	 *
	 * @since 0.2.1
	 */
	public function register_widgets() {

		// Include widgets.
		require_once SOF_UTILITIES_PATH . 'assets/widgets/sof-docs-widget.php';
		require_once SOF_UTILITIES_PATH . 'assets/widgets/sof-journey-teaser-widget.php';
		require_once SOF_UTILITIES_PATH . 'assets/widgets/sof-featured-page-widget.php';
		require_once SOF_UTILITIES_PATH . 'assets/widgets/sof-child-pages-widget.php';

		// Register widgets.
		register_widget( 'SOF_Docs_Widget_Recent_Docs' );
		register_widget( 'SOF_Widget_Journey_Teaser' );
		register_widget( 'SOF_Widget_Featured_Page' );
		register_widget( 'SOF_Widget_Child_Pages' );

	}



} // Class ends.



