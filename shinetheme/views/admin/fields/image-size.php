<?php
$data_value = wpbooking_get_option($data['id'],$data['std']);
$name = 'wpbooking_'.$data['id'];

if(!empty($data['element_list_item'])){
    $name = $data['custom_name'];
}
if(!empty($data['element_list_item'])){
    $data_value = $data['custom_value'];
}

$width = $height = $crop = '';
if(!empty($data_value)){
    $tmp_value = explode(',',$data_value);
    $width = $tmp_value[0];
    $height = $tmp_value[1];
    $crop = $tmp_value[2];
}
?>
<tr class="<?php echo esc_html($name) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>

        <input type="text" class="form-control  width-70 wpbooking_image_thumb_width" value="<?php echo esc_html($width) ?>" placeholder="<?php echo esc_html__("Width",'wp-booking-management-system') ?>">
        X
        <input type="text" class="form-control  width-70 wpbooking_image_thumb_height" value="<?php echo esc_html($height) ?>" placeholder="<?php echo esc_html__("Height",'wp-booking-management-system') ?>">
		<!--<label >
        <input type="checkbox" class="form-control  width-70 wpbooking_image_thumb_crop" <?php if($crop == "on") echo "checked" ?>  >
        <?php //echo esc_html__("Crop the image ?",'wp-booking-management-system') ?>
		</label>-->
        <input type="hidden" class="data_value" value="<?php echo esc_html($data_value) ?>" name="<?php echo esc_attr($name) ?>">
        <i class="wpbooking-desc"><?php echo do_shortcode($data['desc']) ?></i>
    </td>
</tr>