<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 12/5/2016
 * Time: 4:24 PM
 */

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
                            <th><?php esc_html_e('Age Band','wpbooking') ?></th>
                            <th><?php esc_html_e('Minimum Age','wpbooking')?></th>
                            <th><?php esc_html_e('Maximum Age','wpbooking')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php esc_html_e('Adult','wpbooking') ?></td>
                            <td><input type="text" name="<?php echo esc_attr($data['id']) ?>[adult][minimum]" value="<?php echo esc_attr($old_data['adult']['minimum']) ?>" /></td>
                            <td><input type="text" name="<?php echo esc_attr($data['id']) ?>[adult][maximum]" value="<?php echo esc_attr($old_data['adult']['maximum']) ?>" /></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('Child','wpbooking') ?></td>
                            <td><input type="text" name="<?php echo esc_attr($data['id']) ?>[child][minimum]" value="<?php echo esc_attr($old_data['child']['minimum']) ?>" /></td>
                            <td><input type="text" name="<?php echo esc_attr($data['id']) ?>[child][maximum]" value="<?php echo esc_attr($old_data['child']['maximum']) ?>" /></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('Infant','wpbooking') ?></td>
                            <td><input type="text" name="<?php echo esc_attr($data['id']) ?>[infant][minimum]" value="<?php echo esc_attr($old_data['infant']['minimum']) ?>" /></td>
                            <td><input type="text" name="<?php echo esc_attr($data['id']) ?>[infant][maximum]" value="<?php echo esc_attr($old_data['infant']['maximum']) ?>" /></td>
                        </tr>
                    </tbody>
                </table>

                <?php
                if(!empty($data['help_inline'])){
                    printf('<span class="help_inline">%s</span>',$data['help_inline']);
                }
                ?>
            </div>
        </div>
        <div class="metabox-help"><?php echo balanceTags( $data['desc'] ) ?></div>
    </div>
</div>
