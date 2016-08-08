<?php
/**
 * Created by PhpStorm.
 * User: me664
 * Date: 4/26/16
 * Time: 5:15 PM
 */
$booking=WPBooking_Order::inst();
$items=$booking->get_cart();
?>
<div class="wpbooking-cart-widget-content">
<?php
if(empty($items)){
	printf('<p class="alert alert-warning">%s</p>',__('Your cart is currently empty','wpbooking'));
}else{
	?>
		<ul class="cart-widget-items">
			<?php
			foreach($items as $key=>$value){
				$service_type=$value['service_type'];
				?>
				<li>
					<div class="cart-item-img">
						<?php echo get_the_post_thumbnail($value['post_id'],array(90,90)) ?>
					</div>
					<div class="cart-item-content">
						<h5 class="service-title"><a href="<?php echo get_permalink($value['post_id']) ?>" target="_blank" ><?php echo get_the_title($value['post_id']) ?></a></h5>
						<span class="price">
							<?php echo $booking->get_cart_item_total_html($value) ?>
						</span>
						<div class="service-meta">
							<?php do_action('wpbooking_cart_item_information',$value) ?>
							<?php do_action('wpbooking_cart_item_information_'.$service_type,$value) ?>
						</div>
					</div>
				</li>
				<?php
			}

			?>
		</ul>
		<hr>
		<div class="cart-widget-total">
			<p class="total"><?php _e('Total:','wpbooking') ?> <?php echo $booking->get_cart_total() ?></p>
		</div>

		<div class="cart-widget-actions">
			<a href="<?php echo esc_url($booking->get_checkout_url()) ?>" class="btn btn-success"><?php _e('Checkout Now','wpbooking') ?></a>
		</div>
	<?php
}
?>
</div>