<?php

$data_value = wpbooking_get_option($data['id'],array($data['std']));
$name = 'wpbooking_'.$data['id'];

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
        <fieldset>
            <ul class="padding-0">
                <?php if(!empty($data['value'])){ ?>
                    <?php foreach($data['value'] as $key=>$value){
                        $is_check = "";
                        if(!empty($data_value)) {
                            if($data_value == $key) {
                                $is_check = "checked";
                            }
                        }

                        ?>
                        <li>
                            <label>
                                <input type="radio" class="form-control min-width-500" <?php echo esc_html($is_check) ?>   name="<?php echo esc_html($name) ?>" value="<?php echo esc_attr($key) ?>">
                                <?php echo esc_html($value) ?>
                            </label>
                        </li>

                    <?php } ?>
                <?php } ?>
            </ul>
        </fieldset>
        <i class="wpbooking-desc"><?php echo do_shortcode($data['desc']) ?></i>
    </td>
</tr>