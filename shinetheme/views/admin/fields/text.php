<?php
$data=wp_parse_args($data,array(
	'placeholder'=>''
));
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
        <input type="text" id="<?php echo esc_attr($name) ?>" class="form-control  min-width-500" value="<?php echo esc_html($data_value) ?>" name="<?php echo esc_html($name) ?>" placeholder="<?php echo esc_html($data['placeholder']) ?>">
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>