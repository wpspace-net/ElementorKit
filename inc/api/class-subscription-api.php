<?php
/**
 * ElementorKit: Subscription API
 *
 * Search API
 *
 * @package ElementorKit
 * @since 1.0.0
 */

namespace ElementorKit\API;

use ElementorKit\Backend\Subscription;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Subscription API
 *
 * @since 1.0.0
 */
class Subscription_API extends API {

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function verify_plugin_api_key( $request ) {

		// This is the user provided API key that comes from the front end UI:
		$api_key = trim( $request->get_param( 'token' ) );

		if ( ! $api_key ) {
			return $this->format_error(
				'verifyElementorKitAPI',
				'missing_data',
				'Please provide a API key'
			);
		}

		$subscriptionClass = Subscription::get_instance();
		$api_key_response = $subscriptionClass->verify_api_key_and_cache_user_info( $api_key );

		if ( $api_key_response['valid'] ) {
			// we received a valid API key from Extensions API
			return $this->format_success( $api_key_response );
		} else {
			// API key was invalid, see if we have an 'error' code from Extensions API:
			if ( ! empty( $api_key_response['error'] ) && ! empty( $api_key_response['error']['code'] ) ) {
				return $this->format_error(
					'verifyElementorKitAPI',
					$api_key_response['error']['code'],
					$api_key_response['error']['message']
				);
			}
			// Unknown error from API key verification process:
			return $this->format_error(
				'verifyElementorKitAPI',
				'invalid_api_key',
				'Invalid API key provided',
				$api_key_response
			);
		}
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'verifyElementorKitAPI', [ $this, 'verify_plugin_api_key' ] );
	}
}
