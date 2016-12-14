<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/5/2016
 * Time: 10:06 AM
 */
$booking=WPBooking_Checkout_Controller::inst();
$cart=$booking->get_cart();
?>
<h5 class="checkout-form-title"><?php _e('Your booking details','wpbooking')?></h5>
	<?php
	$post_id=$cart['post_id'];
	$service=wpbooking_get_service($cart['post_id']);
	$featured=$service->get_featured_image();
	$service_type=$cart['service_type'];
		?>
		<div class="review-order-item">
			<div class="review-order-item-info">
				<div class="review-order-item-img">
					<a href="<?php echo get_permalink($post_id)?>" target="_blank">
						<?php echo wp_kses($featured['thumb'],array('img'=>array('src'=>array(),'alt'=>array())))?>
					</a>
				</div>
				<div class="review-order-item-title">
					<h4 class="service-name"><a href="<?php echo get_permalink($cart['post_id'])?>" target="_blank"><?php echo get_the_title($cart['post_id'])?></a></h4>
					<div class="wb-hotel-star">
						<?php
						$service->get_star_rating_html();
						?>
					</div>
					<?php if($address=$service->get_address()){
						printf('<p class="service-address"><i class="fa fa-map-marker"></i> %s</p>',$address);
					} ?>

					<?php do_action('wpbooking_review_after_address',$cart) ?>
					<?php do_action('wpbooking_review_after_address_'.$service_type,$cart) ?>
				</div>


			</div>
            <?php do_action('wpbooking_review_checkout_item_information',$cart) ?>
			<?php do_action('wpbooking_review_checkout_item_information_'.$service_type,$cart) ?>
		</div>
<?php do_action('wpbooking_review_order_footer') ?>
