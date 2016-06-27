<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/27/2016
 * Time: 10:44 AM
 */
$tabs=array(
	'services'=>esc_html__('Services','wpbooking'),
	'booking_history'=>esc_html__('Booking History','wpbooking')
);

?>
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<?php
	if(!empty($tabs)){
		$i=1;
		foreach($tabs as $k=>$tab){
			$class=FALSE;

			if($current_tab=WPBooking_Input::get('tab') and $current_tab==$k) $class='active';

			if(!WPBooking_Input::get('tab') and $i==1) $class='active';

			$url=add_query_arg(array(
				'tab'=>$k
			),get_permalink(wpbooking_get_option('myaccount-page')));

			printf('<li role="presentation" class="%s"><a href="%s">%s</a></li>',$class,$url,$tab);
			$i++;
		}
	}
	?>

</ul>
