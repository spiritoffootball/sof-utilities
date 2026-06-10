<?php
/**
 * Logo with Orbiting Ball Shortcode Class.
 *
 * Handles Team Shortcodes.
 *
 * @since 1.0.1
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Logo with Orbiting Ball Shortcodes Class.
 *
 * A class that encapsulates the Logo with Orbiting Ball Shortcode.
 *
 * @since 1.0.1
 */
class Spirit_Of_Football_Shortcode_Orbiter {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.1
	 * @access public
	 * @var Spirit_Of_Football_Utilities
	 */
	public $plugin;

	/**
	 * Shortcodes loader object.
	 *
	 * @since 1.0.1
	 * @access public
	 * @var Spirit_Of_Football_Shortcodes
	 */
	public $shortcodes;

	/**
	 * Constructor.
	 *
	 * @since 1.0.1
	 *
	 * @param Spirit_Of_Football_Shortcodes $shortcodes The Shortcode Loader object.
	 */
	public function __construct( $shortcodes ) {

		// Store references to objects.
		$this->shortcodes = $shortcodes;
		$this->plugin     = $shortcodes->plugin;

		// Init when loader class is loaded.
		add_action( 'sof_utilities/shortcodes/loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialise this object.
	 *
	 * @since 1.0.1
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Register hooks.
		$this->register_hooks();

		// We're done.
		$done = true;

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 1.0.1
	 */
	private function register_hooks() {

		// Register Shortcode.
		add_shortcode( 'sof_orbiter', [ $this, 'shortcode_render' ] );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Add the team to a page via a Shortcode.
	 *
	 * @since 1.0.1
	 *
	 * @param array  $attr The saved shortcode attributes.
	 * @param string $content The enclosed content of the shortcode.
	 * @return string $markup The HTML markup for the shortcode.
	 */
	public function shortcode_render( $attr, $content = null ) {

		// Get "Logo with Orbiting Ball" markup.
		ob_start();
		include SOF_UTILITIES_PATH . 'assets/templates/sof-shortcode-orbiter-template.php';
		$markup = ob_get_contents();
		ob_end_clean();

		// --<
		return $markup;

	}

}
