<?php
if(empty($data['taxonomy'])) return;

$data_value = wpbooking_get_option($data['id'],$data['std']);
if(!is_array($data_value)) $data_value=array();else $data_value=array_values($data_value);

$name = 'wpbooking_'.$data['id'];
if(!empty($data['element_list_item'])){
    $name = $data['custom_name'];
}
if(!empty($data['element_list_item'])){
    $data_value = $data['custom_value'];
}
$terms_array=array();

if(!empty($data['show_only_selected'])){
    $terms_array=array_values($data_value);
}

$terms = get_terms( $data['taxonomy'] ,array('hide_empty' => false,'include'=>$terms_array));
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
    <td class="st-metabox-right">

        <div class="list-terms-checkbox">
            <?php if(!empty($terms)){ ?>
                <?php foreach($terms as $k=>$v){ ?>
                    <div class="term-checkbox">
                        <label>
                            <input <?php echo in_array($v->term_id,$data_value)?'checked':false; ?> type="checkbox" name="<?php echo esc_html($name) ?>[]" value="<?php echo esc_attr($v->term_id) ?>">
                            <span><?php echo esc_html($v->name) ?></span>
                        </label>
                    </div>
                <?php } ?>

            <?php } ?>
        </div>
        <?php if(!empty($data['show_create'])){ ?>

        <div class="add-new-terms">
            <input type="text" class="term-name form-control" placeholder="<?php printf(esc_html__('%s name','wp-booking-management-system'),$tax->label) ?>">
            <a href="#" onclick="return false" class="button button-primary wb-btn-add-term" data-name="<?php echo esc_attr($name) ?>" data-tax="<?php echo esc_attr($data['taxonomy'] ) ?>"><?php echo esc_html__('Add New','wp-booking-management-system') ?> <i class="fa fa-spin  fa-spinner loading-icon"></i></a>
        </div>
        <?php } ?>
        <i class="wpbooking-desc"><?php echo do_shortcode($data['desc']) ?></i>
    </td>
</tr>
