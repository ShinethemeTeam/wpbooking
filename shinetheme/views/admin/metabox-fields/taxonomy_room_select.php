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
$list_room = WPBooking_Accommodation_Service_Type::inst()->_get_room_by_hotel($post_id);
$my_term =wp_get_post_terms($post_id,$data['taxonomy']);
?>




<div class="wpbooking-settings <?php echo esc_html( $class ); ?> field-<?php echo esc_html($data['id']); ?>" <?php echo esc_html( $data_class ); ?>>
    <div class="st-metabox-left">
        <label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group taxonomy_room_select">
                     <input name="<?php echo esc_attr($data['id']) ?>_base[<?php echo esc_attr($data['taxonomy']) ?>]" type="hidden" value="false">
                    <?php
                    if(!empty($data['taxonomy'])){
                        $terms=get_terms($data['taxonomy'],array('taxonomy'=>$data['taxonomy'],'hide_empty'=>false));
                        if(!empty($terms) and !is_wp_error($terms)){
                            $i=1;
                            foreach ($terms as $term) {
                                $checked = "";
                                $checked_all = "";
                                $checked_custom = "";
                                $html_room = '';
                                if(!empty($my_term)){
                                    foreach($my_term as$k=>$v){
                                        if($v->term_id == $term->term_id){
                                            $checked = 'checked="checked"';
                                            if(empty($checked_custom)){
                                                foreach($list_room as $k2=>$v2){
                                                    $data_taxonomy = get_post_meta($v2['ID'],'taxonomy_room',true);
                                                    if(!empty($data_taxonomy[$data['taxonomy']])){
                                                        if(in_array($term->term_id,$data_taxonomy[$data['taxonomy']])){
                                                            $checked_custom = 'checked="checked"';
                                                        }
                                                    }
                                                }
                                            }
                                            if(empty($checked_custom)){
                                                $checked_all = 'checked="checked"';
                                            }
                                        }
                                    }
                                }
                                ?>
                                    <div class="wpbooking-row  <?php if(count($list_room) == 1){ echo 'one_room';}?>">
                                        <div class="wpbooking-col-sm-4">
                                            <label><input class="item_base" <?php echo esc_html($checked) ?> onclick="return false" name="<?php echo esc_attr($data['id']) ?>[<?php echo esc_attr($i) ?>][<?php echo esc_attr($data['taxonomy']) ?>]" value="<?php echo esc_attr($term->term_id) ?>" type="checkbox">
                                                <?php
                                                $icon = get_tax_meta($term->term_id, 'wpbooking_icon');
                                                ?>
                                                <i class="<?php echo wpbooking_handle_icon($icon); ?>"></i>
                                                <?php echo esc_html($term->name) ?>
                                            </label>
                                        </div>
                                         <?php if(count($list_room) != 1){?>
                                            <div class="wpbooking-col-sm-4 class_item_all">
                                                <label><input class="item_all" name="<?php echo esc_attr($data['id']) ?>[<?php echo esc_attr($i) ?>][type_data]"  value="all" <?php echo esc_html($checked_all) ?>  type="checkbox"><?php echo esc_html__("All","wp-booking-management-system") ?></label>
                                            </div>
                                        <?php }?>
                                        <?php if(!empty($list_room)){?>
                                        <div class="wpbooking-col-sm-4 class_item_custom">
                                            <label><input class="item_custom"  name="type_data" value="custom" <?php echo esc_html($checked_custom) ?> type="checkbox"><?php echo esc_html__("Some","wp-booking-management-system") ?></label>
                                            <div class="list_post <?php if(!empty($checked_custom)) echo 'active' ?>">
                                                <?php
                                                if(!empty($list_room)){
                                                    foreach($list_room as $k=>$v){
                                                        $data_taxonomy = get_post_meta($v['ID'],'taxonomy_room',true);
                                                        $checked_post = '';
                                                        if(!empty($data_taxonomy[$data['taxonomy']])){
                                                            if(in_array($term->term_id,$data_taxonomy[$data['taxonomy']])){
                                                                $checked_post = 'checked="checked"';
                                                            }
                                                        }
                                                        ?>
                                                        <p>
                                                            <label><input class="item_post" <?php echo esc_html($checked_post) ?> name="<?php echo esc_attr($data['id']) ?>[<?php echo esc_attr($i) ?>][post_id][]" value="<?php echo esc_html($v['ID']) ?>" type="checkbox"><?php echo esc_html($v['post_title']) ?></label>
                                                        </p>
                                                        <?php
                                                    }
                                                }else{
                                                    ?>
                                                    <p><?php echo esc_html__("No Data",'wp-booking-management-system'); ?></p>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                <?php
                                $i++;
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
    <div class="metabox-help"><?php echo do_shortcode( $data['desc'] ) ?></div>
</div>
