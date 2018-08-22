<?php 
/**
*@since 1.0.0
**/
$post_id = $post_id;

$map_lat = (float) get_post_meta( $post_id, 'map_lat', true );

$map_long = (float) get_post_meta( $post_id, 'map_long', true );

$map_zoom = (int) get_post_meta( $post_id, 'map_zoom', true );

$is_show_map = (int) get_post_meta( $post_id, 'is_show_map', true );

if( !$map_zoom ){ $map_zoom = $data['map_zoom']; }

$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition  ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
$field = '<div class="st-metabox-content-wrapper"><div class="form-group">';

$name_lat = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ).'[map_lat][]' : 'map_lat';
$name_long = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ).'[map_long][]' : 'map_long';
$name_zoom = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ).'[map_zoom][]' : 'map_zoom';

$field .= '<div class="wpbooking-gmap-wrapper"><div class="gmap-container"><div id="'.esc_html( $data['id'] ).'" class="gmap-content"></div>
<input type="text" name="gmap-search" value="" placeholder="'.esc_html__('Enter a address...', 'wp-booking-management-system').'" class="gmap-search">
</div>
<input type="hidden" name="'. $name_lat .'" value="'.esc_html( $map_lat ).'">
<input type="hidden" name="'. $name_long .'" value="'.esc_html( $map_long ).'">
<input type="hidden" name="'. $name_zoom .'" value="'.esc_html( $map_zoom ).'">
';

$field .= '</div></div></div>';

?>
<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
<div class="st-metabox-left">
	<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
</div>
<div class="st-metabox-right">
    <label><input type="checkbox" class="btn_show_map" <?php if($is_show_map) echo 'checked'; ?> name="is_show_map" value="1"> <?php echo esc_html__("Show Map","wp-booking-management-system") ?> </label>
	<div class="content-gmap <?php if(!$is_show_map) echo 'nonex'; ?>">
        <br>
        <?php echo do_shortcode($field); ?>
        <i class="wpbooking-desc"><?php echo do_shortcode( $data['desc'] ) ?></i>
    </div>
</div>
</div>