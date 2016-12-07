<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/18/2016
 * Time: 8:25 AM
 */
$data=wp_parse_args($data,array(
	'ajax_saving'=>1,
	'next_label'=>esc_html__('Save & Next Step','wpbooking')
))
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

        if(WPBooking_Input::get('action') == 'edit'){
            $data['next_label'] = esc_html__('Save','wpbooking');
        }

		if($data['ajax_saving']) $class.=' ajax_saving';
		printf('<a href="#" class="button wb-next-section %s" data-action="%s">%s <i class="fa fa-spinner fa-pulse"></i></a>',$class,WPBooking_Input::get('action'),$data['next_label']);
	}

	?>

</div>
