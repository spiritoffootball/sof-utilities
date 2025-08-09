<?php
/**
 * Mirror Content Class.
 *
 * Handles mirroring of content between The Ball 2018 and SOF Germany 2018 blog.
 *
 * @since 0.2.1
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * SOF Mirror Content Class.
 *
 * A class that encapsulates SOF-specific Content Mirroring functionality. What
 * happens is that when a post is created in the "Daily Ballblog" (which is a
 * term in the 'category' taxonomy), the content is mirrored to The Ball 2018
 * site for translation.
 *
 * @since 0.2.1
 */
class Spirit_Of_Football_Mirror {

	/**
	 * Plugin object.
	 *
	 * @since 0.3
	 * @access public
	 * @var Spirit_Of_Football_Utilities
	 */
	public $plugin;

	/**
	 * English Site ID.
	 *
	 * @since 0.2.1
	 * @access public
	 * @var int $site_id_en The numeric ID of the English site.
	 */
	public $site_id_en = 13;

	/**
	 * German Site ID.
	 *
	 * @since 0.2.1
	 * @access public
	 * @var int $site_id_de The numeric ID of the German site.
	 */
	public $site_id_de = 12;

	/**
	 * "Daily Ballblog" Term ID on German site.
	 *
	 * @since 0.2.1
	 * @access public
	 * @var int $cat_id The numeric ID of the "Daily Ballblog" term.
	 */
	public $term_id = 674;

	/**
	 * The name of the meta key attached to the English post. Contains the
	 * numeric ID of the German post on the SOF eV site.
	 *
	 * @since 0.2.1
	 * @access public
	 * @var int $post_meta_key_de The name of the meta key attached to the English post.
	 */
	public $post_meta_key_de = '_sofev2018_post_id';

	/**
	 * The name of the meta key attached to the German post. Contains the
	 * numeric ID of the English post on The Ball 2018 site.
	 *
	 * @since 0.2.1
	 * @access public
	 * @var int $post_meta_key_en The name of the meta key attached to the German post.
	 */
	public $post_meta_key_en = '_theball2018_post_id';

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
	public function register_hooks() {

		// On The Ball 2018.
		if ( get_current_blog_id() === $this->site_id_en ) {

			// Add meta boxes.
			add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes_theball' ] );

			// Add link to content.
			add_filter( 'the_content', [ $this, 'prepend_link' ], 50, 3 );

			// Bail now.
			return;

		}

		// Only proceed on SOF eV.
		if ( get_current_blog_id() !== $this->site_id_de ) {
			return;
		}

		// Add meta boxes.
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes_sofev' ] );

		// Intercept save event.
		add_action( 'save_post', [ $this, 'save_post' ], 50, 3 );

		// Add link to content.
		add_filter( 'the_content', [ $this, 'prepend_link' ], 50, 3 );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Prepends links to page/post content.
	 *
	 * @since 0.2.1
	 *
	 * @param str $content The content of the page/post.
	 * @return str $content The modified content.
	 */
	public function prepend_link( $content ) {

		// Reference our post.
		global $post;

		// Skip if we're doing the 'get_the_excerpt' filter.
		if ( doing_action( 'get_the_excerpt' ) ) {
			return $content;
		}

		// If on German site.
		if ( get_current_blog_id() === $this->site_id_de ) {
			$link = $this->get_link_en( $post->ID );
			if ( ! empty( $link ) ) {
				$prepend = '<div class="sof_mirror">' . $link . '</div>';
				$content = $prepend . $content;
			}
		}

		// If on English site.
		if ( get_current_blog_id() === $this->site_id_en ) {
			$link = $this->get_link_de( $post->ID );
			if ( ! empty( $link ) ) {
				$prepend = '<div class="sof_mirror">' . $link . '</div>';
				$content = $prepend . $content;
			}
		}

		// --<
		return $content;

	}

	/**
	 * Get the link to the English version on The Ball 2018.
	 *
	 * @since 0.2.1
	 *
	 * @param int $post_id The numeric ID of the German post.
	 * @return str $link The permalink of the English post.
	 */
	public function get_link_en( $post_id ) {

		// Bail if not on German site.
		if ( get_current_blog_id() !== $this->site_id_de ) {
			return;
		}

		// Have we got a mirrored post?
		$english_id = get_post_meta( $post_id, $this->post_meta_key_en, true );

		// Bail if there isn't one.
		if ( empty( $english_id ) ) {
			return;
		}

		// Switch to English site.
		switch_to_blog( $this->site_id_en );

		// Init link.
		$link = '';

		// Check that the post is published.
		if ( 'publish' === get_post_status( $english_id ) ) {

			// Construct link.
			$link = '<a href="' . get_permalink( $english_id ) . '" class="button sof_english_post">' .
						__( 'Read this post in English', 'sof-utilities' ) .
					'</a>';

		}

		// Switch back.
		restore_current_blog();

		// Show link.
		return $link;

	}

	/**
	 * Get the link to the German version on SOF eV.
	 *
	 * @since 0.2.1
	 *
	 * @param int $post_id The numeric ID of the English post.
	 * @return str $link The permalink of the German post.
	 */
	public function get_link_de( $post_id ) {

		// Bail if not on English site.
		if ( get_current_blog_id() !== $this->site_id_en ) {
			return false;
		}

		// Have we got a mirrored post?
		$german_id = get_post_meta( $post_id, $this->post_meta_key_de, true );

		// Bail if there isn't one.
		if ( empty( $german_id ) ) {
			return false;
		}

		// Switch to German site.
		switch_to_blog( $this->site_id_de );

		// Init link.
		$link = '';

		// Check that the post is published.
		if ( 'publish' === get_post_status( $german_id ) ) {

			// Construct link.
			$link = '<a href="' . get_permalink( $german_id ) . '" class="button sof_german_post">' .
						__( 'Read this post in German', 'sof-utilities' ) .
					'</a>';

		}

		// Switch back.
		restore_current_blog();

		// --<
		return $link;

	}

	/**
	 * Adds meta boxes to admin screens on The Ball 2018.
	 *
	 * @since 0.2.1
	 */
	public function add_meta_boxes_theball() {

		// Add our meta box.
		add_meta_box(
			'theball2018_post_options',
			__( 'German version', 'sof-utilities' ),
			[ $this, 'german_version_box' ],
			'post',
			'side',
			'high'
		);

	}

	/**
	 * Adds meta box to post edit screens on The Ball 2018.
	 *
	 * @since 0.2.1
	 *
	 * @param WP_Post $post The object for the current post.
	 */
	public function german_version_box( $post ) {

		// Access post.
		global $post;

		// Use nonce for verification.
		wp_nonce_field( 'theball2018_post_settings', 'theball2018_nonce' );

		// Have we got a mirrored post?
		$existing_id = get_post_meta( $post->ID, $this->post_meta_key_de, true );

		// Bail if there isn't one.
		if ( empty( $existing_id ) ) {

			// Helpful text.
			echo '<p>' . esc_html__( 'This post does not have a German version.', 'sof-utilities' ) . '</p>' . "\n";
			return;

		}

		// -----------------------------------------------------------------
		// Show link to German post
		// -----------------------------------------------------------------

		// Helpful text.
		echo '<p>' . esc_html__( 'This post has a German version.', 'sof-utilities' ) . '</p>' . "\n";

		// Switch to SOF eV.
		switch_to_blog( $this->site_id_de );

		// Get the edit post link.
		$edit_link = get_edit_post_link( $existing_id );

		// Switch back.
		restore_current_blog();

		// Define label.
		$link = __( 'Edit German version', 'sof-utilities' );

		// Show link.
		echo '<p><a href="' . esc_url( $edit_link ) . '">' . esc_html( $link ) . '</a></p>' . "\n";

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Adds meta boxes to admin screens on SOF eV.
	 *
	 * @since 0.2.1
	 */
	public function add_meta_boxes_sofev() {

		// Add our meta box.
		add_meta_box(
			'sofev2018_post_options',
			__( 'English version', 'sof-utilities' ),
			[ $this, 'english_version_box' ],
			'post',
			'side',
			'high'
		);

	}

	/**
	 * Adds meta box to post edit screens on SOF eV.
	 *
	 * @since 0.2.1
	 *
	 * @param WP_Post $post The object for the current post.
	 */
	public function english_version_box( $post ) {

		// Access post.
		global $post;

		// Use nonce for verification.
		wp_nonce_field( 'sofev2018_post_settings', 'sofev2018_nonce' );

		// Have we got a mirrored post?
		$existing_id = get_post_meta( $post->ID, $this->post_meta_key_en, true );

		// If there is one.
		if ( absint( $existing_id ) > 0 ) {

			// -----------------------------------------------------------------
			// Show link to English post.
			// -----------------------------------------------------------------

			// Helpful text.
			echo '<p>' . esc_html__( 'This post has an English version.', 'sof-utilities' ) . '</p>' . "\n";

			// Switch to The Ball 2018.
			switch_to_blog( $this->site_id_en );

			// Get the edit post link.
			$edit_link = get_edit_post_link( $existing_id );

			// Switch back.
			restore_current_blog();

			// Define label.
			$label = __( 'Edit English version', 'sof-utilities' );

			// Show link.
			echo '<p><a href="' . esc_url( $edit_link ) . '">' . esc_html( $label ) . '</a></p>' . "\n";

		} else {

			// Bail if the post does not have the relevant term.
			if ( ! has_term( $this->term_id, 'category', $post->ID ) ) {

				// Helpful text.
				echo '<p>' . esc_html__( 'You will be able to create an English version of this post on The Ball 2018 when you have added it to the "Daily Ballblog" category. You do not have to publish this post first - it can still be in draft mode.', 'sof-utilities' ) . '</p>' . "\n";

			} else {

				// -----------------------------------------------------------------
				// Create English post with content of current post.
				// -----------------------------------------------------------------

				// Helpful text.
				echo '<p>' . esc_html__( 'You can now create a copy of this post on The Ball 2018. When you have done so, you can translate it into English. It is best to make a copy when this post is finished and published.', 'sof-utilities' ) . '</p>' . "\n";

				// Define label.
				$label = __( 'Create an English version', 'sof-utilities' );

				// Show a title.
				echo '
				<div class="checkbox">
					<label for="sofev2018_create_en"><input type="checkbox" value="1" id="sofev2018_create_en" name="sofev2018_create_en" /> ' .
						esc_html( $label ) .
					'</label>
				</div>' . "\n";

			}

		}

	}

	/**
	 * Intercept save post and check if it's part of the "Daily Ballblog".
	 *
	 * @since 0.2.1
	 *
	 * @param int    $post_id The numeric ID of the WP post.
	 * @param object $post The WP post object.
	 * @param bool   $update True if the post is being updated, false otherwise.
	 */
	public function save_post( $post_id, $post, $update ) {

		// Bail if not a valid post.
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		// Bail if not a post.
		if ( 'post' !== $post->post_type ) {
			return;
		}

		// Bail if this is an auto save routine.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_posts', $post->ID ) ) {
			return;
		}

		// If this is a revision, get the real post ID and post object.
		$parent_id = wp_is_post_revision( $post_id );
		if ( $parent_id ) {
			$post_id = $parent_id;
			$post    = get_post( $post_id );
		}

		// Authenticate.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$nonce = isset( $_POST['sofev2018_nonce'] ) ? wp_unslash( $_POST['sofev2018_nonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'sofev2018_post_settings' ) ) {
			return;
		}

		// Bail if our checkbox has not been checked.
		$active = isset( $_POST['sofev2018_create_en'] ) ? absint( $_POST['sofev2018_create_en'] ) : '0';
		if ( 1 !== $active ) {
			return;
		}

		// Bail if the post does not have the relevant term.
		if ( ! has_term( $this->term_id, 'category', $post_id ) ) {
			return;
		}

		// Mirror content.
		$this->mirror_post( $post, $update );

	}

	/**
	 * Mirror the post to a corresponding post on The Ball 2018 site.
	 *
	 * @since 0.2.1
	 *
	 * @param object $post The WP post object.
	 * @param bool   $update True if the post is being updated, false otherwise.
	 */
	public function mirror_post( $post, $update ) {

		// Have we already mirrored this post?
		$existing_id = get_post_meta( $post->ID, $this->post_meta_key_en, true );

		// Bail if we have - for now.
		if ( absint( $existing_id ) > 0 ) {
			return;
		}

		// Parse gallery shortcodes and add pointer to SOF eV.
		$content = str_replace(
			'[gallery ',
			'[gallery sof_site_id="' . $this->site_id_de . '" ',
			$post->post_content
		);

		// Copy relevant data to fresh array.
		$new_post = [
			'post_type'             => $post->post_type,
			'post_status'           => 'draft',
			'post_date'             => $post->post_date,
			'post_date_gmt'         => $post->post_date_gmt,
			'post_title'            => $post->post_title,
			'post_author'           => $post->post_author,
			'post_content'          => $content,
			'post_excerpt'          => $post->post_excerpt,
			'comment_status'        => $post->comment_status,
			'ping_status'           => $post->ping_status,
			'to_ping'               => '', // Quick fix for Windows.
			'pinged'                => '', // Quick fix for Windows.
			'post_content_filtered' => '', // Quick fix for Windows.
			'post_parent'           => 0,
			'menu_order'            => 0,
		];

		// Get location if Geo Mashup is active.
		if ( class_exists( 'GeoMashupDB' ) ) {
			$location = GeoMashupDB::get_post_location( $post->ID );
		}

		// Switch to The Ball 2018.
		switch_to_blog( $this->site_id_en );

		// Insert post in target site.
		$new_id = wp_insert_post( $new_post );

		// If successful.
		if ( ! is_wp_error( $new_id ) ) {

			// Save reverse correspondence.
			$this->save_meta( $new_id, $this->post_meta_key_de, (string) $post->ID );

			// If Geo Mashup is active and we have a location.
			if ( class_exists( 'GeoMashupDB' ) && isset( $location ) ) {

				// Convert new location to array.
				$new_location = (array) $location;

				// Unset redundant data to create new location.
				if ( isset( $new_location['id'] ) ) {
					unset( $new_location['id'] );
				}
				if ( isset( $new_location['object_id'] ) ) {
					unset( $new_location['object_id'] );
				}
				if ( isset( $new_location['label'] ) ) {
					unset( $new_location['label'] );
				}
				if ( isset( $new_location['post_author'] ) ) {
					unset( $new_location['post_author'] );
				}

				// Grab geo date.
				$geo_date = null;
				if ( isset( $new_location['geo_date'] ) ) {
					$geo_date = $new_location['geo_date'];
					unset( $new_location['geo_date'] );
				}

				// Store location for new post.
				$success = GeoMashupDB::set_object_location( 'post', $new_id, $new_location, null, $geo_date );

				// Log the problem if there is one.
				if ( is_wp_error( $success ) ) {
					$e     = new Exception();
					$trace = $e->getTraceAsString();
					$log   = [
						'method'       => __METHOD__,
						'post_id'      => $post->ID,
						'new_id'       => $new_id,
						'location'     => $location,
						'new_location' => $new_location,
						'error'        => $success->get_error_message(),
						'backtrace'    => $trace,
					];
					$this->plugin->log_error( $log );
				}

			}

		}

		// Switch back.
		restore_current_blog();

		// Save correspondence if successful.
		if ( ! is_wp_error( $new_id ) ) {
			$this->save_meta( $post->ID, $this->post_meta_key_en, (string) $new_id );
		}

	}

	/**
	 * Utility that returns the timezone of the current site.
	 *
	 * Gets timezone settings from the database. If a timezone identifier is used,
	 * just turns it into a DateTimeZone. If an offset is used, it tries to find a
	 * suitable timezone. If all else fails it uses UTC.
	 *
	 * @since 0.2.1
	 *
	 * @return DateTimeZone The blog timezone.
	 */
	public function get_timezone() {

		// Have we cached this?
		$tzstring = wp_cache_get( 'sof_timezone' );

		// If not, then calculate and cache.
		if ( false === $tzstring ) {

			$tzstring = get_option( 'timezone_string' );
			$offset   = get_option( 'gmt_offset' );

			/*
			 * We should discourage manual offset.
			 *
			 * @see http://us.php.net/manual/en/timezones.others.php
			 * @see https://bugs.php.net/bug.php?id=45543
			 * @see https://bugs.php.net/bug.php?id=45528
			 *
			 * IANA timezone database that provides PHP's timezone support uses (i.e. reversed) POSIX style signs.
			 */
			// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			if ( empty( $tzstring ) && 0 != $offset && floor( $offset ) == $offset ) {
				$offset_st = $offset > 0 ? "-$offset" : '+' . absint( $offset );
				$tzstring  = 'Etc/GMT' . $offset_st;
			}

			// Issue with the timezone selected, set to 'UTC'.
			if ( empty( $tzstring ) ) {
				$tzstring = 'UTC';
			}

			// Cache timezone string not timezone object.
			wp_cache_set( 'sof_timezone', $tzstring );

		}

		// Bail early if already a DTZ object.
		if ( $tzstring instanceof DateTimeZone ) {
			return $tzstring;
		}

		// Create DTZ object.
		$timezone = new DateTimeZone( $tzstring );

		// --<
		return $timezone;

	}

	/**
	 * Utility to simplify metadata saving.
	 *
	 * @since 0.2.1
	 *
	 * @param int    $post_id The numeric ID of the WordPress post.
	 * @param string $key The meta key.
	 * @param mixed  $data The data to be saved.
	 * @return mixed $data The data that was saved.
	 */
	public function save_meta( $post_id, $key, $data = '' ) {

		// Update if the field already has a value, otherwise add.
		$existing = get_post_meta( $post_id, $key, true );
		if ( false !== $existing ) {
			update_post_meta( $post_id, $key, $data );
		} else {
			add_post_meta( $post_id, $key, $data, true );
		}

		// --<
		return $data;

	}

}
