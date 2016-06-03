<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/3/2016
 * Time: 3:16 PM
 */
$order_id=WPBooking()->get('order_id');
if(!$order_id) return;

$checkout_form_data=$booking->get_order_form_datas($order_id);
	do_action('wpbooking_before_checkout_form_data');

	if(!empty($checkout_form_data) and is_array($checkout_form_data)){?>
		<div class="checkout-form-data">
			<h3><?php _e('Your Information','wpbooking')?></h3>

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