<?php
$data_value = wpbooking_get_option($data['id'],$data['std']);
$name = 'wpbooking_'.$data['id'];

if(!empty($data['element_list_item'])){
    $name = $data['custom_name'];
}
if(!empty($data['element_list_item'])){
    $data_value = $data['custom_value'];
}
$class = $name;
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition wpbooking-form-group ';
    $data_class .= ' data-condition=wpbooking_'.$data['condition'].' ' ;
}
?>
<tr class="<?php echo esc_html($class) ?>" <?php echo esc_attr($data_class) ?>>
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <input type="hidden" id="fg_metadata" class="fg_metadata none" value="<?php echo esc_html($data_value) ?>" name="<?php echo esc_html($name) ?>">
        <br>
        <div class="featuredgallerydiv max-width-500">
                <?php
                $tmp = explode(',',$data_value);
                if(count( $tmp ) > 0 and !empty($tmp[0])){ ?>
                    <?php foreach($tmp as $k=>$v){ ?>
                        <?php
                        $url = wp_get_attachment_image_url($v);
                        if(!empty($url)){?>
                        <img src="<?php echo esc_url($url) ?>" class="demo-image-gallery settings-demo-gallery" >
                    <?php } } ?>
                <?php } ?>
        </div>
        <button id="btn_upload_gallery" class="btn button button-primary btn_upload_gallery" type="button" name=""><?php echo esc_html__("Add Gallery","wp-booking-management-system") ?></button>
        <?php   if(count($tmp = explode(',',$data_value) ) > 0){ ?>
            <button class="btn button btn_remove_demo_gallery button-secondary" type="button" name=""><?php echo esc_html__("Remove Gallery","wp-booking-management-system") ?></button>
        <?php }?>
        <i class="wpbooking-desc"><?php echo do_shortcode($data['desc']) ?></i>
    </td>
</tr>
<script>

</script>