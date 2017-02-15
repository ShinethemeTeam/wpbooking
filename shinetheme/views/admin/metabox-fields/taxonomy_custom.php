<?php

$data=wp_parse_args($data,array(
    'taxonomy'=>false
));
$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
$class.=' width-'.$data['width'];
if(!empty($data['container_class'])) $class.=' '.$data['container_class'];

$list_tax = WPBooking_Admin_Taxonomy_Controller::inst()->get_taxonomies();
if(!empty($list_tax)){
    foreach($list_tax as $taxonomy_id => $taxonomy){
        if(!empty($taxonomy['service_type']) and in_array($data['service_type'],$taxonomy['service_type'])){
            $data['taxonomy'] = $taxonomy_id;
            $data['label'] = $taxonomy['label'];
            $my_term =wp_get_post_terms($post_id,$data['taxonomy']);
            ?>
            <div class="wpbooking-settings <?php echo esc_html( $class ); ?> field-<?php echo esc_html($data['id']); ?>" <?php echo esc_html( $data_class ); ?>>
                <div class="st-metabox-left">
                    <label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
                </div>
                <div class="st-metabox-right">
                    <div class="st-metabox-content-wrapper-new">
                        <div class="form-group taxonomy_room_select taxonomy_fee_select wpbooking-form-group">
                            <input name="<?php echo esc_attr($data['id']) ?>_base[]" type="hidden" value="<?php echo esc_attr($data['taxonomy']) ?>">
                            <div class="clearfix">
                            <?php
                            if(!empty($data['taxonomy'])){
                                $terms=get_terms($data['taxonomy'],array('taxonomy'=>$data['taxonomy'],'hide_empty'=>false));
                                if(!empty($terms) and !is_wp_error($terms)){
                                    $i=1;
                                    foreach ($terms as $term) {
                                        $checked = "";
                                        if(!empty($my_term)){
                                            foreach($my_term as$k=>$v){
                                                if($v->term_id == $term->term_id){
                                                    $checked = 'checked="checked"';
                                                }
                                            }
                                        }
                                        ?>

                                        <div class="term-item ">
                                                <label><input <?php echo esc_html($checked) ?> name="<?php echo esc_attr($data['id']) ?>[<?php echo esc_attr($data['taxonomy']) ?>][]" value="<?php echo esc_attr($term->term_id) ?>" type="checkbox">
                                                    <?php
                                                    $icon = get_tax_meta($term->term_id, 'wpbooking_icon');
                                                    ?>
                                                    <i class="<?php echo wpbooking_handle_icon($icon); ?>"></i>
                                                    <?php echo esc_html($term->name) ?>
                                                </label>
                                            </div>

                                        <?php
                                        $i++;
                                    }
                                }
                            }
                            ?>
                            </div>
                            <?php
                            if(!empty($data['help_inline'])){
                                printf('<span class="help_inline">%s</span>',$data['help_inline']);
                            }
                            ?>

                        </div>
                    </div>
                </div>
                <div class="metabox-help"><?php echo do_shortcode( $data['desc'] ) ?></div>
            </div>

<?php
        }
    }
}
?>

