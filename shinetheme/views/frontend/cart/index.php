<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/1/2016
 * Time: 3:54 PM
 */
$booking=Traveler_Booking::inst();

?>
<div class="traveler-cart-wrap">
	<div class="traveler-cart-table-col">
		<table class="traveler-cart-table">
			<thead>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2">
						<?php _e('Service Information','traveler-booking') ?>
					</td>
					<td><?php _e('Actions')?></td>
				</tr>
				<tbody>
					<?php
					$carts=$booking->get_cart();
					if(!empty($carts)){
						foreach($carts as $key=>$value) {
							$post_id=$value['post_id'];
							$service_type=$value['service_type'];
							?>
								<tr>
									<td>&nbsp;</td>
									<td><a href="<?php echo get_permalink($post_id)?>" target="_blank"><?php echo get_the_post_thumbnail($post_id)?></a></td>
									<td>
										<h4><a href="<?php echo get_permalink($post_id)?>" target="_blank"><?php echo get_the_title($post_id)?></a></h4>
										<?php do_action('traveler_cart_item_information',$value) ?>
										<?php do_action('traveler_cart_item_information_'.$service_type,$value) ?>
									</td>
									<td>
										<a href="<?php echo esc_url(add_query_arg(array('delete_cart_item'=>$key),$booking->get_cart_url())) ?>">
											<?php _e('delete','traveler-booking')?>
										</a>
									</td>
								</tr>

							<?php
						}
					}else{
						?>
						<tr>
							<td colspan="4"><?php _e('Sorry, Your cart is currently empty','traveler-booking') ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</thead>
		</table>
	</div>
	<div class="traveler-cart-summary-col">
		<?php if(!empty($carts)){?>
		<div class="traveler-cart-summary">
			<h3><?php _e('Cart Summary','traveler-booking') ?></h3>
			<ul class="cart-summary-details">
				<li><?php printf(__('Total: %f','traveler-booking'),$booking->get_cart_total()) ?></li>
				<li><?php printf(__('Pay Amount: %f','traveler-booking'),$booking->get_cart_pay_amount()) ?></li>
				<?php do_action('traveler_cart_summary_details',$carts) ?>
			</ul>

			<div class="cart-summary-actions">
				<a href="<?php echo esc_url($booking->get_checkout_url())?>"><?php _e('Checkout Now')?></a>
			</div>
		</div>
		<?php }?>
	</div>
</div>
