<?php
/**
 *@since 1.0.0
 **/

$old_data = (isset( $data['custom_data'] ) ) ? esc_html( $data['custom_data'] ) : get_post_meta( $post_id, esc_html( $data['id'] ), true);

$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

$class.=' width-'.$data['width'];
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );

?>
<div class="form-table wpbooking-settings living_options <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
    <div class="multi-living-option">
        <div class="multi-item-default">
            <label class="multi-item-title"><?php echo esc_html__("Living room #__number_living__","wp-booking-management-system") ?></label>
            <div class="st-metabox-left">
                <label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html__('Number of sofa beds in the room','wp-booking-management-system'); ?></label>
            </div>
            <div class="st-metabox-right">
                <div class="st-metabox-content-wrapper">
                    <div class="form-group">
                        <select name="<?php echo esc_html($name) ?>[__number_living__][sofa]" id="<?php  echo esc_html( $data['id'] ) ?>" class="widefat form-control <?php echo esc_html( $data['class'] ) ?>">
                            <?php
                            for ($i = 1; $i <= 20; $i++) {
                                echo '<option value="' . esc_attr($i) . '" >' . esc_html($i) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="metabox-help"><?php echo do_shortcode( $data['desc'] ) ?></div>
            </div>
            <div class="st-metabox-left">
                <label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html__('Enter the number of guests who can sleep here','wp-booking-management-system'); ?></label>
            </div>
            <div class="st-metabox-right">
                <div class="st-metabox-content-wrapper">
                    <div class="form-group">
                        <select name="<?php  echo esc_html($name) ?>[__number_living__][number]" id="<?php  echo esc_html( $data['id'] ) ?>" class="widefat form-control <?php echo esc_html( $data['class'] ) ?>">
                            <?php
                            for ($i = 1; $i <= 20; $i++) {
                                echo '<option value="' . esc_attr($i) . '" >' . esc_html($i) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="metabox-help"><?php echo do_shortcode( $data['desc'] ) ?></div>
            </div>
        </div>
        <div class="multi-living-options">
            <?php foreach($old_data as $k=>$v){
                $number = $k+1;
                ?>
                <div class="multi-item-row">
                    <label class="multi-item-title"><?php printf(esc_html__('Living room #%d', 'wp-booking-management-system'), $number) ?></label>
                    <div class="st-metabox-left">
                        <label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html__('Number of sofa beds in the room','wp-booking-management-system'); ?></label>
                    </div>
                    <div class="st-metabox-right">
                        <div class="st-metabox-content-wrapper">
                            <div class="form-group">
                                <select name="<?php  echo esc_html($name) ?>[<?php echo esc_attr($number) ?>][sofa]" id="<?php  echo esc_html( $data['id'] ) ?>" class="widefat form-control <?php echo esc_html( $data['class'] ) ?>">
                                    <?php
                                    for ($i = 1; $i <= 20; $i++) {
                                        $check = "";

                                        if($v['sofa'] == $i){
                                            $check = "selected";
                                        }
                                        echo '<option '.esc_attr($check).' value="' . esc_attr($i) . '" >' . esc_html($i) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="metabox-help"><?php echo do_shortcode( $data['desc'] ) ?></div>
                    </div>
                    <div class="st-metabox-left">
                        <label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html__('Enter the number of guests who can sleep here','wp-booking-management-system'); ?></label>
                    </div>
                    <div class="st-metabox-right">
                        <div class="st-metabox-content-wrapper">
                            <div class="form-group">
                                <select name="<?php  echo esc_html($name) ?>[<?php echo esc_attr($number) ?>][number]" id="<?php  echo esc_html( $data['id'] ) ?>" class="widefat form-control <?php echo esc_html( $data['class'] ) ?>">
                                    <?php
                                    for ($i = 1; $i <= 20; $i++) {
                                        $check = "";
                                        if($v['number'] == $i){
                                            $check = "selected";
                                        }
                                        echo '<option '.esc_attr($check).' value="' . esc_attr($i) . '" >' . esc_html($i) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="metabox-help"><?php echo do_shortcode( $data['desc'] ) ?></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>