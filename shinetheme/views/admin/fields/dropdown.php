<?php
$data_value = traveler_get_option($data['id'],$data['std']);
$name = 'traveler_booking_'.$data['id'];

if(!empty($data['element_list_item'])){
    $name = $data['custom_name'];
}
if(!empty($data['element_list_item'])){
    $data_value = $data['custom_value'];
}
?>
<tr class="<?php echo esc_html($name) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <?php if(!empty($data['value'])){ ?>

            <select class="form-control form-control-admin min-width-500" name="<?php echo esc_html($name) ?>">
                <?php foreach($data['value'] as $key=>$value){ ?>
                    <option <?php if($data_value == $key) echo "selected"; ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_html($value) ?></option>
                <?php } ?>
            </select>
        <?php } ?>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>
