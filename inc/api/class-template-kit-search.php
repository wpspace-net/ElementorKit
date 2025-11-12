<?php
/**
 * ElementorKit: Search API
 *
 * Search API
 *
 * @package ElementorKit
 * @since 1.0.0
 */

namespace ElementorKit\API;

use ElementorKit\Utils\Plugin_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Search API
 *
 * @since 1.0.0
 */
class Template_Kit_Search extends API {

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function fetch_premium_search_results( $request ) {
		$search = $request->get_params();

		$api_parameters = [
			'page' => empty( $search['page'] ) || (int) $search['page'] < 1 ? 1 : (int) $search['page'],
		];

		// 'our_query' => 'elements_query'
		$parameter_mapping = [
			'text'       => 'search_terms',
			'industries' => 'industries',
		];

		foreach ( $parameter_mapping as $our_query_key => $elements_query_key ) {
			if ( ! empty( $search[ $our_query_key ] ) && strlen( trim( $search[ $our_query_key ] ) ) > 0 ) {
				$api_parameters[ $elements_query_key ] = sanitize_text_field( urldecode( trim( $search[ $our_query_key ] ) ) );
			}
		}

		$data = Plugin_API::get_instance()->api_call( '/get-template-kits/?' . http_build_query( $api_parameters ) );

		if ( is_wp_error( $data ) ) {
			return $this->format_error(
				'fetchPremiumTemplateKitSearchResults',
				'generic_api_error',
				'Failed to fetch template kit search results: ' . $data->get_error_message()
			);
		}

		return new \WP_REST_Response( $data, 200 );
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'fetchPremiumTemplateKitSearchResults', [ $this, 'fetch_premium_search_results' ] );
	}
}
