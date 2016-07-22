<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/20/2016
 * Time: 3:42 PM
 */
?>

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
									the_post_thumbnail( apply_filters('wpbooking_archive_loop_image_size',FALSE,$service_type,get_the_ID()) );
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
