<?php
/**
 *@since 1.0.0
 **/
$service_type=WPBooking_Service::inst()->get_service_types();

$old_data = get_post_meta( get_the_ID(), esc_html( $data['id'] ), true );

$select = FALSE;

if( $service_type && !empty( $service_type ) ){
	$select= '<select name="'. esc_html( $data['id'] ).'" id="'. esc_html( $data['id'] ) .'" class="form-control '. esc_html( $data['class'] ).'">';
	foreach( $service_type as $key => $value ){
		$checked = '';
		if( !empty( $data['std'] ) && ( esc_html( $key ) == esc_html( $data['std'] ) ) ){
			$checked = ' selected ';
		}
		if( $old_data ){
			if( esc_html( $key ) == $old_data ){
				$checked = ' selected ';
			}else{
				$checked = '';
			}
		}
		
		$select.= '<option value="'. esc_html( $key ).'" '. $checked .'>'. esc_html( $value['label'] ).'</option>';
	}
	$select .= '</select>';
}
?>
<div class="wpbooking-hndle-tag-input hidden">
	<label class="wpbooking-form-group" ><?php echo esc_html($data['label']) ?>
		<?php echo $select?>
	</label>
</div>