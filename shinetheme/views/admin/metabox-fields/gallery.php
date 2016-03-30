<?php 
/**
*@since 1.0.0
**/

$old_data = esc_html( $data['std'] );

$value = get_post_meta( get_the_ID(), esc_html( $data['id'] ), true );
if( !empty( $value ) ){
	$old_data = $value;
}

$class = ' traveler-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' traveler-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] ). '[]';

$field = '<div class="st-metabox-content-wrapper traveler-settings"><div class="form-group">';

$field .= '<input type="text" id="fg_metadata" class="fg_metadata none" value="'. esc_html( $old_data ) .'" name="'. $name .'">
			<br>
        <div class="featuredgallerydiv max-width-500">';

$tmp = explode( ',', $old_data );

if( count( $tmp ) > 0 and !empty( $tmp[ 0 ] ) ){
 	foreach( $tmp as $k => $v ){
        $url = wp_get_attachment_image_src( $v );
        if( !empty( $url ) ){
            $field .= '<img src="'.esc_url($url[0]).'" class="demo-image-gallery settings-demo-gallery" >';
        } 
    }
}

$field .= '</div>';        

$field .= '<button style="margin-right: 10px;" id="btn_upload_gallery" class="btn button button-primary btn_upload_gallery" type="button" name="">'. __("Add Gallery","traveler-booking").'</button>';
if( count( $tmp = explode(',', $old_data ) ) > 0 ){
    $field .= '<button class="btn button btn_remove_demo_gallery button-secondary" type="button" name="">'.__("Remove Gallery","traveler-booking").'</button>';
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