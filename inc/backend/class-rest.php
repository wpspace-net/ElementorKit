<?php
/**
 * ElementorKit: REST API controller
 *
 * REST API controller.
 *
 * @package ElementorKit
 * @since 1.0.0
 */

namespace ElementorKit\Backend;

use ElementorKit\API\Banners;
use ElementorKit\API\Photos_Import;
use ElementorKit\API\Photos_Search;
use ElementorKit\API\Requirements;
use ElementorKit\API\Settings;
use ElementorKit\API\Subscription_API;
use ElementorKit\API\Template_Kit_Install;
use ElementorKit\API\Template_Kit_Search;
use ElementorKit\API\Template_Kit_Import;
use ElementorKit\Utils\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * REST API controller.
 *
 * @since 1.0.0
 */
class REST extends Base {


	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
		// We also add admin-ajax because the REST API is unsuitable for a lot of hosts.
		add_action( 'wp_ajax_elementorkit', [ $this, 'ajax_handler' ] );
	}

	/**
	 * Update: We want to use the old ajax endpoint because the REST API is unsuitable on a lot of hosts.
	 *
	 * Revisit the REST API after Gutenberg becomes stable because that will iron our REST API issues.
	 *
	 * @since 1.0.0
	 */
	public function ajax_handler() {

		$nonce = null;
		if ( isset( $_REQUEST['_wpnonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );
		} elseif ( isset( $_SERVER['HTTP_X_WP_NONCE'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_WP_NONCE'] ) );
		}
		if ( $nonce && wp_verify_nonce( $nonce, 'wp_rest' ) && isset( $_GET['endpoint'] ) ) {
			$namespace = ELEMENTORKIT_API_NAMESPACE;
			$endpoint  = sanitize_text_field( wp_unslash( $_GET['endpoint'] ) );
			$server    = rest_get_server();
			$routes    = $server->get_routes();
			$rest_key  = '/' . $namespace . '/' . $endpoint;
			if ( isset( $routes[ $rest_key ] ) && isset( $routes[ $rest_key ][0] ) ) {
				$request = new \WP_REST_Request( 'PUT' );
				$request->set_headers( $server->get_headers( wp_unslash( $_SERVER ) ) );
				$request->set_body( $server->get_raw_data() );
				$check_required = $request->has_valid_params();
				if ( is_wp_error( $check_required ) ) {
					wp_send_json_error( '-1' );
				} else {
					$check_sanitized = $request->sanitize_params();
					if ( is_wp_error( $check_sanitized ) ) {
						wp_send_json_error( '-2' );
					}
				}

				if ( call_user_func( $routes[ $rest_key ][0]['permission_callback'], $request ) ) {
					$rest_response = call_user_func( $routes[ $rest_key ][0]['callback'], $request );
					if ( ! is_wp_error( $rest_response ) && ! empty( $rest_response->data ) ) {
						wp_send_json( $rest_response->data, $rest_response->status );
					}
				}
			}
		}
		wp_die();
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		Template_Kit_Search::get_instance();
		Photos_Search::get_instance();
		Template_Kit_Import::get_instance();
		Template_Kit_Install::get_instance();
		Subscription_API::get_instance();
		Banners::get_instance();
		Settings::get_instance();
		Photos_Import::get_instance();
		Requirements::get_instance();
	}

}

