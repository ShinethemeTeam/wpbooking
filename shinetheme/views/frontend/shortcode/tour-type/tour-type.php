<?php
extract($atts);
if(!empty($tag_id)){
    $term = get_term( $tag_id, 'wb_tour_type' );
}
$wpbooking_featured_image = get_tax_meta( $term->term_id, 'featured_image_tour_type' );
$thumbnail_url            = wp_get_attachment_image_url( $wpbooking_featured_image,'medium_large');
switch($col){
    case '3':
        $col='4';
        break;
    case '4':
        $col = '3';
        break;
    case '2':
        $col = '6';
        break;
}

$archive = wpbooking_get_option('archive-page');

$archive_url =  get_permalink($archive);
$taxonomy = [
    'wb_tour_type' => $tag_id
];
$link = add_query_arg(
    array(
        'wpbooking_action' => 'archive_filter',
        'service_type'     => 'tour',
        'taxonomy' => $taxonomy
    ),$archive_url
);

?>
<div class="wpbooking-tour-type col-md-<?php echo esc_attr($col) ?>">
    <div class="tour-type item">
        <a href="<?php echo esc_url($link); ?>"> <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_html($term->name); ?>"></a>
        <h4 class="title">
            <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($term->name); ?> (<?php echo esc_html($term->count) ?>)</a>
        </h4>
    </div>
</div>
