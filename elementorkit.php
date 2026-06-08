<?php
/**
 * Plugin Name: ElementorKit
 * Description: Access beautifully designed Elementor template kits and millions of royalty-free photos with ElementorKit.
 * Author: ElementorKit
 * Author URI: https://elementorkit.site
 * Version: 1.1.1
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Elementor tested up to: 3.32.4
 * Elementor Pro tested up to: 3.32.4
 *
 * Text Domain: elementorkit
 *
 * @package ElementorKit
 *
 * ElementorKit for WordPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * ElementorKit for WordPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELEMENTORKIT_SLUG', 'elementorkit' );
define( 'ELEMENTORKIT_VER', '1.1.1' );
define( 'ELEMENTORKIT_FILE', __FILE__ );
define( 'ELEMENTORKIT_DIR', plugin_dir_path( ELEMENTORKIT_FILE ) );
define( 'ELEMENTORKIT_URI', plugins_url( '/', ELEMENTORKIT_FILE ) );
define( 'ELEMENTORKIT_CONTENT_NAME', 'Template Kit' );
define( 'ELEMENTORKIT_PHP_VERSION', '7.4' );
define( 'ELEMENTORKIT_WP_VERSION', '5.0' );
define( 'ELEMENTORKIT_API_NAMESPACE', ELEMENTORKIT_SLUG . '/v2' );

/**
 * Plugin Update Checker
 */
require plugin_dir_path( __FILE__ ) . 'vendor/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$elementorkit_update_checker = PucFactory::buildUpdateChecker(
	'https://github.com/wpspace-net/ElementorKit',
	__FILE__,
	'elementorkit'
);

if ( ! version_compare( PHP_VERSION, ELEMENTORKIT_PHP_VERSION, '>=' ) ) {
	add_action( 'admin_notices', 'elementorkit_fail_php_version' );
} elseif ( ! version_compare( get_bloginfo( 'version' ), ELEMENTORKIT_WP_VERSION, '>=' ) ) {
	add_action( 'admin_notices', 'elementorkit_fail_wp_version' );
} else {
	require ELEMENTORKIT_DIR . 'inc/bootstrap.php';
}

/**
 * Load ElementorKit textdomain.
 *
 * Load gettext translate for ElementorKit text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function elementorkit_load_plugin_textdomain() {
	load_plugin_textdomain( 'elementorkit' );
}
add_action( 'plugins_loaded', 'elementorkit_load_plugin_textdomain' );

/**
 * ElementorKit admin notice for minimum PHP version.
 *
 * Warning when the site doesn't have the minimum required PHP version.
 *
 * @since 1.0.0
 *
 * @return void
 */
function elementorkit_fail_php_version() {
	$message = sprintf(
		/* translators: 1: required PHP version, 2: link to WordPress requirements page */
		esc_html__( 'ElementorKit requires PHP version %1$s+, plugin is currently NOT ACTIVE. Please contact the hosting provider. See the %2$s.', 'elementorkit' ),
		ELEMENTORKIT_PHP_VERSION,
		sprintf(
			'<a href="%s" target="_blank">%s</a>',
			esc_url( 'https://wordpress.org/about/requirements/' ),
			esc_html__( 'WordPress hosting requirements', 'elementorkit' )
		)
	);

	$html_message = sprintf( '<div class="error">%s</div> ', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}

/**
 * ElementorKit admin notice for minimum WordPress version.
 *
 * Warning when the site doesn't have the minimum required WordPress version .
 *
 * @since 1.0.0
 *
 * @return void
 */
function elementorkit_fail_wp_version() {
	/* translators: %s: WordPress version */
	$message      = sprintf( esc_html__( 'ElementorKit requires WordPress version %s+. Because you are using an earlier version, the plugin is currently NOT ACTIVE.', 'elementorkit' ), ELEMENTORKIT_WP_VERSION );
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}
