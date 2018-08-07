<?php
do_action('wpbooking_before_checkout_form_data_preview');
?>
<div class=content-row>
	<div class=col-10>
		<label><?php esc_html_e("Full name:","wp-booking-management-system") ?> </label>
		<p><?php esc_html_e('Jonathan & Leo','wp-booking-management-system') ?></p>
	</div>
	<div class=col-5>
		<label><?php esc_html_e('Email confirmation:','wp-booking-management-system') ?> </label>
		<p><?php esc_html_e('test@gmail.com','wp-booking-management-system') ?></p>
	</div>
	<div class=col-5>
		<label><?php esc_html_e('Telephone:','wp-booking-management-system') ?> </label>
		<p><?php esc_html_e('12345678','wp-booking-management-system') ?></p>
	</div>
	<div class=col-12>
		<label><?php esc_html_e('Address:','wp-booking-management-system') ?> </label>
		<p><?php esc_html_e('48 Boulevard des Invalides','wp-booking-management-system') ?></p>
	</div>
	<div class=col-5>
		<label><?php esc_html_e('Postcode / Zip:','wp-booking-management-system') ?> </label>
		<p><?php esc_html_e('12345','wp-booking-management-system') ?></p>
	</div>
	<div class=col-5>
		<label><?php esc_html_e('Apt/ Unit:','wp-booking-management-system') ?> </label>
		<p><?php esc_html_e('8888','wp-booking-management-system') ?></p>
	</div>
    <div class="col-md-12">
        <label><?php esc_html_e("Special request:","wp-booking-management-system") ?> </label>
        <p>ABC</p>
    </div>
	<div class=col-12>
		<div class=text-center>
			<a class=btn_history href=# ><?php esc_html_e('Booking History','wp-booking-management-system') ?></a>
		</div>
	</div>
</div>
<?php do_action('wpbooking_end_checkout_form_data_preview');?>
