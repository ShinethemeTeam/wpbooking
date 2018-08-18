<?php
$order_id=WPBooking()->get('order_id');
do_action('wpbooking_before_checkout_form_data_preview');
?>
<div class=content-row>
	<?php
	$fist_name = get_post_meta($order_id,'wpbooking_user_first_name',true);
	$last_name = get_post_meta($order_id,'wpbooking_user_last_name',true);
	$full_name = $fist_name.' '.$last_name;
	if(!empty($full_name)){?>
		<div class="col-10">
			<label><?php echo esc_html__("Full name:","wp-booking-management-system") ?> </label>
			<p><?php echo esc_html($full_name) ?></p>
		</div>
	<?php } ?>
	<?php if(!empty($email = get_post_meta($order_id,'wpbooking_user_email',true))){ ?>
		<div class="col-5">
			<label><?php echo esc_html__("Email confirmation:","wp-booking-management-system") ?> </label>
			<p><?php echo esc_html($email) ?></p>
		</div>
	<?php } ?>
	<?php if(!empty($phone = get_post_meta($order_id,'wpbooking_user_phone',true))){ ?>
		<div class="col-5">
			<label><?php echo esc_html__("Telephone:","wp-booking-management-system") ?> </label>
			<p><?php echo esc_html($phone) ?></p>
		</div>
	<?php } ?>
	<?php if(!empty($address = get_post_meta($order_id,'wpbooking_user_address',true))){ ?>
		<div class="col-10">
			<label><?php echo esc_html__("Address:","wp-booking-management-system") ?> </label>
			<p><?php echo esc_html($address) ?></p>
		</div>
	<?php } ?>
	<?php if(!empty($postcode_zip = get_post_meta($order_id,'wpbooking_user_postcode',true))){ ?>
		<div class="col-5">
			<label><?php echo esc_html__("Postcode / Zip:","wp-booking-management-system") ?> </label>
			<p><?php echo esc_html($postcode_zip) ?></p>
		</div>
	<?php } ?>
	<?php if(!empty($apt_unit = get_post_meta($order_id,'wpbooking_user_apt_unit',true))){ ?>
		<div class="col-5">
			<label><?php echo esc_html__("Apt/ Unit:","wp-booking-management-system") ?> </label>
			<p><?php echo esc_html($apt_unit) ?></p>
		</div>
	<?php } ?>
	<?php if(!empty($special_request = get_post_meta($order_id,'wpbooking_user_special_request',true))){ ?>
		<div class="col-10">
			<label><?php echo esc_html__("Special request:","wp-booking-management-system") ?> </label>
			<p><?php echo esc_html($special_request) ?></p>
		</div>
	<?php } ?>
	<div class=col-12>
		<div class=text-center>
			<?php
			$page_account = wpbooking_get_option('myaccount-page');
			if(!empty($page_account)){
				$link_page = get_permalink($page_account);
				?>
				<a href="<?php echo esc_url($link_page) ?>tab/booking_history/" class="btn_history"><?php echo esc_html__("Booking History","wp-booking-management-system") ?></a>
			<?php } ?>
		</div>
	</div>
</div>
<?php do_action('wpbooking_end_checkout_form_data_preview');?>
