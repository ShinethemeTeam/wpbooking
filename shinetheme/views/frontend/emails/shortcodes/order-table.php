<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/21/2016
 * Time: 4:33 PM
 */
$order_id=WPBooking()->get('order_id');
$items=WPBooking()->get('items',array());
?>
<table width="100%" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
		<th class="review-order-item-info"><?php _e('Service','wpbooking')?></th>
		<th class="review-order-item-total"><?php _e('Total','wpbooking')?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($items as $key=>$value)
	{
		$service_type=$value['service_type'];
		?>
		<tr>
			<td class="review-order-item-info">
				<h4 class="service-name"><a href="<?php echo get_permalink($value['post_id'])?>" target="_blank"><?php echo get_the_title($value['post_id'])?></a></h4>
				<?php do_action('wpbooking_order_item_information',$value) ?>
				<?php do_action('wpbooking_order_item_information_'.$service_type,$value) ?>
			</td>
			<td class="review-order-item-total">
				<p class="cart-item-price"><?php echo WPBooking_Currency::format_money($booking->get_order_item_total($value)); ?></p>
			</td>
		</tr>
		<?php
	}?>
	</tbody>
	<tfoot>
		<tr>
			<td><?php _e('Total','wpbooking')?></td>
			<td><?php echo WPBooking_Currency::format_money($booking->get_order_total($order_id));?></td>
		</tr>
		<tr>
			<td><?php _e('Pay Amount','wpbooking')?></td>
			<td><?php echo WPBooking_Currency::format_money($booking->get_order_pay_amount($order_id));?></td>
		</tr>
		<?php do_action('wpbooking_review_order_footer') ?>
	</tfoot>
</table>
