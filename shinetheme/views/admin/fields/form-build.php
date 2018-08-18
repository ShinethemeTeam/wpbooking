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
        <div class="form-build width-800">
            <div class="head-control-left">
                <?php echo esc_html__("Form content",'wp-booking-management-system') ?>
            </div>
            <div class="head-control-right">
                <?php echo esc_html__("Add field here :",'wp-booking-management-system') ?>
                <select>
                    <option><?php echo esc_html__("-- Select Field Type --",'wp-booking-management-system') ?></option>
                </select>
            </div>
        </div>
        <div class="form-build width-800">
            <div class="control-left">
                <textarea  class="form-control " id="<?php echo esc_attr($name) ?>"></textarea>
            </div>
            <div class="control-right">
                <div class="form-control">
                    <div class="form-field">
                        <label>Field name (*)</label>
                        <input class="" type="text">
                    </div>
                    <div class="form-field">
                        <label>Field name 2(*)</label>
                        <input class="" type="text">
                    </div>
                </div>
            </div>
        </div>

        <i class="wpbooking-desc"><?php echo do_shortcode($data['desc']) ?></i>
    </td>
</tr>