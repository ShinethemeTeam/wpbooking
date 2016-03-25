<?php 
/**
*@since 1.0.0
**/

$old_data = esc_html( $data['std'] );

$value = get_post_meta( get_the_ID(), esc_html( $data['id'] ), true);
if( !empty( $value ) ){
	$old_data = $value;
}

$class = ' traveler-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' traveler-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

$attr=FALSE;
if(!empty($data['attr']) and is_array($data['attr'])){
	$attr=implode(' ',$data['attr']);
}

$field = '<div class="st-metabox-content-wrapper"><div class="form-group">';

$field .= '<div style="margin-bottom: 7px;"><input '.$attr.' id="'. esc_html( $data['id'] ).'" type="number" name="'. esc_html( $data['id'] ).'" value="' .esc_html( $old_data ).'" class="widefat form-control '. esc_html( $data['class'] ).'"></div>';

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