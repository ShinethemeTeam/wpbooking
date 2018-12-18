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
$text_domain[] = esc_html__('Do you want to delete this image?','wp-booking-management-system');
$text_domain[] = esc_html__('Attachment Detail','wp-booking-management-system');
$text_domain[] = esc_html__("Choose the list of room details",'wp-booking-management-system');
$text_domain[] = esc_html__("Delete Permanently",'wp-booking-management-system');

$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );
$gallery = $room_data = "";
if(!empty($old_data['gallery'])){
    $gallery = $old_data['gallery'];
}
if(!empty($old_data['room_data'])){
    $room_data = $old_data['room_data'];
}
$list_room = WPBooking_Accommodation_Service_Type::inst()->_get_room_by_hotel($post_id);

$list_room = json_encode($list_room);

$tmp = [];
if(!empty($old_data['gallery'])) {
    $tmp = explode(',', $old_data['gallery']);
}
if( count( $tmp ) > 0 and !empty( $tmp[ 0 ] ) ){

}else{
    $class .= ' wpbooking-no-gallery ';
}

$show_btn_remove = false;
if(!empty($old_data['gallery']) and count( $tmp = explode(',', $old_data['gallery'] ) ) > 0 ){
    $show_btn_remove = true;
}

?>
<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?> field-<?php echo esc_attr($data['id'])?>" <?php echo esc_html( $data_class ); ?>>
    <div class="st-metabox-full ">
        <label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?><br></label>
    </div>
    <div class="st-metabox-full">
        <div class="no-gallery-notice hidden">
            <h3><?php echo esc_html__('No accommodation photo yet.','wp-booking-management-system') ?></h3>
            <p><?php echo esc_html__('Upload at least a photo','wp-booking-management-system'); ?></p>
        </div>
        <div class="st-metabox-content-wrapper wpbooking-settings">
            <div class="form-group">
                <input type="hidden" id="wp_gallery_hotel" class="wp_gallery_hotel none" value="<?php echo esc_attr($gallery) ?>" name="<?php echo esc_attr($name) ?>[gallery]">
                <input type="hidden" class="wb_hotel_gallery_data" value="<?php echo esc_attr($room_data) ?>" name="<?php echo esc_attr($name) ?>[room_data]" >
                <div class="featuredgallerydiv gallery-row" data-domain="<?php echo esc_attr(implode(',',$text_domain)) ?>" data-room="<?php echo esc_attr($list_room) ?>" >
                    <?php
                    if( count( $tmp ) > 0 and !empty( $tmp[ 0 ] ) ){
                        foreach( $tmp as $k => $v ){
                            $url = wp_get_attachment_image_src( $v,'thumbnail' );
                            $url_full = wp_get_attachment_image_src( $v,'full' );
                            if( !empty( $url ) ){
                                ?>
                                <div class="gallery-item">
                                    <img src="<?php echo esc_url($url[0])?>" class="demo-image-gallery settings-demo-image-gallery" >
                                    <div class="gallery-item-control text-center">
                                        <a href="javascript:void(0)" class="gallery-item-btn gallery-item-edit" data-room-select="" data-url="<?php echo esc_url($url_full[0])?>" data-id="<?php echo  esc_attr($v)?>" ><i class="fa fa-pencil-square-o"></i></a>
                                        <a href="javascript:void(0)" data-id="<?php echo  esc_attr($v)?>" class="gallery-item-btn gallery-item-remove"><i class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    }
                    ?>
                </div>
                <div class="clearfix gallery-control">
                    <button  id="" class="button-gallery-primary btn_upload_gallery_hotel mb10 mr_10" type="button" name=""><?php echo esc_html__("Add Gallery","wp-booking-management-system")?></button>

                    <button class="btn_remove_gallery_hotel button-gallery-primary mb10 <?php if(!$show_btn_remove) echo 'none' ?>" type="button" name=""><?php echo esc_html__("Remove Gallery","wp-booking-management-system")?></button>
                </div>
            </div>
        </div>
        <i class="wpbooking-desc"><?php echo do_shortcode( $data['desc'] ) ?></i>
    </div>

</div>