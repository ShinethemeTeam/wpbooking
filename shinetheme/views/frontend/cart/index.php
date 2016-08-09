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
						<?php _e('Property items','wpbooking') ?>
					</th>
					<th class="col-cart-item-type text-center"><?php _e('Property type','wpbooking')?></th>
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
										<a class="delete-cart-item" onclick="return confirm('<?php esc_html_e('Do you want to delete it?','wpbooking') ?>')" href="<?php echo esc_url(add_query_arg(array('delete_cart_item'=>$key),$booking->get_cart_url())) ?>">
											<i class="fa fa-times"></i>
										</a></td>
									<td class="col-cart-item-img"><a href="<?php echo get_permalink($post_id)?>" target="_blank"><?php echo wp_kses($featured['thumb'],array('img'=>array('src'=>array(),'alt'=>array())))?></a></td>
									<td class="col-cart-item-info">
										<h4 class="service-name"><a href="<?php echo get_permalink($post_id)?>" target="_blank"><?php echo get_the_title($post_id)?></a></h4>
										<?php do_action('wpbooking_cart_item_information',$value) ?>
										<?php do_action('wpbooking_cart_item_information_'.$service_type,$value) ?>
									</td>
									<td class="col-cart-item-type text-center">
										<?php echo esc_html($service->get_type_name()) ?>
									</td>
									<td class="col-cart-item-price text-center">
										<?php echo $booking->get_cart_item_total_html($value); ?>
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
		<div class="wpbooking-cart-summary">
			<ul class="cart-summary-details">
				<li><?php printf(__('Total: %s','wpbooking'),'<strong>'.WPBooking_Currency::format_money($booking->get_cart_total()).'</strong>') ?></li>
				<?php do_action('wpbooking_cart_summary_details',$carts) ?>
			</ul>

			<div class="cart-summary-actions">
				<a href="<?php echo esc_url($booking->get_checkout_url())?>" class="wb-btn wb-btn-blue"><?php _e('Checkout Now','wpbooking')?> <i class="fa fa-long-arrow-right"></i></a>
			</div>
		</div>
	</div>
	<?php }?>
</div>
