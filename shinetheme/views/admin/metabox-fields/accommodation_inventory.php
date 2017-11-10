<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 11/8/2017
     * Time: 1:50 PM
     * Since: 1.0.0
     * Updated: 1.0.0
     */

    wp_enqueue_script( 'inventory-js' );
    wp_enqueue_style( 'gantt-css' );

    $post_id = ( isset( $_GET[ 'post' ] ) ) ? (int)$_GET[ 'post' ] : 0;
    if ( !$post_id ):
        ?>
        <div class="wpbooking-condition desc-item service-type-accommodation">
            <strong>Note:</strong> Please save this accommodation before you can see inventory.
        </div>
    <?php else:
        global $post;
        $old_post = $post;
        $args     = [
            'post_type'      => 'wpbooking_hotel_room',
            'posts_per_page' => -1,
            'post_parent'    => $post_id
        ];

        $rooms = [];
        $query = new WP_Query( $args );
        while ( $query->have_posts() ): $query->the_post();
            $rooms[] = [
                'id'   => get_the_ID(),
                'name' => get_the_title()
            ];
        endwhile;
        wp_reset_postdata();
        $post = $old_post;
        ?>
        <div class="gantt wpbooking-gantt wpbooking-inventory" data-id="<?php echo esc_attr( $post_id ); ?>"
             data-rooms="<?php echo esc_attr( json_encode( $rooms ) ); ?>"></div>
    <?php endif; ?>
