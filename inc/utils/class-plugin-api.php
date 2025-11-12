<?php
/**
 * ElementorKit: Extensions API
 *
 * Extensions API
 *
 * @package ElementorKit
 * @since 1.0.0
 */

namespace ElementorKit\Utils;

use ElementorKit\Backend\Options;
use ElementorKit\Backend\Subscription;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Plugin API
 *
 * @since 1.0.0
 */
class Plugin_API extends Base {

	/**
	 * @var string
	 *
	 * @since 1.0.0
	 */
	private $api_endpoint = 'https://api.elementorkit.site';

	/**
	 * @var string
	 *
	 * @since 1.0.0
	 */
	private $token = '';

	public function set_token( $api_key = false ) {
		if ( ! $api_key ) {
			$elementorkit_api_key = Options::get_instance()->get( Subscription::SUBSCRIPTION_API_OPTION );
			if ( $elementorkit_api_key && ! empty( $elementorkit_api_key['api_key'] ) ) {
				$api_key = $elementorkit_api_key['api_key'];
			}
		}
		$this->token = $api_key;
	}

	public function get_token() {
		return $this->token;
	}

	/**
	 *
	 * @param $endpoint
	 * @param string $method
	 * @param array $body_args
	 *
	 * @return \stdClass|\WP_Error
	 */
	public function api_call( $endpoint ) {

		if ( ! $this->token ) {
			$this->set_token();
		}
		$http_args = [
			'user-agent' => 'Mozilla/5.0 (ElementorKit ' . ELEMENTORKIT_VER . ';) ' . home_url(),
			'timeout'    => 15,
		];
		if ( $this->token ) {
			$http_args['headers']['ElementorKit-API-Key'] = $this->token;
		}

		foreach ( [ true, false, ] as $sslverify ) {
			// Unfortunately some hosts ONLY work with sslverify true, and some ONLY work with sslverify false.
			// So we cannot just hard code it to false, we have to try both. SSL first, then broken SSL if that fails.
			$http_args['sslverify'] = $sslverify;
			$response = wp_safe_remote_get( $this->api_endpoint . $endpoint, $http_args );

			if ( $response && ! is_wp_error( $response ) ) {
				break;
			}
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$raw_response = wp_remote_retrieve_body( $response );
		$data         = json_decode( $raw_response, true );

		$response_code            = wp_remote_retrieve_response_code( $response );
		$error_message_to_display = 'Unknown error';

		if ( empty( $data ) || ! is_array( $data ) ) {
			// Did the response contain HTML instead of json?
			if ( strlen( $raw_response ) && ! $data ) {
				// we failed to decode the response into JSON
				$error_message_to_display = 'The API did not respond with valid JSON data.';
			}

			return new \WP_Error( 'no_json', $error_message_to_display, [
				__( 'An error occurred, please try again', 'elementorkit' ),
				var_export( wp_remote_retrieve_body( $response ), true )
			] );
		}

		if ( 200 !== (int) $response_code && 201 !== (int) $response_code ) {
			$error_message_to_display = __( 'HTTP Error', 'elementorkit' );
			if ( $data && ! empty( $data['message'] ) ) {
				$error_message_to_display = $data['message'];
			}
			if ( $data && ! empty( $data['error'] ) && ! empty( $data['error']['message'] ) ) {
				$error_message_to_display = $data['error']['message'];
			}

			// format our error response data into something easier to parse
			return new \WP_Error( $response_code, $error_message_to_display, $data && ! empty( $data['error'] ) ? $data['error'] : $data );
		}

		return $data;
	}

}
