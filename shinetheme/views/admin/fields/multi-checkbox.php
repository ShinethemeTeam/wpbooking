<?php
$custom_value = $data['value'];
?>
<tr class="wpbooking-setting-<?php echo esc_html($data['id']) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <fieldset>
            <ul class="padding-0">
                <?php if(!empty($custom_value)){ ?>
                    <?php foreach($custom_value as $key=>$value){
                        $default = array( 'id' => '' , 'label' => '' , 'std' => '' );
                        $value = wp_parse_args( $value , $default );
                        $data_value = wpbooking_get_option($value['id'],$value['std']);
                        $is_check = "";
                        if($data_value == 'on') {
                            $is_check = "checked";
                        }
                        ?>
                        <li>
                            <label>
                                <input type="checkbox" class="form-control min-width-500" <?php echo esc_html($is_check) ?>   name="wpbooking_booking_<?php echo esc_html($value['id']) ?>">
                                <?php echo esc_html($value['label']) ?>
                            </label>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </fieldset>
        <i class="wpbooking-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>