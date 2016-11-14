<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/14/2016
 * Time: 8:46 AM
 */
$limit=10;
$offset=$limit*(WPBooking_Input::get('page_number',1)-1);
$args=array(
	'post_type'=>'wpbooking_order',
	'posts_per_page'=>20
);

$service_type = WPBooking_Input::get('wb_service_types',array('accommodation'));
$report_type=WPBooking_Input::get('report_type','last_7days');
$start_date=WPBooking_Input::get('date_from');
$end_date=WPBooking_Input::get('date_to');
$chart = new WPBooking_Chart();

$total_sale = $chart->total_in_time_range($service_type,'total_sale',$report_type,$start_date, $end_date);

$query=new WP_Query($args);
$service_types=WPBooking_Service_Controller::inst()->get_service_types();
$report_data=WPBooking_Admin_Order::inst()->get_report_data();

$report_type=WPBooking_Input::get('report_type','last_7days');
$type_array=array(
	'today'=>esc_html__('Today','wpbooking'),
	'yesterday'=>esc_html__('Yesterday','wpbooking'),
	'this_week'=>esc_html__('This week','wpbooking'),
	'last_week'=>esc_html__('Last week','wpbooking'),
	'last_7days'=>esc_html__('Last 7 days','wpbooking'),
	'last_30days'=>esc_html__('Last 30 days','wpbooking'),
	'last_60days'=>esc_html__('Last 60 days','wpbooking'),
	'last_90days'=>esc_html__('Last 90 days','wpbooking'),
	'this_year'=>esc_html__('This Year','wpbooking'),
	'last_year'=>esc_html__('Last Year','wpbooking'),
);

?>
<div class="wb-tab-report-wrap clear">
	<form action="" class="report-form" method="get">
		<input type="hidden" name="page" value="wpbooking_page_orders">
		<input type="hidden" name="tab" value="report">
		<input type="hidden" name="report_type" value="<?php echo esc_attr($report_type) ?>">

		<div class="select-service-type">
			<h4><?php esc_html_e('Service type:','wpbooking') ?></h4>
			<ul class="service_types">
				<li><label ><input type="checkbox" class="check_all_service_type" checked> <?php esc_html_e('All','wpbooking') ?></label></li>
				<?php
				if(!empty($service_types)){
					foreach($service_types as $key=>$val){
						$checked='checked';
						printf('<li><label ><input type="checkbox" %s name="wb_service_types[]" value="%s"> %s</label></li>',$checked,$key,$val->get_info('label'));
					}
				}
				?>
			</ul>
		</div>
		<div class="filter-by-tabs">
			<ul class="filter-by-lists">
				<li><a ><?php esc_html_e('Filter by:','wpbooking') ?></a></li>
				<li <?php if(array_key_exists($report_type,$type_array) or $report_type=='today') echo 'class="active"'; ?> >
					<select  class="select-report-type" name="">
						<?php foreach($type_array as $type_id=>$type){
							printf('<option value="%s" %s>%s</option>',$type_id,selected($report_type,$type_id,false),$type);
						} ?>
					</select>
				</li>

				<li <?php if($report_type=='date_range') echo "class='active'"; ?>>
					<div class="filter-date">
						<label ><?php esc_html_e('From','wpbooking')?> <input type="text" value="<?php echo ($report_type=='date_range')?WPBooking_Input::get('date_from'):false ?>" name="date_from" class="datepicker_start"> <i class="fa fa-calendar"></i></label>
					</div>
					<div class="filter-date">
						<label ><?php esc_html_e('To','wpbooking')?> <input type="text" name="date_to" value="<?php echo ($report_type=='date_range')?WPBooking_Input::get('date_to'):false ?>" class="datepicker_end"> <i class="fa fa-calendar"></i></label>
					</div>
				</li>
				<li class=""><a href="#" class="do-search" ><?php esc_html_e('Go','wpbooking') ?> <i class="fa fa-caret-right"></i></a></li>
			</ul>
		</div>
		<div class="report-content">
			<?php if(empty($report_data)){
				printf('<div class="notice-error"><p>%s</p></div>',esc_html__('There is no data for reporting','wpbooking'));
			}else{
                $total_sale = $chart->total_in_time_range($service_type,'total_sale',$report_type,$start_date, $end_date);
                $net_profit = $chart->total_in_time_range($service_type,'net_profit',$report_type,$start_date, $end_date);
                $items = $chart->total_in_time_range($service_type,'items',$report_type,$start_date, $end_date);
                $total_bookings = $chart->total_in_time_range($service_type,'total_bookings',$report_type,$start_date, $end_date);
                $completed = $chart->total_in_time_range($service_type,'completed',$report_type,$start_date, $end_date);
                $on_hold = $chart->total_in_time_range($service_type,'on_hold',$report_type,$start_date, $end_date);
                $cancelled = $chart->total_in_time_range($service_type,'cancelled',$report_type,$start_date, $end_date);
                $refunded = $chart->total_in_time_range($service_type,'refunded',$report_type,$start_date, $end_date);
                ?>
                <table class="wb-report-total">
                    <tr>
                        <th class="wb-report-column"><?php echo esc_html__('Total Sale','wpbooking'); ?></th>
                        <th class="wb-report-column"><?php echo esc_html__('Net Profit','wpbooking'); ?></th>
                        <th class="wb-report-column"><?php echo esc_html__('Items','wpbooking'); ?></th>
                        <th class="wb-report-column"><?php echo esc_html__('Total Bookings','wpbooking'); ?></th>
                        <th class="wb-report-column"><?php echo esc_html__('Completed','wpbooking'); ?></th>
                        <th class="wb-report-column"><?php echo esc_html__('On Hold','wpbooking'); ?></th>
                        <th class="wb-report-column"><?php echo esc_html__('Cancelled','wpbooking'); ?></th>
                        <th class="wb-report-column"><?php echo esc_html__('Refunded','wpbooking'); ?></th>
                    </tr>
                    <tr>
                        <td class="wb-report-d-column wb-total-sale"><?php echo WPBooking_Currency::format_money($total_sale); ?></td>
                        <td class="wb-report-d-column wb-net-profit"><?php echo WPBooking_Currency::format_money($net_profit); ?></td>
                        <td class="wb-report-d-column">
                            <span class="number"><?php echo esc_attr($items)?></span>
                            <?php echo _n('item','items',wpbooking_covert_to_one($items)); ?>
                        </td>
                        <td class="wb-report-d-column">
                            <span class="number"><?php echo esc_attr($total_bookings)?></span>
                            <?php echo _n('booking','bookings',wpbooking_covert_to_one($total_bookings)); ?>
                        </td>
                        <td class="wb-report-d-column">
                            <span class="number"><?php echo esc_attr($completed)?></span>
                            <?php echo _n('booking','bookings',wpbooking_covert_to_one($completed)); ?>
                        </td>
                        <td class="wb-report-d-column">
                            <span class="number"><?php echo esc_attr($on_hold)?></span>
                            <?php echo _n('booking','bookings',wpbooking_covert_to_one($on_hold)); ?>
                        </td>
                        <td class="wb-report-d-column">
                            <span class="number"><?php echo esc_attr($cancelled)?></span>
                            <?php echo _n('booking','bookings',wpbooking_covert_to_one($cancelled)); ?>
                        </td>
                        <td class="wb-report-d-column">
                            <span class="number"><?php echo esc_attr($refunded)?></span>
                            <?php echo _n('booking','bookings',wpbooking_covert_to_one($refunded)); ?>
                        </td>
                    </tr>
                </table>
                <?php
                echo '<div class="wb-chart-report"><canvas id="wb-chart"></canvas></div>';
            } ?>
		</div>
	</form>
</div>
<?php wp_reset_postdata() ?>