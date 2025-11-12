<?php
/**
 * ElementorKit:
 *
 * This starts things up. Registers the SPL and starts up some classes.
 *
 * @package ElementorKit
 * @since 1.0.0
 */

namespace ElementorKit;

use ElementorKit\Backend\Photos_Embed;
use ElementorKit\Backend\Template_Kits;
use ElementorKit\Backend\Welcome;
use ElementorKit\Utils\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * ElementorKit plugin.
 *
 * The main plugin handler class is responsible for initializing ElementorKit. The
 * class registers and all the components required to run the plugin.
 *
 * @since 1.0.0
 */
class Plugin extends Base {

	/**
	 * Initializing ElementorKit plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_action( 'plugins_loaded', [ $this, 'db_upgrade_check' ] );
		add_filter( 'elementor/admin-top-bar/is-active', [ $this, 'hide_elementor_top_bar' ], 10, 2 );
	}

	/**
	 * Sets up the admin menu options.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_menu() {
		Welcome::get_instance()->admin_menu();
	}

	/**
	 * Sets up the admin menu options.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_init() {
		Photos_Embed::get_instance();
		Template_Kits::get_instance();
	}

	public function db_upgrade_check() {
		if ( is_admin() && get_option( 'elementorkit_version' ) !== ELEMENTORKIT_VER ) {
			$this->activation();
		}
	}

	public function activation() {
		update_option( 'elementorkit_version', ELEMENTORKIT_VER );
		if ( ! get_option( 'elementorkit_install_time' ) ) {
			update_option( 'elementorkit_install_time', time() );
		}
	}

	/**
	 * Hide Elementor admin top bar on ElementorKit pages.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param bool $is_active Whether the admin top bar is active.
	 * @param \WP_Screen $current_screen Current screen object.
	 * @return bool
	 */
	public function hide_elementor_top_bar( $is_active, $current_screen ) {
		// Check if current screen contains ElementorKit slug
		if ( $current_screen && isset( $current_screen->id ) && 
			 strpos( $current_screen->id, ELEMENTORKIT_SLUG ) !== false ) {
			return false;
		}
		
		return $is_active;
	}

}
