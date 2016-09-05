<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/4/2016
 * Time: 3:06 PM
 */
$status=WPBooking_Config::inst()->item('order_item_status');
$payment_status=WPBooking_Config::inst()->item('payment_status');

if(get_query_var('order-detail')){
	echo wpbooking_load_view('account/booking_history/detail');
	return;
}
?>
<h3 class="tab-page-title">
	<?php
	echo esc_html__('Your Booking','wpbooking');
	?>
</h3>
	<form action="" method="get" class="clear">
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