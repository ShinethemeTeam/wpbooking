<?php
/**
 *@since 1.0.0
 **/

if(!empty($data['custom_name'])){
	if(isset($data['custom_data'])) $old_data=$data['custom_data'];
}else{
	$old_data=get_post_meta( $post_id, esc_html( $data['id'] ), true);
}
if( !empty( $value ) ){
	$old_data = $value;
}
if(empty($old_data)){
	$old_data = esc_html( $data['std'] );
}
$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
	$class .= ' wpbooking-condition ';
	$data_class .= ' data-condition='.$data['condition'].' ' ;
}
$class.=' width-'.$data['width'];


?>

<div class="wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
	<div class="st-metabox-left">
		<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
	</div>
	<div class="st-metabox-right">
		<div class="st-metabox-content-wrapper">
			<div class="form-group">
				<label class="wpbooking-switch-wrap">
					<select id="<?php echo esc_html($data['id']) ?>"  name="<?php echo esc_html($data['id']) ?>" <?php checked($old_data,'on') ?>  class="checkbox">
						<option <?php selected($old_data,'on') ?>  value="on">on</option>
						<option <?php selected($old_data,'off') ?> value="off">off</option>
					</select>
					<div class="wpbooking-switch <?php echo ($old_data=='on')?'switchOn':FALSE ?>"></div>
				</label>
			</div>
		</div>
		<div class="metabox-help"><?php echo do_shortcode( $data['desc'] ) ?></div>
	</div>
</div>