<?php
$data_value = traveler_get_option($data['id'],$data['std']);
$name = 'traveler_booking_'.$data['id'];
if(!empty($data['element_list_item'])){
    $name = $data['custom_name'];
}
if(!empty($data['element_list_item'])){
    $data_value = $data['custom_value'];
}
$terms = get_terms( $data['taxonomy'] ,array('hide_empty' => false));
?>
<tr class="<?php echo esc_html($name) ?> traveler-condition" data-condition="enable_load_more:is(1)">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <?php if(!empty($terms)){ ?>
            <select class="form-control form-control-admin min-width-500" name="<?php echo esc_html($name) ?>">
                <?php echo '<option value="">-- ' . __( 'Choose One', 'traveler-booking' ) . ' --</option>'; ?>
                <?php foreach($terms as $k=>$v){ ?>
                    <option <?php if($data_value == $v->term_id) echo "selected"; ?> value="<?php echo esc_attr($v->term_id) ?>"><?php echo esc_html($v->name) ?></option>
                <?php } ?>
            </select>
        <?php } ?>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>
