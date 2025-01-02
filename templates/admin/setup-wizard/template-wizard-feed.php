<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Feed template
 * @since 1.5.19
 * */
?>
<header>
	<h1><?php echo esc_html__( 'Google News Feed', 'gn-publisher' ); ?></h1>
	<p><?php echo esc_html__( '', 'gn-publisher' ); ?></p>
</header>
<div id="gn-google-feed">
   <p><?php echo esc_html__( 'Once installed and activated, you can find your GN Publisher RSS feeds at:', 'gn-publisher' ); ?></p>
   <ul>
   	<?php
   		$permalinks_enabled = 	! empty( get_option( 'permalink_structure' ) );
      	$feed_url			=	esc_url( $permalinks_enabled ? trailingslashit( home_url() ) . 'feed/gn' : add_query_arg( 'feed', 'gn', home_url() ) );
    ?>

	    <li>
	    	<input type="text" class="gn-input" value="<?php echo esc_url( $feed_url ); ?>" data-gn-default="<?php echo esc_url( $feed_url ); ?>" id="gn-feed-0" size="60" readonly>
	      	<div class="gn-tooltip">
		      	<button type="button" class="gn-btn" onclick="gn_copy('gn-feed-0')" onmouseout="gn_out('gn-feed-0')">
		        	<span class="gn-tooltiptext" id="gn-feed-0-tooltip"><?php echo esc_html__( 'Copy URL', 'gn-publisher' ); ?></span>
		        <?php echo esc_html__( 'Copy', 'gn-publisher' ); ?>
		        </button>
	      </div>
	    </li>
	    <?php 
	    $categories = get_categories(); 
      	foreach( $categories as $category ) {
        	$gn_category_link = get_category_link( $category->term_id );

        	//Fix for Feed Url link if category is hidden by adding (.) in category base in wordpress permalinks section
        	$gn_category_link = str_replace('/./','/',$gn_category_link);
        	$gn_category_link = $permalinks_enabled ? trailingslashit( $gn_category_link ) . 'feed/gn' : add_query_arg( 'feed', 'gn', $gn_category_link ); 
        	$id 			  = 'gn-feed-'.$category->term_id;				
        ?>
        <li>
        	<input type="text" class="gn-input" value="<?php echo esc_url( $gn_category_link ); ?>" data-gn-default="<?php echo esc_url( $gn_category_link ); ?>" id="<?php echo esc_attr($id) ;?>" size="60" readonly>
     		<div class="gn-tooltip">
	      		<button class="gn-btn" onclick="gn_copy('<?php echo esc_attr( $id ); ?>')" onmouseout="gn_out('<?php echo esc_attr( $id ); ?>')">
	        		<span class="gn-tooltiptext" id="<?php echo esc_attr( $id ); ?>-tooltip"><?php echo esc_html__( 'Copy URL', 'gn-publisher' ); ?>
	        		</span>
	        		<?php echo esc_html__( 'Copy' , 'gn-publisher' ); ?>
	        	</button>
      		</div>
      	</li>
        <?php
        }
	    ?>
   </ul>
   <p><?php echo esc_html__( 'You are not required to use all of the feeds listed above. Just use the ones you want to include in your Publisher Center. Each feed will contain the thirty most recently updated articles in its category.', 'gn-publisher' ); ?></p>
</div>