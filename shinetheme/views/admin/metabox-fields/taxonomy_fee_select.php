<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 9/29/2016
 * Time: 9:17 AM
 */
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
<div class="wpbooking-settings taxonomy_fee_select <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>

    <div class="st-metabox-content-wrapper">
        <div class="form-group">
            <div class="clearfix">
            <?php
            if(!empty($data['taxonomy'])){
                $terms=get_terms($data['taxonomy'],array('taxonomy'=>$data['taxonomy'],'hide_empty'=>false));
                if(!empty($terms) and !is_wp_error($terms)){
                    foreach ($terms as $term) {
                        ?>
                        <div class="wpbooking-col-sm-4 term-item" >
                            <label><input class="term-checkbox" type="checkbox"><?php echo esc_html($term->name) ?></label>
                            <select class="" name="<?php echo esc_attr($data['id']) ?>[<?php echo esc_html($term->slug) ?>]">
                                    <option value=""><?php echo esc_html__('Please select','wpbooking') ?></option>
                                    <option value="free"><?php echo esc_html__('Yes, free','wpbooking') ?></option>
                                    <option value="paid"><?php esc_html_e('Yes, paid','wpbooking')?></option>
                            </select>
                        </div>
                        <?php
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
    <div class="metabox-help"><?php echo balanceTags( $data['desc'] ) ?></div>
</div>
