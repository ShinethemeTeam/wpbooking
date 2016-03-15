<?php $value = traveler_get_option($data['id'],$data['std']); ?>
<tr class="<?php echo esc_html($data['id']) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <fieldset>
            <ul class="padding-0">
                <?php if(!empty($data['value'])){ ?>
                    <?php $data_value = traveler_get_option($data['id'],array($data['std']));
                    ?>
                    <?php foreach($data['value'] as $key=>$value){
                        $is_check = "";
                        if(!empty($data_value) and is_array($data_value)){
                            foreach($data_value as $key2=>$value2){
                                if($value2 == $key){
                                    $is_check = "checked";
                                }
                            }
                        }

                        ?>
                        <li>
                            <label>
                                <input type="checkbox" class="form-control min-width-300" <?php echo esc_html($is_check) ?>   name="st_traveler_booking_settings[<?php echo esc_html($data['id'])?>][]" value="<?php echo esc_attr($key) ?>">
                                <?php echo esc_html($value) ?>
                            </label>
                        </li>

                    <?php } ?>
                <?php } ?>
            </ul>
        </fieldset>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>






