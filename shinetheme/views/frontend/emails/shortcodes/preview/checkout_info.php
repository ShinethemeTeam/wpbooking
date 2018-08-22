<?php
do_action('wpbooking_before_checkout_form_data_preview');
?>
<div class=content-row>
	<div class=col-10>
		<label><?php echo esc_html__("Full name:","wp-booking-management-system") ?> </label>
		<p><?php echo esc_html__('Jonathan & Leo','wp-booking-management-system') ?></p>
	</div>
	<div class=col-5>
		<label><?php echo esc_html__('Email confirmation:','wp-booking-management-system') ?> </label>
		<p><?php echo esc_html__('test@gmail.com','wp-booking-management-system') ?></p>
	</div>
	<div class=col-5>
		<label><?php echo esc_html__('Telephone:','wp-booking-management-system') ?> </label>
		<p><?php echo esc_html__('12345678','wp-booking-management-system') ?></p>
	</div>
	<div class=col-12>
		<label><?php echo esc_html__('Address:','wp-booking-management-system') ?> </label>
		<p><?php echo esc_html__('48 Boulevard des Invalides','wp-booking-management-system') ?></p>
	</div>
	<div class=col-5>
		<label><?php echo esc_html__('Postcode / Zip:','wp-booking-management-system') ?> </label>
		<p><?php echo esc_html__('12345','wp-booking-management-system') ?></p>
	</div>
	<div class=col-5>
		<label><?php echo esc_html__('Apt/ Unit:','wp-booking-management-system') ?> </label>
		<p><?php echo esc_html__('8888','wp-booking-management-system') ?></p>
	</div>
    <div class="col-md-12">
        <label><?php echo esc_html__("Special request:","wp-booking-management-system") ?> </label>
        <p>ABC</p>
    </div>
	<div class=col-12>
		<div class=text-center>
			<a class=btn_history href=# ><?php echo esc_html__('Booking History','wp-booking-management-system') ?></a>
		</div>
	</div>
</div>
<?php do_action('wpbooking_end_checkout_form_data_preview');?>
