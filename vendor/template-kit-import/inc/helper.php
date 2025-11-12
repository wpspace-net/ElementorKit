<?php

namespace Template_Kit_Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * @param $template_kit_id
 *
 * @return bool|Builder_Elementor|Builder_Elementor_Kit
 */
function template_kit_import_get_builder( $template_kit_id ) {
	// Grab out the uploaded template kit from the CPT.
	$post = $template_kit_id ? get_post( $template_kit_id ) : false;
	if ( $post && CPT_Kits::get_instance()->cpt_slug === $post->post_type ) {
		// Confirmed that the required ID is in fact one of our uploaded template kits.
		$builder = get_post_meta( $post->ID, 'tk_builder', true );
		if ( 'elementor' === $builder ) {
			$builder_class = new Builder_Elementor();
			$builder_class->load_kit( $post->ID );
			return $builder_class;
		} elseif ( TEMPLATE_KIT_IMPORT_TYPE_ELEMENTOR === $builder ) {
			$builder_class = new Builder_Elementor_Kit();
			$builder_class->load_kit( $post->ID );
			return $builder_class;
		}
	}

	return false;
}
