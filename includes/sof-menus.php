<?php
/**
 * Menus Class.
 *
 * Handles SOF-specific Menu modifications.
 *
 * @since 0.1
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * SOF Menus Class.
 *
 * A class that encapsulates SOF-specific Menu manipulation.
 *
 * @since 0.1
 */
class Spirit_Of_Football_Menus {

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

		// Remove Multi-network Menu from admin bar for everyone.
		add_action( 'wp_before_admin_bar_render', [ $this, 'wpmn_remove_menu' ], 2000 );

		// Maybe register SOF CIC hooks.
		$this->sofcic_register_hooks();

		// Maybe register SOF eV hooks.
		$this->sofev_register_hooks();

		// Maybe register SOF Brasil hooks.
		$this->sofbr_register_hooks();

	}

	/**
	 * Register WordPress hooks on the SOF CIC site.
	 *
	 * @since 0.1
	 */
	public function sofcic_register_hooks() {

		// Include only on SOF CIC.
		if ( 'sofcic' != sof_get_site() ) {
			return;
		}

		// Filter menu based on membership.
		add_action( 'wp_nav_menu_objects', [ $this, 'sofcic_filter_menu' ], 20, 2 );

		/*
		 * Amends the BuddyPress dropdown in the WordPress admin bar.
		 *
		 * The top-level items point to "Profile -> Edit" by default, but this
		 * seems kind of unintuitive, so point them to Member Home instead.
		 */
		add_action( 'wp_before_admin_bar_render', [ $this, 'sofcic_admin_bar_tweaks' ], 1000 );

	}

	/**
	 * Register WordPress hooks on the SOF eV site.
	 *
	 * @since 0.1
	 */
	public function sofev_register_hooks() {

		// Include only on SOF eV.
		if ( 'sofev' != sof_get_site() ) {
			return;
		}

		// Filter menu based on membership.
		add_action( 'wp_nav_menu_objects', [ $this, 'sofev_filter_menu' ], 20, 2 );

		/*
		 * Amends the BuddyPress dropdown in the WordPress admin bar.
		 *
		 * The top-level items point to "Profile -> Edit" by default, but this
		 * seems kind of unintuitive, so point them to Member Home instead.
		 */
		add_action( 'wp_before_admin_bar_render', [ $this, 'sofev_admin_bar_tweaks' ], 1000 );

	}

	/**
	 * Register WordPress hooks on the SOF Brasil site.
	 *
	 * @since 0.1
	 */
	public function sofbr_register_hooks() {

		// Include only on SOF Brasil.
		if ( 'sofbr' != sof_get_site() ) {
			return;
		}

		// Filter menu based on membership.
		add_action( 'wp_nav_menu_objects', [ $this, 'sofbr_filter_menu' ], 20, 2 );

		/*
		 * Amends the BuddyPress dropdown in the WordPress admin bar.
		 *
		 * The top-level items point to "Profile -> Edit" by default, but this
		 * seems kind of unintuitive, so point them to Member Home instead.
		 */
		add_action( 'wp_before_admin_bar_render', [ $this, 'sofbr_admin_bar_tweaks' ], 1000 );

	}

	// -------------------------------------------------------------------------

	/**
	 * Remove Multi-network admin bar.
	 *
	 * @since 0.3
	 */
	public function wpmn_remove_menu() {

		// Bail if plugin not present.
		if ( ! function_exists( 'wpmn' ) ) {
			return;
		}

		// Access menu object.
		global $wp_admin_bar;

		// Remove the WordPress Multi-network menu.
		$wp_admin_bar->remove_menu( 'my-networks' );

	}

	// -------------------------------------------------------------------------

	/**
	 * Filter the main menu on the SOF CIC root site.
	 *
	 * @since 0.3
	 *
	 * @param array $sorted_menu_items The menu items, sorted by each menu item's menu order.
	 * @param array $args Array of wp_nav_menu() arguments.
	 * @return $sorted_menu_items The filtered menu items.
	 */
	public function sofcic_filter_menu( $sorted_menu_items, $args ) {

		// Only on front end.
		if ( is_admin() ) {
			return $sorted_menu_items;
		}

		// Only on main blog.
		if ( ! is_main_site() ) {
			return $sorted_menu_items;
		}

		// Only on when CommentPress is enabled.
		if ( ! defined( 'COMMENTPRESS_SOF_DE_VERSION' ) ) {
			return $sorted_menu_items;
		}

		// Allow network admins.
		if ( is_super_admin() ) {
			return $sorted_menu_items;
		}

		/*
		// Allow members.
		if ( current_user_can_for_blog( bp_get_root_blog_id(), 'restrict_content' ) ) {
			return $sorted_menu_items;
		}
		*/

		// Allow logged-in folks.
		if ( is_user_logged_in() ) {
			return $sorted_menu_items;
		}

		// Remove items from array.
		$this->remove_item( $sorted_menu_items, 'post_type', '/activity/' );
		$this->remove_item( $sorted_menu_items, 'post_type', '/activity/' );
		$this->remove_item( $sorted_menu_items, 'post_type', '/members/' );
		$this->remove_item( $sorted_menu_items, 'post_type', '/groups/' );

		// --<
		return $sorted_menu_items;

	}

	/**
	 * Tweak the BuddyPress dropdown in the WordPress admin bar.
	 *
	 * @since 0.3
	 */
	public function sofcic_admin_bar_tweaks() {

		// Bail if no BuddyPress.
		if ( ! function_exists( 'bp_core_get_user_domain' ) ) {
			return;
		}

		// Access object.
		global $wp_admin_bar;

		// Bail if not logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Get user object.
		$user = wp_get_current_user();

		// Remove the WordPress logo menu.
		$wp_admin_bar->remove_menu( 'wp-logo' );

		// Target BuddyPress dropdown parent.
		$args = [
			'id' => 'my-account',
			'href' => trailingslashit( bp_loggedin_user_domain() ),
		];
		$wp_admin_bar->add_node( $args );

		// Target BuddyPress dropdown user info.
		$args = [
			'id' => 'user-info',
			'href' => trailingslashit( bp_loggedin_user_domain() ),
		];
		$wp_admin_bar->add_node( $args );

	}

	// -------------------------------------------------------------------------

	/**
	 * Filter the main menu on the root site.
	 *
	 * @since 0.1
	 *
	 * @param array $sorted_menu_items The menu items, sorted by each menu item's menu order.
	 * @param array $args Array of wp_nav_menu() arguments.
	 * @return $sorted_menu_items The filtered menu items.
	 */
	public function sofev_filter_menu( $sorted_menu_items, $args ) {

		// Only on front end.
		if ( is_admin() ) {
			return $sorted_menu_items;
		}

		// Only on main blog.
		if ( ! is_main_site() ) {
			return $sorted_menu_items;
		}

		// Allow network admins.
		if ( is_super_admin() ) {
			return $sorted_menu_items;
		}

		/*
		// Allow members.
		if ( current_user_can_for_blog( bp_get_root_blog_id(), 'restrict_content' ) ) {
			return $sorted_menu_items;
		}
		*/

		// Allow logged-in folks.
		if ( is_user_logged_in() ) {
			return $sorted_menu_items;
		}

		// Remove items from array.
		$this->remove_item( $sorted_menu_items, 'post_type', '/gruppen/' );
		$this->remove_item( $sorted_menu_items, 'post_type', '/gruppen/' );
		$this->remove_item( $sorted_menu_items, 'post_type', '/mitglieder/' );
		$this->remove_item( $sorted_menu_items, 'post_type', '/activitaet/' );

		// --<
		return $sorted_menu_items;

	}

	/**
	 * Tweak the BuddyPress dropdown in the WordPress admin bar.
	 *
	 * @since 0.2.1
	 */
	public function sofev_admin_bar_tweaks() {

		// Access object.
		global $wp_admin_bar;

		// Bail if not logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Get user object.
		$user = wp_get_current_user();

		// Remove the WordPress logo menu.
		$wp_admin_bar->remove_menu( 'wp-logo' );

		// Target BuddyPress dropdown parent.
		$args = [
			'id' => 'my-account',
			'href' => trailingslashit( bp_loggedin_user_domain() ),
		];
		$wp_admin_bar->add_node( $args );

		// Target BuddyPress dropdown user info.
		$args = [
			'id' => 'user-info',
			'href' => trailingslashit( bp_loggedin_user_domain() ),
		];
		$wp_admin_bar->add_node( $args );

		// Target "editors" and above.
		if ( ! current_user_can( 'edit_posts' ) ) {

			// Avoid links to WordPress admin on main site.
			if ( is_main_site() ) {

				// Target "My Sites".
				$args = [
					'id' => 'my-sites',
					'href' => esc_url( home_url( '/' ) ),
				];
				$wp_admin_bar->add_node( $args );

				// Target "Main Site Name".
				$args = [
					'id' => 'blog-1',
					'href' => esc_url( home_url( '/' ) ),
				];
				$wp_admin_bar->add_node( $args );

				// Remove "Dashboard" from "My Sites -> Main Site".
				$wp_admin_bar->remove_node( 'blog-12-d' );

				// Target "Site Name".
				$args = [
					'id' => 'site-name',
					'href' => esc_url( home_url( '/' ) ),
				];
				$wp_admin_bar->add_node( $args );

				// Remove temporarily.
				$wp_admin_bar->remove_node( 'my-sites' );
				$wp_admin_bar->remove_node( 'blog-1' );
				$wp_admin_bar->remove_node( 'blog-1-d' );
				$wp_admin_bar->remove_node( 'site-name' );
				$wp_admin_bar->remove_node( 'new-content' );
				$wp_admin_bar->remove_node( 'new-media' );

				// Remove "Dashboard".
				$wp_admin_bar->remove_node( 'dashboard' );

			}

		}

	}

	// -------------------------------------------------------------------------

	/**
	 * Filter the main menu on the SOF Brasil site.
	 *
	 * @since 0.3
	 *
	 * @param array $sorted_menu_items The menu items, sorted by each menu item's menu order.
	 * @param array $args Array of wp_nav_menu() arguments.
	 * @return $sorted_menu_items The filtered menu items.
	 */
	public function sofbr_filter_menu( $sorted_menu_items, $args ) {

		// Only on front end.
		if ( is_admin() ) {
			return $sorted_menu_items;
		}

		// Only on main blog.
		if ( ! is_main_site() ) {
			return $sorted_menu_items;
		}

		// Allow network admins.
		if ( is_super_admin() ) {
			return $sorted_menu_items;
		}

		/*
		// Allow members.
		if ( current_user_can_for_blog( bp_get_root_blog_id(), 'restrict_content' ) ) {
			return $sorted_menu_items;
		}
		*/

		// Allow logged-in folks.
		if ( is_user_logged_in() ) {
			return $sorted_menu_items;
		}

		// --<
		return $sorted_menu_items;

	}

	/**
	 * Tweak the BuddyPress dropdown in the WordPress admin bar.
	 *
	 * @since 0.3
	 */
	public function sofbr_admin_bar_tweaks() {

		// Access object.
		global $wp_admin_bar;

		// Bail if not logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Get user object.
		$user = wp_get_current_user();

		// Remove the WordPress logo menu.
		$wp_admin_bar->remove_menu( 'wp-logo' );

		// Remove the WordPress "My Sites" menu for all but network admins.
		if ( ! is_super_admin() ) {
			$wp_admin_bar->remove_menu( 'my-sites' );
		}

		// Bail if no BuddyPress.
		if ( ! function_exists( 'bp_core_get_user_domain' ) ) {
			return;
		}

		// Target BuddyPress dropdown parent.
		$args = [
			'id' => 'my-account',
			'href' => trailingslashit( bp_loggedin_user_domain() ),
		];
		$wp_admin_bar->add_node( $args );

		// Target BuddyPress dropdown user info.
		$args = [
			'id' => 'user-info',
			'href' => trailingslashit( bp_loggedin_user_domain() ),
		];
		$wp_admin_bar->add_node( $args );

	}

	// -------------------------------------------------------------------------

	/**
	 * Filter the main menu on the root site.
	 *
	 * @since 0.1
	 *
	 * @param array $sorted_menu_items The menu items, sorted by each menu item's menu order.
	 * @param str $type The type of menu item we're looking for.
	 * @param array $url_snippet The slug we're looking for in the menu item's target URL.
	 */
	private function remove_item( &$sorted_menu_items, $type, $url_snippet ) {

		// Loop through them and get the menu item's key.
		foreach ( $sorted_menu_items as $key => $item ) {

			// Is it the item we're looking for?
			if ( $item->type == $type && false !== strpos( $item->url, $url_snippet ) ) {

				// Store found key.
				$found = $key;
				break;

			}

		}

		// Remove it if we find it.
		if ( isset( $found ) ) {
			unset( $sorted_menu_items[ $found ] );
		}

	}

}
