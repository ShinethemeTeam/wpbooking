<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 02/07/2018
 * Time: 10:01 SA
 */
extract($atts);
if(!empty($hotel_id)){
    $hotel_id = explode(',',$hotel_id);
}
$args = array(
    'post_type' => 'wpbooking_hotel_room',
    'posts_per_page' => $number_per_page,
    'orderby' => $orderby,
    'order' => $order,
    'post_parent__in' => $hotel_id
);
$room_query = new WP_Query($args);
if($room_query->have_posts()){
    echo '<div class="wpbooking-loop-wrap wpbooking-list-container wpbooking-loop-room-shortcode"><div class="wpbooking-loop-items '.esc_attr($layout).'">';
    while($room_query->have_posts()){
     $room_query->the_post();
        echo wpbooking_load_view( 'shortcode/rooms/items/' . $layout . '',array(
            'col' => $post_per_row,
        ) );
    }
    echo '</div></div>';
}