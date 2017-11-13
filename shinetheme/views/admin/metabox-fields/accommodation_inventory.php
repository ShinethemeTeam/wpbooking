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
        <div class="wpbooking-inventory-form">
            <span class="mr10"><strong><?php echo esc_html__( 'View by period:', 'wpbooking' ); ?></strong></span>
            <input type="text" name="wpbooking-inventory-start" class="wpbooking-inventory-start" value="" placeholder="<?php echo esc_html__('Start date', 'wpbooking') ?>">
            <input type="text" name="wpbooking-inventory-end" class="wpbooking-inventory-end" value="" placeholder="<?php echo esc_html__('End date', 'wpbooking') ?>">
            <button class="wpbooking-inventory-goto"><?php echo esc_html__( 'View', 'wpbooking' ); ?></button>
        </div>
        <div class="gantt wpbooking-gantt wpbooking-inventory" data-id="<?php echo esc_attr( $post_id ); ?>"
             data-rooms="<?php echo esc_attr( json_encode( $rooms ) ); ?>"></div>
        <div class="wpbooking-inventory-color">
            <div class="inventory-color-item">
                <span class="available"></span> <?php echo esc_html__('Available', 'wpbooking'); ?>
            </div>
            <div class="inventory-color-item">
                <span class="unavailable"></span> <?php echo esc_html__('Unavailable', 'wpbooking'); ?>
            </div>
            <div class="inventory-color-item">
                <span class="out_stock"></span> <?php echo esc_html__('Out of Stock', 'wpbooking'); ?>
            </div>
        </div>
    <?php endif; ?>
