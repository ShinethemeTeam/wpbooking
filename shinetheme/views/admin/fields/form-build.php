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
        <div class="form-build width-800">
            <div class="head-control-left">
                <?php _e("Form content",'traveler-booking') ?>
            </div>
            <div class="head-control-right">
                <?php _e("Add field here :",'traveler-booking') ?>
                <select>
                    <option><?php _e("-- Select Field Type --",'traveler-booking') ?></option>
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

        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>