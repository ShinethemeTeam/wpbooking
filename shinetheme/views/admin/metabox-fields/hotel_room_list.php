<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 9/30/2016
 * Time: 5:29 PM
 */

$old_data = esc_html($data['std']);

if (!empty($data['custom_name'])) {
    if (isset($data['custom_data'])) $old_data = $data['custom_data'];
} else {
    $old_data = get_post_meta($post_id, esc_html($data['id']), true);
}
if (!empty($value)) {
    $old_data = $value;
}

$class = ' wpbooking-form-group ';
$data_class = '';
if (!empty($data['condition'])) {
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition=' . $data['condition'] . ' ';
}
$class .= ' width-' . $data['width'];
if (!empty($data['container_class'])) $class .= ' ' . $data['container_class'];

$field = '';

$name = isset($data['custom_name']) ? esc_html($data['custom_name']) : esc_html($data['id']);

$query = new WP_Query(array(
    'post_parent'    => $post_id,
    'posts_per_page' => 200,
    'post_type'=>'wpbooking_hotel_room'
))
?>
<div class="wpbooking-settings hotel_room_list <?php echo esc_html($class); ?>" <?php echo esc_html($data_class); ?>>
    <div class="st-metabox-content-wrapper">
        <div class="form-group">
            <h3 class="field-label"><?php echo esc_html($data['label']) ?></h3>
            <p class="field-desc"><?php echo esc_html($data['desc']) ?></p>
            <div class="wb-room-list">
                <?php while ($query->have_posts()){
                    $query->the_post();
                    ?>
                    <div class="room-item">
                        <div class="room-item-wrap">
                            <div class="room-remain">
                                <span class="room-remain-count">1 left</span>
                                <?php $number = get_post_meta(get_the_ID(),'room_number',true);
                                if(empty($number))$number = 0;
                                ?>
                                <span class="room-remain-left"><?php printf(esc_html__('%d room(s)','wpbooking'),$number) ?></span>
                            </div>
                            <div class="room-image">
                                <?php the_post_thumbnail()?>
                            </div>
                            <h3 class="room-type"><?php the_title()?></h3>
                            <div class="room-actions">
                                <a href="#" data-room_id="<?php the_ID()?>" class="room-edit"><i class="fa fa-pencil-square-o"></i></a>
                                <a href="#" class="room-delete"><i class="fa fa-trash"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php
                }

                ?>
            </div>
            <div class="wp-room-actions">
                <div class="room-count"><?php printf(__('There are %s in your listing','wpbooking'),$query->found_posts?'<span class="n">'.$query->found_posts.'</span> <b>'.esc_html__('rooms','wpbooking').'</b>':'<b>'.esc_html__('no room','wpbooking').'</b>'); ?></div>
                <div class="room-create">
                    <a href="#" data-hotel-id="<?php echo esc_attr($post_id)?> " class="create-room"><?php esc_html_e('Create Room','wpbooking') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="wpbooking-hotel-room-form"></div>
<?php
wp_reset_postdata();
?>