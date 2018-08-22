<?php
$old_data = esc_html( $data['std'] );
if(!empty($data['custom_name'])){
    if(isset($data['custom_data'])) $old_data=$data['custom_data'];
}else{
    $old_data=get_post_meta( $post_id, esc_html( $data['id'] ), true);
}
if( !empty( $value ) ){
    $old_data = $value;
}
$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
$class.=' width-'.$data['width'];
if(!empty($data['container_class'])) $class.=' '.$data['container_class'];
if(!is_array($old_data)) $old_data=array();
$old_data=wp_parse_args($old_data,array(
    'adult'=>array(
        'minimum'=>'',
        'maximum'=>''
    ),
    'child'=>array(
        'minimum'=>'',
        'maximum'=>''
    ),
    'infant'=>array(
        'minimum'=>'',
        'maximum'=>''
    ),
))
?>
<div class="wpbooking-settings <?php echo esc_html( $class ); ?> wb-age-options-field " <?php echo esc_html( $data_class ); ?>>
    <div class="st-metabox-left">
        <label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group">
                <table cellspacing="0" cellpadding="0" class=" wb-age-options-table">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Age Band','wp-booking-management-system') ?></th>
                            <th><?php echo esc_html__('Minimum Age','wp-booking-management-system')?></th>
                            <th><?php echo esc_html__('Maximum Age','wp-booking-management-system')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="wpbooking-condition" data-condition="onoff_people__:not_in(adult)">
                            <td><?php echo esc_html__('Adult','wp-booking-management-system') ?></td>
                            <td><input type="number" class="age_adult_min" min="0" placeholder="0" name="<?php echo esc_attr($data['id']) ?>[adult][minimum]" value="<?php echo esc_attr($old_data['adult']['minimum']) ?>" /></td>
                            <td><input type="number" class="age_adult_max" min="0" placeholder="0" name="<?php echo esc_attr($data['id']) ?>[adult][maximum]" value="<?php echo esc_attr($old_data['adult']['maximum']) ?>" /></td>
                        </tr>
                        <tr class="wpbooking-condition" data-condition="onoff_people__:not_in(child)">
                            <td><?php echo esc_html__('Child','wp-booking-management-system') ?></td>
                            <td><input type="number" class="age_child_min" min="0" placeholder="0" name="<?php echo esc_attr($data['id']) ?>[child][minimum]" value="<?php echo esc_attr($old_data['child']['minimum']) ?>" /></td>
                            <td><input type="number" class="age_child_max" min="0" placeholder="0" name="<?php echo esc_attr($data['id']) ?>[child][maximum]" value="<?php echo esc_attr($old_data['child']['maximum']) ?>" /></td>
                        </tr>
                        <tr class="wpbooking-condition" data-condition="onoff_people__:not_in(infant)">
                            <td><?php echo esc_html__('Infant','wp-booking-management-system') ?></td>
                            <td><input type="number" class="age_infant_min" min="0" placeholder="0" name="<?php echo esc_attr($data['id']) ?>[infant][minimum]" value="<?php echo esc_attr($old_data['infant']['minimum']) ?>" /></td>
                            <td><input type="number" class="age_infant_max" min="0" placeholder="0" name="<?php echo esc_attr($data['id']) ?>[infant][maximum]" value="<?php echo esc_attr($old_data['infant']['maximum']) ?>" /></td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-message error adult_notice hidden"><?php echo esc_html__('Please enter the maximum age greater or equal to the minimum age','wp-booking-management-system');?></div>
                <?php
                if(!empty($data['help_inline'])){
                    printf('<span class="help_inline">%s</span>',$data['help_inline']);
                }
                ?>
            </div>
        </div>
        <div class="metabox-help"><?php echo do_shortcode( $data['desc'] ) ?></div>
    </div>
</div>
