<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/18/2016
 * Time: 8:25 AM
 */
$data=wp_parse_args($data,array(
	'ajax_saving'=>1,
	'next_label'=>esc_html__('Save','wpbooking')
))
?>
<div class="text-right wb-section-navigation wb-room-form clear" style="clear: both">
	<?php
	if(!isset($data['prev']) or $data['prev']){
		$class = 'full';
		if(!isset($data['next']) or $data['next']){
			$class = 'w50';
		}
		printf('<a href="#" class="button wb-all-rooms %s">%s</a>',$class,esc_html__('All Rooms','wpbooking'));
	}
	if(!isset($data['next']) or $data['next']){
		$class = 'full';
		if(!isset($data['prev']) or $data['prev']){
			$class = 'w50';
		}

		if($data['ajax_saving']) $class.=' ajax_saving';
		printf('<a href="#" class="button wb-save-room %s">%s <i class="fa fa-spinner fa-pulse"></i></a>',$class,$data['next_label']);
	}

	?>

</div>
