<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Finish template
 * @since 1.5.19
 * */
?>
<header>
	<h1><?php echo esc_html__( 'Set Up Completed', 'gn-publisher' ); ?></h1>
</header>
<?php GNPUB_Status::gnpub_render_status_tab_html(); ?>