<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/8/2016
 * Time: 4:59 PM
 */
echo wpbooking_get_message();
$order=new WB_Order(get_the_ID());
$booking=WPBooking_Order::inst();
$order_items=$order->get_items();

$checkout_form_data=$order->get_checkout_form_data();
do_action('wpbooking_before_order_content');

?>
<div class="wpbooking-order-detail-page">
	<div class="wpbooking-thankyou-message text-center">
		<i class="fa fa-check-circle success-icon"></i>
		<h3>
		<?php
		if($customer_name=$order->get_customer('name')){
			printf(esc_html__('%s, your order has been received!','wpbooking'),$customer_name);
		}else{
			esc_html_e('Thank you, your order has been received!','wpbooking');
		}
		?>
		</h3>
		<p><?php if($email=$order->get_customer_email()){
				printf(esc_html__('Booking details has been send to %s','wpbooking'),$email);
			} ?></p>
	</div>
	<div class="order-head-info">
		<div class="head-info">
			<span class="head-info-title"><?php  esc_html_e('ID:','wpbooking')?></span>
			<span class="head-info-content hl">#<?php the_ID() ?></span>
		</div>
		<div class="head-info">
			<span class="head-info-title"><?php  esc_html_e('Booking Status:','wpbooking')?></span>
			<span class="head-info-content"><?php echo ($order->get_status_html()) ?></span>
		</div>
		<div class="head-info">
			<span class="head-info-title"><?php  esc_html_e('Booking Date:','wpbooking')?></span>
			<span class="head-info-content"><?php echo esc_html($order->get_booking_date()) ?></span>
		</div>
		<div class="head-info">
			<span class="head-info-title"><?php  esc_html_e('Total:','wpbooking')?></span>
			<span class="head-info-content"><?php echo WPBooking_Currency::format_money($order->get_total(array('without_deposit'=>true))) ?></span>
		</div>
		<div class="head-info">
			<span class="head-info-title"><?php  esc_html_e('Payment method:','wpbooking')?></span>
			<span class="head-info-content"><?php echo esc_html($order->get_payment_gateway()) ?></span>
		</div>
	</div>
	<?php do_action('wpbooking_before_order_information_table',$order) ?>
<table class="order-information-table">
	<thead>
	<tr>
		<th class="review-order-item-info" valign="top"><?php _e('Service Items','wpbooking')?></th>
		<th class="review-order-item-type"><?php _e('Service Type','wpbooking')?></th>
		<th class="review-order-item-total"><?php _e('Total','wpbooking')?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($order_items as $key=>$value)
	{
		$service_type=$value['service_type'];
		$order_item=new WB_Order_Item($value['id']);
		?>
		<tr valign="top">
			<td class="review-order-item-info">
				<h4 class="service-name"><a href="<?php echo get_permalink($value['post_id'])?>" target="_blank"><?php echo get_the_title($value['post_id'])?></a></h4>
				<?php do_action('wpbooking_order_item_information',$value) ?>
				<?php do_action('wpbooking_order_item_information_'.$service_type,$value) ?>
			</td>
			<td class="review-order-item-type">
				<?php echo esc_html($order_item->get_type_name()) ?>
			</td>
			<td class="review-order-item-total">
				<p class="cart-item-price"><?php echo ($order_item->get_total_html()) ?></p>
			</td>
		</tr>
		<?php
	}?>
	</tbody>
	<tfoot>
		<tr>
			<td >&nbsp;</td>
			<td colspan="2" class="text-right">
				<div class="review-cart-total">
				<span class="total-title">
					<?php _e('Total Price:', 'wpbooking') ?>
				</span>
					<span class="total-amount"><?php echo WPBooking_Currency::format_money($order->get_total(array(
							'without_deposit'        => true,
							'without_tax'            => true,
							'without_extra_price'    => true,
							'without_addition_price' => true,
							'without_discount'=>true

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


					<?php if ($price = $order->get_tax_price()) { ?>
						<span class="total-title">
							<?php _e('Tax:', 'wpbooking') ?>
						</span>

						<span class="total-amount"><?php echo WPBooking_Currency::format_money($price); ?></span>
					<?php } ?>


					<?php if ($price = $order->get_discount_price()) { ?>
						<span class="total-title">
						<?php _e('Discount:', 'wpbooking') ?>
					</span>
						<span class="total-amount">-<?php echo WPBooking_Currency::format_money($price); ?></span>
					<?php } ?>

					<span class="total-line"></span>
					<?php $total_amount = $order->get_total(array('without_deposit'=>true));
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
		<?php do_action('wpbooking_order_information_footer') ?>
	</tfoot>
</table>
<?php
	do_action('wpbooking_before_checkout_form_data');

	if(!empty($checkout_form_data) and is_array($checkout_form_data)){?>
	<div class="checkout-form-data">
		<h3 class="section-title"><?php _e('Customer Information','wpbooking')?></h3>

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

	do_action('wpbooking_end_checkout_form_data');

	do_action('wpbooking_end_order_content');
	?>
</div>
