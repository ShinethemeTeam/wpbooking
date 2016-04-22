<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/8/2016
 * Time: 4:59 PM
 */
echo traveler_get_message();
$booking=Traveler_Booking::inst();
$order_items=$booking->get_order_items(get_the_ID());

$checkout_form_data=$booking->get_order_form_datas();
do_action('traveler_before_order_content');
?>
<h3><?php _e('Your Order','traveler-booking')?></h3>
<table class="order-information-table">
	<thead>
	<tr>
		<th class="review-order-item-info" colspan="2" valign="top"><?php _e('Service','traveler-booking')?></th>
		<th class="review-order-item-total"><?php _e('Total','traveler-booking')?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($order_items as $key=>$value)
	{
		$service_type=$value['service_type'];
		?>
		<tr valign="top">
			<td>
				<?php echo get_the_post_thumbnail($value['post_id']) ?>
			</td>
			<td class="review-order-item-info">
				<h4 class="service-name"><a href="<?php echo get_permalink($value['post_id'])?>" target="_blank"><?php echo get_the_title($value['post_id'])?></a></h4>
				<?php do_action('traveler_order_item_information',$value) ?>
				<?php do_action('traveler_order_item_information_'.$service_type,$value) ?>
			</td>
			<td class="review-order-item-total">
				<p class="cart-item-price"><?php echo Traveler_Currency::format_money($booking->get_order_item_total($value),$value['currency']); ?></p>
			</td>
		</tr>
		<?php
	}?>
	</tbody>
	<tfooter>
		<tr>
			<td colspan="2"><?php _e('Total','traveler-booking')?></td>
			<td><?php echo Traveler_Currency::format_money($booking->get_order_total(get_the_ID()));?></td>
		</tr>
		<?php do_action('traveler_order_information_footer') ?>
	</tfooter>
</table>
<?php
	do_action('traveler_before_checkout_form_data');

	if(!empty($checkout_form_data) and is_array($checkout_form_data)){?>
	<div class="checkout-form-data">
		<h3><?php _e('Your Information','traveler-booking')?></h3>

		<ul class="checkout-form-list">
			<?php foreach($checkout_form_data as $key=>$value){
				?>
				<li class="form-item">
					<span class="form-item-title">
						<?php echo $value['title'] ?>:
					</span>
					<span class="form-item-value">
						<?php echo $value['value'] ?>
					</span>
				</li>
				<?php
			} ?>
		</ul>
	</div>
	<?php }

	do_action('traveler_end_checkout_form_data');

do_action('traveler_end_order_content');