<?php 
/**
*@since 1.0.0
**/

$old_data = esc_html( $data['std'] );

$value = get_post_meta( $post_id, esc_html( $data['id'] ), true);
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
$attr=FALSE;
if(!empty($data['attr']) and is_array($data['attr'])){
	$attr=implode(' ',$data['attr']);
}

$field = '<div class="st-metabox-content-wrapper"><div class="form-group">';

$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );

$field .= '<div style="margin-bottom: 7px;"><input '.$attr.' id="'. esc_html( $data['id'] ).'" type="number" name="'. $name .'" value="' .esc_html( $old_data ).'" class="widefat form-control '. esc_html( $data['class'] ).'"></div>';

$field .= '</div></div>';

?>
<div class="wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>

<div class="st-metabox-left">
	<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
</div>
<div class="st-metabox-right">
	<?php echo $field; ?>
	<i class="wpbooking-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
</div>
</div>