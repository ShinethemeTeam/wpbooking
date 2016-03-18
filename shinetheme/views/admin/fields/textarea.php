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
        <label for="<?php echo esc_html($name) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <textarea id="<?php echo esc_html($data['id']) ?>" name="<?php echo esc_html($name) ?>" class="form-control form-control-admin min-width-500"><?php echo esc_html($data_value) ?></textarea>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>

</tr>