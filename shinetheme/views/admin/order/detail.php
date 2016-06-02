<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/2/2016
 * Time: 5:28 PM
 */
global $post;
$order_id=$post->ID;
$booking=WPBooking_Order::inst();

$items=$booking->get_order_items($order_id);
$checkout_form_data=$booking->get_order_form_datas();

?>
<div class="wpbooking-order-information">
	<table class="wpbooking-order-table" width="100%" cellpadding="0" cellspacing="0">
		<thead>
		<tr>
			<th width="30px"><?php esc_html_e('No','wpbooking') ?></th>
			<th class="review-order-item-info"><?php _e('Service','wpbooking')?></th>
			<th class="review-order-item-total"><?php _e('Total','wpbooking')?></th>
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
				<td><?php echo esc_html($i) ?></td>
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
		}
		$i++;
		?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="2"><?php _e('Total','wpbooking')?> <?php echo WPBooking_Currency::format_money($booking->get_order_total($order_id));?></td>
		</tr>
		<tr>
			<td colspan="2"><?php _e('Pay Amount','wpbooking')?> <?php echo WPBooking_Currency::format_money($booking->get_order_pay_amount($order_id));?></td>
		</tr>
		</tfoot>
	</table>

	<?php
	do_action('wpbooking_before_checkout_form_data');

	if(!empty($checkout_form_data) and is_array($checkout_form_data)){?>
	<hr>
	<div class="checkout-form-data">
		<h3><?php _e('Checkout Information','wpbooking')?></h3>

		<ul class="checkout-form-list">
			<?php foreach($checkout_form_data as $key=>$value){
				$value_html= WPBooking_Admin_Form_Build::inst()->get_form_field_data($value);
				if($value_html){
				?>
				<li class="form-item">
					<span class="form-item-title">
						<?php echo do_shortcode($value['title']) ?>:
					</span>
					<span class="form-item-value">
						<?php echo do_shortcode($value_html) ?>
					</span>
				</li>
				<?php
				}
			} ?>
		</ul>
	</div>
	<?php }
	do_action('wpbooking_end_checkout_form_data');?>
</div>