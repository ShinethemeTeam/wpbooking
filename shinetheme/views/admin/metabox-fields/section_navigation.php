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
));
$sec_class = '';
$action = WPBooking_Input::get('action');
if($action == 'edit'){
    $sec_class = 'wb-action-edit';
}

?>
<div class="text-right <?php echo esc_attr($sec_class); ?> wb-section-navigation clear" style="clear: both">
	<?php
	if(!isset($data['prev']) or $data['prev']){
		$class = 'full';
		if(!isset($data['next']) or $data['next']){
			$class = 'w50';
		}
        if($action == 'edit'){
            $class = 'w30';
        }
		printf('<a href="#" class="button wb-prev-section %s">%s</a>',$class,esc_html__('Previous','wpbooking'));
	}
    if($action == 'edit'){
        printf('<a href="#" class="button wb-save-now-section wb-next-section w30 ajax_saving" data-action="%s">%s <i class="fa fa-spinner fa-pulse"></i></a>',WPBooking_Input::get('action'),esc_html__('Save Now','wpbooking'));
    }
    if( isset($data['step']) && $action == 'edit' && $data['step'] == 'finish'){
        $data['next'] = false;
    }
	if(!isset($data['next']) or $data['next']){
		$class = 'full';
		if(!isset($data['prev']) or $data['prev']){
			$class = 'w50';
		}
        $loading = '<i class="fa fa-spinner fa-pulse"></i>';
        if($action == 'edit'){
            $data['next_label'] = esc_html__('Next Step','wpbooking');
            $class = 'w30 wb-next';
            $loading = '';
        }

		if($data['ajax_saving']) $class.=' ajax_saving';
		printf('<a href="#" class="button wb-next-section %s">%s %s</a>',$class,$data['next_label'], $loading);
	}

	?>

</div>
