<?php
$data_value = wpbooking_get_option($data['id'],$data['std']);
$name = 'wpbooking_'.$data['id'];

if(!empty($data['element_list_item'])){
    $name = $data['custom_name'];
}
if(!empty($data['element_list_item'])){
    $data_value = $data['custom_value'];
}

$min = '';
if(!empty($data['min'])){
    $min = $data['min'];
}
$max = '';
if(!empty($data['max'])){
    $min = $data['max'];
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
        <input type="number" id="<?php echo esc_attr($name) ?>" min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($max); ?>" class="form-control" value="<?php echo esc_html($data_value) ?>" name="<?php echo esc_html($name) ?>" placeholder="<?php echo esc_html($data['label']) ?>">
        <i class="wpbooking-desc"><?php echo do_shortcode($data['desc']) ?></i>
    </td>
</tr>