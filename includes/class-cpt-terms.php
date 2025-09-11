<?php
/**
 * Terms Class.
 *
 * Handles functionality for Terms.
 *
 * @since 1.0.0
 *
 * @package Spirit_Of_Football_Utilities
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * SOF Terms Class.
 *
 * A class that encapsulates SOF-specific functionality for Terms.
 *
 * @since 1.0.0
 */
class Spirit_Of_Football_CPT_Terms {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Utilities
	 */
	public $plugin;

	/**
	 * CPT object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_CPTs
	 */
	public $cpts;

	/**
	 * Protected Terms in the "Individual Type" Custom Taxonomy.
	 *
	 * These are limited to the SOF eV site.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $slugs_ind_type_sofev = [
		'vorstand',
		'team',
		'staff',
		'alumni',
	];

	/**
	 * Protected Terms in the "Individual Type" Custom Taxonomy.
	 *
	 * These are limited to the "The Ball 2022" site.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $slugs_ind_type_ball2022 = [
		'advisory-board',
		'backroom-staff',
		'supporters',
		'the-squad',
	];

	/**
	 * Protected Terms in the "Organisation Type" Custom Taxonomy.
	 *
	 * These are limited to the SOF eV site.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $slugs_org_type_sofev = [
		'partner',
		'spirit-of-football',
		'foerderpartner',
	];

	/**
	 * Protected Terms in the "Organisation Tags" Custom Taxonomy.
	 *
	 * These are limited to the SOF eV site.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $slugs_org_tag_sofev = [
		'deutschland',
		'erfurt',
		'welt',
	];

	/**
	 * Protected Terms in the "Award Type" Custom Taxonomy.
	 *
	 * These are limited to the SOF eV site.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $slugs_award_type_sofev = [
		'featured-footer',
	];

	/**
	 * Protected Terms in the "Partner Type" Custom Taxonomy.
	 *
	 * These are limited to the "The Ball 2022" site.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $slugs_partner_type_ball2022 = [
		'powered-by',
	];

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Spirit_Of_Football_CPTs $plugin The plugin object.
	 */
	public function __construct( $parent ) {

		// Store references.
		$this->plugin = $parent->plugin;
		$this->cpts   = $parent;

		// Init when parent is loaded.
		add_action( 'sof_utilities/cpts/loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialise this object.
	 *
	 * @since 1.0.0
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Bootstrap class.
		$this->sofev_register_hooks();
		$this->theball_register_hooks();

		/**
		 * Fires when this class is loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_utilities/cpts/terms/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Registers hook callbacks on the SOF eV network.
	 *
	 * @since 1.0.0
	 */
	private function sofev_register_hooks() {

		// Include only on SOF eV network.
		if ( sof_get_site() !== 'sofev' ) {
			return;
		}

		// Add default Term to the "Individual Type" Custom Taxonomy.
		//add_filter( 'sof_people/cpt/individual/args', [ $this, 'sofev_term_default' ] );

		// Protect Terms in the "Individual Type" Custom Taxonomy.
		add_filter( 'user_has_cap', [ $this, 'sofev_term_caps' ], 10, 4 );

	}

	/**
	 * Registers hook callbacks on the "The Ball" network.
	 *
	 * @since 1.0.0
	 */
	private function theball_register_hooks() {

		// Bail if not on The Ball network.
		if ( sof_get_site() !== 'theball' ) {
			return;
		}

		// Add default Term to the "Individual Type" Custom Taxonomy.
		//add_filter( 'sof_people/cpt/individual/args', [ $this, 'theball_term_default' ] );

		// Protect Terms in the "Individual Type" Custom Taxonomy.
		add_filter( 'user_has_cap', [ $this, 'theball_term_caps' ], 10, 4 );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Protect Terms from being deleted on the SOF eV website.
	 *
	 * @since 1.0.0
	 *
	 * @param bool[]   $allcaps Array of key/value pairs where keys represent a capability name
	 *                          and boolean values represent whether the user has that capability.
	 * @param string[] $caps    Required primitive capabilities for the requested capability.
	 * @param array    $args {
	 *     Arguments that accompany the requested capability check.
	 *
	 *     @type string    $0 Requested capability.
	 *     @type int       $1 Concerned user ID.
	 *     @type mixed  ...$2 Optional second and further parameters, typically object ID.
	 * }
	 * @param WP_User  $user    The user object.
	 */
	public function sofev_term_caps( $allcaps, $caps, $args, $user ) {

		// Bail if not the "edit_term" or "delete_term" capability.
		if ( 'edit_term' !== $args[0] && 'delete_term' !== $args[0]  ) {
			return $allcaps;
		}

		// Get the Term object.
		$term = get_term( $args[2] );

		// Disable editing or deleting all Terms in the "Quote Type" Custom Taxonomy.
		if ( 'quote-type' === $term->taxonomy ) {
			$allcaps['manage_categories'] = false;
		}

		// Disable editing or deleting Terms in the "Award Type" Custom Taxonomy.
		if ( 'award-type' === $term->taxonomy ) {
			if ( in_array( $term->slug, $this->slugs_award_type_sofev ) ) {
				$allcaps['manage_categories'] = false;
			}
		}

		// Disable editing or deleting Terms in the "Organisation Type" Custom Taxonomy.
		if ( 'organisation-type' === $term->taxonomy ) {
			if ( in_array( $term->slug, $this->slugs_org_type_sofev ) ) {
				$allcaps['manage_categories'] = false;
			}
		}

		// Disable editing or deleting Terms in the "Organisation Tags" Custom Taxonomy.
		if ( 'organisation-tag' === $term->taxonomy ) {
			if ( in_array( $term->slug, $this->slugs_org_tag_sofev ) ) {
				$allcaps['manage_categories'] = false;
			}
		}

		// Disable editing or deleting Terms in the "Individual Type" Custom Taxonomy.
		if ( 'individual-type' === $term->taxonomy ) {
			if ( in_array( $term->slug, $this->slugs_ind_type_sofev ) ) {
				$allcaps['manage_categories'] = false;
			}
		}

		// --<
		return $allcaps;

	}

	/**
	 * Adds a default Term to the "Individual Type" Custom Taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The default taxonomy configuration arguments.
	 * @return array $args The modified taxonomy configuration arguments.
	 */
	public function sofev_term_default( $args ) {

		// Default Term setup.
		$args['default_term'] = [
			'name' => __( 'Supporters', 'sof-utilities' ),
			'slug' => 'supporter',
		];

		// --<
		return $args;

	}

	/**
	 * Protect Terms from being deleted in the "Individual Type" Custom Taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @param bool[]   $allcaps Array of key/value pairs where keys represent a capability name
	 *                          and boolean values represent whether the user has that capability.
	 * @param string[] $caps    Required primitive capabilities for the requested capability.
	 * @param array    $args {
	 *     Arguments that accompany the requested capability check.
	 *
	 *     @type string    $0 Requested capability.
	 *     @type int       $1 Concerned user ID.
	 *     @type mixed  ...$2 Optional second and further parameters, typically object ID.
	 * }
	 * @param WP_User  $user    The user object.
	 */
	public function theball_term_caps( $allcaps, $caps, $args, $user ) {

		// Bail if not the "edit_term" or "delete_term" capability.
		if ( 'edit_term' !== $args[0] && 'delete_term' !== $args[0]  ) {
			return $allcaps;
		}

		// Get the Term object.
		$term = get_term( $args[2] );

		// Disable editing or deleting all Terms in the "Quote Type" Custom Taxonomy.
		if ( 'quote-type' === $term->taxonomy ) {
			$allcaps['manage_categories'] = false;
		}

		// Disable editing or deleting Terms in the "Partner Type" Custom Taxonomy.
		if ( 'partner-type' === $term->taxonomy ) {
			if ( in_array( $term->slug, $this->slugs_partner_type_ball2022 ) ) {
				$allcaps['manage_categories'] = false;
			}
		}

		// Disable editing or deleting Terms in the "Individual Type" Custom Taxonomy.
		if ( 'individual-type' === $term->taxonomy ) {
			if ( in_array( $term->slug, $this->slugs_ind_type_ball2022 ) ) {
				$allcaps['manage_categories'] = false;
			}
		}

		// --<
		return $allcaps;

	}

	/**
	 * Adds a default Term to the "Individual Type" Custom Taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The default taxonomy configuration arguments.
	 * @return array $args The modified taxonomy configuration arguments.
	 */
	public function theball_term_default( $args ) {

		// Default Term setup.
		$args['default_term'] = [
			'name' => __( 'Supporters', 'sof-utilities' ),
			'slug' => 'supporters',
		];

		// --<
		return $args;

	}

}
