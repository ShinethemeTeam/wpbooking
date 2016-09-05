<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/30/2016
 * Time: 2:01 PM
 */
$types=WPBooking_Service_Controller::inst()->get_service_types();
$status=WPBooking_Config::inst()->item('order_item_status');
$payment_status=WPBooking_Config::inst()->item('payment_status');

if(get_query_var('order-detail')){
	echo wpbooking_load_view('account/orders/detail');
	return;
}
?>
	<h3 class="tab-page-title">
		<?php
		echo esc_html__('All Orders','wpbooking');
		?>
	</h3>
	<ul class="subsubsub">
		<?php
		$tabs=array(
			'listing'=>esc_html__('Listing','wpbooking'),
			'calendar'=>esc_html__('Calendar','wpbooking'),
		);
		$i=0;
		foreach($tabs as $k=>$v){

			$current_tab=(string)WPBooking_Input::get('subtab');

			$class=FALSE;
			if(array_key_exists($current_tab,$tabs) and $k==$current_tab) $class='current';
			elseif($i==0 and !$current_tab) $class='current';

			$url='#';
			if(!$class){
				$url=esc_url(add_query_arg(array(
					'subtab'=>$k,

				)),get_permalink(wpbooking_get_option('myaccount-page')).'tab/orders/');
			}

			printf('<li><a href="%s" class="%s">%s</a></li>',$url,$class,$v);
			if($i!=count($tabs)-1){
				echo '|';
			}

			$i++;
		}
		?>
	</ul>
	<form action="" method="get" class="clear">
		<?php if(WPBooking_Input::get('subtab')){
			printf('<input type="hidden" name=subtab value=%s>',WPBooking_Input::get('subtab'));
		} ?>

		<div class="tablenav top">
			<div class="alignleft actions">
				<select name="service_type" class="postform">
					<optgroup label="<?php esc_html_e('Service Type','wpbooking') ?>">
						<option value="0"><?php esc_html_e('All Service Types','wpbooking') ?></option>
						<?php foreach($types as $k=>$v){
							printf('<option value="%s" %s>%s</option>',$k,selected(WPBooking_Input::get('service_type'),$k,FALSE),$v->get_info('label'));
						} ?>
					</optgroup>
				</select>
				<select name="status" class="postform">
					<optgroup label="<?php esc_html_e('Status','wpbooking') ?>">
						<option value="0"><?php esc_html_e('All Status','wpbooking') ?></option>
						<?php foreach($status as $k=>$v){
							printf('<option value="%s" %s>%s</option>',$k,selected(WPBooking_Input::get('status'),$k,FALSE),$v['label']);
						} ?>
					</optgroup>
				</select>
				<input type="submit" id="doaction" class="button action" value="<?php esc_html_e('Filter','wpbooking') ?>">
			</div>

			<div class="clear"></div>
			<?php
			if($current_tab=WPBooking_Input::get('subtab')){
				echo wpbooking_load_view('account/orders/loop-'.$current_tab);
			}else{
				echo wpbooking_load_view('account/orders/loop-listing');
			}
			?>
			<div class="clear"></div>
	</form>
<?php wp_reset_postdata()?>