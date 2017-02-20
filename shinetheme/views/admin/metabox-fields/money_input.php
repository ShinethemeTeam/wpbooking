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
$class.=' width-'.$data['width'];
if(!empty($data['container_class'])) $class.=' '.$data['container_class'];
$field = '<div class="st-metabox-content-wrapper"><div class="form-group">';
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );
$field .= '<div class="mb7"><input id="'. esc_html( $data['id'] ).'" type="text" name="'. $name .'" value="' .esc_html( $old_data ).'" class="widefat form-control '. esc_html( $data['class'] ).'"></div>';
$field .= '</div></div>';
?>

<div class="wpbooking-settings <?php echo esc_html( $class ); ?> wb-money-input-field" <?php echo esc_html( $data_class ); ?>>
	<div class="st-metabox-left">
		<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
	</div>
	<div class="st-metabox-right">
		<div class="st-metabox-content-wrapper">
			<div class="form-group">
				<div class="input-group <?php echo esc_attr($data['class']) ?>">
					<span class="input-group-addon" ><?php echo WPBooking_Currency::get_current_currency('title').' '.WPBooking_Currency::get_current_currency('symbol') ?></span>
					<input type="number" class="form-control" id="<?php echo esc_html($data['id']) ?>" value="<?php echo esc_html($old_data)?>" name="<?php echo esc_html($name)?>"  >
				</div>

				<?php
				if(!empty($data['help_inline'])){
					printf('<span class="help_inline">%s</span>',$data['help_inline']);
				}
				?>
			</div>
		</div>
		<div class="metabox-help"><?php echo do_shortcode( $data['desc'] ) ?></div>
	</div>
</div>
