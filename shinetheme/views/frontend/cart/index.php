<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/1/2016
 * Time: 3:54 PM
 */
$booking=WPBooking_Order::inst();
echo wpbooking_get_message();
?>
<div class="wpbooking-cart-wrap">
	<div class="wpbooking-cart-table-col">
		<table class="wpbooking-cart-table">
			<thead>
				<tr>
					<th class="small-col text-center">&nbsp;</th>
					<th colspan="2" class="col-cart-item-info">
						<?php _e('Service items','wpbooking') ?>
					</th>
					<th class="col-cart-item-type text-center"><?php _e('Service type','wpbooking')?></th>
					<th class="col-cart-item-price text-center"><?php _e('Total','wpbooking')?></th>
				</tr>

			</thead>
			<tbody>
					<?php
					$carts=$booking->get_cart();
					$current=0;
					if(!empty($carts)){
						foreach($carts as $key=>$value) {
							$post_id=$value['post_id'];
							$service_type=$value['service_type'];
							$service=new WB_Service($post_id);
							$featured=$service->get_featured_image();
							?>
								<tr>
									<td class="text-center">
										<a class="delete-cart-item" onclick="return confirm('<?php esc_html_e('Do you want to delete it?','wpbooking') ?>')" href="<?php echo esc_url(add_query_arg(array('delete_item_hotel_room'=>$key),$booking->get_cart_url())) ?>">
											<i class="fa fa-times"></i>
										</a></td>
									<td class="col-cart-item-img"><a href="<?php echo get_permalink($post_id)?>" target="_blank"><?php echo wp_kses($featured['thumb'],array('img'=>array('src'=>array(),'alt'=>array())))?></a></td>
									<td class="col-cart-item-info">
										<h4 class="service-name"><a href="<?php echo get_permalink($post_id)?>" target="_blank"><?php echo get_the_title($post_id)?></a></h4>
										<?php if($service->get_address()){
											printf('<p class="service-address"><i class="fa fa-map-marker"></i> %s</p>',$service->get_address());
										} ?>
										<?php do_action('wpbooking_cart_item_information',$value,array('current_page'=>'cart')) ?>
										<?php do_action('wpbooking_cart_item_information_'.$service_type,$value,array('current_page'=>'cart')) ?>
									</td>
									<td class="col-cart-item-type text-center">
										<?php echo esc_html($service->get_type_name()) ?>
									</td>
									<td class="col-cart-item-price text-center">
										<?php echo ($booking->get_cart_item_total_html($value,array('without_deposit'=>true))); ?>
									</td>
								</tr>

							<?php
							$current++;
						}
					}else{
						?>
						<tr>
							<td colspan="5"><?php _e('Sorry, Your cart is currently empty','wpbooking') ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
		</table>
	</div>
	<?php if(!empty($carts)){?>
	<div class="wpbooking-cart-summary-col">
		<div class="col-coupon-form">
			<div class="wpbooking-coupon-form">
				<div class="wb-message"></div>
				<input type="text" class="form-control wb-coupon-code"
					   placeholder="<?php esc_html_e('Enter coupon code', 'wpbooking') ?>">
				<a type="button" class="wb-btn wb-btn-default wb-coupon-apply"><?php esc_html_e('Apply', 'wpbooking') ?>
					<i class="loading fa fa-spinner fa-pulse fa-fw"></i></a>
			</div>
		</div>
		<div class="col-cart-summary">

			<div class="wpbooking-cart-summary">
				<?php echo wpbooking_load_view('cart/cart-total-box') ?>
				<div class="cart-summary-actions">
					<a href="<?php echo esc_url($booking->get_checkout_url())?>" class="wb-btn wb-btn-blue"><?php _e('Checkout Now','wpbooking')?> <i class="fa fa-long-arrow-right"></i></a>
				</div>
			</div>
		</div>
	</div>
	<?php }?>
</div>
