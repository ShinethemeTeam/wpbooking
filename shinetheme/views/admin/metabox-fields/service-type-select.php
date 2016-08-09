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
						if($old_data){
							$check=checked($old_data,$key,FALSE);
						}elseif($i==0){
							$check='checked="checked"';
						}
						printf('<label class="wb-radio-button"><input type="radio" name="%s" value="%s" %s> %s</label>',$data['id'],$key,$check,$value['label']);
						$i++;
					}
				} ?>
			</div>
		</div>
		<div class="metabox-help"><?php echo balanceTags( $data['desc'] ) ?></div>
	</div>
</div>