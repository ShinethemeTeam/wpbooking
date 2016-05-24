<?php 
/**
*@since 1.0.0
**/

$old_data = esc_html( $data['std'] );

if(!empty($data['custom_name'])){
	if(isset($data['custom_data'])) $old_data=$data['custom_data'];
}else{
	$old_data=get_post_meta( get_the_ID(), esc_html( $data['id'] ), true);
}
if( !empty( $value ) ){
	$old_data = $value;
}

$class = ' traveler-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' traveler-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

$field = '<div class="st-metabox-content-wrapper"><div class="form-group">';

$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );

$field .= '<div style="margin-bottom: 7px;"><input id="'. esc_html( $data['id'] ).'" type="text" name="'. $name .'" value="' .esc_html( $old_data ).'" class="widefat form-control '. esc_html( $data['class'] ).'"></div>';

$field .= '</div></div>';

?>

<div class="form-table traveler-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
	<div class="st-metabox-left">
		<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
	</div>
	<div class="st-metabox-right">
		<?php echo $field; ?>
		<i class="traveler-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
	</div>
</div>