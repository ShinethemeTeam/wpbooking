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
<div class="wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>

    <div class="st-metabox-content-wrapper">
        <div class="form-group">
            <div class="wpbooking-row">
            <?php
            if(!empty($data['taxonomy'])){
                $terms=get_terms($data['taxonomy'],array('taxonomy'=>$data['taxonomy'],'hide_empty'=>false));
                if(!empty($terms) and !is_wp_error($terms)){
                    foreach ($terms as $term) {
                        ?>
                        <div class="wpbooking-col-sm-6">
                            <label><input type="checkbox"><?php echo esc_html($term->name) ?></label>
                            <select class="" name="<?php echo esc_attr($data['id']) ?>[]">
                                    <option value="3">Please select</option>
                                    <option value="4">Yes, free</option>
                                    <option value="5">Yes, paid</option>
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
