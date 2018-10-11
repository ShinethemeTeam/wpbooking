<?php
    if(!empty($order_data['rooms'])){
        $booking=WPBooking_Checkout_Controller::inst();
        $order_id = $order_data['order_id'];
        $rawcart = get_post_meta($order_id, 'wb_cart_rooms', true);
        $cart=$booking->get_cart();
        ?>
        <tr>
            <th width=50%>
                <?php echo esc_html__('Rooms','wp-booking-management-system') ?>
            </th>
            <th class=text-center>
                <?php echo esc_html__('Price','wp-booking-management-system') ?> (<?php echo esc_attr(get_post_meta($order_id,'currency',true)) ?>)
            </th>
            <th width=15% class=text-center>
                <?php echo esc_html__('Number','wp-booking-management-system') ?>
            </th>
            <th class=text-center>
                <?php echo esc_html__('Total','wp-booking-management-system') ?>(<?php echo esc_attr(get_post_meta($order_id,'currency',true)) ?>)
            </th>
        </tr>
        <tr>
            <td>
                <?php
                    foreach($order_data['rooms'] as $k=>$v){
                        $room_id = $v['room_id'];
                        $rawroom = (isset($rawcart[$room_id]))? $rawcart[$room_id]: [];
                        $service_room=new WB_Service($room_id);
                        $featured=$service_room->get_featured_image_room();
                        $price_room = $v['price'];
                        $price_total_room = $v['price_total'];
                        $v['extra_fees'] = unserialize($v['extra_fees']);
                        $v['raw_data'] = unserialize($v['raw_data']);
                        ?>
                        <div class=col-3>
                            <div class=room-image>
                                <?php echo wp_kses($featured['thumb'],array('img'=>array('src'=>array(),'alt'=>array())))?>
                            </div>
                        </div>
                        <div class=col-7>
                            <div class=bold>
                                <h3> <?php echo esc_html(get_the_title($room_id)) ?> </h3>
                            </div>
                            <div>
                                <?php /*if($max = $service_room->get_meta('max_guests')){ */?><!--
                            <div class="sub-title"><?php /*echo esc_html__("Max","wp-booking-management-system") */?> <?php /*echo esc_attr($max) */?> <?php /*echo esc_html__("people","wp-booking-management-system") */?></div>
                        --><?php /*} */?>
                                <div class="sub-title">
                                    <?php if(!empty($cart['adult_number'])){?>
                                        <span>
                                    <?php echo esc_html__('Adults:','wp-booking-management-system').' '.esc_html($cart['adult_number']); ?>
                                </span><br>
                                    <?php } ?>
                                    <?php if(!empty($cart['children_number'])){?>
                                        <span>
                                    <?php echo esc_html__('Children:','wp-booking-management-system').' '.esc_html($cart['children_number']); ?>
                                </span>
                                    <?php } ?>
                                </div>
                            </div>
                            <br>
                            <span class=btn_detail_checkout><?php echo esc_html__("Details","wp-booking-management-system") ?></span>
                            <div class=extra-service>
                                <h4 class=color_black><?php echo esc_html__("Price by Night","wp-booking-management-system") ?></h4>
                                <div class=extra-item>
                                    <table class=color_black>
                                        <thead>
                                        <tr>
                                            <th width=60%>
                                                <?php echo esc_html__("Night","wp-booking-management-system") ?>
                                            </th>
                                            <th class=text-center>
                                                <?php echo esc_html__("Price","wp-booking-management-system") ?>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i=1; foreach( $v['raw_data'] as $k_list_date => $v_list_date){ ?>
                                            <tr>
                                                <td>
                                                    <?php echo esc_html__("Night","wp-booking-management-system") ?> <?php echo esc_html($i) ?>
                                                    <br>
                                                    <span class="desc">(<?php echo date(get_option('date_format') , $k_list_date) ?>)</span>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo WPBooking_Currency::format_money($v_list_date) ?>
                                                </td>
                                            </tr>
                                            <?php $i++;} ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php
                                if(!empty($v['extra_fees'])){ ?>
                                    <?php
                                    foreach($v['extra_fees'] as $extra_service){
                                        if(!empty($extra_service['data'])){
                                            ?>
                                            <div class=extra-service>
                                                <h4 class=color_black><?php echo esc_html($extra_service['title']) ?></h4>
                                                <div class=extra-item>
                                                    <table class=color_black>
                                                        <thead>
                                                        <tr class=color_black>
                                                            <th width=60%>
                                                                <?php echo esc_html__("Service name",'wp-booking-management-system') ?>
                                                            </th>
                                                            <th class=text-center>
                                                                <?php echo esc_html__("Price",'wp-booking-management-system') ?>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        <?php
                                                            foreach($extra_service['data'] as $value){
                                                                ?>
                                                                <tr class=color_black>
                                                                    <td >
                                                                        <?php echo esc_html($value['title']) ?><br>
                                                                        x  <span class="desc"><?php echo esc_html($value['quantity']) ?></span>
                                                                    </td>
                                                                    <td class=text-center>
                                                                        <?php echo WPBooking_Currency::format_money($value['price']) ?>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                        ?>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } ?>
                                <?php } ?>
                        </div>
                        <?php
                    }
                ?>
            </td>
            <td class=text-center>
                <?php echo WPBooking_Currency::format_money($price_room); ?>
            </td>
            <td class=text-center>
                <?php echo esc_attr($v['number']) ?>
            </td>
            <td class=text-center>
                <div class="sub-title">
                    <?php
                        if ( isset( $rawroom[ 'discount_by_day' ] ) && !empty( $rawroom[ 'discount_by_day' ] ) ) {
                            ?>
                            <?php echo esc_html__( 'Discount', 'wp-booking-management-system' ); ?>:
                            <strong><?php echo (float)$rawroom[ 'discount_by_day' ][ 'price' ] . '%'; ?></strong>
                            <?php
                        }
                    ?>
                </div>
                <hr>
                <h4>
                    <?php
                        echo WPBooking_Currency::format_money($price_total_room);
                    ?>
                </h4>
            </td>
        </tr>
        <?php
    }
?>