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
		$class = 'full';
		if(!isset($data['next']) or $data['next']){
			$class = 'w50';
		}
		printf('<a href="#" class="button wb-prev-section %s">%s</a>',$class,esc_html__('Previous','wpbooking'));
	}
	if(!isset($data['next']) or $data['next']){
		$class = 'full';
		if(!isset($data['prev']) or $data['prev']){
			$class = 'w50';
		}
		printf('<a href="#" class="button wb-next-section %s">%s</a>',$class,esc_html__('Next Step','wpbooking'));
	}

	?>

</div>
