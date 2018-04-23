<?php

/**
 * SOF Mirror Content Class.
 *
 * A class that encapsulates SOF-specific Content Mirroring functionality. What
 * happens is that when a post is created in the "Daily Ballblog" (which is a
 * term in the 'category' taxonomy), the content is mirrored to The Ball 2018
 * site for translation.
 *
 * The
 *
 * @since 0.2.1
 *
 * @package WordPress
 * @subpackage SOF
 */
class Spirit_Of_Football_Mirror {

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

		// on The Ball 2018
		if ( $this->site_id_en == get_current_blog_id() ) {

			// add meta boxes
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes_theball' ) );

			// add link to content
			add_filter( 'the_content', array( $this, 'prepend_link' ), 50, 3 );

			// bail now
			return;

		}

		// only proceed on SOF eV
		if ( $this->site_id_de != get_current_blog_id() ) return;

		// add meta boxes
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes_sofev' ) );

		// intercept save event
		add_action( 'save_post', array( $this, 'save_post' ), 50, 3 );

		// add link to content
		add_filter( 'the_content', array( $this, 'prepend_link' ), 50, 3 );

	}



	// #########################################################################



	/**
	 * Prepends links to page/post content.
	 *
	 * @since 0.2.1
	 *
	 * @param str $content The content of the page/post.
	 * @return str $content The modified content.
	 */
	public function prepend_link( $content ) {

		// reference our post
		global $post;

		// skip if we're doing the 'get_the_excerpt' filter
		if ( doing_action( 'get_the_excerpt' ) ) return $content;

		// if on German site
		if ( $this->site_id_de == get_current_blog_id() ) {
			$link = $this->get_link_en( $post->ID );
			if ( ! empty( $link ) ) {
				$prepend = '<div class="sof_mirror">' . $link . '</div>';
				$content = $prepend . $content;
			}
		}

		// if on English site
		if ( $this->site_id_en == get_current_blog_id() ) {
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

		// bail if not on German site
		if ( $this->site_id_de != get_current_blog_id() ) return;

		// have we got a mirrored post?
		$english_id = get_post_meta( $post_id, $this->post_meta_key_en, true );

		// bail if there isn't one
		if ( empty( $english_id ) ) return;

		// switch to English site
		switch_to_blog( $this->site_id_en );

		// init link
		$link = '';

		// check that the post is published
		if ( 'publish' == get_post_status( $english_id ) ) {

			// construct link
			$link = '<a href="' . get_permalink( $english_id ) . '" class="button sof_english_post">' .
						__( 'Read this post in English', 'sof-utilities' ) .
					'</a>';

		}

		// switch back
		restore_current_blog();

		/*
		$e = new Exception;
		$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'post_id' => $post_id,
			'english_id' => $english_id,
			'link' => $link,
			'backtrace' => $trace,
		), true ) );
		*/

		// show link
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

		// bail if not on English site
		if ( $this->site_id_en != get_current_blog_id() ) return false;

		// have we got a mirrored post?
		$german_id = get_post_meta( $post_id, $this->post_meta_key_de, true );

		// bail if there isn't one
		if ( empty( $german_id ) ) return false;

		// switch to German site
		switch_to_blog( $this->site_id_de );

		// init link
		$link = '';

		// check that the post is published
		if ( 'publish' == get_post_status( $german_id ) ) {

			// construct link
			$link = '<a href="' . get_permalink( $german_id ) . '" class="button sof_german_post">' .
						__( 'Read this post in German', 'sof-utilities' ) .
					'</a>';

		}

		// switch back
		restore_current_blog();

		/*
		$e = new Exception;
		$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'post_id' => $post_id,
			'german_id' => $german_id,
			'link' => $link,
			'backtrace' => $trace,
		), true ) );
		*/

		// --<
		return $link;

	}



	/**
	 * Adds meta boxes to admin screens on The Ball 2018.
	 *
	 * @since 0.2.1
	 */
	public function add_meta_boxes_theball() {

		// add our meta box
		add_meta_box(
			'theball2018_post_options',
			__( 'German version', 'sof-utilities' ),
			array( $this, 'german_version_box' ),
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

		// access post
		global $post;

		// use nonce for verification
		wp_nonce_field( 'theball2018_post_settings', 'theball2018_nonce' );

		// have we got a mirrored post?
		$existing_id = get_post_meta( $post->ID, $this->post_meta_key_de, true );

		// bail if there isn't one
		if ( empty( $existing_id ) ) return;

		// -----------------------------------------------------------------
		// Show link to German post
		// -----------------------------------------------------------------

		// helpful text
		echo '<p>' . __( 'This post has a German version.', 'sof-utilities' ) . '</p>' . "\n";

		// switch to SOF eV
		switch_to_blog( $this->site_id_de );

		// get the edit post link
		$edit_link = get_edit_post_link( $existing_id );

		// switch back
		restore_current_blog();

		// define label
		$link = __( 'Edit German version', 'sof-utilities' );

		// show link
		echo '<p><a href="' . $edit_link . '">' . $link . '</a></p>' . "\n";

	}



	// #########################################################################



	/**
	 * Adds meta boxes to admin screens on SOF eV.
	 *
	 * @since 0.2.1
	 */
	public function add_meta_boxes_sofev() {

		// add our meta box
		add_meta_box(
			'sofev2018_post_options',
			__( 'English version', 'sof-utilities' ),
			array( $this, 'english_version_box' ),
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

		// access post
		global $post;

		// use nonce for verification
		wp_nonce_field( 'sofev2018_post_settings', 'sofev2018_nonce' );

		// have we got a mirrored post?
		$existing_id = get_post_meta( $post->ID, $this->post_meta_key_en, true );

		// if there is one
		if ( absint( $existing_id ) > 0 ) {

			// -----------------------------------------------------------------
			// Show link to English post
			// -----------------------------------------------------------------

			// helpful text
			echo '<p>' . __( 'This post has an English version.', 'sof-utilities' ) . '</p>' . "\n";

			// switch to The Ball 2018
			switch_to_blog( $this->site_id_en );

			// get the edit post link
			$edit_link = get_edit_post_link( $existing_id );

			/*
			$e = new Exception;
			$trace = $e->getTraceAsString();
			error_log( print_r( array(
				'method' => __METHOD__,
				'post' => $post,
				'existing_id' => $existing_id,
				'edit_link' => $edit_link,
				'backtrace' => $trace,
			), true ) );
			*/

			// switch back
			restore_current_blog();

			// define label
			$link = __( 'Edit English version', 'sof-utilities' );

			// show link
			echo '<p><a href="' . $edit_link . '">' . $link . '</a></p>' . "\n";

		} else {

			// bail if the post does not have the relevant term
			if ( ! has_term( $this->term_id, 'category', $post->ID ) ) {

				// helpful text
				echo '<p>' . __( 'You will be able to create an English version of this post on The Ball 2018 when you have added it to the "Daily Ballblog" category. You do not have to publish this post first - it can still be in draft mode.', 'sof-utilities' ) . '</p>' . "\n";

			} else {

				// -----------------------------------------------------------------
				// Create English post with content of current post
				// -----------------------------------------------------------------

				// helpful text
				echo '<p>' . __( 'You can now create a copy of this post on The Ball 2018. When you have done so, you can translate it into English. It is best to make a copy when this post is finished and published.', 'sof-utilities' ) . '</p>' . "\n";

				// define label
				$label = __( 'Create an English version', 'sof-utilities' );

				// show a title
				echo '
				<div class="checkbox">
					<label for="sofev2018_create_en"><input type="checkbox" value="1" id="sofev2018_create_en" name="sofev2018_create_en" /> ' .
						$label .
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
	 * @param int $post_id The numeric ID of the WP post.
	 * @param object $post The WP post object.
	 * @param bool $update True if the post is being updated, false otherwise.
	 */
	public function save_post( $post_id, $post, $update ) {

		// bail if not a valid post
		if ( ! $post instanceOf WP_Post ) return;

		// bail if not a post
		if ( $post->post_type != 'post' ) return;

		// bail if this is an auto save routine
		if ( defined( 'DOING_AUTOSAVE' ) AND DOING_AUTOSAVE ) return;

		// check permissions
		if ( ! current_user_can( 'edit_posts', $post->ID ) ) return;

		// if this is a revision, get the real post ID and post object
		if ( $parent_id = wp_is_post_revision( $post_id ) ) {
			$post_id = $parent_id;
			$post = get_post( $post_id );
		}

		// authenticate
		$nonce = isset( $_POST['sofev2018_nonce'] ) ? $_POST['sofev2018_nonce'] : '';
		if ( ! wp_verify_nonce( $nonce, 'sofev2018_post_settings' ) ) return;

		// bail if our checkbox has not been checked
		$active = isset( $_POST['sofev2018_create_en'] ) ? absint( $_POST['sofev2018_create_en'] ) : '0';
		if ( $active !== 1 ) return;

		// bail if the post does not have the relevant term
		if ( ! has_term( $this->term_id, 'category', $post_id ) ) return;

		/*
		$e = new Exception;
		$trace = $e->getTraceAsString();
		error_log( print_r( array(
			'method' => __METHOD__,
			'post_id' => $post_id,
			'post' => $post,
			'update' => $update,
			'has-term' => $has_term ? 'y' : 'n',
			'backtrace' => $trace,
		), true ) );
		*/

		// mirror content
		$this->mirror_post( $post, $update );

	}



	/**
	 * Mirror the post to a corresponding post on The Ball 2018 site.
	 *
	 * @since 0.2.1
	 *
	 * @param object $post The WP post object.
	 * @param bool $update True if the post is being updated, false otherwise.
	 */
	public function mirror_post( $post, $update ) {

		// have we already mirrored this post?
		$existing_id = get_post_meta( $post->ID, $this->post_meta_key_en, true );

		// bail if we have (for now)
		if ( absint( $existing_id ) > 0 ) return;

		// parse gallery shortcodes and add pointer to SOF eV
		$content = str_replace(
			'[gallery ',
			'[gallery sof_site_id="' . $this->site_id_de . '" ',
			$post->post_content
		);

		// copy relevant data to fresh array
		$new_post = array(
			'post_type' => $post->post_type,
			'post_status' => 'draft',
			'post_date' => $post->post_date,
			'post_date_gmt' => $post->post_date_gmt,
			'post_title' => $post->post_title,
			'post_author' => $post->post_author,
			'post_content' => $content,
			'post_excerpt' => $post->post_excerpt,
			'comment_status' => $post->comment_status,
			'ping_status' => $post->ping_status,
			'to_ping' => '', // quick fix for Windows
			'pinged' => '', // quick fix for Windows
			'post_content_filtered' => '', // quick fix for Windows
			'post_parent' => 0,
			'menu_order' => 0,
		);


		// if Geo Mashup is active
		if ( class_exists( 'GeoMashupDB' ) ) {

			// get location
			$location = GeoMashupDB::get_post_location( $post->ID );

			/*
			$e = new Exception;
			$trace = $e->getTraceAsString();
			error_log( print_r( array(
				'method' => __METHOD__,
				'post_id' => $post->ID,
				'location' => $location,
				'backtrace' => $trace,
			), true ) );
			*/

		}

		// switch to The Ball 2018
		switch_to_blog( $this->site_id_en );

		// insert post in target site
		$new_id = wp_insert_post( $new_post );

		// if successful
		if ( ! is_wp_error( $new_id ) ) {

			// save reverse correspondence
			$this->save_meta( $new_id, $this->post_meta_key_de, (string) $post->ID );

			// if Geo Mashup is active
			if ( class_exists( 'GeoMashupDB' ) ) {

				// convert new location to array
				$new_location = (array) $location;

				// unset redundant data to create new location
				unset( $new_location['id'] );
				unset( $new_location['object_id'] );
				unset( $new_location['label'] );
				unset( $new_location['post_author'] );

				// grab geo date
				$geo_date = $new_location['geo_date'];
				unset( $new_location['geo_date'] );

				// store location for new post
				$success = GeoMashupDB::set_object_location( 'post', $new_id, $new_location, null, $geo_date );

				// log the problem if there is one
				if ( is_wp_error( $success ) ) {
					$e = new Exception;
					$trace = $e->getTraceAsString();
					error_log( print_r( array(
						'method' => __METHOD__,
						'post_id' => $post->ID,
						'new_id' => $new_id,
						'location' => $location,
						'new_location' => $new_location,
						'error' => $success->get_error_message(),
						'backtrace' => $trace,
					), true ) );
				}

			}

		}

		// switch back
		restore_current_blog();

		// save correspondence if successful
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

		// have we cached this?
		$tzstring = wp_cache_get( 'sof_timezone' );

		// if not, then calculate and cache
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
			 * IANA timezone database that provides PHP's timezone support uses (i.e. reversed) POSIX style signs
			 */
			if ( empty( $tzstring ) && 0 != $offset && floor( $offset ) == $offset ) {
				$offset_st = $offset > 0 ? "-$offset" : '+' . absint( $offset );
				$tzstring  = 'Etc/GMT' . $offset_st;
			}

			// issue with the timezone selected, set to 'UTC'
			if( empty( $tzstring ) ){
				$tzstring = 'UTC';
			}

			// cache timezone string not timezone object
			wp_cache_set( 'sof_timezone', $tzstring );

		}

		// bail early if already a DTZ object
		if ( $tzstring instanceOf DateTimeZone ) return $tzstring;

		// create DTZ object
		$timezone = new DateTimeZone( $tzstring );

		// --<
		return $timezone;

	}



	/**
	 * Utility to simplify metadata saving.
	 *
	 * @since 0.2.1
	 *
	 * @param int $post_id The numeric ID of the WordPress post.
	 * @param string $key The meta key.
	 * @param mixed $data The data to be saved.
	 * @return mixed $data The data that was saved.
	 */
	public function save_meta( $post_id, $key, $data = '' ) {

		// update if the field already has a value, otherwise add
		$existing = get_post_meta( $post_id, $key, true );
		if ( false !== $existing ) {
			update_post_meta( $post_id, $key, $data );
		} else {
			add_post_meta( $post_id, $key, $data, true );
		}

		// --<
		return $data;

	}



} // class Spirit_Of_Football_Mirror ends



