<?php
$status=WPBooking_Config::inst()->item('order_status');
$payment_status=WPBooking_Config::inst()->item('payment_status');
?>
<h3 class="tab-page-title">
	<?php
	echo esc_html__('Booking History','wpbooking');
	?>
</h3>
	<form action="" method="get">
		<div class="control-filter wpbooking-bootstrap p5">
			<div class="row">
				<div class="col-md-3">
					<?php
					$list_service_type = WPBooking_Service_Controller::inst()->get_service_types();
					$wpbooking_service_type = WPBooking_Input::request('wpbooking_service_type');
					?>
					<select class="form-control" name="wpbooking_service_type">
						<option value=""><?php esc_html_e("Service Type","wpbooking") ?></option>
						<?php if(!empty($list_service_type)){
							foreach($list_service_type as $k=>$v){
								echo "<option ".selected($k,$wpbooking_service_type,false)." value='{$k}'>{$v->get_info('label')}</option>";
							}
						} ?>
					</select>
				</div>
				<div class="col-md-2">
					<?php
					$all_status=WPBooking_Config::inst()->item('order_status');
					$status = WPBooking_Input::request('wpbooking_status');
					?>
					<select class="form-control"  name="wpbooking_status">
						<option value=""><?php esc_html_e("Status","wpbooking") ?></option>
						<?php
						if(!empty($all_status)){
							foreach($all_status as $k=>$v){
								echo "<option ".selected($k,$status,false)." value='{$k}'>{$v['label']}</option>";
							}
						} ?>
					</select>
				</div>
				<div class="col-md-2">
					<button type="submit" class="wb-button"><?php esc_html_e("Filter","wpbooking") ?></button>
				</div>
			</div>
		</div>
		<?php if(WPBooking_Input::get('subtab')){
			printf('<input type="hidden" name=subtab value=%s>',WPBooking_Input::get('subtab'));
		} ?>
		<div class="tablenav top">
			<?php
			if($current_tab=WPBooking_Input::get('subtab')){
				echo wpbooking_load_view('account/booking_history/loop-'.$current_tab);
			}else{
				echo wpbooking_load_view('account/booking_history/loop-listing');
			}
			?>
			<div class="clear"></div>
	</form>
<?php wp_reset_postdata()?>