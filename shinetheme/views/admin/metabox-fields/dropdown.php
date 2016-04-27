<?php 
/**
*@since 1.0.0
**/

$old_data = (isset( $data['custom_data'] ) ) ? esc_html( $data['custom_data'] ) : get_post_meta( get_the_ID(), esc_html( $data['id'] ), true);

$class = ' traveler-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' traveler-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );

$field = '<div class="st-metabox-content-wrapper"><div class="form-group">';

if( is_array( $data['value'] ) && !empty( $data['value'] ) ){
	$field .= '<div style="margin-bottom: 7px;"><select name="'. $name .'" id="'. esc_html( $data['id'] ) .'" class="widefat form-control '. esc_html( $data['class'] ).'">';
	foreach( $data['value'] as $key => $value ){
		$checked = '';
		if( !empty( $data['std'] ) && ( esc_html( $key ) == esc_html( $data['std'] ) ) ){
			$checked = ' selected ';
		}
		if( $old_data && !empty( $old_data ) ){
			if( esc_html( $key ) == esc_html( $old_data ) ){
				$checked = ' selected ';
			}else{
				$checked = '';
			}
		}
		
		$field .= '<option value="'. esc_html( $key ).'" '. $checked .'>'. esc_html( $value ).'</option>';
	}
	$field .= '</select></div>';
}

$field .= '</div></div>';

?>
<div class="form-table traveler-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
<div class="st-metabox-left">
	<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
</div>
<div class="st-metabox-right">
	<?php echo do_shortcode($field); ?>
	<i class="traveler-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
</div>
</div>