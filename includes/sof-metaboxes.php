<?php
/**
 * Metaboxes Class.
 *
 * Handles SOF-specific Metaboxes on default post types.
 *
 * @package Spirit_Of_Football_Utilities
 * @since 0.1
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;



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
	 * @since 0.1
	 */
	public function register_hooks() {

		// Exclude from SOF eV for now.
		if ( 'sofev' == sof_get_site() ) {
			return;
		}

		// Add meta boxes.
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );

		// Intercept save.
		add_action( 'save_post', [ $this, 'save_post' ], 1, 2 );

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

		// Add our meta box.
		add_meta_box(
			'sof_page_options',
			__( 'Title Visibility', 'sof-utilities' ),
			[ $this, 'title_visibility_box' ],
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

		// Use nonce for verification.
		wp_nonce_field( 'sof_page_settings', 'sof_nonce' );

		// ---------------------------------------------------------------------
		// Set "Title Visibility" Status
		// ---------------------------------------------------------------------

		// Show a title.
		echo '<p><strong><label for="show_heading">' . __( 'Make title hidden', 'sof-utilities' ) . '</label></strong>';

		// Set key
		$db_key = 'show_heading';

		// Default to "not checked".
		$checked = '';

		// Override if the custom field has a value and it's the checked value.
		$existing = get_post_meta( $post->ID, $db_key, true );
		if ( false !== $existing AND $existing == '1' ) {
			$checked = ' checked="checked"';
		}

		// Select.
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

		// We don't use post_id because we're not interested in revisions.

		// Store our page meta data.
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

		// If no post, kick out.
		if ( ! $post_obj ) {
			return;
		}

		// Authenticate.
		$_nonce = isset( $_POST['sof_nonce'] ) ? $_POST['sof_nonce'] : '';
		if ( ! wp_verify_nonce( $_nonce, 'sof_page_settings' ) ) {
			return;
		}

		// Is this an auto save routine?
		if ( defined('DOING_AUTOSAVE') AND DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_page', $post_obj->ID ) ) {
			return;
		}

		// Check for revision.
		if ( $post_obj->post_type == 'revision' ) {

			// Get parent.
			if ( $post_obj->post_parent != 0 ) {
				$post = get_post( $post_obj->post_parent );
			} else {
				$post = $post_obj;
			}

		} else {
			$post = $post_obj;
		}

		// ---------------------------------------------------------------------
		// Okay, we're through...
		// ---------------------------------------------------------------------

		global $wpdb;

		// If default page type.
		if ( $post->post_type == 'page' ) {

			// Set key.
			$key = 'show_heading';

			// Find the data.
			$_data = ( isset( $_POST[$key] ) ) ? esc_sql( $_POST[$key] ) : '0';

			// Attached Quote.
			$this->_save_meta( $post, 'show_heading', $_data );

		}

	}



	/**
	 * Utility to automate meta data saving.
	 *
	 * @since 0.1
	 *
	 * @param WP_Post $post The WordPress post object.
	 * @param string $key The meta key.
	 * @param mixed $data The data to be saved.
	 * @return mixed $data The data that was saved.
	 */
	private function _save_meta( $post, $key, $data = '' ) {

		// If the custom field already has a value.
		$existing = get_post_meta( $post->ID, $key, true );
		if ( false !== $existing ) {

			// Update the data.
			update_post_meta( $post->ID, $key, $data );

		} else {

			// Add the data.
			add_post_meta( $post->ID, $key, $data, true );

		}

		// --<
		return $data;

	}



} // Class ends.



