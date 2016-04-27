<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/5/2016
 * Time: 10:06 AM
 */
$booking=Traveler_Booking::inst();

$cart=$booking->get_cart();
?>
<h3><?php _e('Your Order','traveler-booking')?></h3>
<table class="review-order-table">
	<thead>
		<tr>
			<th class="review-order-item-info"><?php _e('Service','traveler-booking')?></th>
			<th class="review-order-item-total"><?php _e('Total','traveler-booking')?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($cart as $key=>$value)
	{
		$service_type=$value['service_type'];
		?>
		<tr>
			<td class="review-order-item-info">
				<h4 class="service-name"><a href="<?php echo get_permalink($value['post_id'])?>" target="_blank"><?php echo get_the_title($value['post_id'])?></a></h4>
				<?php do_action('traveler_review_order_item_information',$value) ?>
				<?php do_action('traveler_review_order_item_information_'.$service_type,$value) ?>
			</td>
			<td class="review-order-item-total">
				<p class="cart-item-price"><?php echo $booking->get_cart_item_total_html($value); ?></p>
			</td>
		</tr>
		<?php
	}?>
	</tbody>
	<tfooter>
		<tr>
			<td><?php _e('Total','traveler-booking')?></td>
			<td><?php echo Traveler_Currency::format_money($booking->get_cart_total());?></td>
		</tr>
		<tr>
			<td><?php _e('Pay Amount','traveler-booking')?></td>
			<td><?php echo Traveler_Currency::format_money($booking->get_cart_pay_amount());?></td>
		</tr>
		<?php do_action('traveler_review_order_footer') ?>
	</tfooter>
</table>
