<?php

/**
 * SOF Custom Post Types Class.
 *
 * A class that encapsulates SOF-specific Custom Post Types.
 *
 * @since 0.1
 *
 * @package WordPress
 * @subpackage SOF
 */
class Spirit_Of_Football_CPTs {



	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// nothing

	}



	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// always create post types
		//add_action( 'init', array( $this, 'create_post_types' ) );

	}




	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 0.1
	 */
	public function activate() {

		// pass through
		$this->create_post_types();

		// go ahead and flush
		flush_rewrite_rules();

	}



	/**
	 * Actions to perform on plugin deactivation. (NOT deletion)
	 *
	 * @since 0.1
	 */
	public function deactivate() {

		// flush rules to reset
		flush_rewrite_rules();

	}



	// #########################################################################



	/**
	 * Create our Custom Post Types.
	 *
	 * @since 0.1
	 */
	public function create_post_types() {

	}



} // class Spirit_Of_Football_CPTs ends



