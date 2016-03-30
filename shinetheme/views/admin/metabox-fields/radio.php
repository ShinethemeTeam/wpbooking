<?php 
/**
*@since 1.0.0
**/

$old_data = get_post_meta( get_the_ID(), esc_html( $data['id'] ), true );
$class = ' traveler-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' traveler-condition  ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
$field = '<div class="st-metabox-content-wrapper"><div class="form-group">';

$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] ). '[]';

if( is_array( $data['value'] ) && !empty( $data['value'] ) ){

	foreach( $data['value'] as $key => $value ){
		$checked = '';
		if( !empty( $data['std'] ) && ( esc_html( $key ) == esc_html( $data['std'] ) ) ){
			$checked = ' checked ';
		}
		if( $old_data && !empty( $old_data ) ){
			if(  esc_html( $key ) == esc_html( $old_data ) ){
				$checked = ' checked ';
			}else{
				$checked = '';
			}
		}
		
		$field .= '<div style="margin-bottom: 7px;"><label><input type="radio" name="'. $name .'" id="'. esc_html( $data['id'] ).'" class="'. esc_html( $data['class'] ) . '" value="'. esc_html( $key ) .'" ' . $checked .'> <span>'. esc_html( $value ) .'</span></label></div>';
	}
}

$field .= '</div></div>';

?>

<tr class="<?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
	<th scope="row">
		<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
	</th>
	<td>
		<?php echo $field; ?>
		<i class="traveler-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
	</td>
</tr>