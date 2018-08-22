<?php
$old_data = esc_html( $data['std'] );
if(!empty($data['custom_name'])){
	if(isset($data['custom_data'])) $old_data=$data['custom_data'];
}else{
	$old_data=get_post_meta( $post_id, esc_html( $data['id'] ), true);
}
if( !empty( $value ) ){
	$old_data = $value;
}
$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
	$class .= ' wpbooking-condition ';
	$data_class .= ' data-condition='.$data['condition'].' ' ;
}
if(!empty($data['container_class'])) $class.=' '.$data['container_class'];
$class.=' width-'.$data['width'];
$field = '<div class="st-metabox-content-wrapper"><div class="form-group">';
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );
$field .= '<div class="mb7"><input id="'. esc_html( $data['id'] ).'" type="text" name="'. $name .'" value="' .esc_html( $old_data ).'" class="widefat form-control '. esc_html( $data['class'] ).'"></div>';
$field .= '</div></div>';
?>
<div class="wpbooking-settings  <?php echo esc_html( $class ); ?> wb-property-size-field" <?php echo esc_html( $data_class ); ?>>
	<div class="st-metabox-left">
		<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
	</div>
	<div class="st-metabox-right">
		<div class="st-metabox-content-wrapper">
			<input type="text" class="form-control small" name="<?php echo esc_html__($name) ?>" id="<?php echo esc_html( $data['id'] ); ?>" value="<?php echo esc_attr($old_data) ?>" placeholder="<?php echo esc_html__('Eg: 100','wp-booking-management-system') ?>">
			<select name="<?php echo esc_html($data['unit_id'])  ?>" id="<?php echo esc_html($data['unit_id'])  ?>" class="unit_type">
				<?php $old=get_post_meta($post_id,$data['unit_id'],TRUE); ?>
				<option value="meter" <?php selected('meter',$old) ?>><?php echo esc_html__('Meter','wp-booking-management-system') ?></option>
				<option value="feet" <?php selected('feet',$old) ?>><?php echo esc_html__('Feet','wp-booking-management-system') ?></option>
			</select>
		</div>
		<i class="wpbooking-desc"><?php echo do_shortcode( $data['desc'] ) ?></i>
	</div>
</div>
