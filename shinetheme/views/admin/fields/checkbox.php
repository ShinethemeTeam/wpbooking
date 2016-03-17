<?php
$data_value = traveler_get_option($data['id'],$data['std']);
$name = 'traveler_booking_'.$data['id'];
if(!empty($data['element_list_item'])){
    $name = $data['custom_name'];
}
if(!empty($data['element_list_item'])){
    $data_value = $data['custom_value'];
}
$is_check="";
if($data_value == 'on'){
    $is_check = "checked";
}
?>
<tr class="<?php echo esc_html($name) ?> traveler-form-group">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <input type="checkbox" id="<?php echo esc_attr($name) ?>" class="form-control min-width-500" <?php echo esc_html($is_check) ?>   name="<?php echo esc_html($name) ?>">
        <?php echo esc_html($data['label']) ?>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>






