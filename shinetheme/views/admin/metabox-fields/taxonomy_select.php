<?php
$data = wp_parse_args($data, array(
    'taxonomy' => false
));
$class = ' wpbooking-form-group ';
$data_class = '';
if (!empty($data['condition'])) {
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition=' . $data['condition'] . ' ';
}
$class .= ' width-' . $data['width'];
if (!empty($data['container_class'])) $class .= ' ' . $data['container_class'];
$posts_terms=wp_get_post_terms($post_id,$data['taxonomy']);
$term_ids=array();
if(!empty($posts_terms) and !is_wp_error($posts_terms)){
    foreach ($posts_terms as $terms){
        $term_ids[]=$terms->term_id;
    }
}
$old_data = esc_html( $data['std'] );
if(!empty($data['custom_name'])){
    if(isset($data['custom_data'])) $old_data=$data['custom_data'];
}else{
    $old_data=get_post_meta( $post_id, esc_html( $data['id'] ), true);
}
if( !empty( $value ) ){
    $old_data = $value;
}
?>
<div
    class="wpbooking-settings taxonomy_fee_select <?php echo esc_html($class); ?> field-<?php echo esc_html($data['id']); ?>" <?php echo esc_html($data_class); ?>>
    <div class="st-metabox-left">
        <label for="<?php echo esc_html($data['id']); ?>"><?php echo esc_html($data['label']); ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group">
                <div class="clearfix">
                    <?php
                    if (!empty($data['taxonomy'])) {
                        $terms = get_terms($data['taxonomy'], array('taxonomy' => $data['taxonomy'], 'hide_empty' => false));
                        if (!empty($terms) and !is_wp_error($terms)) {
                            foreach ($terms as $term) {
                                $checked=in_array($term->term_id,$term_ids)?'checked':false;
                                ?>
                                <div class="term-item <?php echo ($checked)?'active':false?>">
                                    <label>
                                        <input class="term-checkbox" <?php echo esc_attr($checked) ?> name="<?php echo esc_attr($data['id']) ?>[]"
                                                  type="checkbox" value="<?php echo esc_attr($term->term_id) ?>">
                                        <?php
                                        $icon = get_tax_meta($term->term_id, 'wpbooking_icon');
                                        ?>
                                        <i class="<?php echo wpbooking_handle_icon($icon); ?>"></i>
                                        <?php echo esc_html($term->name) ?>
                                    </label>

                                </div>
                                <?php
                            }
                        }
                    }
                    ?>
                </div>
                <?php
                if (!empty($data['help_inline'])) {
                    printf('<span class="help_inline">%s</span>', $data['help_inline']);
                }
                ?>

            </div>
        </div>
        <div class="metabox-help"><?php echo do_shortcode($data['desc']) ?></div>
    </div>
</div>
