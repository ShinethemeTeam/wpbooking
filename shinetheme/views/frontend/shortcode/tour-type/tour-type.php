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
?>
<div class="wpbooking-tour-type col-md-<?php echo esc_attr($col) ?>">
    <div class="tour-type item">
        <a href="<?php echo esc_url(get_term_link($term->term_id)); ?>"> <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_html($term->name); ?>"></a>
        <h4 class="title">
            <a href="<?php echo esc_url(get_term_link($term->term_id)); ?>"><?php echo esc_html($term->name); ?> (<?php echo esc_html($term->count) ?>)</a>
        </h4>
    </div>
</div>
