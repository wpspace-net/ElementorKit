<?php
/**
 * ElementorKit: Subscription
 *
 * Subscription management
 *
 * @package ElementorKit
 * @since 1.0.0
 */

namespace ElementorKit\Backend;

use ElementorKit\Utils\Base;
use ElementorKit\Utils\Plugin_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Subscription management.
 *
 * @since 1.0.0
 */
class Subscription extends Base {

	const SUBSCRIPTION_API_OPTION = 'elementorkit-api-key';
	const SUBSCRIPTION_API_CACHE = 3600; // Cache results locally this long.
	const SUBSCRIPTION_INACTIVE = 'inactive';

	/**
	 * Calls the Plugin API to verify if the provided API key is correct.
	 * Caches the users subscription_status so our plugin can know what to do for different cases.
	 *
	 * @return array
	 */
	public function verify_api_key_and_cache_user_info( $api_key = false ) {

		if ( $api_key ) {
			// If we've been given an Plugin API key value, we pass that through to our API library:
			Plugin_API::get_instance()->set_token( $api_key );
		}

		// Call the user_into endpoint, this returns the users subscription_status
		$result = Plugin_API::get_instance()->api_call( '/verify-api-key/' );
		if ( ! is_wp_error( $result ) && is_array( $result ) && ! empty( $result['subscription_status'] ) ) {
			// We've got a successful result from the Plugin API, cache the result locally so we don't have to hit this endpoint over and over again.
			$cached_auth_information = [
				'valid'  => true,
				'api_key'  => Plugin_API::get_instance()->get_token(),
				'time'   => time(),
				'status' => $result['subscription_status'],
			];
		} else {
			$cached_auth_information = [
				'valid'  => false,
				'api_key'  => '',
				'time'   => time(),
				'status' => 'error',
			];

			// We check if our response was a "wp_error" so we can sniff more details about the issue out of the response:
			if ( is_wp_error( $result ) && is_array( $result->errors ) && is_array( $result->error_data ) ) {
				$error_status  = key( $result->errors );
				$error_data    = $result->error_data[ $error_status ];

				$cached_auth_information['error'] = [
					'message' => ! empty( $error_data['message'] ) ? $error_data['message'] : current( $result->errors[ $error_status ] ),
					'code'    => ! empty( $error_data['code'] ) ? $error_data['code'] : false,
				];
			}
		}

		Options::get_instance()->set( self::SUBSCRIPTION_API_OPTION, $cached_auth_information );

		return $cached_auth_information;
	}

	public function get_subscription_status(){
		$cached_auth_information = Options::get_instance()->get( self::SUBSCRIPTION_API_OPTION );

		if ( $cached_auth_information && ! empty( $cached_auth_information['status'] ) ) {
			return $cached_auth_information['status'];
		}

		return 'unknown';
	}
}
