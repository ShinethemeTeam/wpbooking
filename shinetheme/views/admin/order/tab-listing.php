<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/14/2016
 * Time: 8:46 AM
 */
global $wpdb;
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$args=array(
	'post_type'=>'wpbooking_order',
	'posts_per_page'=>20,
    'paged' => $paged,
    'status' => 'any'
);

$inject=WPBooking_Query_Inject::inst();
$inject->inject();
if(!empty(WPBooking_Input::get('search_keyword')) && $keyword = WPBooking_Input::get('keyword')){
    $args['s']=$keyword;
}

if($m = WPBooking_Input::get('m')){
    $args['m'] = $m;
}

$table = WPBooking_Order_Model::inst()->get_table_name(false);
$table_prefix = WPBooking_Order_Model::inst()->get_table_name();
$inject->join($table, $table_prefix . '.order_id=' . $wpdb->posts . '.ID');
$inject->groupby($wpdb->posts . '.ID');

if($service_type = WPBooking_Input::get('order_service_type')){
    $inject->where($table_prefix.'.service_type',$service_type);
}
if($status = WPBooking_Input::get('status')){
    $inject->where($table_prefix.'.status',$status);
}
if($payment_method = WPBooking_Input::get('payment_method')){
    $inject->where($table_prefix.'.payment_method',$payment_method);
}

$query=new WP_Query($args);
?>
<form action="<?php echo admin_url('admin.php') ?>" method="get" class="clear">
	<input type="hidden" name="page" value="wpbooking_page_orders">
	<?php if(WPBooking_Input::get('tab')){
		printf('<input type="hidden" name=tab value=%s>',WPBooking_Input::get('tab'));
	} ?>

	<div class="tablenav top">
		<?php if(!WPBooking_Input::get('tab') or WPBooking_Input::get('tab')=='listing'){?>
			<div class="alignleft actions bulkactions">
				<label for="bulk-action-selector-top" class="screen-reader-text"><?php esc_html_e('Select bulk action','wpbooking')?></label>
				<select name="action" id="wpbooking_bulk_edit_order">
					<option value="" selected ><?php esc_html_e('Bulk Actions','wpbooking') ?></option>
					<option value="onhold_booking"><?php esc_html_e('Mark as On-Hold','wpbooking')  ?></option>
					<option value="complete_booking"><?php esc_html_e('Mark as Completed','wpbooking')  ?></option>
					<option value="cancel_booking"><?php esc_html_e('Mark as Cancelled','wpbooking')  ?></option>
					<option value="refunded_booking"><?php esc_html_e('Mark as Refunded','wpbooking')  ?></option>
					<option value="trash"><?php esc_html_e('Move to Trash','wpbooking')  ?></option>
					<option value="permanently_delete"><?php esc_html_e('Permanently Delete','wpbooking')  ?></option>
				</select>
				<input type="submit" id="wpbooking_apply_order" name="wpbooking_apply_changes" class="button action" value="Apply">
			</div>
		<?php } ?>
		<div class="alignleft actions">
			<select name="order_service_type" class="postform">
                <option value=""><?php esc_html_e('All Service Types','wpbooking') ?></option>
                <?php
                $types = WPBooking_Service_Controller::inst()->get_service_types();
                foreach($types as $k=>$v){
                    printf('<option value="%s" %s>%s</option>',$k,selected(WPBooking_Input::get('order_service_type'),$k,FALSE),$v->get_info('label'));
                } ?>
			</select>
			<select name="status" class="postform">
                <option value=""><?php esc_html_e('All Status','wpbooking') ?></option>
                <?php
                $status = WPBooking_Config::inst()->item('order_status');
                if(!empty($status) && is_array($status)) {
                    foreach ($status as $k => $v) {
                        printf('<option value="%s" %s>%s</option>', $k, selected(WPBooking_Input::get('status'), $k, FALSE), $v['label']);
                    }
                }?>
			</select>
            <?php
            wpbooking_get_months_dropdown_html('wpbooking_order');
            ?>
            <select name="payment_method" class="postform">
                <option value=""><?php esc_html_e('All Payment Method','wpbooking') ?></option>
                <?php
                $getaway = WPBooking_Payment_Gateways::inst()->get_gateways();

                if(!empty($getaway) && is_array($getaway)) {
                    foreach ($getaway as $k => $v) {
                        printf('<option value="%s" %s>%s</option>', $k, selected(WPBooking_Input::get('payment_method'), $k, FALSE), $v->get_info('label'));
                    }
                }?>
            </select>
			<input type="submit" id="doaction" class="button action" value="<?php esc_html_e('Filter','wpbooking') ?>">
		</div>
		<!--			End .actions-->

		<div class="tablenav-pages">
			<p class="search-box">
				<label class="screen-reader-text" for="post-search-input"><?php esc_html_e('Search Order','wpbooking') ?></label>
				<input type="search" name="keyword" value="<?php echo WPBooking_Input::get('keyword') ?>">
				<input type="submit" name="search_keyword" class="button" value="<?php esc_html_e('Search Order','wpbooking') ?>"></p>
		</div>
	</div>
	<!--		End top-->

		<table class="wp-list-table widefat fixed striped posts">
			<thead>
			<tr>
				<th id="cb" class="manage-column column-cb check-column">
					<input id="cb-select-all-1" type="checkbox">
				</th>
				<th class="manage-column column-id sortable">
                    <p class="id"><?php esc_html_e('ID','wpbooking') ?></p>
                    <p class="status"><?php esc_html_e('Status - Payment Method','wpbooking') ?></p>
                    <p class="customer"><?php esc_html_e('Customer Information','wpbooking') ?></p>
                </th>
				<th class="manage-column column-primary asc"> <span class="wb-left-label"><?php esc_html_e('Booking Information','wpbooking') ?></span><span class="wb-right-label"><?php echo esc_html__('Total (Deposit/Remain) ','wpbooking').'('.WPBooking_Currency::get_current_currency('currency').')'?></span></th>
				<th class="wb-column-empty"></th>
                <th class="manage-column column-customer asc"> <?php esc_html_e('Booking Date','wpbooking') ?></th>
				<th class="manage-column column-service asc"> <?php esc_html_e('Service Type','wpbooking') ?></th>
			</tr>
			</thead>

			<tbody>
			<?php if($query->have_posts()){
				while($query->have_posts()){
					$query->the_post();
					$url_edit=add_query_arg(array('page'=>'wpbooking_page_orders','action' => 'onhold_booking','wpbooking_order_item'=>get_the_ID()),admin_url('admin.php'));
					$url_complete=add_query_arg(array('page'=>'wpbooking_page_orders','action'=>'complete_booking','wpbooking_order_item'=>get_the_ID()),admin_url('admin.php'));
					$url_cancel=add_query_arg(array('page'=>'wpbooking_page_orders','action'=>'cancel_booking','wpbooking_order_item'=>get_the_ID()),admin_url('admin.php'));
					$url_refund=add_query_arg(array('page'=>'wpbooking_page_orders','action'=>'refunded_booking','wpbooking_order_item'=>get_the_ID()),admin_url('admin.php'));
					$order=new WB_Order(get_the_ID());
					$service_type=$order->get_service_type();
                    $order_data = $order->get_order_data();
                    $room_data = $order->get_order_room_data();
					?>
					<tr>
						<th class="manage-column column-cb check-column">
							<input  type="checkbox" name="wpbooking_order_item[]" value="<?php echo esc_attr(get_the_ID()) ?>">
						</th>
						<td class="wb-column-action">
                            <div class="id">
							<a href="<?php echo esc_url($url_edit)  ?>">#<?php echo esc_attr(get_the_ID()) ?></a>
                            </div>
                            <div class="status">
                                <?php
                                echo $order->get_status_html();
                                echo '<br>';
                                echo '<span class="payment">'.$order->get_payment_gateway().'</span>';
                                ?>
                            </div>
                            <div class="customer">
                                <a href="<?php echo esc_url(add_query_arg( 'user_id', $order->get_customer('id'), self_admin_url( 'user-edit.php' ) )); ?>"><strong><?php echo $order->get_customer('full_name'); ?></strong></a><br>
                                <span class="wb-button-customer"><em><?php echo esc_html__('details ','wpbooking'); ?></em><span class="caret"></span></span>
                                <ul class="none wb-customer-detail">
                                    <li><strong><?php echo esc_html__('Email address :','wpbooking'); ?></strong><br><?php echo esc_attr($order->get_customer('email')); ?></li>
                                    <li><strong><?php echo esc_html__('Phone : ','wpbooking'); ?></strong><br><?php echo esc_attr($order->get_customer('phone')); ?></li>
                                    <li><strong><?php echo esc_html__('Address : ','wpbooking'); ?></strong><br><?php echo esc_attr($order->get_customer('apt')); ?> <?php echo esc_attr($order->get_customer('address')); ?></li>
                                </ul>
                            </div>
							<div class="wb-row-actions none">
								<span class="on_hold"><a href="<?php echo esc_url($url_edit)  ?>" title="<?php esc_html_e('View this item','wpbooking')?>"><?php esc_html_e('On Hold','wpbooking')?></a> </span>
								<span class="complete"><a href="<?php echo esc_url($url_complete)  ?>" title="<?php esc_html_e('Complete this item','wpbooking')?>"><?php esc_html_e('Complete','wpbooking')?></a> </span>
								<span class="cancel"><a href="<?php echo esc_url($url_cancel)  ?>" title="<?php esc_html_e('Cancel this item','wpbooking')?>"><?php esc_html_e('Cancel','wpbooking')?></a> </span>
								<span class="refund"><a href="<?php echo esc_url($url_refund)  ?>" title="<?php esc_html_e('Refund this item','wpbooking')?>"><?php esc_html_e('Refund','wpbooking')?></a> </span>
								<span class="move_trash trash"><a href="<?php echo add_query_arg(array('action'=>'trash','wpbooking_apply_changes'=>'1','wpbooking_order_item'=>array(get_the_ID()))) ?>" onclick="return confirm('<?php esc_html_e('Are you want to move to trash?','wpbooking') ?>')" title="<?php esc_html_e('Move to trash','wpbooking')?>"><?php esc_html_e('Trash','wpbooking')?></a> </span>
								<span class="resend_email">
									<a href="<?php echo add_query_arg(array('wpbooking_resend_email'=>'true','order_id'=>get_the_ID())) ?>" title="<?php esc_html_e('Resend Email this item','wpbooking')?>">
										<?php esc_html_e('Resend Mail','wpbooking')?></a>
								</span>

							</div>
						</td>
						<td class="wb-booking-information">
                            <span class="wb-booking-info">
                                <a href="<?php echo esc_url(get_permalink($order_data['post_id'])); ?>" target="_blank"><strong><?php echo get_the_title($order_data['post_id']); ?></strong></a><br>
                                <span class="wp-button-booking"><em><?php echo esc_html__('details ','wpbooking'); ?></em><span class="caret"></span></span>
                            </span>
                            <span class="wb-price-total">
                                <?php
                                echo '<strong>'.WPBooking_Currency::format_money($order->get_total()).'</strong><br>';
                                if($dr_price = $order->get_deposit_and_remain_html()){
                                    echo '<span class="wb-deposit-remain">( '.$dr_price.' )</span>';
                                }
                                ?>
                            </span>
                            <ul class="none wb-booking-detail">
                                <li><?php echo esc_html__('Rooms: ','wpbooking') ?></li>
                                <?php
                                foreach($room_data as $key => $value){
                                    $extra_fees = unserialize($value['extra_fees']);
                                    $price = WPBooking_Currency::format_money($value['price']);
                                    echo '<li class="wb-room-item"><span class="wb-room-name"><strong>'.get_the_title($value['room_id']).' x'.$value['number'].'</strong></span>';
                                    echo '<span class="wb-room-price">'.$price.'</span>';
                                    echo '</li>';
                                    if(!empty($extra_fees['extra_service']['data']) && is_array($extra_fees['extra_service']['data'])){
                                        foreach($extra_fees['extra_service']['data'] as $k => $v){
                                            echo '<li class="wb-room-item"><span class="wb-extra-title">'.$v['title'].' x'.$v['quantity'].'</span>';
                                            echo '<span class="wb-extra-price">'.WPBooking_Currency::format_money($v['price']).'</span>';
                                            echo '</li>';
                                        }
                                    }
                                }
                                ?>
                            </ul>
						</td>
                        <td class="wb-column-empty"></td>
						<td class="manage-column column-date asc">
							<?php
							echo $order->get_booking_date();
							?>
						</td>
						<td class="manage-column column-date asc">
                            <?php
                            $service_type_obj = WPBooking_Service_Controller::inst()->get_service_type($service_type);
                            if($service_type_obj){
                                echo ($service_type_obj->get_info('label'));
                            }
                            ?>
						</td>
					</tr>
					<?php
				}
			}else{
				?>
				<tr>
					<td colspan="10"><?php esc_html_e('No Booking Found','wpbooking') ?></td>
				</tr>
				<?php
			} ?>
			</tbody>
		</table>
		<div class="wpbooking-paginate">
			<?php

			echo paginate_links(array(
				'base'=>admin_url('admin.php').'%_%',
				'total'=>$query->max_num_pages,
				'current'=>WPBooking_Input::get('page_number',1),
				'format'=>'?page_number=%#%',
				'add_args'=>array()
			));

			wp_reset_postdata();

            $inject->clear();
			?>
		</div>

	<div class="clear"></div>
</form>
