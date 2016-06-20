<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/20/2016
 * Time: 3:42 PM
 */
?>

<div id="pagination" class="text-right">
	<?php
	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );
	if ( isset( $url_parts[1] ) ) {
		wp_parse_str( $url_parts[1], $query_args );
	}
	$pagenum_link = esc_url(remove_query_arg( array_keys( $query_args ), $pagenum_link ));
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';
	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';
	$args = array(
		'base'     => $pagenum_link,
		'format'   => $format,
		'total'    => $my_query->max_num_pages,
		'current'  => $paged,
		'add_args' =>$query_args,
		'prev_text' => __( 'Previous', "wpbooking" ),
		'next_text' => __( 'Next', "wpbooking" ),
	);
	echo paginate_links( $args );
	?>
</div>
