<?php
/**
 *@since 1.0.0
 **/
$service_type=WPBooking_Service_Controller::inst()->get_service_types();

$old_data = get_post_meta( $post_id, esc_html( $data['id'] ), true );

$select = FALSE;

$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
	$class .= ' wpbooking-condition ';
	$data_class .= ' data-condition='.$data['condition'].' ' ;
}
$class.=' width-'.$data['width'];
?>
<div class="wpbooking-settings <?php echo esc_html( $class ); ?>">

	<div class="st-metabox-left">
		<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
	</div>
	<div class="st-metabox-right">
		<div class="st-metabox-content-wrapper">
			<div class="list-radio">
				<?php if( $service_type && !empty( $service_type ) ){
					$i=0;
					foreach( $service_type as $key => $value ){
						$check=FALSE;
						$active = '';
                        if(WPBooking_Input::get('action') == 'edit'){
                            $active = 'hidden';
                        }
						if($old_data){
							$check=checked($old_data,$key,FALSE);
							if(!empty($check)){
								$active = 'active';
							}
						}elseif($i==0){
							$check='checked="checked"';
							$active = 'active';
						}
						printf('<label class="wb-radio-button wb_service_type %s"><input type="radio" name="%s" value="%s" %s> %s</label>',$active,$data['id'],$key,$check,$value->get_info('label'));
						$i++;
					}
				} ?>
			</div>
			<div class="service-type-desc">
				<?php if( $service_type && !empty( $service_type ) ){
					$i=0;
					foreach( $service_type as $key => $value ){
						$check=FALSE;
						if($old_data){
							$check=checked($old_data,$key,FALSE);
						}elseif($i==0){
							$check='checked="checked"';
						}
						printf('<div data-condition="wb_service_type:is(%s)" class="wpbooking-condition desc-item service-type-%s">%s</div>',$key,$key,'<strong>'.$value->get_info('label').':</strong> '.$value->get_info('desc'));
						$i++;
					}
				} ?>
			</div>
		</div>
		<div class="metabox-help"><?php echo do_shortcode( $data['desc'] ) ?></div>
	</div>
</div>