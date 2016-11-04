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
	$service=new WB_Service($cart['post_id']);
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
					<?php if($address=$service->get_address()){
						printf('<p class="service-address"><i class="fa fa-map-marker"></i> %s</p>',$address);
					} ?>
					<p class="review-order-item-price"></p>
					<div class="review-order-item-form-to">
						<span><?php esc_html_e("From:","wpbooking") ?> </span> <?php echo date(get_option('date_format'),$cart['check_in_timestamp']) ?> &nbsp
						<span><?php esc_html_e("To:","wpbooking") ?> </span><?php echo date(get_option('date_format'),$cart['check_out_timestamp']) ?> &nbsp
						<?php
						$diff=$cart['check_out_timestamp'] - $cart['check_in_timestamp'];
						$diff = $diff / (60 * 60 * 24);
						if($diff > 1){
							echo sprintf(esc_html__('(%s days)','wpbooking'),$diff);
						}else{
							echo sprintf(esc_html__('(%s day)','wpbooking'),$diff);
						}
						?>
					</div>
				</div>
			</div>
            <?php do_action('wpbooking_review_checkout_item_information',$cart) ?>
			<?php do_action('wpbooking_review_checkout_item_information_'.$service_type,$cart) ?>
		</div>
<?php do_action('wpbooking_review_order_footer') ?>
