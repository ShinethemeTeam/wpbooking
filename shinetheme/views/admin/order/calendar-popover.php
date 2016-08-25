<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/16/2016
 * Time: 3:49 PM
 */
$service_type=$item['service_type'];
?>
<ul class="calendar-order-popover-information">
	<li class="customer-information">
		<?php
		if($item['customer_id'] and $user=get_user_meta($item['customer_id'],'display_name')){
			printf('<b>%s</b>: %s',esc_html__('Customer','wpbooking'),$user);
		}else{

			printf('<b>%s</b>: %s',esc_html__('Customer','wpbooking'),esc_html__('Guest','wpbooking'));
		}
		?>
	</li>
	<li class="service-type">
		<?php if($service_type and $service_type_obj=WPBooking_Service_Controller::inst()->get_service_type($service_type)){
			printf('<b>%s</b>: %s',esc_html__('Service Type','wpbooking'),$service_type_obj['label']);
		} ?>
	</li>
	<li class="booking-data">
		<?php do_action('wpbooking_order_item_information',$item) ?>
		<?php do_action('wpbooking_order_item_information_'.$service_type,$item) ?>
	</li>

	<li class="order-status">
		<?php
		printf(esc_html__('Status: %s','wpbooking'),wpbooking_order_item_status_html($item['status']));
		?>
	</li>
</ul>