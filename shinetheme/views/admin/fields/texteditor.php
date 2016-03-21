<?php
$data_value = traveler_get_option($data['id'],$data['std']);
$name = 'traveler_booking_'.$data['id'];

if(!empty($data['element_list_item'])){
    $name = $data['custom_name'];
}
if(!empty($data['element_list_item'])){
    $data_value = $data['custom_value'];
}

$class = str_ireplace('[','_',$name);
$class = str_ireplace(']','_',$class);
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
        <div class="min-width-500 width-800 content_editor">
            <?php
            if(!empty($data['element_list_item'])){
                $content = $data_value;
                $editor_id = 'tmp_traveler_booking_'.$data['id'];
                $settings = array('media_buttons' => false, 'textarea_name' => $name);
                wp_editor($content, $editor_id, $settings);
                ?>
            <?php
            }else{
                wp_editor(stripslashes($data_value),$name);
            }
            ?>
        </div>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>