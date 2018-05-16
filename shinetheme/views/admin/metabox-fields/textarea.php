<?php
/**
*@since 1.0.0
**/
$old_data = esc_html( $data['std'] );

$data=wp_parse_args($data,array(
	'cols'=>30,
	'rows'=>10,
));

$value = (isset( $data['custom_data'] ) ) ? esc_html( $data['custom_data'] ) : get_post_meta( $post_id, esc_html( $data['id'] ), true);
if( !empty( $value ) ){
	$old_data = $value;
}

$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
$field = '<div class="st-metabox-content-wrapper"><div class="form-group">';

$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );



$field .= '<div class="mb7"><textarea  cols="'. esc_html( $data['cols'] ).'" rows="'. esc_html( $data['rows'] ).'"  id="'. esc_html( $data['id'] ).'" name="'. $name .'" class=" form-control widefat '. esc_html( $data['class'] ).'">'. esc_attr( $old_data ).'</textarea></div>';

$field .= '</div></div>';

?>

<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
<div class="st-metabox-left">
	<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
</div>
<div class="st-metabox-right">
	<?php echo ($field); ?>
	<i class="wpbooking-desc"><?php echo do_shortcode( $data['desc'] ) ?></i>
</div>
</div>