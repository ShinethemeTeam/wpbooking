<?php 
/**
*@since 1.0.0
**/

$old_data = array('gallery' => '','room_data'=>'');

$value = get_post_meta( $post_id, esc_html( $data['id'] ), true );

if( !empty( $value ) ){
	$old_data = $value;
}

$class = ' wb-form-group-gallery ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

$data_class.=' width-'.$data['width'];

$text_domain = array();
$text_domain[] = esc_html__('Do you want delete this image?','wpbooking');
$text_domain[] = esc_html__('Attachment Detail','wpbooking');
$text_domain[] = esc_html__("Choose list room's detail",'wpbooking');
$text_domain[] = esc_html__("Delete Permanently",'wpbooking');

$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );
$gallery = $room_data = "";
if(!empty($old_data['gallery'])){
    $gallery = $old_data['gallery'];
}
if(!empty($old_data['room_data'])){
    $room_data = $old_data['room_data'];
}
$list_hotel = WPBooking_Hotel_Service_Type::inst()->_get_room_by_hotel($post_id);

$json_hotel = json_encode($list_hotel);

$field = '<div class="st-metabox-content-wrapper wpbooking-settings"><div class="form-group">';
$field .= '<input type="hidden" id="wp_gallery_hotel" class="wp_gallery_hotel none" value="'. $gallery .'" name="'. $name .'[gallery]">';
$field .= "<input type=\"hidden\" class=\"wb_hotel_gallery_data\" value='".  $room_data ."' name='". $name ."[room_data]' >
			<br>";
$field .= '<div class="featuredgallerydiv gallery-row" data-domain="'.implode(',',$text_domain).'" data-room=\''.$json_hotel.'\'>';

$tmp = '';
if(!empty($old_data['gallery'])) {
    $tmp = explode(',', $old_data['gallery']);
}

if( count( $tmp ) > 0 and !empty( $tmp[ 0 ] ) ){
 	foreach( $tmp as $k => $v ){
        $url = wp_get_attachment_image_src( $v,array(200,200) );
        $url_full = wp_get_attachment_image_src( $v,'full' );
        if( !empty( $url ) ){
            $field .= '<div class="gallery-item">';
            $field .= '<img src="'.esc_url($url[0]).'" class="demo-image-gallery settings-demo-image-gallery" >';
            $field .= '<div class="gallery-item-control text-center">
                        <a href="javascript:void(0)" class="gallery-item-btn gallery-item-edit" data-room-select="" data-url="'.$url_full[0].'" data-id="'.$v.'" ><i class="fa fa-pencil-square-o"></i></a>
                        <a href="javascript:void(0)" data-id="'.$v.'" class="gallery-item-btn gallery-item-remove"><i class="fa fa-trash"></i></a></div>';
            $field .= '</div>';
        }
    }
}


$field .= '</div>';        

$field .= '<div class="clearfix"><button style="margin-right: 10px;" id="" class="button-gallery-primary btn_upload_gallery_hotel mb10" type="button" name="">'. __("Add Gallery","wpbooking").'</button>';
if( count( $tmp = explode(',', $old_data['gallery'] ) ) > 0 ){
    $field .= '<button class="btn_remove_gallery_hotel button-gallery-primary mb10" type="button" name="">'.__("Remove Gallery","wpbooking").'</button>';
}

$field .= '</div></div></div>';

?>
<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
    <div class="st-metabox-full">
        <label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
    </div>
    <div class="st-metabox-full">
        <?php echo $field; ?>
        <i class="wpbooking-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
    </div>

</div>