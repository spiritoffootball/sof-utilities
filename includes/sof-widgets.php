<?php

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
	 * Constructor.
	 *
	 * @since 0.2.1
	 */
	public function __construct() {

		// nothing

	}



	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.2.1
	 */
	public function register_hooks() {

		// register widget
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

	}




	// #########################################################################



	/**
	 * Register widgets for this component.
	 *
	 * @since 0.2.1
	 */
	public function register_widgets() {

		// include widgets
		require_once SOF_UTILITIES_PATH . 'assets/widgets/sof-docs-widget.php';
		require_once SOF_UTILITIES_PATH . 'assets/widgets/sof-journey-teaser-widget.php';

		// register widgets
		register_widget( 'SOF_Docs_Widget_Recent_Docs' );
		register_widget( 'SOF_Widget_Journey_Teaser' );

	}



} // class ends



