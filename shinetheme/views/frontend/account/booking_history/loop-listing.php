<?php
$limit = 10;
$page_number = WPBooking_Input::get('page_number', 1);
$inject=WPBooking_Query_Inject::inst();
global $wpdb;
$inject->inject();
$inject->join('wpbooking_order',$wpdb->prefix.'posts.ID = '.$wpdb->prefix.'wpbooking_order.order_id');
$arg = array(
	'post_type'  => 'wpbooking_order',
	'posts_per_page'=>$limit,
	'paged'=>$page_number,
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
			<span><?php echo esc_html__('ID','wp-booking-management-system') ?></span>
		</td>
		<td width="40%">
			<span class="left-10"><?php echo esc_html__('SERVICE', 'wp-booking-management-system') ?></span>
		</td>
		<td class="text-center" width="20%">
			<span><?php echo esc_html__('STATUS - PAYMENT', 'wp-booking-management-system') ?></span>
		</td>
		<td width="23%" class="text-center">
			<?php echo esc_html__('TOTAL', 'wp-booking-management-system') ?><br>
			<?php echo esc_html__('(DEPOSIT / REMAIN)', 'wp-booking-management-system') ?><br>
			(<?php echo WPBooking_Currency::get_current_currency('currency') ?>)
		</td>
        <?php
        do_action('wpbooking_after_listing_title');
        ?>
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
					<?php do_action('wpbooking_order_history_after_service_name',$order_data) ?>
					<?php do_action('wpbooking_order_history_after_service_name_'.$service_type,$order_data) ?>
					<div class="link-details">
						<a href="<?php echo add_query_arg(array('wpbooking_detail'=>'true'), get_permalink(get_the_ID()) ) ?>" >
                            <?php echo esc_html__('Details','wp-booking-management-system') ?>
							<i class="fa fa-caret-down" aria-hidden="true"></i>
                        </a>
					</div>
				</td>
				<td class="booking-data">
					<?php
					$user = WPBooking_User::inst();
					echo do_shortcode($user->get_status_booking_history_html($status));
					?>
					<span class="payment_method"><?php echo do_shortcode($user->get_payment_gateway($payment_method)) ?></span>
				</td>
				<td class="booking-price text-center">
					<div class="total">
						<?php
						$total_price = $order->get_total(array('without_deposit'=>false));
						echo do_shortcode(WPBooking_Currency::format_money($total_price));
						?>
					</div>
					<?php if(!empty($order_data['deposit_price'])){ ?>
					<div class="sub-total">
						(<?php echo WPBooking_Currency::format_money($order_data['deposit_price']); ?>
						/
						<?php
						$remain_price = $total_price - $order_data['deposit_price'];
						echo WPBooking_Currency::format_money($remain_price);
						?>)
					</div>
					<?php } ?>
				</td>
                <?php
                do_action('wpbooking_in_loop_listing_detail', get_the_ID());
                ?>
			</tr>
			<?php
		}
	} else {
		?>
		<tr>
			<td colspan="5"><?php echo esc_html__('No Booking Found', 'wp-booking-management-system') ?></td>
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