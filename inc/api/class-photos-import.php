<?php
/**
 * ElementorKit: Photos Import
 *
 * Photos Import
 *
 * @package ElementorKit
 * @since 1.0.0
 */

namespace ElementorKit\API;

use ElementorKit\Utils\Plugin_API;
use ElementorKit\Backend\Options;
use ElementorKit\Backend\Downloaded_Items;
use ElementorKit\Utils\Limits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Search API
 *
 * @since 1.0.0
 */
class Photos_Import extends API {

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function import_photo( $request ) {

		$photo_id    = $request->get_param( 'photoId' );
		$photo_title = sanitize_text_field( $request->get_param( 'photoTitle' ) );

		// Core WP image handling classes:
		require_once( ABSPATH . '/wp-admin/includes/file.php' );
		require_once( ABSPATH . '/wp-admin/includes/media.php' );
		require_once( ABSPATH . '/wp-admin/includes/image.php' );

		// todo: check we haven't already downloaded this photo, don't download it a second time if it still exists in media library
		$already_downloaded_id = Downloaded_Items::get_instance()->find_downloaded_id( $photo_id );
		if ( $already_downloaded_id ) {
			$already_downloaded_item = get_post( $already_downloaded_id );
			if ( $already_downloaded_item && ! is_wp_error( $already_downloaded_item ) && $already_downloaded_item->ID ) {
				$fullsize_path = get_attached_file( $already_downloaded_item->ID );
				if ( is_file( $fullsize_path ) ) {
					// still exists! don't download this item again.
					$data = [
						'success'           => true,
						'imported_photo_id' => $already_downloaded_item->ID,
						'attachment_data'   => wp_prepare_attachment_for_js( $already_downloaded_item->ID ),
					];

					return $this->format_success( $data );
				}
			}
		}

		$photo_resize_enabled = (bool) Options::get_instance()->get( 'photo_resize_enabled', false );
		Limits::get_instance()->raise_limits( ! $photo_resize_enabled );

		// Reach out to Extensions API for a download request of this item
		$api_response = Plugin_API::get_instance()->api_call( '/get-element-download/?type=photo&id=' . $photo_id );

		if ( is_wp_error( $api_response ) ) {
			$api_error_data = $api_response->get_error_data();
			if ( $api_error_data && ! empty( $api_error_data['message'] ) ) {
				return $this->format_error(
					'importPhoto',
					'generic_api_error',
					'Failed to download photo: ' . $api_error_data['message']
				);
			}

			return $this->format_error(
				'importPhoto',
				'generic_api_error',
				'Failed to download photo: ' . $api_response->get_error_message()
			);
		}

		$file_name = '';
		if ( $photo_title ) {
			$file_name = preg_replace( '#[^a-z0-9]+#', '-', basename( strtolower( $photo_title ) ) ) . '.jpg';
		}
		if ( ! $file_name ) {
			$file_name = 'elements-' . $photo_id . '.jpg';
		}
		$file_description = $photo_title;

		if ( $api_response && ! is_wp_error( $api_response ) && ! empty( $api_response['download_urls']['original'] ) ) {

			// Download our remote ZIP file to a local temporary file:
			$temporary_image_name = wp_tempnam( $file_name );
			$download_response    = wp_safe_remote_get( $api_response['download_urls']['original'], array(
				'timeout'  => 20,
				'stream'   => true,
				'filename' => $temporary_image_name
			) );

			// If we failed to download return an error
			if ( is_wp_error( $download_response ) ) {
				return $this->format_error(
					'importPhoto',
					'generic_api_error',
					$download_response->get_error_message()
				);
			}

			$file_data = file_get_contents( $temporary_image_name );
			$upload    = wp_upload_bits( $file_name, 0, $file_data );
			if ( $upload && ! is_wp_error( $upload ) && empty( $upload['error'] ) && ! empty( $upload['file'] ) ) {
				$info      = wp_check_filetype( $upload['file'] );
				$post_data = [
					'post_title'   => $photo_title,
					'post_excerpt' => $file_description,
					'post_content' => $file_description,
				];
				if ( $info ) {
					$post_data['post_mime_type'] = $info['type'];
				}
				$attachment_id = wp_insert_attachment( $post_data, $upload['file'] );
				if ( $attachment_id && ! is_wp_error( $attachment_id ) ) {
					$attachment_meta = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
					wp_update_attachment_metadata( $attachment_id, $attachment_meta );
					$result['success']       = true;
					$result['attachment_id'] = $attachment_id;
					// Update list of imported images.
					update_post_meta( $attachment_id, 'elementorkit', $photo_id );
					update_post_meta( $attachment_id, '_wp_attachment_image_alt', $photo_title );

					@unlink( $temporary_image_name );

					// If we get here we've got a successful license event from Elements. Lets flag that in our database so
					// we can update the UI on future page loads.
					Downloaded_Items::get_instance()->record_download_event( $photo_id, $attachment_id );

					$data = [
						'success'           => true,
						'imported_photo_id' => $attachment_id,
						'attachment_data'   => wp_prepare_attachment_for_js( $attachment_id ),
					];

					return $this->format_success( $data );
				}
			}
			@unlink( $temporary_image_name );
		}

		return $this->format_error(
			'importPhoto',
			'generic_api_error',
			'Failed to import photo. '
		);
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'importPhoto', [ $this, 'import_photo' ] );
	}
}
