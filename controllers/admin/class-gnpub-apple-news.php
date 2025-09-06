<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This controller is responsible for handling apple news publish
 * 
 * @since 1.5.24
 */
class GNPUB_Apple_News {

	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) ) ;
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );

	}

	/**
	 * Enqueue scripts and styles
	 * @param 	$hook_suffix 	string
	 * @since 	1.5.24
	 * */
	public function enqueue_scripts_and_styles( $hook_suffix ) {
		
		if ( $hook_suffix == "settings_page_gn-publisher-settings" || $hook_suffix == 'admin_page_gnpub-setup-wizard' || $hook_suffix == 'post.php' ) {
		
			$min = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script('gn-admin-applenews-script', GNPUB_URL . "/assets/js/gn-admin-apple-news{$min}.js", array('jquery'), GNPUB_VERSION, true );
			wp_localize_script('gn-admin-applenews-script', 'gn_script_apple_news_vars', array(
				'nonce' => wp_create_nonce( 'gn-admin-nonce' ),
				)
			);

			wp_enqueue_script( 'gn-admin-applenews-script' );

		}
	}

	/**
	 * Add meta boxes to the post types
	 * @since 1.5.24
	 * */
	public function add_meta_boxes() {
		
		$gn_options 	=	get_option( 'gnpub_new_options' );

		// Check if apple news is enabled and configured
		if ( ! empty( $gn_options['gnpub_apple_news'] ) && ! empty( $gn_options['gnpub_apple_news_channel_id'] ) && ! empty( $gn_options['gnpub_apple_news_api_key_id'] ) && ! empty( $gn_options['gnpub_apple_news_api_key_secret'] ) ) {
			
			add_meta_box( 
                'gnpub_apple_news_meta_options', 
                esc_html__( 'Apple News', 'schema-and-structured-data-for-wp' ), 
                array( $this, 'apple_news_meta_callback' ),
                'post',
                'side', 
                'default' 
            );

		}

	}

	/**
	 * Callback function for meta box
	 * @param 	$post 	WP_Post
	 * @since 	1.5.24
	 * */
	public function apple_news_meta_callback( $post ) {
		?>
		<tr class="gnpub-apple-news-meta-wrapper">
				<td>
					<button type="button" id="gnpub-apple-news-publish-btn" class="button button-primary"><p style="margin-bottom: 0px;"><?php echo esc_html__( 'Publish Apple News', 'gn-publisher' ) ?></p></button>
					<div class="description"><?php echo esc_html__( 'Click on publish apple news button to publish the article on apple news publisher platform', 'gn-publisher' ); ?></div>
				</td>
			</tr>
		<?php	
	}

}

new GNPUB_Apple_News();