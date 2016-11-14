<?php
if(!empty($cart['rooms'])){
    $booking=WPBooking_Checkout_Controller::inst();
    ?>
    <div class="review-order-item-table wpbooking-bootstrap">
        <table>
            <thead>
            <tr>
                <td width="5%"></td>
                <td width="50%"><?php esc_html_e('Rooms','wpbooking') ?></td>
                <td class="text-center"><?php esc_html_e('Price','wpbooking') ?> (<?php echo WPBooking_Currency::get_current_currency('currency') ?>)</td>
                <td class="text-center" width="15%"><?php esc_html_e('Number','wpbooking') ?></td>
                <td class="text-center"><?php esc_html_e('Total','wpbooking') ?> (<?php echo WPBooking_Currency::get_current_currency('currency') ?>)</td>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($cart['rooms'] as $k=>$v){
                $service_room=new WB_Service($k);
                $featured=$service_room->get_featured_image_room();
                $price_room = WPBooking_Accommodation_Service_Type::inst()->_get_price_room_in_cart($cart,$k);
                $price_total_room = WPBooking_Accommodation_Service_Type::inst()->_get_total_price_room_in_cart($cart,$k);
                ?>
                <tr class="room-<?php echo esc_attr($k) ?> ">
                    <td width="5%"  class="text-center">
                        <a class="delete-cart-item tooltip_desc" onclick="return confirm('<?php esc_html_e('Do you want to delete it?','wpbooking') ?>')" href="<?php echo esc_url(add_query_arg(array('delete_item_hotel_room'=>$k),$booking->get_checkout_url())) ?>">
                            <i class="fa fa-trash-o"></i>
                            <span class="tooltip_content"><?php esc_html_e("Remove this room",'wpbooking') ?></span>
                        </a>
                    </td>
                    <td width="50%">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-3 room-image">
                                    <?php echo wp_kses($featured['thumb'],array('img'=>array('src'=>array(),'alt'=>array())))?>
                                </div>
                                <div class="col-md-9">

                                    <div class="room-info">
                                        <div class="title"><?php echo get_the_title($k) ?></div>
                                        <?php if($max = $service_room->get_meta('max_guests')){ ?>
                                            <div class="sub-title"><?php esc_html_e("Max","wpbooking") ?> <?php echo esc_attr($max) ?> <?php esc_html_e("people","wpbooking") ?></div>
                                        <?php } ?>
                                        <?php
                                        if(!empty($v['list_date_price'])){ ?>
                                            <span class="btn_detail_checkout"><?php esc_html_e("Details","wpbooking") ?> <i class="fa fa-caret-down" aria-hidden="true"></i>
</span>
                                        <?php } ?>
                                        <div class="content_details">
                                            <?php
                                            if(!empty($v['list_date_price'])){ ?>
                                                <div class="extra-service">
                                                    <div class="title"><?php esc_html_e("Price by Night","wpbooking") ?></div>
                                                    <div class="extra-item">
                                                        <table>
                                                            <thead>
                                                            <tr>
                                                                <th width="60%">
                                                                    <?php esc_html_e("Night","wpbooking") ?>
                                                                </th>
                                                                <th class="text-center">
                                                                    <?php esc_html_e("Price","wpbooking") ?>
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php $i=1; foreach( $v['list_date_price'] as $k_list_date => $v_list_date){ ?>
                                                                <tr>
                                                                    <td>
                                                                        <?php esc_html_e("Night","wpbooking") ?> <?php echo esc_html($i) ?>
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
                                            <?php } ?>



                                            <?php
                                            if(!empty($v['extra_fees'])){ ?>
                                                <?php
                                                foreach($v['extra_fees'] as $extra_service){
                                                    if(!empty($extra_service['data'])){
                                                        ?>
                                                        <div class="extra-service">
                                                            <div class="title"><?php echo esc_html($extra_service['title']) ?></div>
                                                            <div class="extra-item">
                                                                <table>
                                                                    <thead>
                                                                    <tr>
                                                                        <th width="60%" >
                                                                            <?php esc_html_e("Service name",'wpbooking') ?>
                                                                        </th>
                                                                        <th class="text-center">
                                                                            <?php esc_html_e("Price",'wpbooking') ?>
                                                                        </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php
                                                                    foreach($extra_service['data'] as $value){
                                                                        ?>
                                                                        <tr>
                                                                            <td >
                                                                                <?php echo esc_html($value['title']) ?><br>
                                                                                x  <span class="desc"><?php echo esc_html($value['quantity']) ?></span>
                                                                            </td>
                                                                            <td class="text-center">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="container-fluid td-inner">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="room-info">
                                        <div class="title price">
                                            <?php echo WPBooking_Currency::format_money($price_room); ?>
                                        </div>
                                        <div class="sub-title">&nbsp</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center" width="15%">
                        <div class="container-fluid td-inner">
                            <div class="row">
                                <div class="col-md-12"><?php echo esc_attr($v['number']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="container-fluid td-inner">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    echo WPBooking_Currency::format_money($price_total_room);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>