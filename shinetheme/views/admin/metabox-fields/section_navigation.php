<?php
$data=wp_parse_args($data,array(
	'ajax_saving'=>1,
	'next_label'=>esc_html__('Save & Next Step','wp-booking-management-system'),
	'step'=>''
));
$sec_class = '';
$action = WPBooking_Input::get('action');
$sec_class = 'wb-action-edit ';
$sec_class .= WPBooking_Assets::build_css_class('clear: both');
?>
<div class="text-right <?php echo esc_attr($sec_class); ?> wb-section-navigation clear">
	<?php
	if(!isset($data['prev']) or $data['prev']){
		$class = 'full';
		if(!isset($data['next']) or $data['next']){
			$class = 'w30';
		}
        if($action == 'edit'){
            $class = 'w30';
        }
		printf('<a href="#" class="button wb-prev-section %s">%s</a>',$class,esc_html__('Previous','wp-booking-management-system'));
	}
	if( isset($data['step']) && $action == 'edit' && $data['step'] == 'finish'){
		$data['next'] = false;
	}
	if(!isset($data['next']) or $data['next']){

		$class = 'w30 ';
		$loading=false;
		if($action == 'edit'){
			$data['next_label'] = esc_html__('Save & Next Step','wp-booking-management-system');
			$loading = '';
		}
		if($data['ajax_saving']) $class.=' ajax_saving';
		printf('<a href="#" class="button wb-next-section %s">%s %s <i class="fa fa-spinner fa-pulse"></i></a>',$class,$data['next_label'], $loading);
	}
    if(!in_array($data['step'],array('finish'))){
        $class = (!empty($data['class']))?$data['class']:'';
        printf('<a href="#" class="button wb-save-now-section wb-next-section w30 ajax_saving wb-next %s" data-action="%s">%s <i class="fa fa-spinner fa-pulse"></i></a>', $class, WPBooking_Input::get('action'),esc_html__('Save Now','wp-booking-management-system'));
    }
    if($action == 'edit' and in_array($data['step'],array('finish'))){
        $class = (!empty($data['class']))?$data['class']:'';
        printf('<a href="#" class="button wb-save-now-section wb-next-section w30 ajax_saving %s" data-action="%s">%s <i class="fa fa-spinner fa-pulse"></i></a>', $class, WPBooking_Input::get('action'),esc_html__('Save','wp-booking-management-system'));
    }
	?>
</div>
