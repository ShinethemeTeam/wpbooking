<?php 
/**
*@since 1.0.0
**/

$old_data = esc_html( $data['std'] );

$value = get_post_meta( $post_id, esc_html( $data['id'] ), true );

if( !empty( $value ) ){
    if(is_array($value)){
        $value = $value['gallery'];
    }
	$old_data = $value;
}


$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

$data_class.=' width-'.$data['width'];

$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );

$field = '<div class="st-metabox-content-wrapper wpbooking-settings"><div class="form-group">';

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
}else{
    $class .= ' wpbooking-no-gallery ';
}

$field .= '</div>';        

$field .= '<button id="" class="btn_upload_gallery mr10" type="button" name="">'. esc_html__("Add Gallery","wp-booking-management-system").'</button>';

$field .= '<button class="btn_remove_demo_gallery '.((!empty($old_data) && count( $tmp ) > 0 )?'':'hidden').'" type="button" name="">'.esc_html__("Remove Gallery","wp-booking-management-system").'</button>';


$field .= '</div></div>';

?>
<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?> wpbooking-gallery" <?php echo esc_html( $data_class ); ?>>
<div class="st-metabox-left">
	<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
</div>
<div class="st-metabox-right">
    <div class="no-gallery-notice hidden">
        <h3><?php echo sprintf(esc_html__('No %s photo yet.','wp-booking-management-system'), (isset($data['service_type'])?$data['service_type']:'')); ?></h3>
        <p><?php echo esc_html__('Upload at least a photo','wp-booking-management-system'); ?></p>
    </div>
	<?php echo do_shortcode($field); ?>
	<i class="wpbooking-desc"><?php echo do_shortcode( $data['desc'] ) ?></i>
</div>
</div>