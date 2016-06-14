<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/13/2016
 * Time: 9:33 AM
 */
$types=WPBooking_Service::inst()->get_service_types();
$status=WPBooking_Config::inst()->item('payment_status');
?>
<div class="wrap">
	<h1><?php esc_html_e('All Bookings','wpbooking') ?></h1>
	<ul class="subsubsub">
		<?php
		$tabs=array(
			'listing'=>esc_html__('Listing','wpbooking'),
			'calendar'=>esc_html__('Calendar','wpbooking'),
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

	<form action="<?php echo admin_url('admin.php') ?>" method="get" class="clear">
		<input type="hidden" name="page" value="wpbooking_page_orders">
		<?php if(WPBooking_Input::get('tab')){
			printf('<input type="hidden" name=tab value=%s',WPBooking_Input::get('tab'));
		} ?>
		<div class="tablenav top">
			<div class="alignleft actions">
				<select name="service_type" class="postform">
					<option value="0"><?php esc_html_e('All Service Types','wpbooking') ?></option>
					<?php foreach($types as $k=>$v){
						printf('<option value="%s" %s>%s</option>',$k,selected(WPBooking_Input::get('service_type'),$k,FALSE),$v['label']);
					} ?>
				</select>
				<select name="status" class="postform">
					<optgroup label="<?php esc_html_e('Status','wpbooking') ?>">
						<option value="0"><?php esc_html_e('All Status','wpbooking') ?></option>
						<?php foreach($status as $k=>$v){
							printf('<option value="%s" %s>%s</option>',$k,selected(WPBooking_Input::get('status'),$k,FALSE),$v['label']);
						} ?>
					</optgroup>
				</select>
				<select name="payment_status" class="postform">
					<optgroup label="<?php esc_html_e('Payment Status','wpbooking') ?>">
						<option value="0"><?php esc_html_e('All Payment Status','wpbooking') ?></option>
						<?php foreach($status as $k=>$v){
							printf('<option value="%s" %s>%s</option>',$k,selected(WPBooking_Input::get('payment_status'),$k,FALSE),$v['label']);
						} ?>
					</optgroup>
				</select>
				<input type="submit" id="doaction" class="button action" value="<?php esc_html_e('Filter','wpbooking') ?>">
			</div>
		</div>

		<?php
		if($current_tab=WPBooking_Input::get('tab')){
			echo wpbooking_admin_load_view('order/loop-'.$current_tab);
		}else{
			echo wpbooking_admin_load_view('order/loop-listing');
		}
		?>
	</form>
	<div class="clear"></div>
</div>
<?php wp_reset_postdata()?>