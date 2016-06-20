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
    'post_type' => 'wpbooking_service' ,
    's'         => '' ,
    'paged'     => $paged,
    'posts_per_page'     => 3,
);
$service_type = '';
$is_page = get_the_ID();
$list_page_search = apply_filters("wpbooking_add_page_archive_search",array());
if(!empty($list_page_search[$is_page]))
{
    $service_type = $list_page_search[$is_page];
}
$my_query = WPBooking_Service::inst()->query($args,$service_type);
echo wpbooking_load_view('wrap/start');
?>
<div class="wpbooking-container">
    <div class="row">
        <!--<div class="col-md-3">
            <?php /*echo get_sidebar(); */?>
        </div>-->
        <div class="col-md-12 ">
			<div class="wpbooking-loop-wrap">
				<ul class="wpbooking-loop-items">
					<?php
					global $wp_query;
					if($my_query->have_posts()){
						while ( $my_query->have_posts() ) {
							$my_query->the_post();
							switch($service_type){
								case "room":
									?>
									<li <?php post_class('content-item') ?>>

										<div class="row">
											<div class="col-md-3">
												<?php if(has_post_thumbnail() and get_the_post_thumbnail()){
													the_post_thumbnail( array( 200, 150 ) );
												}?>


											</div>
											<div class="col-md-6 ">
												<a href="<?php echo get_the_permalink() ?>" class="">
													<h5 class="booking-item-title"><?php the_title(); ?></h5>
												</a>
												<?php if($address = get_post_meta(get_the_ID(),'address',true)){ ?>
													<span class="info-item service-address">
                                                    <i class="fa fa-map-marker"></i>
														<?php echo get_post_meta(get_the_ID(),'address',true); ?>
                                                </span>
												<?php } ?>
												<?php
												$taxonomy = WPBooking_Admin_Taxonomy_Controller::inst()->get_taxonomies();
												if(!empty($taxonomy)) {
													foreach( $taxonomy as $k => $v ) {
														if(in_array($service_type,$v['service_type'])){
															$terms = get_the_terms( get_the_ID() , $v['name'] );
															if(!empty( $terms )) {
																echo "<div class='taxonomy-item info-item'>";
																echo "".$v['label'].": ";
																$list = array();
																foreach( $terms as $key2 => $value2 ) {
																	$list []=  esc_html( $value2->name ) ;
																}
																echo implode(', ',$list);
																echo "</div>";
															}
														}
													}
												}?>
												<div class="service-rating-review">
													<?php
													echo wpbooking_service_rate_to_html();
													?>
												</div>

											</div>
											<div class="col-md-3">
												<?php echo wpbooking_service_price_html() ?>
											</div>
										</div>
									</li>
									<?php
									break;
							}
						}
					}else{
						printf('<h3>%s</h3>',esc_html__('Found nothing match your search','wpbooking'));
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
						'prev_text' => __( 'Previous', "wpbooking" ),
						'next_text' => __( 'Next', "wpbooking" ),
					);
					echo paginate_links( $args );
					?>
				</div>
			</div>
        </div>
    </div>
</div>
<?php
echo wpbooking_load_view('wrap/end');
wp_reset_query();
get_footer();
