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

		// nothing

	}



	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// include only on SOF eV for now...
		if ( 'sofev' != sof_get_site() ) return;

		// filter menu based on membership
		add_action( 'wp_nav_menu_objects', array( $this, 'filter_menu' ), 20, 2 );

		/*
		 * Amends the BuddyPress dropdown in the WordPress admin bar. The top-level
		 * items point to "Profile -> Edit" by default, but this seems kind of
		 * unintuitive, so point them to Member Home instead.
		 */
		add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar_tweaks' ), 1000 );

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
	public function filter_menu( $sorted_menu_items, $args ) {

		// only on front end
		if( is_admin() ) return $sorted_menu_items;

		// only on main blog
		if( ! is_main_site() ) return $sorted_menu_items;

		// allow network admins
		if ( is_super_admin() ) return $sorted_menu_items;

		// allow members
		//if ( current_user_can_for_blog( bp_get_root_blog_id(), 'restrict_content' ) ) return $sorted_menu_items;

		// allow logged-in folks
		if ( is_user_logged_in() ) return $sorted_menu_items;

		// remove items from array
		$this->remove_item( $sorted_menu_items, 'post_type', '/gruppen/' );
		$this->remove_item( $sorted_menu_items, 'post_type', '/gruppen/' );
		$this->remove_item( $sorted_menu_items, 'post_type', '/mitglieder/' );
		$this->remove_item( $sorted_menu_items, 'post_type', '/activitaet/' );

		// --<
		return $sorted_menu_items;

	}



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

		// loop through them and get the menu item's key
		foreach( $sorted_menu_items AS $key => $item ) {

			// is it the item we're looking for?
			if ( $item->type == $type AND false !== strpos( $item->url, $url_snippet ) ) {

				// store found key
				$found = $key;
				break;

			}

		}

		// remove it if we find it
		if ( isset( $found ) ) {
			unset( $sorted_menu_items[$found] );
		}

	}



	// #########################################################################



	/**
	 * Tweak the BuddyPress dropdown in the WordPress admin bar.
	 *
	 * @since 0.2.1
	 */
	public function admin_bar_tweaks() {

		// access object
		global $wp_admin_bar;

		// bail if not logged in
		if ( ! is_user_logged_in() ) return;

		// get user object
		$user = wp_get_current_user();

		// get member type
		//$member_type = bp_get_member_type( $user->ID );

		// remove the WordPress logo menu
		$wp_admin_bar->remove_menu( 'wp-logo' );

		// target BuddyPress dropdown parent
		$args = array(
			'id' => 'my-account',
			'href' => trailingslashit( bp_loggedin_user_domain() ),
		);
		$wp_admin_bar->add_node( $args );

		// target BuddyPress dropdown user info
		$args = array(
			'id' => 'user-info',
			'href' => trailingslashit( bp_loggedin_user_domain() ),
		);
		$wp_admin_bar->add_node( $args );

		// target community and network members
		//if ( $member_type == 'community' OR $member_type == 'network' ) {
		if ( ! current_user_can( 'edit_posts' ) ) {

			// avoid links to WordPress admin on main site
			if ( is_main_site() ) {

				///*
				// target "My Sites"
				$args = array(
					'id' => 'my-sites',
					'href' => esc_url( home_url( '/' ) ),
				);
				$wp_admin_bar->add_node( $args );

				// target "Main Site Name"
				$args = array(
					'id' => 'blog-1',
					'href' => esc_url( home_url( '/' ) ),
				);
				$wp_admin_bar->add_node( $args );

				// remove "Dashboard" from "My Sites -> Main Site"
				$wp_admin_bar->remove_node( 'blog-12-d' );

				// target "Site Name"
				$args = array(
					'id' => 'site-name',
					'href' => esc_url( home_url( '/' ) ),
				);
				$wp_admin_bar->add_node( $args );
				//*/

				///*
				// remove temporarily
				$wp_admin_bar->remove_node( 'my-sites' );
				$wp_admin_bar->remove_node( 'blog-1' );
				$wp_admin_bar->remove_node( 'blog-1-d' );
				$wp_admin_bar->remove_node( 'site-name' );
				$wp_admin_bar->remove_node( 'new-content' );
				$wp_admin_bar->remove_node( 'new-media' );
				//*/

				// remove "Dashboard"
				$wp_admin_bar->remove_node( 'dashboard' );

			}

		}

	}



} // class Spirit_Of_Football_Menus ends



