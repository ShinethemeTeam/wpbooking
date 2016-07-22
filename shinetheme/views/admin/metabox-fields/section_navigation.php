<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/18/2016
 * Time: 8:25 AM
 */
?>
<div class="text-right wb-section-navigation clear" style="clear: both">
	<?php
	if(!isset($data['prev']) or $data['prev']){
		printf('<a href="#" class="button wb-prev-section">%s</a>',esc_html__('Prev','wpbooking'));
	}
	if(!isset($data['next']) or $data['next']){
		printf('<a href="#" class="button wb-next-section">%s</a>',esc_html__('Next','wpbooking'));
	}

	?>

</div>
