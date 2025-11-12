<?php
/**
 * ElementorKit: Template Kit Install
 *
 * Template Kit Install
 *
 * @package ElementorKit
 * @since 1.0.0
 */

namespace ElementorKit\API;

use ElementorKit\Utils\Plugin_API;
use ElementorKit\Backend\Options;
use ElementorKit\Backend\Template_Kits;
use ElementorKit\Backend\Downloaded_Items;
use ElementorKit\Utils\Limits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * API for handling template kit installs
 *
 * @since 1.0.0
 */
class Template_Kit_Install extends API {

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function install_template_kit_from_elements( $request ) {

        if ( ! current_user_can( 'install_themes' ) ) {
            return $this->format_error(
                'installTemplateKit',
                'missing_permissions',
                'Please contact your administrator to perform this action.'
            );
        }

		$template_kit_humane_id = $request->get_param( 'templateKitId' );

		Limits::get_instance()->raise_limits();

		// Reach out to Extensions API for a download request of this item
		$api_response = Plugin_API::get_instance()->api_call( '/get-element-download/?type=template-kit&id=' . $template_kit_humane_id );

		if ( is_wp_error( $api_response ) ) {
			$api_error_data = $api_response->get_error_data();
			if ( $api_error_data && ! empty( $api_error_data['message'] ) ) {
				return $this->format_error(
					'installTemplateKit',
					'zip_failure',
					'Failed to download item: ' . $api_error_data['message']
				);
			}

			return $this->format_error(
				'installTemplateKit',
				'zip_failure',
				'Failed to download item: ' . $api_response->get_error_message()
			);
		}

		// Check if we get a successful API response with a download URL we can work with:
		if ( $api_response && ! is_wp_error( $api_response ) && ! empty( $api_response['download_urls']['original'] ) ) {

			// Download our remote ZIP file to a local temporary file:
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			$temporary_zip_file_path = wp_tempnam( 'tk-' . $template_kit_humane_id );
			$download_response       = wp_safe_remote_get( $api_response['download_urls']['original'], array(
				'timeout'  => 60,
				'stream'   => true,
				'filename' => $temporary_zip_file_path
			) );

			// If we failed to download return an error
			if ( is_wp_error( $download_response ) ) {
				return $this->format_error(
					'installTemplateKit',
					'zip_failure',
					$download_response->get_error_message()
				);
			}

			// Assume we downloaded successfully:
			$error_or_template_kit_id = Template_Kits::get_instance()->process_zip_file( $temporary_zip_file_path );
			unlink( $temporary_zip_file_path );

			if ( is_wp_error( $error_or_template_kit_id ) ) {
				return $this->format_error(
					'installTemplateKit',
					'zip_failure',
					$error_or_template_kit_id->get_error_message()
				);
			}

			// If we get here we've got a successful license event from Elements. Lets flag that in our database so
			// we can update the UI on future page loads.
			Downloaded_Items::get_instance()->record_download_event( $template_kit_humane_id, $error_or_template_kit_id );

			$data = [
				'success'         => true,
				'template_kit_id' => $error_or_template_kit_id,
			];

			return $this->format_success( $data );
		}

		return $this->format_error(
			'installTemplateKit',
			'zip_failure',
			'Failed to download item, please try again.'
		);

	}

	/**
	 * @param $request \WP_REST_Request
	 */
	public function upload_template_kit_zip_file( $request ) {

        if ( ! current_user_can( 'install_themes' ) ) {
            return $this->format_error(
                'installTemplateKit',
                'missing_permissions',
                'Please contact your administrator to perform this action.'
            );
        }

		Limits::get_instance()->raise_limits();

		$all_files = $request->get_file_params();
		if ( $all_files && ! empty( $all_files['file'] ) ) {
			if ( is_uploaded_file( $all_files['file']['tmp_name'] ) && ! $all_files['file']['error'] ) {
				// We've got a successful file upload!
				$temp_file_name           = $all_files['file']['tmp_name'];
				$error_or_template_kit_id = Template_Kits::get_instance()->process_zip_file( $temp_file_name );
				unlink( $temp_file_name );

				if ( is_wp_error( $error_or_template_kit_id ) ) {
					return $this->format_error(
						'uploadTemplateKitZipFile',
						'zip_failure',
						$error_or_template_kit_id->get_error_message()
					);
				}

				// If we get here we assume the kit installed correctly.
				return $this->format_success( [
					'templateKitId' => $error_or_template_kit_id,
					'message'       => 'Zip installed successfully'
				] );
			}
		}

		return $this->format_error(
			'uploadTemplateKitZipFile',
			'zip_failure',
			'Failed to process ZIP file, please ensure the selected file is the correct Template Kit format.'
		);
	}

	/**
	 * Deletes the template kit.
	 *
	 * @param $request \WP_REST_Request
	 */
	public function delete_template_kit( $request ) {

        if ( ! current_user_can( 'install_themes' ) ) {
            return $this->format_error(
                'installTemplateKit',
                'missing_permissions',
                'Please contact your administrator to perform this action.'
            );
        }

		$template_kit_id = $request->get_param( 'templateKitId' );
		Template_Kits::get_instance()->delete_template_kit( $template_kit_id );

		return $this->format_success(
			array(
				'message' => 'Kit deleted successfully',
			)
		);
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'installPremiumTemplateKit', [ $this, 'install_template_kit_from_elements' ] );
		$this->register_endpoint( 'uploadTemplateKitZipFile', [ $this, 'upload_template_kit_zip_file' ] );
		$this->register_endpoint( 'deleteTemplateKit', [ $this, 'delete_template_kit' ] );
	}
}
