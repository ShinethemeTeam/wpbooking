<?php 
/**
*@since 1.0.0
**/

$old_data = (isset( $data['custom_data'] ) ) ? esc_html( $data['custom_data'] ) : get_post_meta( $post_id, esc_html( $data['id'] ), true);

$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

$class.=' width-'.$data['width'];
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );

$field = '<div class="st-metabox-content-wrapper"><div class="form-group">';

if(!empty($data['taxonomy'])){
	$args=array('taxonomy'=>$data['taxonomy'],'hide_empty'=>false);
	if(isset($data['parent'])) $args['parent']=$data['parent'];

	$terms=get_terms($args);
	if(!is_wp_error($terms) and !empty($terms)){
		$data['value']=array();
		foreach ($terms as $term){
			$data['value'][$term->term_id]=$term->name;
		}
	}
    $field .= '<input type="hidden" name="is_tax" value="'.$name.'">';
}

if( !empty( $data['value'] ) && is_array( $data['value'] ) ){
	$array_with_out_key=FALSE;
	$keys = array_keys( $data['value']);
	if($keys[0]===0){
		$array_with_out_key=true;
	}

	$field .= '<div><select name="'. $name .'" id="'. esc_html( $data['id'] ) .'" class="widefat form-control '. esc_html( $data['class'] ).'">';
	foreach( $data['value'] as $key => $value ){
		$compare=$key;
		if($array_with_out_key) $compare=$value;

		$checked = '';
		if( !empty( $data['std'] ) && ( esc_html( $key ) == esc_html( $data['std'] ) ) ){
			$checked = ' selected ';
		}
		if( $old_data && !empty( $old_data ) ){
			if( esc_html( $compare ) == esc_html( $old_data ) ){
				$checked = ' selected ';
			}else{
				$checked = '';
			}
		}
		$option_val=$key;
		if($array_with_out_key) $option_val=$value;

        // Check Taxonomy wpbooking_is_multi_bedroom
        // Check Taxonomy wpbooking_is_multi_livingroom
        if(!empty($data['taxonomy']) and function_exists('get_term_meta')){
            if(get_term_meta($key,'wpbooking_is_multi_bedroom',true)){
                $checked.=' muilti_bedroom=1';
            }
            if(get_term_meta($key,'wpbooking_is_multi_livingroom',true)){
                $checked.=' muilti_livingroom=1';
            }
        }

		$field .= '<option value="'. esc_html( $option_val ).'" '. $checked .'>'. esc_html( $value ).'</option>';
	}
	$field .= '</select></div>';
}
$field .= '<div class="metabox-help">'.do_shortcode( $data['desc'] ).'</div>';
$field .= '</div></div>';

?>
<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
<div class="st-metabox-left">
	<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
</div>
<div class="st-metabox-right">
	<?php echo do_shortcode($field); ?>
</div>
</div>