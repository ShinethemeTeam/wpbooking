<?php
$data=wp_parse_args($data,array(
	'checkbox_label'=>''
));
$data_value = wpbooking_get_option($data['id']);
$name = 'wpbooking_'.$data['id'];
if(!empty($data['element_list_item'])){
    $name = $data['custom_name'];
}
if(!empty($data['element_list_item'])){
    $data_value = $data['custom_value'];
}
$is_check="";
if($data_value == 1 or $data_value=='on'){
    $is_check = "checked";
}
$class = $name;
$class.=' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition  ';
    $data_class .= ' data-condition=wpbooking_'.$data['condition'].' ' ;
}
?>
<tr class="<?php echo esc_html($class) ?> " <?php echo esc_attr($data_class) ?>>
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
		<label >
        <input type="checkbox" id="<?php echo esc_attr($name) ?>" class="form-control min-width-500" <?php echo esc_html($is_check) ?>   name="<?php echo esc_html($name) ?>" value="1">
        <?php echo esc_html($data['checkbox_label']?$data['checkbox_label']:$data['label']) ?>
		</label>
        <i class="wpbooking-desc"><?php echo do_shortcode($data['desc']) ?></i>
    </td>
</tr>






