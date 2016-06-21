<?php 
/**
*@since 1.0.0
**/

$old_data = get_post_meta( get_the_ID(), esc_html( $data['id'] ), true );

$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

$class.=' width-'.$data['width'];
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] ). '[]';


$field = '<div class="st-metabox-content-wrapper"><div class="form-group">';

if(  !empty( $data['choices'] ) and is_array( $data['choices'] ) ){

	foreach( $data['choices'] as $key => $value ){
		$checked = '';
		if( !empty( $data['std'] ) && ( esc_html( $key ) == esc_html( $data['std'] ) ) ){
			$checked = ' checked ';
		}
		if( $old_data && is_array( $old_data ) ){
			if( in_array( esc_html( $key ), $old_data ) ){
				$checked = ' checked ';
			}else{
				$checked = '';
			}
		}
		
		$field .= '<div style="margin-bottom: 7px;"><label><input type="checkbox" name="'. $name .'" id="'. esc_html( $data['id'] ).'" class="'. esc_html( $data['class'] ) . '" value="'. esc_html( $key ) .'" ' . $checked .'> <span>'. esc_html( $value ) .'</span></label></div>';
	}
}elseif(!empty($data['checkbox_label'])){
	$value=$data['checkbox_label'];
	$checked=FALSE;
	if(empty($data['checkbox_value'])) $data['checkbox_value']=1;

	$checked=checked($old_data,$data['checkbox_value'],FALSE);
	$name=$data['id'];
	$key=$data['checkbox_value'];

	$field .= '<div style="margin-bottom: 7px;"><label><input type="checkbox" name="'. $name .'" id="'. esc_html( $data['id'] ).'" class="'. esc_html( $data['class'] ) . '" value="'. esc_html( $key ) .'" ' . $checked .'> <span>'. esc_html( $value ) .'</span></label></div>';
}

$field .= '</div></div>';

?>
<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
<div class="st-metabox-left">
	<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
</div>
<div class="st-metabox-right">
	<?php echo $field; ?>
	<i class="wpbooking-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
</div>
</div>