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
	 * @since 0.1
	 */
	public function register_hooks() {

		// Sort Sites by Network for all Users.
		add_filter( 'get_blogs_of_user', [ $this, 'sort_sites' ] );

		// Modify the WordPress admin bar on all Sites.
		add_action( 'wp_before_admin_bar_render', [ $this, 'wp_modify_menu' ], 1000 );

		// Remove Multi-network Menu from admin bar for everyone.
		add_action( 'wp_before_admin_bar_render', [ $this, 'wpmn_remove_menu' ], 2000 );

		// Always remove Site Icons. Prevents 404 for SOF Brasil.
		add_filter( 'wp_admin_bar_show_site_icons', '__return_false' );

		// Maybe register SOF CIC hooks.
		$this->sofcic_register_hooks();

		// Maybe register SOF eV hooks.
		$this->sofev_register_hooks();

		// Maybe register SOF Brasil hooks.
		$this->sofbr_register_hooks();

		/*
		// Maybe register The Ball hooks.
		$this->theball_register_hooks();
		*/

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Sorts the array of Sites by Network for all Users.
	 *
	 * @since 0.4
	 *
	 * @param array $sites The array of Sites by Network.
	 * @return array $sites The modified array of Sites by Network.
	 */
	public function sort_sites( $sites ) {

		// Do initial sort.
		uasort( $sites, [ $this, 'sort_by_network' ] );

		// Return early if we have no Networks.
		$networks = get_networks();
		if ( empty( $networks ) ) {
			return $sites;
		}

		// Collect Main Sites so we can exclude Sub-sites below.
		$main_sites = [];
		foreach ( $networks as $network ) {
			$main_site_id = $network->site_id;
			if ( array_key_exists( $main_site_id, $sites ) ) {
				$main_sites[ $main_site_id ] = $sites[ $main_site_id ];
			}
		}

		// Group Sub-sites by Network.
		$grouped = [];
		foreach ( $sites as $site ) {
			if ( ! array_key_exists( $site->userblog_id, $main_sites ) ) {
				$grouped[ $site->site_id ][ $site->userblog_id ] = $site;
			}
		}

		// Sort Sub-sites by Site Title.
		foreach ( $grouped as $site_id => $group ) {
			uasort( $group, [ $this, 'sort_by_blogname' ] );
			$grouped[ $site_id ] = $group;
		}

		// Rebuild Sites array.
		$sites = [];
		foreach ( $networks as $network ) {

			// If we have a Main Site, add it.
			if ( array_key_exists( $network->site_id, $main_sites ) ) {
				$sites[ $network->site_id ] = $main_sites[ $network->site_id ];
			}

			// If we have a group of Sub-sites, add them.
			if ( array_key_exists( $network->id, $grouped ) ) {
				$sites += $grouped[ $network->id ];
			}

		}

		// --<
		return $sites;

	}

	/**
	 * Sorts the array of Sites by Network ID.
	 *
	 * @since 0.4
	 *
	 * @param object $a The reference array item.
	 * @param object $b The comparison array item.
	 * @return int -1, 0, or 1 if the first argument is considered less than, equal to, or greater than the second.
	 */
	public function sort_by_network( $a, $b ) {

		// Return early if equal.
		if ( (int) $a->site_id === (int) $b->site_id ) {
			return 0;
		}

		// Return comparison.
		return ( (int) $a->site_id < (int) $b->site_id ) ? -1 : 1;

	}

	/**
	 * Sorts the array of Sites by "blogname".
	 *
	 * @since 0.4
	 *
	 * @param object $a The reference array item.
	 * @param object $b The comparison array item.
	 * @return int -1, 0, or 1 if the first argument is considered less than, equal to, or greater than the second.
	 */
	public function sort_by_blogname( $a, $b ) {

		// Return early if equal.
		if ( $a->blogname === $b->blogname ) {
			return 0;
		}

		// Return comparison.
		return ( strtolower( $a->blogname ) < strtolower( $b->blogname ) ) ? -1 : 1;

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Modifies the WordPress admin bar on all Sites.
	 *
	 * @since 0.3.1
	 */
	public function wp_modify_menu() {

		// Access object.
		global $wp_admin_bar;

		// Bail if not logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Remove the WordPress logo menu.
		$wp_admin_bar->remove_menu( 'wp-logo' );

		/*
		$blog_names = [];
		$sites = $wp_admin_bar->user->blogs;
		foreach ( $sites as $site_id => $site ) {
			$blog_names[ $site_id ] = strtoupper( $site->blogname );
		}

		// Remove main blog from list - we want that to show at the top.
		unset( $blog_names[1] );

		// Order by name
		asort( $blog_names );

		// Create new array
		$wp_admin_bar->user->blogs = [];

		// Add main blog back in to list
		if ( $sites[1] ) {
			$wp_admin_bar->user->blogs[1] = $sites[1];
		}

		// Add others back in alphabetically
		foreach ( $blog_names as $site_id => $name ) {
			$wp_admin_bar->user->blogs[ $site_id ] = $sites[ $site_id ];
		}
		*/

	}

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

		// Bail if not logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Remove the WordPress Multi-network menu.
		$wp_admin_bar->remove_menu( 'my-networks' );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Register WordPress hooks on the SOF CIC network.
	 *
	 * @since 0.1
	 */
	public function sofcic_register_hooks() {

		// Include only on SOF CIC network.
		if ( 'sofcic' !== sof_get_site() ) {
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

		// Target BuddyPress dropdown parent.
		$args = [
			'id'   => 'my-account',
			'href' => trailingslashit( bp_loggedin_user_domain() ),
		];
		$wp_admin_bar->add_node( $args );

		// Target BuddyPress dropdown user info.
		$args = [
			'id'   => 'user-info',
			'href' => trailingslashit( bp_loggedin_user_domain() ),
		];
		$wp_admin_bar->add_node( $args );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Register WordPress hooks on the SOF eV network.
	 *
	 * @since 0.1
	 */
	public function sofev_register_hooks() {

		// Include only on SOF eV network.
		if ( sof_get_site() !== 'sofev' ) {
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

		// BuddyPress-only modifications.
		if ( function_exists( 'bp_loggedin_user_domain' ) ) {

			// Target BuddyPress dropdown parent.
			$args = [
				'id'   => 'my-account',
				'href' => trailingslashit( bp_loggedin_user_domain() ),
			];
			$wp_admin_bar->add_node( $args );

			// Target BuddyPress dropdown user info.
			$args = [
				'id'   => 'user-info',
				'href' => trailingslashit( bp_loggedin_user_domain() ),
			];
			$wp_admin_bar->add_node( $args );

		}

		// Target anyone less than "editor".
		if ( ! current_user_can( 'edit_posts' ) ) {

			// Avoid links to WordPress admin on main site.
			if ( is_main_site() ) {

				// Target "My Sites".
				$args = [
					'id'   => 'my-sites',
					'href' => esc_url( home_url( '/' ) ),
				];
				$wp_admin_bar->add_node( $args );

				// Target "Main Site Name".
				$args = [
					'id'   => 'blog-1',
					'href' => esc_url( home_url( '/' ) ),
				];
				$wp_admin_bar->add_node( $args );

				// Remove "Dashboard" from "My Sites -> Main Site".
				$wp_admin_bar->remove_node( 'blog-12-d' );

				// Target "Site Name".
				$args = [
					'id'   => 'site-name',
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

	// -----------------------------------------------------------------------------------

	/**
	 * Register WordPress hooks on the SOF Brasil network.
	 *
	 * @since 0.1
	 */
	public function sofbr_register_hooks() {

		// Include only on SOF Brasil network.
		if ( 'sofbr' !== sof_get_site() ) {
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
			'id'   => 'my-account',
			'href' => trailingslashit( bp_loggedin_user_domain() ),
		];
		$wp_admin_bar->add_node( $args );

		// Target BuddyPress dropdown user info.
		$args = [
			'id'   => 'user-info',
			'href' => trailingslashit( bp_loggedin_user_domain() ),
		];
		$wp_admin_bar->add_node( $args );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Register WordPress hooks on the "The Ball" network.
	 *
	 * @since 0.3.1
	 */
	public function theball_register_hooks() {

		// Amend the WordPress admin bar.
		add_action( 'wp_before_admin_bar_render', [ $this, 'theball_admin_bar_tweaks' ], 1000 );

		// Include only on The Ball network.
		if ( 'theball' !== sof_get_site() ) {
			return;
		}

		// Get the current site.
		$current_site = get_site();
		if ( empty( $current_site ) ) {
			return;
		}

		// Bail if not the 2022 path.
		if ( '/2022/' !== $current_site->path ) {
			return;
		}

	}

	/**
	 * Tweak the WordPress admin bar.
	 *
	 * @since 0.2.1
	 */
	public function theball_admin_bar_tweaks() {

		// Access object.
		global $wp_admin_bar;

		// Bail if not logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Removes an item of a given type and URL from an array of menu items.
	 *
	 * @since 0.1
	 *
	 * @param array $sorted_menu_items The menu items, sorted by each menu item's menu order.
	 * @param str   $type The type of menu item we're looking for.
	 * @param array $url_snippet The slug we're looking for in the menu item's target URL.
	 */
	private function remove_item( &$sorted_menu_items, $type, $url_snippet ) {

		// Loop through them and get the menu item's key.
		foreach ( $sorted_menu_items as $key => $item ) {

			// Store found key if it's the item we're looking for.
			if ( $item->type === $type && false !== strpos( $item->url, $url_snippet ) ) {
				$found = $key;
				break;
			}

		}

		// Remove item if we found it.
		if ( isset( $found ) ) {
			unset( $sorted_menu_items[ $found ] );
		}

	}

}
