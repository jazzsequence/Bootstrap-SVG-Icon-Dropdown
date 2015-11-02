<?php
/**
 * Bootstrap SVG Icon Dropdown
 * Uses a Bootstrap pseudo-dropdown to allow users to select an SVG icon.
 *
 * @package   Bootstrap SVG Icon Dropdown
 * @version   1.0.0
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @link      https://github.com/jazzsequence/Bootstrap-SVG-Icon-Dropdown
 * @copyright 2015 Chris Reynolds
 */

if ( ! class_exists( 'Bootstrap_SVG_Icon_Dropdown' ) ) {

	/**
	 * Main Bootstrap SVG Icon Dropdown class
	 * @package Bootstrap SVG Icon Dropdown
	 */
	class Bootstrap_SVG_Icon_Dropdown {

		/**
		 * Option key, and option page slug
		 *
		 * @var   string
		 * @since 1.0.0
		 * @todo  Change this to your option key if using on a WordPress options page.
		 */
		public $key = 'bootstrap_svg_options';

		/**
		 * Options page metabox id
		 *
		 * @var   string
		 * @since 1.0.0
		 * @todo  Change this to your metabox id if using with CMB2.
		 */
		private $metabox_id = 'bootstrap_svg_options_metabox';

		/**
		 * Options Page title
		 *
		 * @var   string
		 * @since 1.0.0
		 */
		protected $title = '';

		/**
		 * Constructor
		 *
		 * @since  1.0.0
		 * @return void
		 * @todo   Replace title and textdomain with your options page title and plugin textdomain (if using on a WordPress options page).
		 */
		public function __construct() {
			$this->hooks();
			$this->includes();

			// Replace the title and textdomain with your own.
			$this->title = __( 'Bootstrap SVG Icon Settings', 'bootstrap-svg-dropdown' );
		}

		/**
		 * Include external files.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function includes() {
			require_once( basename( __FILE__ ) . '/inc/template-tags.php' );
		}

		/**
		 * Initiate our hooks
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function hooks() {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'add_options_page' ) );
			add_action( 'cmb2_init', array( $this, 'fields' ) );
			add_action( 'admin_print_scripts', array( $this, 'load_svgs' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'cmb2_render_bootstrap_select', array( $this, 'render_bootstrap_select' ), 10, 5 );
		}

		/**
		 * Register our setting to WP
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function admin_init() {
			register_setting( $this->key, $this->key );
		}

		/**
		 * Load the SVG defs.
		 * @return void
		 * @todo   Replace the screen ID with your actual WordPress admin screen ID.
		 */
		public function load_svgs() {
			$screen = get_current_screen();

			// Replace this with your actual screen id. You can var_dump( $screen->id ) to figure out what it should be.
			if ( 'bootstrap_svg_options_page' == $screen->id ) {
				wds_include_svg_definitions();
			}

		}

		/**
		 * Enqueue js and css.
		 * @param  string $hook The page hook for the admin page.
		 * @return void
		 */
		public function admin_scripts( $hook ) {
			if ( 'clp_library_resource_page_wds_clp_training_options' == $hook ) {
				$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true ) || ( isset( $_GET['script_debug'] ) ) ? true : false;
				$min   = '.min';

				if ( true === $debug ) {
					$min = '';
				}

				wp_enqueue_style( 'bootstrap-select', wds_clp_training_courses()->url . 'assets/css/bootstrap-select' . $min . '.css', '', '20150727' );
				wp_enqueue_script( 'bootstrap-dropdown', wds_clp_training_courses()->url . 'assets/js/dropdowns-enhancement' . $min . '.js', array( 'jquery' ), '20150724', true );
			}
		}

		/**
		 * Add menu options page
		 *
		 * @since  1.0.0
		 * @return void
		 * @todo   Update with your information.
		 */
		public function add_options_page() {
			$this->options_page = add_submenu_page(
				'bootstrap_parent_menu',             // Replace with your admin page slug.
				$this->title,                        // Page title.
				__( 'Bootstrap SVG Icons', 'clp' ),  // Replace with your menu title.
				'manage_options',                    // Capability.
				$this->key,                          // Menu slug.
				array( $this, 'admin_page_display' ) // Function to display admin page.
			);
		}

		/**
		 * Admin page markup. Mostly handled by CMB2
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function admin_page_display() {
			?>
			<div class="wrap cmb2-options-page <?php echo $this->key; // WPCS: XSS ok. ?>">
				<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
				<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
			</div>
			<?php
		}

		/**
		 * Add custom fields to the options page.
		 *
		 * @since  1.0.0
		 * @return void
		 * @todo   Replace placeholder data with your option ID and name.
		 */
		public function fields() {

			$box = new_cmb2_box( array(
				'id'      => $this->metabox_id,
				'hookup'  => false,
				'show_on' => array(
					// These are important, don't remove.
					'key'   => 'options-page',
					'value' => array( $this->key ),
				),
			) );

			$box->add_field( array(
				'name'    => __( 'Bootstrap Icon', 'bootstrap-svg-dropdown' ), // Replace with your option name and textdomain.
				'id'      => 'bootstrap_icon',           // Replace with your ID.
				'type'    => 'bootstrap_select',
				'options' => $this->get_svg_list(),
			) );

		}

		/**
		 * Get the full list of SVG files
		 * @return array An array of SVGs that can be used in a CMB2 option list.
		 */
		public function get_svg_list() {
			$svgs = array();
			foreach ( glob( wds_get_svg_directory() . '*.svg' ) as $svg ) {
				$slug = str_replace( array( wds_get_svg_directory(), '.svg' ), '', $svg );
				$svgs[ $slug ] = wds_get_svg( $slug ) . ' ' . ucfirst( str_replace( '-', ' ', $slug ) );
			}

			return $svgs;
		}

		/**
		 * Display the custom CMB2 field.
		 * @param  string $field             The option field being rendered.
		 * @param  string $escaped_value     The value selected.
		 * @param  int    $object_id         The ID of the thing we're editing (post ID, user ID, etc).
		 * @param  string $object_type       The *type* of thing we're editing (CPT, user, option, etc).
		 * @param  object $field_type_object Not used here.
		 * @return void
		 */
		public function render_bootstrap_select( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
			$id      = $field->args['id'];
			$options = $field->args['options'];

			echo '<div class="btn-group">';
			echo '<button data-toggle="dropdown" id="' . esc_attr( $id ) . '" class="btn btn-default dropdown-toggle">';
			echo ( ! $escaped_value ) ? __( '- Choose an icon -', 'clp' ) : $options[ $escaped_value ]; // WPCS: XSS ok.
			echo ' <span class="caret"></span></button>';
			echo '<ul class="dropdown-menu">';

			foreach ( $options as $slug => $name ) {
				$selected = ( isset( $options[ $escaped_value ] ) && $slug == $options[ $escaped_value ] ) ? ' selected="selected"' : '';
				echo '<li><input type="radio" name="' . $id . '" id="' . $id . '_' . $slug . '" value="' . $slug . '"' . $selected .'><label for="' . $id . '_' . $slug . '">' . $name . '</label></li>'; // WPCS: XSS ok.
			}

			echo '</ul>';
			echo '</div>';

		}
	}

}

// Kick it off.
bootstrap_svg_dropdown_init();
