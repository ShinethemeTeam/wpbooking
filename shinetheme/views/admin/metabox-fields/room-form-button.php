<?php
$data=wp_parse_args($data,array(
	'ajax_saving'=>1,
	'next_label'=>esc_html__('Save','wp-booking-management-system')
));
$css = WPBooking_Assets::build_css_class('clear: both');
?>
<div class="text-right wb-section-navigation wb-room-form clear <?php echo esc_attr($css)?>">
	<?php
	if(!isset($data['prev']) or $data['prev']){
		$class = 'full';
		if(!isset($data['next']) or $data['next']){
			$class = 'w50';
		}
		printf('<a href="#" class="button wb-all-rooms %s"><i class="fa fa-chevron-circle-left fa-force-show" aria-hidden="true"></i> %s</a>',$class,esc_html__('Back to All Rooms','wp-booking-management-system'));
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
