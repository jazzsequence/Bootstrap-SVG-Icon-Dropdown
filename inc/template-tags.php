<?php
/**
 * Bootstrap SVG Icon Dropdown Helper Functions
 * Helper functions for using Bootstrap SVG Icon Dropdown class
 *
 * @package   Bootstrap SVG Icon Dropdown
 * @version   1.0.0
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @link      https://github.com/jazzsequence/Bootstrap-SVG-Icon-Dropdown
 * @copyright 2015 Chris Reynolds
 */

/**
 * Initialization function.
 * @since  1.0.0
 * @return object Returns the class.
 */
function bootstrap_svg_dropdown_init() {
	return new Bootstrap_SVG_Icon_Dropdown();
}

/**
 * Add SVG definitions to <head>.
 *
 * @return void
 */
function wds_include_svg_definitions() {
	// Define svg sprite file.
	$svg_defs = wds_get_svg_defs_sprite();

	if ( file_exists( $svg_defs ) ) {
		require_once( $svg_defs );
	}
}
add_action( 'wp_head', 'wds_include_svg_definitions' );

/**
 * Get the SVG defs sprite.
 *
 * @return string Path to svg-defs.svg
 * @todo   The /images/svg-defs.svg must point to your SVG sprite.
 */
function wds_get_svg_defs_sprite() {
	return get_template_directory() . '/images/svg-defs.svg';
}

/**
 * Get the source SVG files directory.
 *
 * @return string Path to svg images directory.
 * @todo   The /images/svg/ directory must exist and must have your svg files.
 */
function wds_get_svg_directory() {
	// Replace with the path to your svg images directory.
	return get_template_directory() . '/images/svg/';
}

/**
 * Echo SVG markup.
 *
 * @param  string $icon_name Use the icon name, such as "facebook-square".
 */
function wds_do_svg( $icon_name ) {
	echo wds_get_svg( $icon_name ); // WPCS: XSS ok.
}

/**
 * Return SVG markup.
 *
 * @param  string $icon_name Use the icon name, such as "facebook-square".
 */
function wds_get_svg( $icon_name ) {

	$svg = '<svg class="icon icon-' . esc_html( $icon_name ) . '">';
	$svg .= '	<use xlink:href="#icon-' . esc_html( $icon_name ) . '"></use>';
	$svg .= '</svg>';

	return $svg;
}