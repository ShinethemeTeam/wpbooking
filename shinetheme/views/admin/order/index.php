<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/13/2016
 * Time: 9:33 AM
 */
$types=WPBooking_Service_Controller::inst()->get_service_types();
$status=WPBooking_Config::inst()->item('order_item_status');
$payment_status=WPBooking_Config::inst()->item('payment_status');
?>
<div class="wrap">
	<h1><?php esc_html_e('All Bookings','wpbooking') ?></h1>
	<?php echo wpbooking_get_admin_message() ?>
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
			printf('<input type="hidden" name=tab value=%s>',WPBooking_Input::get('tab'));
		} ?>

		<div class="tablenav top">
			<?php if(!WPBooking_Input::get('tab') or WPBooking_Input::get('tab')=='list'){?>
			<div class="alignleft actions bulkactions">
				<label for="bulk-action-selector-top" class="screen-reader-text"><?php esc_html_e('Select bulk action','wpbooking')?></label>
				<select name="action" id="wpbooking_bulk_edit_order">
					<option value="" selected="selected"><?php esc_html_e('Bulk Actions','wpbooking') ?></option>
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
				<select name="service_type" class="postform">
					<optgroup label="<?php esc_html_e('Service Type','wpbooking') ?>">
						<option value="0"><?php esc_html_e('All Service Types','wpbooking') ?></option>
						<?php foreach($types as $k=>$v){
							printf('<option value="%s" %s>%s</option>',$k,selected(WPBooking_Input::get('service_type'),$k,FALSE),$v['label']);
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
				<select name="payment_status" class="postform">
					<optgroup label="<?php esc_html_e('Payment Status','wpbooking') ?>">
						<option value="0"><?php esc_html_e('All Payment Status','wpbooking') ?></option>
						<?php foreach($payment_status as $k=>$v){
							printf('<option value="%s" %s>%s</option>',$k,selected(WPBooking_Input::get('payment_status'),$k,FALSE),$v['label']);
						} ?>
					</optgroup>
				</select>
				<input type="submit" id="doaction" class="button action" value="<?php esc_html_e('Filter','wpbooking') ?>">
			</div>
<!--			End .actions-->

			<div class="tablenav-pages">
				<p class="search-box">
					<label class="screen-reader-text" for="post-search-input"><?php esc_html_e('Search Order','wpbooking') ?></label>
					<input type="search"  name="keyword" value="<?php echo WPBooking_Input::get('keyword') ?>">
					<input type="submit"  class="button" value="<?php esc_html_e('Search Order','wpbooking') ?>"></p>
			</div>
		</div>
<!--		End top-->

		<div class="clear"></div>
		<?php
		if($current_tab=WPBooking_Input::get('tab')){
			echo wpbooking_admin_load_view('order/loop-'.$current_tab);
		}else{
			echo wpbooking_admin_load_view('order/loop-listing');
		}
		?>
	<div class="clear"></div>
	</form>
</div>
<?php wp_reset_postdata()?>