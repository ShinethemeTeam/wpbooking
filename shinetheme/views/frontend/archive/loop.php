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
			$service=new WB_Service();
			switch($service_type){
				case "room":
					?>
					<li <?php post_class('loop-item') ?>>
						<div class="content-item">
							<div class="service-gallery">
								<a href="#" class="service-fav <?php if($service->check_favorite()) echo 'active'; ?>"><i class="fa fa-heart"></i></a>
								<div class="service-gallery-slideshow">
									<?php
									$gallery=$service->get_gallery();
									if(!empty($gallery)){
										foreach($gallery as $media){
											printf('<div class="slider-item">%s</div>',$media['gallery']);
										}
									}
									?>
								</div>
								<div class="service-author">
									<a href="#"><?php echo ($service->get_author('avatar')) ?></a>
								</div>
							</div>
							<div class="service-content">
								<h3 class="service-title"><a href="<?php the_permalink()?>"><?php the_title()?></a></h3>
								<div class="service-address-rate">
									<?php $address=$service->get_address();
									if($address){
									?>
									<div class="service-address">
										<i class="fa fa-map-marker"></i> <?php echo esc_html($address) ?>
									</div>
									<?php }?>
									<div class="service-rate">
										<?php
										$service->get_rate_html();
										?>
									</div>
								</div>
								<?php do_action('wpbooking_after_service_address_rate',get_the_ID(),$service->get_type(),$service) ?>
								<div class="service-price-book-now">
									<div class="service-price">
										<?php
										$service->get_price_html();
										?>
									</div>
									<div class="service-book-now">
										<a class="btn wb-btn-primary" href="<?php the_permalink() ?>"><?php esc_html_e('Book Now','wpbooking') ?></a>
									</div>
								</div>
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
