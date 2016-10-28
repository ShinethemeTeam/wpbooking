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
$query=new WP_Query($args);
$service_types=WPBooking_Service_Controller::inst()->get_service_types();
$report_data=WPBooking_Admin_Order::inst()->get_report_data();
$report_type=WPBooking_Input::get('report_type','last_7_days');
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
				<li><a href="#" class="change-report" data-range="year"><?php esc_html_e('Year','wpbooking') ?></a></li>
				<li><a href="#" class="change-report" data-range="last_month"><?php esc_html_e('Last month','wpbooking') ?></a></li>
				<li><a href="#" class="change-report" data-range="this_month"><?php esc_html_e('This month','wpbooking') ?></a></li>
				<li class="active"><a href="#" class="change-report" data-range="last_7_days"><?php esc_html_e('Last 7 days','wpbooking') ?></a></li>
				<li>
					<div class="filter-date">
						<label ><?php esc_html_e('From','wpbooking')?> <input type="text" name="date_from" class="datepicker_start"> <i class="fa fa-calendar"></i></label>
					</div>
					<div class="filter-date">
						<label ><?php esc_html_e('To','wpbooking')?> <input type="text" name="date_to" class="datepicker_end"> <i class="fa fa-calendar"></i></label>
					</div>
				</li>
				<li class=""><a href="#" class="do-search" ><?php esc_html_e('Go','wpbooking') ?> <i class="fa fa-caret-right"></i></a></li>
			</ul>
		</div>
		<div class="report-content">
			<?php if(empty($report_data)){
				printf('<div class="notice-error"><p>%s</p></div>',esc_html__('There is no data for reporting','wpbooking'));
			} ?>
		</div>
	</form>
</div>
<?php wp_reset_postdata() ?>