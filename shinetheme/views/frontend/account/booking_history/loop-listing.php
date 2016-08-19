<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/14/2016
 * Time: 8:46 AM
 */

$limit = 20;
$offset = $limit * (WPBooking_Input::get('page_number', 1) - 1);
$order_model = WPBooking_Order_Model::inst();
// Filter
if (WPBooking_Input::get('service_type')) {
	$order_model->where('service_type', WPBooking_Input::get('service_type'));
}
if (WPBooking_Input::get('status')) {
	$order_model->where('status', WPBooking_Input::get('status'));
}

if (WPBooking_Input::get('payment_status')) {
	$order_model->where('payment_status', WPBooking_Input::get('payment_status'));
}
$total = $order_model->select('count(id) as total')->get()->row();

$order_model->limit($limit, $offset)->orderby('id', 'desc');

//Filter
if (WPBooking_Input::get('service_type')) {
	$order_model->where('service_type', WPBooking_Input::get('service_type'));
}
if (WPBooking_Input::get('status')) {
	$order_model->where('status', WPBooking_Input::get('status'));
}

if (WPBooking_Input::get('payment_status')) {
	$order_model->where('payment_status', WPBooking_Input::get('payment_status'));
}
if (WPBooking_Input::get('keyword')) {
	$order_model->like('id', WPBooking_Input::get('keyword'));
}
$order_model->where('customer_id', get_current_user_id());
//$rows=$order_model->get()->result();
$arg = array(
	'post_type'  => 'wpbooking_order',
	'meta_key'   => 'customer_id',
	'meta_value' => get_current_user_id()
);

$order_query = new WP_Query($arg);
?>
<table class="wpbooking-account-table">
	<thead>
	<tr>
		<td id="cb" class="column-min">
			<?php esc_html_e('Order') ?>
		</td>
		<td class="manage-column column-title column-primary sortable">
			<?php esc_html_e('Property', 'wpbooking') ?>
		</td>
		<td class="manage-column column-title column-primary sortable">
			<?php esc_html_e('Date Created', 'wpbooking') ?>
		</td>
		<td class="manage-column column-date asc"> <?php esc_html_e('Total', 'wpbooking') ?></td>
		<td class="manage-column column-date asc">&nbsp;</td>
	</tr>
	</thead>

	<tbody>
	<?php if ($order_query->have_posts()) {
		while($order_query->have_posts()) {
			$order_query->the_post();
			$url = get_permalink(wpbooking_get_option('myaccount-page')) . 'order-detail/' . get_the_ID();
			$order = new WB_Order(get_the_ID());

			$order_items=$order->get_items();
			?>
			<tr>
				<td class="manage-column column-min ">
					<a href="<?php echo esc_url($url) ?>" class="order-number">#<?php the_ID()  ?></a>
				</td>
				<td>
					<ul class="order-items">
					<?php if(!empty($order_items)){
						foreach($order_items as $item){
							printf('<li><a class="" href="%s" target="_blank">%s</a></li>',get_permalink($item['post_id']),get_the_title($item['post_id']));
						}
					} ?>
					</ul>
				</td>
				<td class="booking-data">
					<?php the_time(get_option('date_format'))?>
				</td>
				<td class="manage-column column-date-booking asc">
					<?php
					echo WPBooking_Currency::format_money($order->get_total())
					?>
				</td>
				<td class="manage-column column-date asc">
					<a href="<?php echo esc_url($url) ?>" class="wb-btn wb-btn-blue"><?php esc_html_e('View','wpbooking')?></a>
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