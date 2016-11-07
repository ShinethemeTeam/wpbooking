<?php
$limit = 20;
$offset = $limit * (WPBooking_Input::get('page_number', 1) - 1);
$inject=WPBooking_Query_Inject::inst();
global $wpdb;
$inject->inject();
$inject->join('wpbooking_order',$wpdb->prefix.'posts.ID = '.$wpdb->prefix.'wpbooking_order.order_id');
$arg = array(
	'post_type'  => 'wpbooking_order',
);
$inject->where($wpdb->prefix.'wpbooking_order.user_id',get_current_user_id());
if ($service_type = WPBooking_Input::request('wpbooking_service_type')) {
	$inject->where($wpdb->prefix.'wpbooking_order.service_type',$service_type);
}
if ($status = WPBooking_Input::get('wpbooking_status')) {
	$arg['post_status'] = $status;
}
$order_query = new WP_Query($arg);
$inject->clear();
?>
<table class="wpbooking-account-table">
	<thead>
	<tr>
		<td class="" width="5%">
			<span><?php esc_html_e('ID','wpbooking') ?></span>
		</td>
		<td width="40%">
			<span class="left-10"><?php esc_html_e('SERVICE', 'wpbooking') ?></span>
		</td>
		<td class="text-center" width="20%">
			<span><?php esc_html_e('STATUS - PAYMENT', 'wpbooking') ?></span>
		</td>
		<td width="23%" class="text-center">
			<?php esc_html_e('TOTAL', 'wpbooking') ?><br>
			<?php esc_html_e('( DEPOSIT / REMAIN )', 'wpbooking') ?><br>
			( <?php echo WPBooking_Currency::get_current_currency('currency') ?> )
		</td>
	</tr>
	</thead>

	<tbody>
	<?php if ($order_query->have_posts()) {
		while($order_query->have_posts()) {
			$order_query->the_post();

			$order = new WB_Order(get_the_ID());
			$order_data=$order->get_order_data();

			$service=new WB_Service($order_data['post_id']);

			$payment_method = $order_data['payment_method'];
			$status = $order_data['status'];

			?>
			<tr>
				<td class="manage-column column-min ">
					<a href="<?php echo get_permalink(get_the_ID()) ?>" class="order-number">#<?php the_ID()  ?></a>
				</td>
				<td>
					<h4 class="service-name">
						<a href="<?php echo get_permalink($order_data['post_id'])?>" target="_blank">
							<?php echo get_the_title($order_data['post_id'])?>
						</a>
					</h4>
					<div class="item-form-to">
						<span><?php esc_html_e("From:","wpbooking") ?> </span> <?php echo date(get_option('date_format'),$order_data['check_in_timestamp']) ?> &nbsp
						<span><?php esc_html_e("To:","wpbooking") ?> </span><?php echo date(get_option('date_format'),$order_data['check_out_timestamp']) ?> &nbsp
						<?php
						$diff=$order_data['check_out_timestamp'] - $order_data['check_in_timestamp'];
						$diff = $diff / (60 * 60 * 24);
						if($diff > 1){
							echo sprintf(esc_html__('(%s days)','wpbooking'),$diff);
						}else{
							echo sprintf(esc_html__('(%s day)','wpbooking'),$diff);
						}
						?>
					</div>
					<div class="link-details">
						<a href="<?php echo get_permalink(get_the_ID()) ?>" ><?php esc_html_e('details','wpbooking') ?> <i class="fa fa-caret-right" aria-hidden="true"></i></a>
					</div>
				</td>
				<td class="booking-data">
					<?php
					$user = WPBooking_User::inst();
					echo balanceTags($user->get_status_booking_history_html($status));
					?>

					<span class="payment_method"><?php echo balanceTags($user->get_payment_gateway($payment_method)) ?></span>
				</td>
				<td class="booking-price text-center">
					<div class="total">
						<?php
						$total_price = WPBooking_Currency::format_money($order->get_total(array('without_deposit'=>false)));
						echo balanceTags($total_price);
						?>
					</div>
					<?php if(!empty($order_data['deposit_price'])){ ?>
					<div class="sub-total">
						(
						<?php echo WPBooking_Currency::format_money($order_data['deposit_price']); ?>
						/
						<?php
						$remain_price =$total_price - $order_data['deposit_price'];
						echo WPBooking_Currency::format_money($order_data['deposit_price']);
						?>
						)
					</div>
					<?php } ?>
				</td>
			</tr>
			<?php
		}
	} else {
		?>
		<tr>
			<td colspan="5"><?php esc_html_e('No Booking Found', 'wpbooking') ?></td>
		</tr>
		<?php
	} ?>
	</tbody>
</table>
<div class="wpbooking-paginate">
	<?php
	echo paginate_links(array(
		'total'    => $order_query->max_num_pages,
		'current'  => WPBooking_Input::get('page_number', 1),
		'format'   => '?page_number=%#%',
		'add_args' => array()
	));
	?>
</div>
<?php wp_reset_postdata(); ?>