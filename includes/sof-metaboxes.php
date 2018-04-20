<?php

/**
 * SOF Metaboxes Class.
 *
 * A class that encapsulates SOF-specific Metaboxes on default post types.
 *
 * @since 0.1
 *
 * @package WordPress
 * @subpackage SOF
 */
class Spirit_Of_Football_Metaboxes {



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

		// exclude from SOF eV for now...
		if ( 'sofev' == sof_get_site() ) return;

		// add meta boxes
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		// intercept save
		add_action( 'save_post', array( $this, 'save_post' ), 1, 2 );

	}




	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 0.1
	 */
	public function activate() {

	}



	/**
	 * Actions to perform on plugin deactivation. (NOT deletion)
	 *
	 * @since 0.1
	 */
	public function deactivate() {

	}



	// #########################################################################



	/**
	 * Adds meta boxes to admin screens.
	 *
	 * @since 0.1
	 */
	public function add_meta_boxes() {

		// add our meta box
		add_meta_box(
			'sof_page_options',
			__( 'Title Visibility', 'sof-utilities' ),
			array( $this, 'title_visibility_box' ),
			'page',
			'side'
		);

	}



	/**
	 * Adds meta box to page edit screens.
	 *
	 * @since 0.1
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function title_visibility_box( $post ) {

		// Use nonce for verification
		wp_nonce_field( 'sof_page_settings', 'sof_nonce' );

		// ---------------------------------------------------------------------
		// Set "Title Visibility" Status
		// ---------------------------------------------------------------------

		// show a title
		echo '<p><strong><label for="show_heading">' . __( 'Make title hidden', 'sof-utilities' ) . '</label></strong>';

		// set key
		$db_key = 'show_heading';

		// default to "not checked"
		$checked = '';

		// override if the custom field has a value and it's the checked value
		$existing = get_post_meta( $post->ID, $db_key, true );
		if ( false !== $existing AND $existing == '1' ) {
			$checked = ' checked="checked"';
		}

		// select
		echo '
		<input id="show_heading" name="show_heading" value="1" type="checkbox" ' . $checked . '/>
		</p>
		';

	}



	/**
	 * Stores our additional params.
	 *
	 * @since 0.1
	 *
	 * @param integer $post_id the ID of the post (or revision).
	 * @param integer $post the post object.
	 */
	public function save_post( $post_id, $post ) {

		// we don't use post_id because we're not interested in revisions

		// store our page meta data
		$result = $this->_save_page_meta( $post );

	}



	// #########################################################################



	/**
	 * When a page is saved, this also saves the options.
	 *
	 * @since 0.1
	 *
	 * @param WP_Post $post_obj The object for the post (or revision).
	 */
	private function _save_page_meta( $post_obj ) {

		// if no post, kick out
		if ( ! $post_obj ) return;

		// authenticate
		$_nonce = isset( $_POST['sof_nonce'] ) ? $_POST['sof_nonce'] : '';
		if ( ! wp_verify_nonce( $_nonce, 'sof_page_settings' ) ) { return; }

		// is this an auto save routine?
		if ( defined('DOING_AUTOSAVE') AND DOING_AUTOSAVE ) { return; }

		// Check permissions
		if ( ! current_user_can( 'edit_page', $post_obj->ID ) ) { return; }

		// check for revision
		if ( $post_obj->post_type == 'revision' ) {

			// get parent
			if ( $post_obj->post_parent != 0 ) {
				$post = get_post( $post_obj->post_parent );
			} else {
				$post = $post_obj;
			}

		} else {
			$post = $post_obj;
		}

		// ---------------------------------------------------------------------
		// okay, we're through...
		// ---------------------------------------------------------------------

		global $wpdb;

		// if default page type...
		if ( $post->post_type == 'page' ) {

			// set key
			$key = 'show_heading';

			// find the data
			$_data = ( isset( $_POST[$key] ) ) ? esc_sql( $_POST[$key] ) : '0';

			// Attached Quote
			$this->_save_meta( $post, 'show_heading', $_data );

		}

	}



	/**
	 * Utility to automate meta data saving.
	 *
	 * @since 0.1
	 *
	 * @param WP_Post $post_obj The WordPress post object.
	 * @param string $key The meta key.
	 * @param mixed $data The data to be saved.
	 * @return mixed $data The data that was saved.
	 */
	private function _save_meta( $post, $key, $data = '' ) {

		// if the custom field already has a value...
		$existing = get_post_meta( $post->ID, $key, true );
		if ( false !== $existing ) {

			// update the data
			update_post_meta( $post->ID, $key, $data );

		} else {

			// add the data
			add_post_meta( $post->ID, $key, $data, true );

		}

		// --<
		return $data;

	}



} // class Spirit_Of_Football_Metaboxes ends



