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
$class.=' wpbooking-form-group ';
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
        <?php if(!empty($data['value'])){ ?>

            <select id="<?php echo esc_attr($name) ?>" class="form-control  min-width-500" name="<?php echo esc_html($name) ?>">
                <?php foreach($data['value'] as $key=>$value){ ?>
                    <option <?php if($data_value == $key) echo "selected"; ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_html($value) ?></option>
                <?php } ?>
            </select>
        <?php } ?>
        <i class="wpbooking-desc"><?php echo do_shortcode($data['desc']) ?></i>
    </td>
</tr>
