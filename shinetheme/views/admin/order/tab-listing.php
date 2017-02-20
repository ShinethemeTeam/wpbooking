<?php
global $wpdb;
$paged = ( WPBooking_Input::get('page_number') ) ? WPBooking_Input::get('page_number') : 1;
$args=array(
	'post_type'=>'wpbooking_order',
	'posts_per_page'=>20,
    'paged' => $paged,
    'status' => 'any'
);
$inject=WPBooking_Query_Inject::inst();
$inject->inject();
if(!empty(WPBooking_Input::get('search_keyword')) && $keyword = WPBooking_Input::get('keyword')){
    $args['p']= str_replace('#','',$keyword);
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

if($author_id = WPBooking_Input::get('author_id')){
	$inject->where($table_prefix.'.author_id',$author_id);
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
					<option value="" selected="selected" ><?php esc_html_e('Bulk Actions','wpbooking') ?></option>
					<option value="onhold_booking"><?php esc_html_e('Mark as On-Hold','wpbooking')  ?></option>
					<option value="complete_booking"><?php esc_html_e('Mark as Completed','wpbooking') ?></option>
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
                <option value=""><?php esc_html_e('All Types of Service','wpbooking') ?></option>
                <?php
                $types = WPBooking_Service_Controller::inst()->get_service_types();
                foreach($types as $k=>$v){
                    printf('<option value="%s" %s>%s</option>',$k,selected(WPBooking_Input::get('order_service_type'),$k,FALSE),$v->get_info('label'));
                } ?>
			</select>
			<select name="status" class="postform">
                <option value=""><?php esc_html_e('All Statuses','wpbooking') ?></option>
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
                <option value=""><?php esc_html_e('All Methods of Payment','wpbooking') ?></option>
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
				<input type="search" name="keyword" value="<?php echo WPBooking_Input::get('keyword') ?>" placeholder="<?php echo esc_html__('ID','wpbooking') ?>">
				<input type="submit" name="search_keyword" class="button" value="<?php esc_html_e('Search Order','wpbooking') ?>"></p>
		</div>
	</div>
	<!--		End top-->

		<table class="wp-list-table widefat striped posts">
			<thead>
			<tr>
				<th id="cb" class="manage-column column-cb check-column">
					<input id="cb-select-all-1" type="checkbox">
				</th>
				<th class="manage-column column-id sortable">
                    <p class="id"><?php esc_html_e('ID','wpbooking') ?></p>
                    <p class="status"><?php esc_html_e('Status – Method of Payment','wpbooking') ?></p>
                    <p class="customer"><?php esc_html_e('Customer Information','wpbooking') ?></p>
                </th>
				<th class="manage-column column-primary"> <span class="wb-left-label"><?php esc_html_e('Booking Information','wpbooking') ?></span><span class="wb-right-label"><?php echo esc_html__('Total (Deposit/Remain) ','wpbooking').'('.WPBooking_Currency::get_current_currency('currency').')'?></span></th>
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
                                echo do_shortcode($order->get_status_html());
                                echo '<br>';
                                echo '<span class="payment">'.do_shortcode($order->get_payment_gateway()).'</span>';
                                ?>
                            </div>
                            <div class="customer">
                                <?php
                                $customer_html = '<a href="'.esc_url(add_query_arg( 'user_id', $order->get_customer('id'), self_admin_url( 'user-edit.php' ) )).'"><strong>'.esc_attr($order->get_customer('full_name')).'</strong></a><br>';
                                $customer_html .= '<span class="wb-button-customer"><em>'.esc_html__('details ','wpbooking').'</em><span class="caret"></span></span>';
                                $customer_html .= '<ul class="none wb-customer-detail">';
                                $customer_html .= '<li><strong>'.esc_html__('Email address: ','wpbooking').'</strong><br>'.esc_attr($order->get_customer('email')).'</li>';
                                $customer_html .= '<li><strong>'.esc_html__('Phone: ','wpbooking').'</strong><br>'.esc_attr($order->get_customer('phone')).'</li>';
                                $customer_html .= '<li><strong>'.esc_html__('Address: ','wpbooking').'</strong><br>'.esc_attr($order->get_customer('apt')).' '.esc_attr($order->get_customer('address')).'</li>';
                                $customer_html .= '</ul>';

                                echo apply_filters('wpbooking_admin_order_customer_html', $customer_html, get_the_ID());
                                ?>
                            </div>
							<div class="wb-row-actions none">
								<span class="complete"><a href="<?php echo esc_url($url_complete)  ?>" title="<?php esc_html_e('Complete this item','wpbooking')?>"><?php esc_html_e('Complete','wpbooking')?></a> </span>
								<span class="move_trash trash"><a href="<?php echo add_query_arg(array('action'=>'trash','wpbooking_apply_changes'=>'1','wpbooking_order_item'=>array(get_the_ID()))) ?>" onclick="return confirm('<?php esc_html_e('Do you want to move to trash?','wpbooking') ?>')" title="<?php esc_html_e('Move to trash','wpbooking')?>"><?php esc_html_e('Trash','wpbooking')?></a> </span>
								<span class="resend_email">
									<a href="<?php echo add_query_arg(array('wpbooking_resend_email'=>'true','order_id'=>get_the_ID())) ?>" title="<?php esc_html_e('Resend Email to this item','wpbooking')?>">
										<?php esc_html_e('Resend Mail','wpbooking')?></a>
								</span>
                                <?php
                                do_action('wpbooking_after_list_button_action', get_the_ID());
                                ?>
							</div>
						</td>
						<td class="wb-booking-information">
                            <span class="wb-booking-info">
                                <a href="<?php echo esc_url(get_permalink($order_data['post_id'])); ?>" target="_blank"><strong><?php echo get_the_title($order_data['post_id']); ?></strong></a><br>
                                <span class="wp-button-booking"><em><?php echo esc_html__('details ','wpbooking'); ?></em><span class="caret"></span></span>
                            </span>
                            <span class="wb-price-total">
                                <?php
                                echo '<strong>'.WPBooking_Currency::format_money($order_data['price']).'</strong><br>';
                                if($dr_price = $order->get_deposit_and_remain_html()){
                                    echo '<span class="wb-deposit-remain">( '.do_shortcode($dr_price).' )</span>';
                                }
                                ?>
								<?php do_action("wpbooking_admin_after_order_detail_total_price",get_the_ID(),$order_data) ?>
                            </span>
                            <ul class="none wb-booking-detail">
                                <?php
                                if($service_type == 'tour') {
                                    if(!empty($order_data['raw_data'])){
                                        $raw_data = json_decode($order_data['raw_data']);
                                        if(!empty($raw_data->pricing_type)){
                                            if(!empty($raw_data->adult_number)){
                                                $calendar_price = ($raw_data->pricing_type == 'per_person')?$raw_data->calendar->adult_price:$raw_data->calendar->calendar_price;
                                                echo '<li class="wb-room-item"><span class="wb-room-name"><strong>'.esc_html__('Adult','wpbooking').' x '.esc_html($raw_data->adult_number).'</strong></span>';
                                                echo '<span class="wb-room-price">' . WPBooking_Currency::format_money($calendar_price) . '</span>';
                                                echo '</li>';
                                            }
                                            if(!empty($raw_data->children_number)){
                                                $calendar_price = ($raw_data->pricing_type == 'per_person')?$raw_data->calendar->child_price:$raw_data->calendar->calendar_price;
                                                echo '<li class="wb-room-item"><span class="wb-room-name"><strong>'.esc_html__('Children','wpbooking').' x '.esc_html($raw_data->children_number).'</strong></span>';
                                                echo '<span class="wb-room-price">' . WPBooking_Currency::format_money($calendar_price) . '</span>';
                                                echo '</li>';
                                            }
                                            if(!empty($raw_data->infant_number)){
                                                $calendar_price = ($raw_data->pricing_type == 'per_person')?$raw_data->calendar->infant_price:$raw_data->calendar->calendar_price;
                                                echo '<li class="wb-room-item"><span class="wb-room-name"><strong>'.esc_html__('Infant','wpbooking').' x '.esc_html($raw_data->infant_number).'</strong></span>';
                                                echo '<span class="wb-room-price">' . WPBooking_Currency::format_money($calendar_price) . '</span>';
                                                echo '</li>';
                                            }
                                        }

										$extra_fees = unserialize($order_data['extra_fees']);
										if(!empty($extra_fees)){
											foreach($extra_fees as $k=>$v){
												echo '<li class=""><span class="wb-room-name"><strong>'.$v['title'].'</strong></span>';
												echo '</li>';
												foreach($v['data'] as $key=>$value){
													echo '<li class="wb-room-item"><span class="wb-room-name"><strong>&nbsp&nbsp&nbsp&nbsp'.$value['title'].' x '.$value['quantity'].'</strong></span>';
													echo '<span class="wb-room-price">' . WPBooking_Currency::format_money($value['quantity'] * $value['price']) . '</span>';
													echo '</li>';
												}
											}
										}
                                    }

                                }else{
                                    echo '<li>'.esc_html__('Rooms: ','wpbooking').'</li>';
                                    foreach ($room_data as $key => $value) {
                                        $extra_fees = unserialize($value['extra_fees']);
                                        $price = WPBooking_Currency::format_money($value['price']);
                                        echo '<li class="wb-room-item"><span class="wb-room-name"><strong>' . get_the_title($value['room_id']) . ' x' . esc_html($value['number']) . '</strong></span>';
                                        echo '<span class="wb-room-price">' . do_shortcode($price) . '</span>';
                                        echo '</li>';
                                        if (!empty($extra_fees['extra_service']['data']) && is_array($extra_fees['extra_service']['data'])) {
                                            foreach ($extra_fees['extra_service']['data'] as $k => $v) {
                                                echo '<li class="wb-room-item"><span class="wb-extra-title">' . esc_html($v['title']) . ' x' . esc_html($v['quantity']) . '</span>';
                                                echo '<span class="wb-extra-price">' . WPBooking_Currency::format_money($v['price']) . '</span>';
                                                echo '</li>';
                                            }
                                        }
                                    }
                                }
                                ?>
                            </ul>
                            <button type="button" class="toggle-row"><span class="screen-reader-text"><?php echo esc_html__('Show more details','wpbooking'); ?></span></button>
						</td>
                        <td class="wb-column-empty"></td>
						<td class="manage-column column-date asc">
							<?php
							echo do_shortcode($order->get_booking_date());
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
					<td colspan="10"><?php esc_html_e('Not Found Booking','wpbooking') ?></td>
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
