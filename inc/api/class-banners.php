<?php
/**
 * ElementorKit: Banners API
 *
 * Banners API
 *
 * @package ElementorKit
 * @since 1.0.0
 */

namespace ElementorKit\API;

use ElementorKit\Backend\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Search API
 *
 * @since 1.0.0
 */
class Banners extends API {

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function dismiss_banner( $request ) {

		$banner_id = $request->get_param( 'bannerId' );

		// Get a list of dismissed banners (defaulting to an empty array)
		$dismissed_banners = Options::get_instance()->get( 'dismissed_banners', [] );

		// Add the users dismissed banner to the options array
		$dismissed_banners[ $banner_id ] = true;

		// Save this new options array back to the DB:
		Options::get_instance()->set( 'dismissed_banners', $dismissed_banners );

		// Return some success to react:
		return $this->format_success( [
			'closed' => true,
		] );
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'dismissBanner', [ $this, 'dismiss_banner' ] );
	}
}
