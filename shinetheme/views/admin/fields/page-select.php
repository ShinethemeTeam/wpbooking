<?php
    $data_value = wpbooking_get_option( $data[ 'id' ], $data[ 'std' ] );
    $name       = 'wpbooking_' . $data[ 'id' ];
    if ( !empty( $data[ 'element_list_item' ] ) ) {
        $name = $data[ 'custom_name' ];
    }
    if ( !empty( $data[ 'element_list_item' ] ) ) {
        $data_value = $data[ 'custom_value' ];
    }
    $my_posts = new WP_Query( [ 'post_type' => [ 'page' ], 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'post_status' => 'any' ] );
    $class      = $name;
    $data_class = '';
    if ( !empty( $data[ 'condition' ] ) ) {
        $class      .= ' wpbooking-condition wpbooking-form-group ';
        $data_class .= ' data-condition=wpbooking_' . $data[ 'condition' ] . ' ';
    }
?>
    <tr class="<?php echo esc_html( $class ) ?>" <?php echo esc_attr( $data_class ) ?>>
        <th scope="row">
            <label for="<?php echo esc_html( $data[ 'id' ] ) ?>"><?php echo esc_html( $data[ 'label' ] ) ?>:</label>
        </th>
        <td>
            <?php if ( !empty( $my_posts->have_posts() ) ) { ?>
                <select id="<?php echo esc_attr( $name ) ?>" class="form-control  min-width-500"
                        name="<?php echo esc_html( $name ) ?>">
                    <?php echo '<option value="">-- ' . esc_html__( 'Choose One', 'wp-booking-management-system' ) . ' --</option>'; ?>
                    <?php while ( $my_posts->have_posts() ) {
                        $my_posts->the_post(); ?>
                        <option <?php if ( $data_value == get_the_ID() ) echo "selected"; ?>
                                value="<?php echo esc_attr( get_the_ID() ) ?>"><?php echo esc_html( get_the_title() ) ?></option>
                    <?php } ?>
                </select>
            <?php } ?>
            <i class="wpbooking-desc"><?php echo do_shortcode( $data[ 'desc' ] ) ?></i>
        </td>
    </tr>
<?php
    wp_reset_postdata();
?>