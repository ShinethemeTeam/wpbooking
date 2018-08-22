<?php
$limit=20;
$offset=$limit*(WPBooking_Input::get('page_number',1)-1);
$order_model=WPBooking_Order_Model::inst();
// Filter
if(WPBooking_Input::get('service_type')){
	$order_model->where('service_type',WPBooking_Input::get('service_type'));
}
if(WPBooking_Input::get('status')){
	$order_model->where('status',WPBooking_Input::get('status'));
}

if(WPBooking_Input::get('payment_status')){
	$order_model->where('payment_status',WPBooking_Input::get('payment_status'));
}
$total=$order_model->select('count(id) as total')->get()->row();

$order_model->limit($limit,$offset)->orderby('id','desc');

//Filter
if(WPBooking_Input::get('service_type')){
	$order_model->where('service_type',WPBooking_Input::get('service_type'));
}
if(WPBooking_Input::get('status')){
	$order_model->where('status',WPBooking_Input::get('status'));
}

if(WPBooking_Input::get('payment_status')){
	$order_model->where('payment_status',WPBooking_Input::get('payment_status'));
}
if(WPBooking_Input::get('keyword')){
	$order_model->like('id',WPBooking_Input::get('keyword'));
}
$order_model->where('partner_id',get_current_user_id());
$rows=$order_model->get()->result();
?>
<table class="wp-list-table widefat fixed striped posts">
	<thead>
	<tr>
		<td id="cb" class="manage-column column-cb check-column select-all">
			<input id="cb-select-all-1" type="checkbox">
		</td>
		<td class="manage-column column-title column-primary sortable">
			<?php echo esc_html__('ID - Customer','wp-booking-management-system') ?>
		</td>
		<td class="manage-column column-title column-primary sortable">
			<?php echo esc_html__('Booking Data','wp-booking-management-system') ?>
		</td>
		<td class="manage-column column-date-booking asc"> <?php echo esc_html__('Booking Date','wp-booking-management-system') ?></td>
		<td class="manage-column column-date asc"> <?php echo esc_html__('Total','wp-booking-management-system') ?></td>
	</tr>
	</thead>

	<tbody>
	<?php if(!empty($rows)){
		foreach($rows as $row){
			$url=get_permalink(wpbooking_get_option('myaccount-page')).'order-detail/'.$row['id'];
			$service_type=$row['service_type'];
			$order=new WB_Order($row['order_id']);
			?>
			<tr>
				<th class="manage-column column-cb check-column ">
					<input  type="checkbox" class="" name="wpbooking_order_item[]" value="<?php echo esc_attr($row['id']) ?>">
				</th>
				<td>
					<a href="<?php echo esc_url($url)  ?>">#<?php echo esc_attr($row['id']) ?></a>
					-
					<a class="service-name" href="<?php echo esc_attr(get_permalink($row['post_id']))?>" target="_blank"><?php echo get_the_title($row['post_id'])?></a>
					- <?php echo esc_html__('by','wp-booking-management-system') ?>
					<?php if($row['customer_id']){
						$user=get_userdata($row['customer_id']);
						if(!$user){
							printf('<label class="label label-warning">%s</label>',esc_html__('Unknown','wp-booking-management-system'));
						}else{
							printf('<label class="label label-info"><a href="%s" target="_blank"> %s</a></label>',get_edit_user_link($row['customer_id']),$user->display_name);
						}
					}else{
						printf('<label class="label label-default">%s</label>',esc_html__('Guest','wp-booking-management-system'));
					} ?>
					<div class="row-actions">
						<span class="edit"><a href="<?php echo esc_url($url)  ?>" title="<?php echo esc_html__('View Detail','wp-booking-management-system')?>"><?php echo esc_html__('Detail','wp-booking-management-system')?></a> | </span>
						<span class="move_trash trash"><a href="<?php echo add_query_arg(array('action'=>'cancel','wpbooking_apply_changes'=>'1','wpbooking_order_item'=>array($row['id']))) ?>" onclick="return confirm('<?php echo esc_html__('Are you want to move to trash?','wp-booking-management-system') ?>')" title="<?php echo esc_html__('Move to trash','wp-booking-management-system')?>"><?php echo esc_html__('Trash','wp-booking-management-system')?></a></span>
					</div>
				</td>
				<td class="booking-data">
					<?php do_action('wpbooking_order_item_information',$row) ?>
					<?php do_action('wpbooking_order_item_information_'.$service_type,$row) ?>
					<?php
					$service_type_obj=WPBooking_Service_Controller::inst()->get_service_type($service_type);
					if($service_type_obj){
						printf('<strong>%s</strong>',$service_type_obj->get_info('label'));
					}
					?>
					<br>
					<?php
					echo wpbooking_order_item_status_html($row['status']);
					?>
					<br>
					<?php
					echo wpbooking_payment_status_html($row['payment_status']);
					?>
					<?php if($gateway_label= wpbooking_get_order_item_used_gateway($row['payment_id'])){
						?>
						<br>-
						<?php
						echo ($gateway_label);
					}?>
				</td>
				<td class="manage-column column-date-booking asc">
					<?php
					echo get_the_time(get_option('date_format').' - '.get_option('time_format'),$row['order_id']);
					?>
				</td>
				<td class="manage-column column-date asc">
					<?php
					echo do_shortcode($order->get_item_total_html($row));
					?>
				</td>
			</tr>
			<?php
		}
	}else{
		?>
		<tr>
			<td colspan="5"><?php echo esc_html__('No Booking Found','wp-booking-management-system') ?></td>
		</tr>
		<?php
	} ?>
	</tbody>
</table>
<div class="wpbooking-paginate">
<?php
echo paginate_links(array(
	'total'=>ceil($total['total']/$limit),
	'current'=>WPBooking_Input::get('page_number',1),
	'format'=>'?page_number=%#%',
	'add_args'=>array()
));
?>
</div>
