<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get translatepress active languages
 * @since 1.5.19
 * */
function gnpub_get_active_language_slugs() {

	// Ensure TranslatePress class exists
	if ( function_exists('gnpub_pro_is_translatepress_enabled') && gnpub_pro_is_translatepress_enabled() ) {

	  	$trp 					=	TRP_Translate_Press::get_trp_instance();
		$trp_settings 			=	$trp->get_component( 'settings' );
		$trp_settings 			=	$trp_settings->get_settings();
		$available_languages 	=	$trp_settings['translation-languages'];

		return $available_languages;

	}

	return array('en');

}