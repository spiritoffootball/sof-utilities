<?php

/**
 * SOF Menus Class.
 *
 * A class that encapsulates SOF-specific Menu manipulation.
 *
 * @since 0.1
 *
 * @package WordPress
 * @subpackage SOF
 */
class Spirit_Of_Football_Menus {



	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// Nothing.

	}



	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// Remove Multi-network Menu from admin bar for everyone.
		add_action( 'wp_before_admin_bar_render', array( $this, 'wpmn_remove_menu' ), 2000 );

		// Maybe register SOF CIC hooks.
		$this->sofcic_register_hooks();

		// Maybe register SOF eV hooks.
		$this->sofev_register_hooks();

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
		add_action( 'wp_nav_menu_objects', array( $this, 'sofcic_filter_menu' ), 20, 2 );

		/*
		 * Amends the BuddyPress dropdown in the WordPress admin bar.
		 *
		 * The top-level items point to "Profile -> Edit" by default, but this
		 * seems kind of unintuitive, so point them to Member Home instead.
		 */
		add_action( 'wp_before_admin_bar_render', array( $this, 'sofcic_admin_bar_tweaks' ), 1000 );

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
		add_action( 'wp_nav_menu_objects', array( $this, 'sofev_filter_menu' ), 20, 2 );

		/*
		 * Amends the BuddyPress dropdown in the WordPress admin bar.
		 *
		 * The top-level items point to "Profile -> Edit" by default, but this
		 * seems kind of unintuitive, so point them to Member Home instead.
		 */
		add_action( 'wp_before_admin_bar_render', array( $this, 'sofev_admin_bar_tweaks' ), 1000 );

	}



	// #########################################################################



	/**
	 * Remove Multi-network admin bar.
	 *
	 * @since 0.3
	 */
	public function wpmn_remove_menu() {

		// Bail if plugin not present.
		if ( ! function_exists( 'wpmn' ) ) return;

		// Access menu object.
		global $wp_admin_bar;

		// Remove the WordPress Multi-network menu.
		$wp_admin_bar->remove_menu( 'my-networks' );

	}



	// #########################################################################



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

		// --<
		return $sorted_menu_items;

	}



	/**
	 * Tweak the BuddyPress dropdown in the WordPress admin bar.
	 *
	 * @since 0.3
	 */
	public function sofcic_admin_bar_tweaks() {

	}



	// #########################################################################



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
		if( is_admin() ) return $sorted_menu_items;

		// Only on main blog.
		if( ! is_main_site() ) return $sorted_menu_items;

		// Allow network admins.
		if ( is_super_admin() ) return $sorted_menu_items;

		// Allow members.
		//if ( current_user_can_for_blog( bp_get_root_blog_id(), 'restrict_content' ) ) return $sorted_menu_items;

		// Allow logged-in folks.
		if ( is_user_logged_in() ) return $sorted_menu_items;

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
		if ( ! is_user_logged_in() ) return;

		// Get user object.
		$user = wp_get_current_user();

		// Get member type.
		//$member_type = bp_get_member_type( $user->ID );

		// Remove the WordPress logo menu.
		$wp_admin_bar->remove_menu( 'wp-logo' );

		// Target BuddyPress dropdown parent.
		$args = array(
			'id' => 'my-account',
			'href' => trailingslashit( bp_loggedin_user_domain() ),
		);
		$wp_admin_bar->add_node( $args );

		// Target BuddyPress dropdown user info.
		$args = array(
			'id' => 'user-info',
			'href' => trailingslashit( bp_loggedin_user_domain() ),
		);
		$wp_admin_bar->add_node( $args );

		// Target community and network members.
		//if ( $member_type == 'community' OR $member_type == 'network' ) {
		if ( ! current_user_can( 'edit_posts' ) ) {

			// Avoid links to WordPress admin on main site.
			if ( is_main_site() ) {

				///*
				// Target "My Sites".
				$args = array(
					'id' => 'my-sites',
					'href' => esc_url( home_url( '/' ) ),
				);
				$wp_admin_bar->add_node( $args );

				// Target "Main Site Name".
				$args = array(
					'id' => 'blog-1',
					'href' => esc_url( home_url( '/' ) ),
				);
				$wp_admin_bar->add_node( $args );

				// Remove "Dashboard" from "My Sites -> Main Site".
				$wp_admin_bar->remove_node( 'blog-12-d' );

				// Target "Site Name".
				$args = array(
					'id' => 'site-name',
					'href' => esc_url( home_url( '/' ) ),
				);
				$wp_admin_bar->add_node( $args );
				//*/

				///*
				// Remove temporarily.
				$wp_admin_bar->remove_node( 'my-sites' );
				$wp_admin_bar->remove_node( 'blog-1' );
				$wp_admin_bar->remove_node( 'blog-1-d' );
				$wp_admin_bar->remove_node( 'site-name' );
				$wp_admin_bar->remove_node( 'new-content' );
				$wp_admin_bar->remove_node( 'new-media' );
				//*/

				// Remove "Dashboard".
				$wp_admin_bar->remove_node( 'dashboard' );

			}

		}

	}



	// #########################################################################



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
		foreach( $sorted_menu_items AS $key => $item ) {

			// Is it the item we're looking for?
			if ( $item->type == $type AND false !== strpos( $item->url, $url_snippet ) ) {

				// Store found key.
				$found = $key;
				break;

			}

		}

		// Remove it if we find it.
		if ( isset( $found ) ) {
			unset( $sorted_menu_items[$found] );
		}

	}



} // Class ends.



