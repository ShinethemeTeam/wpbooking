<?php
    if ( !class_exists( 'WPBooking_Widget_Form_Search' ) ) {
        class WPBooking_Widget_Form_Search extends WP_Widget
        {
            static $_inst;
            public function __construct()
            {
                $widget_ops = [ 'classname' => '', 'description' => "[WPBooking] Search Form" ];
                parent::__construct( __CLASS__, esc_html__( 'WPBooking Search Form', "wp-booking-management-system" ), $widget_ops );
            }

            /**
             * @param array $args
             * @param array $instance
             */
            public function widget( $args, $instance )
            {

                $widget_args = wp_parse_args( $args, [
                    'before_widget' => '',
                    'after_widget'  => '',
                    'before_title'  => '',
                    'after_title'   => '',
                ] );
                extract( $instance = wp_parse_args( $instance, [ 'title' => '', 'service_type' => '', 'field_search' => "", 'before_widget' => false, 'after_widget' => false ] ) );
                $service_type = $instance[ 'service_type' ];
                $title        = apply_filters( 'widget_title', empty( $title ) ? '' : $title, $instance, $this->id_base );

                echo do_shortcode( $widget_args[ 'before_widget' ] );

                $page_search = get_post_type_archive_link( 'wpbooking_service' );

                $search_more_fields = [];
                ?>
                <form class="wpbooking-search-form is_search_form" action="<?php echo esc_url( $page_search ) ?>">

                    <?php
                        if ( !empty( $instance[ 'title' ] ) ) {
                            echo do_shortcode( $widget_args[ 'before_title' ] ) . apply_filters( 'widget_title', $instance[ 'title' ] ) . $widget_args[ 'after_title' ];
                        }
                        $hidden_fields = $_GET;
                    ?>
                    <input type="hidden" name="wpbooking_action" value="archive_filter">
                    <input type="hidden" name="service_type" value="<?php echo esc_attr( $service_type ) ?>">
                    <input type="hidden" name="wpbooking_search_form_archive" value="<?php echo esc_attr($this->number); ?>">

                    <div class="wpbooking-search-form-wrap">
                        <?php
                            if ( !empty( $field_search[ $service_type ] ) ) {
                                foreach ( $field_search[ $service_type ] as $k => $v ) {

                                    // Calculate Hidden Fields
                                    if ( !empty( $hidden_fields[ $v[ 'field_type' ] ] ) ) {
                                        unset( $hidden_fields[ $v[ 'field_type' ] ] );
                                    }
                                    if ( $v[ 'field_type' ] == 'location_suggestion' ) {
                                        unset( $hidden_fields[ 'location_id' ] );
                                    }

                                    $v = wp_parse_args( $v, [
                                        'in_more_filter' => ''
                                    ] );
                                    if ( $v[ 'in_more_filter' ] ) {
                                        $search_more_fields[ $k ] = $v;
                                        continue;
                                    }
                                    $this->get_field_html( $v, $service_type );
                                }
                            } ?>

                        <?php if ( !empty( $search_more_fields ) ) {  ?>
                            <div class="wpbooking-search-form-more-wrap">
                                <a href="#" onclick="return false" class="btn btn-link wpbooking-show-more-fields"><span
                                            class=""><?php echo esc_html__( 'Advanced Search', 'wp-booking-management-system' ) ?> <i
                                                class="fa fa-caret-down" aria-hidden="true"></i></span></a>
                                <div class="wpbooking-search-form-more">
                                    <?php
                                        foreach ( $search_more_fields as $k => $v ) {
                                            $this->get_field_html( $v, $service_type );
                                        } ?>
                                </div>
                            </div>
                            <?php
                        } ?>
                        <div class="search-button-wrap">
                            <button class="wb-button"
                                    type="submit"><?php echo esc_html__( "Search", 'wp-booking-management-system' ) ?></button>
                        </div>
                    </div>
                    <?php
                        if ( $layout = WPBooking_Input::get( 'layout' ) ) {
                            echo '<input type="hidden" name="layout" value="' . $layout . '">';
                        }
                    ?>
                </form>
                <?php

                echo do_shortcode( $widget_args[ 'after_widget' ] );
            }

            function get_field_html( $v, $service_type )
            {

                $required = "";
                if ( $v[ 'required' ] == "yes" ) {
                    $required = 'required';
                }
                $value = WPBooking_Input::get( $v[ 'field_type' ], '' );
                switch ( $v[ 'field_type' ] ) {
                    case "location_id":
                    case "location_suggestion":
                        ?>
                        <div class="item-search item-search-location">
                            <label
                                    for="<?php echo esc_html( $v[ 'field_type' ] ) ?>"><?php echo esc_html( $v[ 'title' ] ) ?></label>

                            <div class="item-search-content">
                                <?php
                                    $class = false;
                                    if ( $v[ 'field_type' ] == 'location_suggestion' ) {
                                        $class = 'wpbooking-select2';
                                    }
                                    if ( $v[ 'required' ] == 'yes' ) $class .= ' wb-required';
                                    $args        = [
                                        'show_option_none'  => (!empty($v['placeholder']))? esc_html($v['placeholder']) :esc_html__( '-- Select --', "wp-booking-management-system" ),
                                        'option_none_value' => "",
                                        'hierarchical'      => 1,
                                        'name'              => 'location_id',
                                        'class'             => $class,
                                        'id'                => $v[ 'field_type' ],
                                        'taxonomy'          => 'wpbooking_location',
                                        'orderby'           => 'name',
                                        'order'             => 'ASC',
                                        'hide_empty'        => 0,
                                    ];
                                    $is_taxonomy = WPBooking_Input::get( 'location_id' );
                                    if ( !empty( $is_taxonomy ) ) {
                                        $args[ 'selected' ] = $is_taxonomy;
                                    }
                                    wp_dropdown_categories( $args );
                                ?>
                            </div>
                            <div class="wb-collapse"></div>
                        </div>
                        <?php
                        break;
                    case "pickup":
                        ?>
                        <div class="item-search item-search-pickup">
                            <label
                                    for="<?php echo esc_html( $v[ 'field_type' ] ) ?>"><?php echo esc_html( $v[ 'title' ] ) ?></label>

                            <div class="item-search-content">
                                <?php
                                    $class = false;
                                    if ( $v[ 'required' ] == 'yes' ) $class .= ' wb-required';
                                    $args        = [
                                        'show_option_none'  => (!empty($v['placeholder']))? esc_html($v['placeholder']) :esc_html__( '-- Select --', "wp-booking-management-system" ),
                                        'option_none_value' => "",
                                        'hierarchical'      => 1,
                                        'name'              => 'pickup',
                                        'class'             => $class,
                                        'id'                => $v[ 'field_type' ],
                                        'taxonomy'          => 'wpbooking_location',
                                        'orderby'           => 'name',
                                        'order'             => 'ASC',
                                        'hide_empty'        => 0,
                                    ];
                                    $is_taxonomy = WPBooking_Input::get( 'pickup' );
                                    if ( !empty( $is_taxonomy ) ) {
                                        $args[ 'selected' ] = $is_taxonomy;
                                    }
                                    wp_dropdown_categories( $args );
                                ?>
                            </div>
                            <div class="wb-collapse"></div>
                        </div>
                        <?php
                        break;
                    case "dropoff":
                        ?>
                        <div class="item-search item-search-dropoff">
                            <label
                                    for="<?php echo esc_html( $v[ 'field_type' ] ) ?>"><?php echo esc_html( $v[ 'title' ] ) ?></label>

                            <div class="item-search-content">
                                <?php
                                    $class = false;
                                    if ( $v[ 'required' ] == 'yes' ) $class .= ' wb-required';
                                    $args        = [
                                        'show_option_none'  => (!empty($v['placeholder']))? esc_html($v['placeholder']) :esc_html__( '-- Select --', "wp-booking-management-system" ),
                                        'option_none_value' => "",
                                        'hierarchical'      => 1,
                                        'name'              => 'dropoff',
                                        'class'             => $class,
                                        'id'                => $v[ 'field_type' ],
                                        'taxonomy'          => 'wpbooking_location',
                                        'orderby'           => 'name',
                                        'order'             => 'ASC',
                                        'hide_empty'        => 0,
                                    ];
                                    $is_taxonomy = WPBooking_Input::get( 'dropoff' );
                                    if ( !empty( $is_taxonomy ) ) {
                                        $args[ 'selected' ] = $is_taxonomy;
                                    }
                                    wp_dropdown_categories( $args );
                                ?>
                            </div>
                            <div class="wb-collapse"></div>
                        </div>
                        <?php
                        break;
                    case "taxonomy":
                        if ( !empty( $v[ 'taxonomy' ] ) )
                            $tax = get_taxonomy( $v[ 'taxonomy' ] );
                        if ( $tax )
                            $terms = get_terms( $v[ 'taxonomy' ], [ 'hide_empty' => false, ] );
                        if ( empty( $terms ) )
                            continue;
                        ?>
                        <div class="item-search item-search-taxonomy">
                            <label
                                    for="<?php echo esc_html( $v[ 'field_type' ] ) ?>"><?php echo esc_html( $v[ 'title' ] ) ?></label>

                            <div class="item-search-content">
                                <?php
                                    $class = '';
                                    if ( $v[ 'required' ] == 'yes' ) $class = ' wb-required';
                                    if ( $v[ 'taxonomy_show' ] == 'dropdown' ) {
                                        $args        = [
                                            'show_option_none'  => (!empty($v['placeholder']))? esc_html($v['placeholder']) : esc_html__( '-- Select --', "wp-booking-management-system" ),
                                            'option_none_value' => "",
                                            'hierarchical'      => 1,
                                            'name'              => $v[ 'field_type' ] . '[' . $v[ 'taxonomy' ] . ']',
                                            'class'             => $class,
                                            'id'                => $v[ 'field_type' ] . '[' . $v[ 'taxonomy' ] . ']',
                                            'taxonomy'          => $v[ 'taxonomy' ],
                                            'hide_empty'        => 0,
                                        ];
                                        $is_taxonomy = WPBooking_Input::request( $v[ 'field_type' ] );
                                        if ( !empty( $is_taxonomy[ $v[ 'taxonomy' ] ] ) ) {
                                            $args[ 'selected' ] = $is_taxonomy[ $v[ 'taxonomy' ] ];
                                        }
                                        wp_dropdown_categories( $args );
                                        ?>
                                        <input type="hidden" value="<?php echo esc_attr( $v[ 'taxonomy_operator' ] ) ?>"
                                               name="<?php echo esc_attr( "taxonomy_operator" . '[' . esc_attr( $v[ 'taxonomy' ] ) . ']' ) ?>"/>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="item-search-content">
                                            <div class="list-checkbox">
                                                <?php
                                                    $value_item = false;
                                                    if ( !empty( $v[ 'taxonomy' ] ) ) {
                                                        $tax = get_taxonomy( $v[ 'taxonomy' ] );
                                                        if ( $tax ) {
                                                            $terms = get_terms( $v[ 'taxonomy' ], [ 'hide_empty' => false, ] );

                                                            $show_number = 5;

                                                            if ( !empty( $value[ $v[ 'taxonomy' ] ] ) ) $value_item = $value[ $v[ 'taxonomy' ] ]; else $value_item = false;
                                                            if ( !empty( $terms ) ) {
                                                                foreach ( $terms as $key2 => $value2 ) {
                                                                    $check = "";
                                                                    if ( in_array( $value2->term_id, explode( ',', $value_item ) ) ) {
                                                                        $check = "checked";
                                                                    }
                                                                    if ( is_tax( $v[ 'taxonomy' ] ) and get_queried_object()->term_id == $value2->term_id ) {
                                                                        $check = "checked";
                                                                    }
                                                                    $class = false;
                                                                    if ( $key2 >= $show_number ) {
                                                                        $class = 'hidden_term';
                                                                    }
                                                                    ?>
                                                                    <div
                                                                            class="term-item <?php echo esc_attr( $class ) ?>">
                                                                        <label><input
                                                                                    class="wb-checkbox-search item_taxonomy"
                                                                                    type="checkbox" <?php echo esc_html( $check ) ?>
                                                                                    id="<?php echo "item_" . $value2->term_id ?>"
                                                                                    value="<?php echo esc_html( $value2->term_id ) ?>">
                                                                            <?php echo esc_html( $value2->name ) ?>
                                                                        </label>
                                                                    </div>
                                                                    <?php
                                                                    if ( $key2 == ( $show_number - 1 ) and count( $terms ) > $show_number ) {
                                                                        ?>
                                                                        <div class="">
                                                                            <label
                                                                                    class="show-more-terms"><?php echo esc_html__( 'More...', 'wp-booking-management-system' ) ?></label>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                }
                                                            }
                                                        }

                                                    }

                                                ?>
                                                <input type="hidden" value="<?php echo esc_attr( $value_item ) ?>"
                                                       class="data_taxonomy"
                                                       name="<?php echo esc_attr( $v[ 'field_type' ] . '[' . esc_attr( $v[ 'taxonomy' ] ) . ']' ) ?>"/>
                                                <input type="hidden"
                                                       value="<?php echo esc_attr( $v[ 'taxonomy_operator' ] ) ?>"
                                                       name="<?php echo esc_attr( "taxonomy_operator" . '[' . esc_attr( $v[ 'taxonomy' ] ) . ']' ) ?>"/>
                                            </div>
                                        </div>
                                    <?php } ?>
                            </div>
                            <div class="wb-collapse"></div>
                        </div>
                        <?php
                        break;

                    //Hotel star
                    case 'star_rating':
                        ?>
                        <div class="item-search item-search-star">
                            <label
                                    for="<?php echo esc_html( $v[ 'field_type' ] ) ?>"><?php echo esc_html( $v[ 'title' ] ) ?></label>

                            <div class="item-search-content">
                                <div class="list-checkbox list_star">
                                    <?php
                                        $data = [
                                            "5" => esc_html__( '5 stars', 'wp-booking-management-system' ),
                                            "4" => esc_html__( '4 stars', 'wp-booking-management-system' ),
                                            "3" => esc_html__( '3 stars', 'wp-booking-management-system' ),
                                            "2" => esc_html__( '2 stars', 'wp-booking-management-system' ),
                                            "1" => esc_html__( '1 star', 'wp-booking-management-system' ),
                                        ];
                                        if ( !empty( $data ) ) {
                                            foreach ( $data as $key2 => $value2 ) {
                                                $check = "";
                                                if ( in_array( $key2, explode( ',', $value ) ) ) {
                                                    $check = "checked";
                                                }
                                                ?>
                                                <label><input
                                                            class="wb-checkbox-search item_taxonomy <?php if ( $v[ 'required' ] == 'yes' ) echo 'wb-required' ?>"
                                                            type="checkbox" <?php echo esc_html( $check ) ?>
                                                            id="<?php echo "item_" . $key2 ?>"
                                                            value="<?php echo esc_html( $key2 ) ?>">
                                                    <span class="label_star"> <?php echo( $value2 ) ?></span>
                                                    <?php
                                                        /* #### */
                                                        for($i=0;$i<$key2;$i++){
                                                            echo '<span class="icon_star"><i class="fa fa-star"></i></span>';
                                                        }
                                                    ?>
                                                </label>
                                                <?php
                                            }
                                        }
                                    ?>
                                    <input type="hidden" value="<?php echo esc_attr( $value ) ?>" class="data_taxonomy"
                                           name="<?php echo esc_attr( $v[ 'field_type' ] ) ?>">
                                </div>
                            </div>
                            <div class="wb-collapse"></div>
                        </div>
                        <?php
                        break;

                    case "check_in":
                        $check_in = WPBooking_Input::request( 'checkin_y' ) . "-" . WPBooking_Input::request( 'checkin_m' ) . "-" . WPBooking_Input::request( 'checkin_d' );
                        if ( $check_in == '--' ) $check_in = ''; else$check_in = date( wpbooking_date_format(), strtotime( $check_in ) );
                        if ( empty( $check_in ) ) {
                            $check_in = date( wpbooking_date_format() );
                        }

                        $check_out = WPBooking_Input::request( 'checkout_y' ) . "-" . WPBooking_Input::request( 'checkout_m' ) . "-" . WPBooking_Input::request( 'checkout_d' );
                        if ( $check_out == '--' ) $check_out = ''; else$check_out = date( wpbooking_date_format(), strtotime( $check_out ) );
                        if ( empty( $check_out ) ) {
                            $check_out = date( wpbooking_date_format(), strtotime( '+1 day', strtotime( date( 'Y-m-d' ) ) ) );
                        }
                        $check_in_out = current_time( wpbooking_date_format() ) . '-' . date( wpbooking_date_format(), strtotime( '+1 day', current_time( 'timestamp' ) ) );

                        $currentdate = current_time( 'timestamp' );
                        $nextdate    = strtotime( '+1 day', $currentdate );

                        $id = $v[ 'field_type' ] . '_' . rand( 0, time() );

                        $title = $v[ 'title' ];

                        wp_enqueue_script( 'wpbooking-daterangepicker-js' );
                        wp_enqueue_style( 'wpbooking-daterangepicker' );
                        ?>
                        <div class="item-search-checkin date-group clearfix">
                            <label class="title"
                                   for="<?php echo esc_attr( $id ) ?>"><?php echo esc_html( $title ); ?></label>
                            <div class="item-search datepicker-field">
                                <div class="item-search-content">
                                    <label>
                                        <input class="checkin_d" name="checkin_d"
                                               value="<?php echo esc_html( WPBooking_Input::request( 'checkin_d', date( 'd', $currentdate ) ) ) ?>"
                                               type="hidden">
                                        <input class="checkin_m" name="checkin_m"
                                               value="<?php echo esc_html( WPBooking_Input::request( 'checkin_m', date( 'm', $currentdate ) ) ) ?>"
                                               type="hidden">
                                        <input class="checkin_y" name="checkin_y"
                                               value="<?php echo esc_html( WPBooking_Input::request( 'checkin_y', date( 'Y', $currentdate ) ) ) ?>"
                                               type="hidden">
                                        <input
                                                class="wpbooking-date-start <?php if ( $v[ 'required' ] == 'yes' ) echo 'wb-required' ?>"
                                                readonly type="text" <?php echo esc_html( $required ) ?>
                                                id="<?php echo esc_attr( $id ) ?>"
                                                placeholder="<?php echo esc_html( $v[ 'placeholder' ] ) ?>"
                                                value="<?php echo esc_html( $check_in ) ?>">

                                        <input class="wpbooking-check-in-out" type="text" name="check_in_out"
                                               value="<?php echo esc_html( WPBooking_Input::request( 'check_in_out', $check_in_out ) ); ?>">
                                    </label>
                                </div>
                                <div class="wb-collapse"></div>
                            </div>
                            <i class="fa fa-long-arrow-right arrow-icon"></i>
                            <div class="item-search datepicker-field">
                                <div class="item-search-content">
                                    <label>
                                        <input class="checkout_d" name="checkout_d"
                                               value="<?php echo esc_html( WPBooking_Input::request( 'checkout_d', date('d', $nextdate) ) ) ?>"
                                               type="hidden">
                                        <input class="checkout_m" name="checkout_m"
                                               value="<?php echo esc_html( WPBooking_Input::request( 'checkout_m', date('m', $nextdate) ) ) ?>"
                                               type="hidden">
                                        <input class="checkout_y" name="checkout_y"
                                               value="<?php echo esc_html( WPBooking_Input::request( 'checkout_y', date('Y', $nextdate) ) ) ?>"
                                               type="hidden">
                                        <input
                                                class="wpbooking-date-end <?php if ( $v[ 'required' ] == 'yes' ) echo 'wb-required' ?>"
                                                readonly type="text" <?php echo esc_html( $required ) ?>
                                                id="<?php echo esc_attr( $id ) ?>"
                                                placeholder="<?php echo esc_html( $v[ 'placeholder' ] ) ?>"
                                                value="<?php echo esc_html( $check_out ) ?>">
                                    </label>
                                </div>
                                <div class="wb-collapse"></div>
                            </div>
                        </div>
                        <?php
                        break;
                    case 'adult_child':
                        ?>
                        <div class="item-search item-search-adult item-adult-search">
                            <label for="adult_s"><?php echo esc_html__( 'Adult', 'wp-booking-management-system' ); ?></label>
                            <div class="item-search-content">
                                <select id="adult_s" name="adult_s"
                                        class="small-input <?php if ( $v[ 'required' ] == 'yes' ) echo 'wb-required' ?>">
                                    <option value=""><?php echo esc_html__( '- Select -', 'wp-booking-management-system' ) ?></option>
                                    <?php for ( $i = 1; $i <= 20; $i++ ) {
                                        printf( '<option value="%s" %s>%s</option>', $i, selected( WPBooking_Input::get( 'adult_s' ), $i, false ), $i );
                                    } ?>
                                </select>
                            </div>
                            <div class="wb-collapse"></div>
                        </div>
                        <div class="item-search item-search-child item-child-search">
                            <label for="child_s"><?php echo esc_html__( 'Children', 'wp-booking-management-system' ); ?></label>
                            <div class="item-search-content">
                                <select id="child_s" name="child_s"
                                        class="small-input <?php if ( $v[ 'required' ] == 'yes' ) echo 'wb-required' ?>">
                                    <option value=""><?php echo esc_html__( '- Select -', 'wp-booking-management-system' ) ?></option>
                                    <?php for ( $i = 1; $i <= 20; $i++ ) {
                                        printf( '<option value="%s" %s>%s</option>', $i, selected( WPBooking_Input::get( 'child_s' ), $i, false ), $i );
                                    } ?>
                                </select>
                            </div>
                            <div class="wb-collapse"></div>
                        </div>
                        <?php
                        break;
                    case "price":
                        wp_enqueue_script( 'wpbooking-ion-range-slider' );
                        wp_enqueue_style( 'wpbooking-ion-range-slider' );
                        wp_enqueue_style( 'wpbooking-ion-range-slider-html5' );

                        $min_max_price = WPBooking_Service_Model::inst()->get_min_max_price( [ 'service_type' => $service_type ] );
                        $min_max_price = wp_parse_args( $min_max_price, [
                            'min' => false,
                            'max' => false
                        ] );
                        ?>
                        <div class="item-search item-search-price search-price">
                            <?php if ( $v[ 'title' ] ) { ?><label
                                for="<?php echo esc_html( $v[ 'field_type' ] ) ?>"><?php echo esc_html( $v[ 'title' ] ) ?></label> <?php } ?>

                            <div class="item-search-content">
                                <?php
                                    $prefix = $postfix = '';
                                    switch ( WPBooking_Currency::get_current_currency( 'position' ) ) {
                                        case "right":
                                        case "right_with_space":
                                            $postfix = WPBooking_Currency::get_current_currency( 'symbol' );
                                            break;
                                        case "left_with_space":
                                        case "left":
                                        default:
                                            $prefix = WPBooking_Currency::get_current_currency( 'symbol' );
                                            break;
                                    }
                                ?>
                                <input type="text" data-type="double" data-prefix="<?php echo esc_html( $prefix ) ?>"
                                       data-postfix="<?php echo esc_html( $postfix ) ?>"
                                       data-min="<?php echo esc_attr( $min_max_price[ 'min' ] ) ?>"
                                       data-max="<?php echo esc_attr( $min_max_price[ 'max' ] ) ?>"
                                       class="wpbooking-ionrangeslider" <?php echo esc_html( $required ) ?>
                                       id="<?php echo esc_html( $v[ 'field_type' ] ) ?>"
                                       name="<?php echo esc_html( $v[ 'field_type' ] ) ?>"
                                       placeholder="<?php echo esc_html( $v[ 'placeholder' ] ) ?>"
                                       value="<?php echo esc_html( $value ) ?>">
                            </div>
                            <?php if ( $v[ 'title' ] ) { ?>
                                <div class="wb-collapse"></div> <?php } ?>
                        </div>
                        <?php
                        break;
                }

                do_action( 'wpbooking_after_get_search_field_html', $v, $service_type );

            }


            /**
             * @param array $new_instance
             * @param array $old_instance
             *
             * @return array
             */
            public function update( $new_instance, $old_instance )
            {
                if ( empty( $new_instance[ 'field_search' ] ) ) $new_instance[ 'field_search' ] = "";
                else {
                    $post_type                                    = $new_instance[ 'service_type' ];
                    $data                                         = $new_instance[ 'field_search' ][ $post_type ];
                    $new_instance[ 'field_search' ]               = [];
                    $new_instance[ 'field_search' ][ $post_type ] = $data;
                }

                return wp_parse_args( $new_instance, $old_instance );
            }

            public function form( $instance )
            {
                $instance = wp_parse_args( (array)$instance, [
                    'title'        => '',
                    'wpbooking_search_form'        => '',
                    'service_type' => '',
                    'field_search' => ""
                ] );
                extract( $instance );
                /* #### */
                $wpbooking_shortcode = '[wpbooking_search_form id="'.$this->number.'"]';
                ?>
                <p><label
                            for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><strong><?php echo esc_html__( 'Title:', "wp-booking-management-system" ); ?></strong>
                        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                               name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                               value="<?php echo esc_attr( $title ); ?>"/></label></p>
                <p><label
                            for="<?php echo esc_attr( $this->get_field_id( 'wpbooking_search_form' ) ); ?>"><strong><?php echo esc_html__( 'WPBooking Search Form Shortcode:', "wp-booking-management-system" ); ?></strong>
                        <input readonly="readonly" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wpbooking_search_form' ) ); ?>"
                               name="<?php echo esc_attr( $this->get_field_name( 'wpbooking_search_form' ) ); ?>" type="text"
                               value="<?php echo esc_attr( $wpbooking_shortcode ); ?>"/></label></p>
                <p>
                    <label
                            for="<?php echo esc_attr( $this->get_field_id( 'service_type' ) ); ?>"><strong><?php echo esc_html__( 'Types of service:', 'wp-booking-management-system' ); ?></strong>
                        <?php
                            $data = WPBooking_Service_Controller::inst()->get_service_types();
                        ?>
                        <select name="<?php echo esc_attr( $this->get_field_name( 'service_type' ) ); ?>"
                                class="option_service_search_form widefat"
                                id="<?php echo esc_attr( $this->get_field_id( 'service_type' ) ); ?>">
                            <option value=""><?php echo esc_html__( "-- Select --", 'wp-booking-management-system' ) ?></option>
                            <?php
                                if ( !empty( $data ) ) {
                                    foreach ( $data as $k => $v ) {
                                        $select = "";
                                        if ( $service_type == $k ) {
                                            $select = "selected";
                                        }
                                        echo '<option ' . esc_attr( $select ) . ' value="' . esc_attr( $k ) . '">' . esc_html( $v->get_info( 'label' ) ) . '</option>';
                                    }
                                }
                            ?>
                        </select>
                    </label>
                </p>
                <?php $all_list_field = WPBooking_Service_Controller::inst()->get_search_fields();
                if ( !empty( $all_list_field ) ) {
                    foreach ( $all_list_field as $key => $value ) {
                        ?>
                        <div
                                class="list_item_widget  div_content_<?php echo esc_attr( $key ) ?> <?php if ( $key != $service_type ) echo "hide"; ?>">
                            <label><strong><?php echo esc_html__( "Search Fields:", "wp-booking-management-system" ) ?></strong></label>
                            <div class="list-group content_list_search_form_widget">

                                <?php
                                    $number = 0;
                                    if ( !empty( $field_search[ $key ] ) ) {
                                        $list = $field_search[ $key ];
                                        foreach ( $list as $k => $v ) {
                                            ?>
                                            <div class="list-group-item">

                                                <div class="control">
                                                    <a class="btn_edit_field_search_form"><?php echo esc_html__( "Edit", "wp-booking-management-system" ) ?></a>
                                                    |
                                                    <a class="btn_remove_field_search_form"><?php echo esc_html__( "Remove", "wp-booking-management-system" ) ?></a>
                                                </div>
                                                <div class="control-hide hide">
                                                    <table class="form-table wpbooking-settings">
                                                        <?php
                                                            $hteml_title_form = "";
                                                            foreach ( $value as $k1 => $v1 ) {
                                                                $default = [ 'name' => '', 'label' => '', 'type' => '', 'options' => '', 'class' => '', 'value' => '' ];
                                                                $v1      = wp_parse_args( $v1, $default );

                                                                if ( !empty( $v[ $v1[ 'name' ] ] ) )
                                                                    $data_value = $v[ $v1[ 'name' ] ];
                                                                else $data_value = false;

                                                                if ( $v1[ 'name' ] == 'title' ) {
                                                                    $hteml_title_form = $data_value;
                                                                }
                                                                if ( $v1[ 'type' ] == 'text' ) {
                                                                    ?>
                                                                    <tr class="<?php echo esc_attr( $v1[ 'class' ] ) ?> div_<?php echo esc_attr( $v1[ 'name' ] ) ?>">
                                                                        <th> <?php echo esc_html( $v1[ 'label' ] ) ?>:
                                                                        </th>
                                                                        <td><input type="text"
                                                                                   name="<?php echo esc_attr( $this->get_field_name( 'field_search' ) ); ?>[<?php echo esc_attr( $key ) ?>][<?php echo esc_attr( $number ) ?>][<?php echo esc_attr( $v1[ 'name' ] ) ?>]"
                                                                                   class="form-control <?php echo esc_attr( $v1[ 'name' ] ) ?>"
                                                                                   value="<?php echo esc_html( $data_value ) ?>">
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                if ( $v1[ 'type' ] == 'checkbox' ) {
                                                                    ?>
                                                                    <tr class="<?php echo esc_attr( $v1[ 'class' ] ) ?> div_<?php echo esc_attr( $v1[ 'name' ] ) ?>">
                                                                        <th> <?php echo esc_html( $v1[ 'label' ] ) ?>:
                                                                        </th>
                                                                        <td><label><input
                                                                                        type="checkbox" <?php checked( 1, $data_value ) ?>
                                                                                        value="1"
                                                                                        name="<?php echo esc_attr( $this->get_field_name( 'field_search' ) ); ?>[<?php echo esc_attr( $key ) ?>][<?php echo esc_attr( $number ) ?>][<?php echo esc_attr( $v1[ 'name' ] ) ?>]"
                                                                                        class=" <?php echo esc_attr( $v1[ 'name' ] ) ?>"> <?php echo esc_html__( 'Yes', 'wp-booking-management-system' ) ?>
                                                                            </label></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                if ( $v1[ 'type' ] == 'dropdown' ) {
                                                                    $options = $v1[ 'options' ];
                                                                    ?>
                                                                    <tr class="<?php echo esc_attr( $v1[ 'class' ] ) ?> div_<?php echo esc_attr( $v1[ 'name' ] ) ?>">
                                                                        <th> <?php echo esc_html( $v1[ 'label' ] ) ?>:
                                                                        </th>
                                                                        <td>
                                                                            <select
                                                                                    class="form-control <?php echo esc_attr( $v1[ 'name' ] ) ?>"
                                                                                    name="<?php echo esc_attr( $this->get_field_name( 'field_search' ) ); ?>[<?php echo esc_attr( $key ) ?>][<?php echo esc_attr( $number ) ?>][<?php echo esc_attr( $v1[ 'name' ] ) ?>]">
                                                                                <?php
                                                                                    if ( !empty( $options ) ) {
                                                                                        foreach ( $options as $k2 => $v2 ) {
                                                                                            $select = "";
                                                                                            if ( $data_value == $k2 ) {
                                                                                                $select = "selected";
                                                                                            }
                                                                                            echo "<option {$select} value={$k2}>{$v2}</option>";
                                                                                        }
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            } ?>
                                                    </table>
                                                </div>
                                                <div
                                                        class="head-title"><?php echo esc_html( $hteml_title_form ) ?></div>
                                            </div>
                                            <?php
                                            $number++;
                                        }
                                    }
                                ?>
                            </div>
                            <div class="widget-control-actions">
                                <div class="alignleft">
                                    <input type="button" value="<?php echo esc_html__( 'Add Field', 'wp-booking-management-system' ) ?>"
                                           data-number="<?php echo esc_attr( $number ) ?>"
                                           data-name-field-search="<?php echo esc_attr( $this->get_field_name( 'field_search' ) ); ?>"
                                           data-post-type="<?php echo esc_attr( $key ) ?>"
                                           class="button button-primary left btn_add_field_search_form" id="#">
                                    <p>
                                        <i><?php echo esc_html__( 'Remember to hit Save button after adding or removing new search field', 'wp-booking-management-system' ) ?></i>
                                    </p>
                                </div>
                                <br class="clear">
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>

                <?php
                if ( !empty( $all_list_field ) ) {
                    foreach ( $all_list_field as $key => $value ) {
                        ?>
                        <div class="div_content_hide_<?php echo esc_attr( $key ) ?> hide">
                            <div class="list-group-item">
                                <div class="control">
                                    <a class="btn_edit_field_search_form"><?php echo esc_html__( "Edit", "wp-booking-management-system" ) ?></a>
                                    |
                                    <a class="btn_remove_field_search_form"><?php echo esc_html__( "Remove", "wp-booking-management-system" ) ?></a>
                                </div>
                                <div class="control-hide">
                                    <table class="form-table wpbooking-settings">
                                        <?php foreach ( $value as $k => $v ) { ?>
                                            <?php
                                            $default = [ 'name' => '', 'label' => '', 'type' => '', 'options' => '', 'class' => '', 'value' => '' ];
                                            $v       = wp_parse_args( $v, $default );
                                            if ( $v[ 'type' ] == 'text' ) {
                                                ?>
                                                <tr class="<?php echo esc_attr( $v[ 'class' ] ) ?> div_<?php echo esc_attr( $v[ 'name' ] ) ?>">
                                                    <th> <?php echo esc_html( $v[ 'label' ] ) ?>:</th>
                                                    <td><input type="text" placeholder=""
                                                               name="__name_field_search__[<?php echo esc_attr( $key ) ?>][__number__][<?php echo esc_attr( $v[ 'name' ] ) ?>]"
                                                               class="form-control <?php echo esc_attr( $v[ 'name' ] ) ?>">
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            if ( $v[ 'type' ] == 'checkbox' ) {
                                                ?>
                                                <tr class="<?php echo esc_attr( $v[ 'class' ] ) ?> div_<?php echo esc_attr( $v[ 'name' ] ) ?>">
                                                    <th> <?php echo esc_html( $v[ 'label' ] ) ?>:</th>
                                                    <td><label><input type="checkbox" value="1"
                                                                      name="__name_field_search__[<?php echo esc_attr( $key ) ?>][__number__][<?php echo esc_attr( $v[ 'name' ] ) ?>]"
                                                                      class="<?php echo esc_attr( $v[ 'name' ] ) ?>"> <?php echo esc_html__( 'Yes', 'wp-booking-management-system' ) ?>
                                                        </label></td>
                                                </tr>
                                                <?php
                                            }
                                            if ( $v[ 'type' ] == 'dropdown' ) {
                                                $options = $v[ 'options' ];
                                                ?>
                                                <tr class="<?php echo esc_attr( $v[ 'class' ] ) ?> div_<?php echo esc_attr( $v[ 'name' ] ) ?>">
                                                    <th> <?php echo esc_html( $v[ 'label' ] ) ?>:</th>
                                                    <td>
                                                        <select
                                                                class="form-control <?php echo esc_attr( $v[ 'name' ] ) ?>"
                                                                name="__name_field_search__[<?php echo esc_attr( $key ) ?>][__number__][<?php echo esc_attr( $v[ 'name' ] ) ?>]">
                                                            <?php
                                                                if ( !empty( $options ) ) {
                                                                    foreach ( $options as $k1 => $v1 ) {
                                                                        echo "<option value={$k1}>{$v1}</option>";
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        <?php } ?>
                                    </table>
                                </div>
                                <div class="head-title"></div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <?php
            }
            static function inst(){
                if(!self::$_inst){
                    self::$_inst = new self();
                }
                return self::$_inst;
            }
        }

        function wpbooking_widget_form_search()
        {
            register_widget( 'WPBooking_Widget_Form_Search' );
        }

        add_action( 'widgets_init', 'wpbooking_widget_form_search' );
        WPBooking_Widget_Form_Search::inst();
    }
