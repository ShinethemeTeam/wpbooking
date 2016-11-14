<?php
do_action('wpbooking_before_checkout_form_data_preview');
?>
<div class=content-row>
	<div class=col-10>
		<label><?php esc_html_e("Full name:","wpbooking") ?> </label>
		<p><?php esc_html_e('Jonathan & Leo','wpbooking') ?></p>
	</div>
	<div class=col-5>
		<label><?php esc_html_e('Email confirmation:','wpbooking') ?> </label>
		<p><?php esc_html_e('test@gmail.com','wpbooking') ?></p>
	</div>
	<div class=col-5>
		<label><?php esc_html_e('Telephone:','wpbooking') ?> </label>
		<p><?php esc_html_e('12345678','wpbooking') ?></p>
	</div>
	<div class=col-12>
		<label><?php esc_html_e('Address:','wpbooking') ?> </label>
		<p><?php esc_html_e('48 Boulevard des Invalides','wpbooking') ?></p>
	</div>
	<div class=col-5>
		<label><?php esc_html_e('Postcode / Zip:','wpbooking') ?> </label>
		<p><?php esc_html_e('12345','wpbooking') ?></p>
	</div>
	<div class=col-5>
		<label><?php esc_html_e('Apt/ Unit:','wpbooking') ?> </label>
		<p><?php esc_html_e('8888','wpbooking') ?></p>
	</div>
    <div class="col-md-12">
        <label><?php esc_html_e("Special request:","wpbooking") ?> </label>
        <p>ABC</p>
    </div>
	<div class=col-12>
		<div class=text-center>
			<a class=btn_history href=# ><?php esc_html_e('Booking History','wpbooking') ?></a>
		</div>
	</div>
</div>
<?php do_action('wpbooking_end_checkout_form_data_preview');?>
