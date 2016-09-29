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

?>
<div class="wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
    <div class="st-metabox-left">
        <label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group taxonomy_room_select">

                    <?php
                    if(!empty($data['taxonomy'])){
                        $terms=get_terms($data['taxonomy'],array('taxonomy'=>$data['taxonomy'],'hide_empty'=>false));
                        if(!empty($terms) and !is_wp_error($terms)){
                            foreach ($terms as $term) {
                                //var_dump($term);
                                ?>
                                    <div class="wpbooking-row">
                                        <div class="wpbooking-col-sm-4">
                                            <label><input class="item_base" onclick="return false" name="taxonomy_room[<?php echo esc_attr($data['taxonomy']) ?>][]" value="<?php echo esc_attr($term->slug) ?>" type="checkbox"><?php echo esc_html($term->name) ?></label>
                                        </div>
                                        <div class="wpbooking-col-sm-4">
                                            <label><input class="item_all" type="checkbox"><?php echo esc_html_e("All","wpbooking") ?></label>
                                        </div>
                                        <div class="wpbooking-col-sm-4">
                                            <label><input class="item_custom" type="checkbox"><?php echo esc_html_e("Some","wpbooking") ?></label>
                                            <div class="list_post">
                                                <p>
                                                    <label><input class="item_post" name="taxonomy_room[<?php echo esc_attr($data['taxonomy']) ?>][post_id][]" value="1" type="checkbox"><?php echo esc_html_e("Room 1","wpbooking") ?></label>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                            }
                        }
                    }
                    ?>

                <?php
                if(!empty($data['help_inline'])){
                    printf('<span class="help_inline">%s</span>',$data['help_inline']);
                }
                ?>

            </div>
        </div>
    </div>
    <div class="metabox-help"><?php echo balanceTags( $data['desc'] ) ?></div>
</div>
