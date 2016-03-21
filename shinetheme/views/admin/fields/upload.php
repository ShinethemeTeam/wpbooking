<?php
$data_value = traveler_get_option($data['id'],$data['std']);
$name = 'traveler_booking_'.$data['id'];

if(!empty($data['element_list_item'])){
    $name = $data['custom_name'];
}
if(!empty($data['element_list_item'])){
    $data_value = $data['custom_value'];
}
$class = $name;
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' traveler-condition traveler-form-group ';
    $data_class .= ' data-condition=traveler_booking_'.$data['condition'].' ' ;
}
?>
<tr class="<?php echo esc_html($class) ?>" <?php echo esc_attr($data_class) ?>>
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <input type="text" id="st_url_media" class="demo-url-image form-control form-control-admin min-width-500" value="<?php echo esc_html($data_value) ?>" name="<?php echo esc_html($name) ?>" placeholder="<?php echo esc_html($data['label']) ?>">
        <button class="btn button btn_remove_demo_image button-secondary" type="button" name=""><?php _e("Remove","traveler-booking") ?></button>
        <br>
        <img src="<?php echo esc_url($data_value) ?>" id="demo_img" class="demo-image form-control settings-demo-image form-control-admin <?php if(empty($data_value)) echo "none"; ?>" >
        <br>
        <button id="btn_upload_media" class="btn button button-primary btn_upload_media" type="button" name=""><?php _e("Upload","traveler-booking") ?></button>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>
