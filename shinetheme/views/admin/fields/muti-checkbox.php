<?php
$custom_value = $data['value'];
?>
<tr class="traveler-setting-<?php echo esc_html($data['id']) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <fieldset>
            <ul class="padding-0">
                <?php if(!empty($custom_value)){ ?>
                    <?php foreach($custom_value as $key=>$value){
                        $data_value = traveler_get_option($value['id'],$value['std']);
                        $is_check = "";
                        if($data_value == 'on') {
                            $is_check = "checked";
                        }
                        ?>
                        <li>
                            <label>
                                <input type="checkbox" class="form-control min-width-500" <?php echo esc_html($is_check) ?>   name="traveler_booking_<?php echo esc_html($value['id']) ?>">
                                <?php echo esc_html($value['label']) ?>
                            </label>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </fieldset>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>