<?php
$types=WPBooking_Service_Controller::inst()->get_service_types();
$status=WPBooking_Config::inst()->item('order_status');
$payment_status=WPBooking_Config::inst()->item('payment_status');
?>
<div class="wrap">
	<h1><?php echo esc_html__('All Bookings','wp-booking-management-system') ?></h1>
	<?php echo wpbooking_get_admin_message() ?>
	<ul class="subsubsub">
		<?php
		$tabs=array(
			'listing'=>esc_html__('Listing','wp-booking-management-system'),
			'report'=>esc_html__('Report','wp-booking-management-system'),
		);
		$i=0;
		foreach($tabs as $k=>$v){
			$current_tab=(string)WPBooking_Input::get('tab');
			$class=FALSE;
			if(array_key_exists($current_tab,$tabs) and $k==$current_tab) $class='current';
			elseif($i==0 and !$current_tab) $class='current';
			$url='#';
			if(!$class) $url=add_query_arg(array(
				'page'=>'wpbooking_page_orders',
				'tab'=>$k
			),admin_url('admin.php'));
			printf('<li><a href="%s" class="%s">%s</a></li>',$url,$class,$v);
			if($i!=count($tabs)-1){
				echo '|';
			}
			$i++;
		}
		?>
	</ul>
	<?php
	if($current_tab=WPBooking_Input::get('tab')){
		echo wpbooking_admin_load_view('order/tab-'.$current_tab);
	}else{
		echo wpbooking_admin_load_view('order/tab-listing');
	}
	?>
</div>
<?php wp_reset_postdata()?>