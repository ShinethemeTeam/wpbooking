<?php
extract($atts);
if(empty($orderby)){
    $orderby = 'date';
}
$meta_key = '';
if($orderby == 'rate'){
    $orderby = 'meta_value';
    $meta_key = 'star_rating';
}
$args = array(
    'post_type' => 'wpbooking_service',
    'order' => $order,
    'orderby' => $orderby,
    'posts_per_page' => $number_per_page,
    /*'meta_query' => array(
        array(
            'key' => 'enable_property',
            'value' => 'on',
            'compare' => 'NOT EXISTS'
        ),
    ),*/
    'meta_key' => $meta_key,
);
if(!empty($location_id)){
    $location_ids = explode(',',$location_id);
    $args['tax_query'][] = array(
        'taxonomy' => 'wpbooking_location',
        'field' => 'id',
        'terms' => $location_ids
    );
}

/* Choose service type( Tour, Accommodation ) */
if(!empty($service_type)){
    $args['meta_query'][] = array(
        array(
            'key' => 'service_type',
            'value' => $service_type,
        ),
    );
}
/*if(empty($layout) || $layout !='grid' && $layout != 'list' && $layout != 'slide'){
    $layout = 'grid';
}*/

/* custom post */
if(!empty($service_id)){
    $ids = explode(',',$service_id);
    $args = array(
        'post_type' => 'wpbooking_service',
        'post__in' => $ids,
        'orderby' => 'post__in',
        'meta_query' => array(
            array(
                'key' => 'enable_property',
                'value' => 'on',
            ),
        ),
    );
}
$services_query = new WP_Query($args);
if($services_query->have_posts()){
    echo '<div class="wpbooking-loop-wrap wpbooking-list-service wpbooking-list-container"><div class="wpbooking-loop-items '.esc_attr($layout).'">';
        while($services_query->have_posts()) {
            $services_query->the_post();
            echo wpbooking_load_view( 'shortcode/services/items/' . $layout . '',array(
                'col' => $post_per_row,
            ) );
        }
        wp_reset_postdata();
        
    echo '</div></div>';
}