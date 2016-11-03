<?php
if(!empty($order_data['rooms'])){
    $booking=WPBooking_Checkout_Controller::inst();
    ?>
    <div class="review-order-item-table wpbooking-bootstrap">
        <table>
            <thead>
            <tr>
                <td width="50%" class="col-title"><?php esc_html_e('Rooms','wpbooking') ?></td>
                <td class="text-center"><?php esc_html_e('Price','wpbooking') ?> (<?php echo WPBooking_Currency::get_current_currency('currency') ?>)</td>
                <td class="text-center" width="15%"><?php esc_html_e('Number','wpbooking') ?></td>
                <td class="text-center"><?php esc_html_e('Total','wpbooking') ?> (<?php echo WPBooking_Currency::get_current_currency('currency') ?>)</td>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($order_data['rooms'] as $k=>$v){
                $room_id = $v['room_id'];
                $service_room=new WB_Service($room_id);
                $featured=$service_room->get_featured_image_room();
                $price_room = $v['price'];
                $price_total_room = $v['price_total'];
                $v['extra_fees'] = unserialize($v['extra_fees']);
                ?>
                <tr class="room-<?php echo esc_attr($k) ?> ">
                    <td width="50%">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-3 room-image">
                                    <?php echo wp_kses($featured['thumb'],array('img'=>array('src'=>array(),'alt'=>array())))?>
                                </div>
                                <div class="col-md-9">
                                    <div class="room-info">
                                        <div class="title"><?php echo get_the_title($room_id) ?></div>
                                        <?php if($max = $service_room->get_meta('max_guests')){ ?>
                                            <div class="sub-title"><?php esc_html_e("Max","wpbooking") ?> <?php echo esc_attr($max) ?> <?php esc_html_e("people","wpbooking") ?></div>
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
                                                            <?php
                                                            foreach($extra_service['data'] as $value){
                                                                echo balanceTags(" <div>+ ".$value['title']." x ".$value['quantity']."</div>");
                                                            }
                                                            ?>
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
                                        <?php
                                        if(!empty($v['extra_fees'])){ ?>
                                            <?php
                                            foreach($v['extra_fees'] as $extra_service){
                                                if(!empty($extra_service['data'])){
                                                    ?>
                                                    <div class="extra-service">
                                                        <div class="title">&nbsp</div>
                                                        <div class="extra-item price">
                                                            <?php
                                                            foreach($extra_service['data'] as $value){
                                                                echo "<div>".WPBooking_Currency::format_money($value['price'])."</div>";
                                                            }
                                                            ?>
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