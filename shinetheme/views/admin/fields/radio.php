<?php $data_value = traveler_get_option($data['id'],array($data['std'])) ?>
<tr class="traveler-setting-<?php echo esc_html($data['id']) ?>">
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
                                <input type="radio" class="form-control min-width-500" <?php echo esc_html($is_check) ?>   name="traveler_booking_<?php echo esc_html($data['id']) ?>" value="<?php echo esc_attr($key) ?>">
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