<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/27/2016
 * Time: 10:44 AM
 */
$tabs=WPBooking_User::inst()->get_tabs();

?>
<!-- Nav tabs -->
<ul class="wb-account-nav" role="tablist">
	<?php
	if(!empty($tabs)){
		$i=1;
		foreach($tabs as $k=>$tab){
			$class=FALSE;

			if($current_tab=get_query_var('tab') and $current_tab==$k) $class='active';

			if(!get_query_var('tab') and $i==1) $class='active';

			$url=get_permalink(wpbooking_get_option('myaccount-page')).'tab/'.$k;

			switch($k){
				case "logout":
					$url=wp_logout_url();
					break;
			}

			printf('<li role="presentation" class="%s"><a href="%s">%s</a></li>',$class,$url,$tab);
			$i++;
		}
	}
	?>

</ul>
