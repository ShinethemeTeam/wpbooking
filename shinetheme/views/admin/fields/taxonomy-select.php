<?php
if(empty($data['taxonomy'])) return;
$data_value = wpbooking_get_option($data['id'],$data['std']);
$name = 'wpbooking_'.$data['id'];
if(!empty($data['element_list_item'])){
    $name = $data['custom_name'];
}
if(!empty($data['element_list_item'])){
    $data_value = $data['custom_value'];
}
$terms = get_terms( $data['taxonomy'] ,array('hide_empty' => false));
$tax=get_taxonomy($data['taxonomy']);

$class = $name;
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition wpbooking-form-group ';
    $data_class .= ' data-condition=wpbooking_'.$data['condition'].' ' ;
}
?>
<tr class="<?php echo esc_html($class) ?>" <?php echo esc_attr($data_class) ?>>
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <?php if(!empty($terms)){ ?>
            <select class="form-control  min-width-500" name="<?php echo esc_html($name) ?>">
                <?php echo '<option value="">-- ' . esc_html__( 'Choose One', 'wp-booking-management-system' ) . ' --</option>'; ?>
                <?php foreach($terms as $k=>$v){ ?>
                    <option <?php if($data_value == $v->term_id) echo "selected"; ?> value="<?php echo esc_attr($v->term_id) ?>"><?php echo esc_html($v->name) ?></option>
                <?php } ?>
            </select>
        <?php } ?>
        <i class="wpbooking-desc"><?php echo do_shortcode($data['desc']) ?></i>
    </td>
</tr>
