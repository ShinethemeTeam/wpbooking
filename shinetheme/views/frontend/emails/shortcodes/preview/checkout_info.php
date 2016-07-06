<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/3/2016
 * Time: 3:16 PM
 */
do_action('wpbooking_before_checkout_form_data_preview');
?>
	<div class="checkout-form-data">
		<h3><?php _e('Your Information','wpbooking')?></h3>

		<ul class="checkout-form-list">
			<li class="form-item">
				<span class="form-item-title">
					<?php esc_html_e('Field A','wpbooking') ?>:
				</span>
				<span class="form-item-value">
					<?php esc_html_e('Value A','wpbooking') ?>
				</span>
			</li>
			<li class="form-item">
				<span class="form-item-title">
					<?php esc_html_e('Field A','wpbooking') ?>:
				</span>
				<span class="form-item-value">
					<?php esc_html_e('Value A','wpbooking') ?>
				</span>
			</li>
			<li class="form-item">
				<span class="form-item-title">
					<?php esc_html_e('Field A','wpbooking') ?>:
				</span>
				<span class="form-item-value">
					<?php esc_html_e('Value A','wpbooking') ?>
				</span>
			</li>
		</ul>
	</div>
<?php
do_action('wpbooking_end_checkout_form_data_preview');