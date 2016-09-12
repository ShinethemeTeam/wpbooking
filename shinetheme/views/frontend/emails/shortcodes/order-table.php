<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/21/2016
 * Time: 4:33 PM
 */
$order_id=WPBooking()->get('order_id');
if(!$order_id) return;
$order=new WB_Order($order_id);
$items=$order->get_items();
?>
<table width="100%" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
		<th><?php _e('No','wpbooking') ?></th>
		<th class="review-order-item-info"><?php _e('Service','wpbooking')?></th>
		<th class="review-order-item-total"><?php _e('Price','wpbooking')?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;
	foreach($items as $key=>$value)
	{
		$service_type=$value['service_type'];
		?>
		<tr>
			<td class="small-td" width="5"><?php echo esc_html($i) ?></td>
			<td class="review-order-item-info">
				<h4 class="service-name"><a href="<?php echo get_permalink($value['post_id'])?>" target="_blank"><?php echo get_the_title($value['post_id'])?></a></h4>
				<?php do_action('wpbooking_order_item_information',$value,array('for_email'=>TRUE)) ?>
				<?php do_action('wpbooking_order_item_information_'.$service_type,$value,array('for_email'=>TRUE)) ?>
				<?php do_action('wpbooking_email_order_item_information_'.$service_type,$value) ?>
			</td>
			<td class="review-order-item-total">
				<p class="cart-item-price"><?php echo WPBooking_Currency::format_money($order->get_item_total($value)); ?></p>
			</td>
		</tr>
		<?php
		$i++;
	}?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3" class="text-right">
				<div class="review-cart-total">
				<span class="total-title">
					<?php _e('Total Price:', 'wpbooking') ?>
				</span>
					<span class="total-amount"><?php echo WPBooking_Currency::format_money($order->get_total(array(
							'without_deposit'        => true,
							'without_tax'            => true,
							'without_extra_price'    => true,
							'without_addition_price' => false,

						))); ?></span>

					<?php if ($price = $order->get_extra_price()) { ?>
						<span class="total-title">
					<?php _e('Extra Price:', 'wpbooking') ?>
				</span>
						<span class="total-amount"><?php echo WPBooking_Currency::format_money($price); ?></span>
					<?php } ?>

					<?php if ($price = $order->get_addition_price()) { ?>
						<span class="total-title">
					<?php _e('Addition:', 'wpbooking') ?>
				</span>
						<span class="total-amount"><?php echo WPBooking_Currency::format_money($price); ?></span>
					<?php } ?>

					<?php if ($price = $order->get_discount_price()) { ?>
						<span class="total-title">
					<?php _e('Discount:', 'wpbooking') ?>
				</span>
						<span class="total-amount">-<?php echo WPBooking_Currency::format_money($price); ?></span>
					<?php } ?>

					<?php if ($price = $order->get_tax_price()) { ?>
						<span class="total-title">
					<?php _e('Tax:', 'wpbooking') ?>
				</span>
						<span class="total-amount"><?php echo WPBooking_Currency::format_money($price); ?></span>
					<?php } ?>

					<span class="total-line"></span>
					<?php $total_amount = $order->get_total(array('without_deposit'=>true)); $discount=$order->get_discount_price();
					$total_amount-=$discount;
					?>
					<span class="total-title">
					<?php _e('Total Amount:', 'wpbooking') ?>
				</span>
					<span class="total-amount big"><?php echo WPBooking_Currency::format_money($total_amount); ?></span>

					<?php if ($price = $order->get_paynow_price()) { ?>
						<span class="total-title">
						<?php _e('Deposit:', 'wpbooking') ?>
					</span>
						<span class="total-amount big"><?php echo WPBooking_Currency::format_money($price); ?></span>

						<?php if ($total_amount - $price > 0) {
							?>
							<span class="total-title">
							<?php _e('Remain:', 'wpbooking') ?>
						</span>
							<span
								class="total-amount big"><?php echo WPBooking_Currency::format_money($total_amount - $price); ?></span>
							<?php
						} ?>
					<?php } ?>
				</div>
			</td>
		</tr>
		<?php do_action('wpbooking_review_order_footer') ?>
	</tfoot>
</table>
