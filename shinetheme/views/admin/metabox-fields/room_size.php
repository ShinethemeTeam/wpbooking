<?php

$old_data = (isset($data['custom_data'])) ? esc_html($data['custom_data']) : get_post_meta($post_id, esc_html($data['id']), TRUE);

$class = ' wpbooking-form-group ';
$data_class = '';
if (!empty($data['condition'])) {
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition=' . $data['condition'] . ' ';
}
if (!empty($data['container_class'])) $class .= ' ' . $data['container_class'];

$class .= ' width-' . $data['width'];
$name = isset($data['custom_name']) ? esc_html($data['custom_name']) : esc_html($data['id']);


?>
<div class="form-table wpbooking-settings <?php echo esc_html($class); ?>" <?php echo esc_html($data_class); ?>>
    <div class="st-metabox-left">
        <label for="<?php echo esc_html($data['id']); ?>"><?php echo esc_html($data['label']); ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group room_size">
                <div class="wpbooking-row room_size_content">
                    <?php
                    $arg =  array(
                        'post_type'      => 'wpbooking_hotel_room',
                        'posts_per_page' => '200',
                        'post_status' => array('pending', 'future', 'publish'),
                        'post_parent'=>$post_id
                    );
                    query_posts($arg);
                    while(have_posts()){
                        the_post();
                        $size=get_post_meta(get_the_ID(), 'room_size', TRUE);
                        if($size<1) $size=1;
                        ?>
                        <div class="wpbooking-col-sm-6">
                            <div class="form-group">
                                <p><?php the_title() ?></p>
                                <div class="input-group">
                                    <input class="form-control" min="1"  id="room_size[<?php the_ID() ?>]" value="<?php echo esc_attr($size)  ?>" name="room_size[<?php the_ID() ?>]"  type="number">
                                    <span data-condition="room_measunit:is(metres)" class="input-group-addon wpbooking-condition">m<sup>2</sup></span>
                                    <span data-condition="room_measunit:is(feet)" class="input-group-addon wpbooking-condition">ft<sup>2</sup></span>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    wp_reset_query();
                    ?>
                </div>
            </div>
        </div>
        <i class="wpbooking-desc"><?php echo do_shortcode($data['desc']) ?></i>
    </div>
</div>
