<?php
$booking=WPBooking_Order::inst();
$value=$order_item;
$order=new WB_Order($order_id);
$checkout_form_data=$order->get_checkout_form_data();
?>
<div class="wrap">
	<h1><?php echo esc_html__('Order Item Detail','wp-booking-management-system') ?></h1>
	<?php echo wpbooking_get_admin_message() ?>
	<div id="poststuff">
		<div class="wpbooking-order-information wpbooking-order-detail postbox ">
			<h3 class="hndle"><?php echo esc_html__('Order Items','wp-booking-management-system') ?></h3>
				<div class="inside">
					<?php
					$service_type=$value['service_type'];
					?>
					<div class="review-order-item-info">
						<a class="service-name" href="<?php echo get_permalink($value['post_id'])?>" target="_blank"><?php echo esc_html(get_the_title($value['post_id']))?></a>
						<?php do_action('wpbooking_order_item_information',$value) ?>
						<?php do_action('wpbooking_order_item_information_'.$service_type,$value) ?>
					</div>
					<div class="review-order-item-total">
						<p class="cart-item-price"><?php echo esc_html__('Total','wp-booking-management-system'); echo WPBooking_Currency::format_money($order->get_item_total($value)); ?></p>
					</div>
					<?php
					do_action('wpbooking_before_checkout_form_data');

					if(!empty($checkout_form_data) and is_array($checkout_form_data)){?>
						<hr>
						<div class="checkout-form-data">
							<h3><?php echo esc_html__('Billing Details','wp-booking-management-system')?></h3>

							<ul class="checkout-form-list">
								<?php foreach($checkout_form_data as $key=>$value){
									$value_html= '';
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
					<?php }?>

					<hr>
					<h3><?php echo esc_html__('Method of Payment','wp-booking-management-system') ?></h3>
					<?php
					$selected_gateway=get_post_meta($order_id,'wpbooking_selected_gateway',true);
                    $selected_gateway=WPBooking_Payment_Gateways::inst()->get_gateway($selected_gateway);
                    if($selected_gateway){
                        echo esc_html($selected_gateway->get_info('label'));
                    }else
                    {
                        echo esc_html($selected_gateway);
                    }
					?>
					<?php do_action('wpbooking_end_checkout_form_data');?>
				</div>
		</div>
	</div>
</div>