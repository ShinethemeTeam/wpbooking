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
            <div class="form-group zoom_size">
                <div class="wpbooking-row">
                    <div class="wpbooking-col-sm-6">
                        <div class="form-group">
                            <p><?php esc_html_e('Deluxe Queen Studio', 'wpbooking') ?></p>
                            <div class="input-group">
                                <input class="form-control" id="deluxe_queen_studio" value="<?php echo get_post_meta(get_the_ID(), 'deluxe_queen_studio', TRUE) ?>" name="deluxe_queen_studio"  type="text">
                                <span class="input-group-addon">m<sup>2</sup></span>
                            </div>
                        </div>
                    </div>
                    <div class="wpbooking-col-sm-6">
                        <div class="form-group">
                            <p><?php esc_html_e('Queen room', 'wpbooking') ?></p>
                            <div class="input-group">
                                <input class="form-control" id="deluxe_queen_studio" value="<?php echo get_post_meta(get_the_ID(), 'queen_room', TRUE) ?>" name="queen_room"  type="text">
                                <span class="input-group-addon">m<sup>2</sup></span>
                            </div>
                        </div>
                    </div>
                    <div class="wpbooking-col-sm-6">
                        <div class="form-group">
                            <p><?php esc_html_e('Double room', 'wpbooking') ?></p>
                            <div class="input-group">
                                <input class="form-control" id="double_room" value="<?php echo get_post_meta(get_the_ID(), 'double_room', TRUE) ?>" name="double_room"  type="text">
                                <span class="input-group-addon">m<sup>2</sup></span>
                            </div>
                        </div>
                    </div>
                    <div class="wpbooking-col-sm-6">
                        <div class="form-group">
                            <p><?php esc_html_e('Single room', 'wpbooking') ?></p>
                            <div class="input-group">
                                <input class="form-control" id="single_room" value="<?php echo get_post_meta(get_the_ID(), 'single_room', TRUE) ?>" name="single_room"  type="text">
                                <span class="input-group-addon">m<sup>2</sup></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <i class="wpbooking-desc"><?php echo balanceTags($data['desc']) ?></i>
    </div>
</div>
