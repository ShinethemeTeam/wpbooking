<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/21/2016
 * Time: 4:33 PM
 */
?>
<table width="100%" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
		<th><?php _e('No','wpbooking') ?></th>
		<th class="review-order-item-info"><?php _e('Service','wpbooking')?></th>
		<th class="review-order-item-total"><?php _e('Price','wpbooking')?></th>
	</tr>
	</thead>
	<tbody>
		<tr>
			<td class="small-td" width="5">1</td>
			<td class="review-order-item-info">
				<h4 class="service-name"><?php esc_html_e('Service A','wpbooking') ?></h4>
			</td>
			<td class="review-order-item-total">
				<p class="cart-item-price">$ 80.00</p>
			</td>
		</tr>
		<tr>
			<td class="small-td" width="5">2</td>
			<td class="review-order-item-info">
				<h4 class="service-name"><?php esc_html_e('Service B','wpbooking') ?></h4>
			</td>
			<td class="review-order-item-total">
				<p class="cart-item-price">$ 20.00</p>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2"><?php _e('Total','wpbooking')?></td>
			<td>$ 100.00</td>
		</tr>
		<tr>
			<td colspan="2"><?php _e('Pay Amount','wpbooking')?></td>
			<td>$ 50.00</td>
		</tr>
		<?php do_action('wpbooking_review_order_footer_preview') ?>
	</tfoot>
</table>
