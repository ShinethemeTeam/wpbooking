<?php
get_header();
if(get_query_var( 'paged' )) {
    $paged = get_query_var( 'paged' );
} else if(get_query_var( 'page' )) {
    $paged = get_query_var( 'page' );
} else {
    $paged = 1;
}
$args = array(
    'post_type' => 'traveler_service' ,
    's'         => '' ,
    'paged'     => $paged,
    'posts_per_page'     => 3,
);
$service_type = '';
$is_page = get_the_ID();
$list_page_search = apply_filters("traveler_add_page_archive_search",array());
if(!empty($list_page_search[$is_page]))
{
    $service_type = $list_page_search[$is_page];
}
$my_query = Traveler_Service::inst()->query($args,$service_type);
?>
<div class="traveler-container">
    <div>
        <ul>
            <?php
            global $wp_query;
            if($my_query->have_posts()){
                while ( $my_query->have_posts() ) {
                    $my_query->the_post();
                    switch($service_type){
                        case "room":
                            ?>
                                <li class="content-item">

                                    <div class="row">
                                        <div class="col-md-3">
                                            <?php if(has_post_thumbnail() and get_the_post_thumbnail()){
                                                the_post_thumbnail( array( 200, 150 ) );
                                            }?>


                                        </div>
                                        <div class="col-md-9">
                                            <a href="<?php echo get_the_permalink() ?>" class="">
                                                <h5 class="booking-item-title"><?php the_title(); ?></h5>
                                            </a>
                                            <p>
                                                <i class="fa fa-map-marker"></i>
                                                <?php echo get_post_meta(get_the_ID(),'address',true); ?>
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            <?php
                            break;
                    }
                }
            }
            ?>
        </ul>
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
                'prev_text' => __( 'Previous', "traveler-booking" ),
                'next_text' => __( 'Next', "traveler-booking" ),
            );
            echo paginate_links( $args );
            ?>
        </div>
    </div>
</div>
<?php
wp_reset_query();
get_footer();
