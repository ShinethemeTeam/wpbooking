<?php
/**
 *@since 1.0.0
 **/
$data=wp_parse_args($data,array(
    'max_star'=>5
));
$old_data = (isset( $data['custom_data'] ) ) ? esc_html( $data['custom_data'] ) : get_post_meta( $post_id, esc_html( $data['id'] ), true);

$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

$class.=' width-'.$data['width'];
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );

$field = '<div class="st-metabox-content-wrapper"><div class="form-group">';


    $field .= '<div><select name="'. $name .'" id="'. esc_html( $data['id'] ) .'" class="widefat form-control '. esc_html( $data['class'] ).'">';
    for( $i=1;$i<=$data['max_star'] ;$i++ ){

        $checked = '';
        if( !empty( $data['std'] ) && ( esc_html( $i ) == esc_html( $data['std'] ) ) ){
            $checked = ' selected ';
        }
        if( $old_data && !empty( $old_data ) ){
            if( esc_html( $i ) == esc_html( $old_data ) ){
                $checked = ' selected ';
            }else{
                $checked = '';
            }
        }
        $star=' ';
        for($k=1;$k<=$i; $k++){
            $star.='✯';
        }

        $field .= '<option value="'. esc_html( $i ).'" '. $checked .'>'. $i.$star .'</option>';
    }
    $field .= '</select></div>';

$field .= '</div></div>';

?>
<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
    <div class="st-metabox-left">
        <label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
    </div>
    <div class="st-metabox-right">
        <?php echo do_shortcode($field); ?>
        <div class="metabox-help"><?php echo do_shortcode( $data['desc'] ) ?></div>
    </div>
</div>