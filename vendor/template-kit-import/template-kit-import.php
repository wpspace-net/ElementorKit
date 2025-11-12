<?php
/**
 * Imported from "Template Kit Import" Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'TEMPLATE_KIT_IMPORT_SLUG' ) ) {
	define( 'TEMPLATE_KIT_IMPORT_SLUG', 'template-kit-import' );
	define( 'TEMPLATE_KIT_IMPORT_FILE', __FILE__ );
	define( 'TEMPLATE_KIT_IMPORT_DIR', plugin_dir_path( TEMPLATE_KIT_IMPORT_FILE ) );

	/**
	 * Our supported import types
	 */
	define( 'TEMPLATE_KIT_IMPORT_TYPE_ELEMENTOR', 'elementor-kit' );

	require TEMPLATE_KIT_IMPORT_DIR . 'inc/bootstrap.php';
}
