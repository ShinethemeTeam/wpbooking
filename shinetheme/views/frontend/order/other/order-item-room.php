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
                $v['raw_data'] = unserialize($v['raw_data']);
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
                                        if(!empty($v['raw_data'])){ ?>
                                            <button class="wb-button btn_detail_checkout"><?php esc_html_e("Details","wpbooking") ?></button>
                                        <?php } ?>
                                        <div class="content_details">
                                            <?php
                                            if(!empty($v['raw_data'])){ ?>
                                                <div class="extra-service">
                                                    <div class="title"><?php esc_html_e("Price by night","wpbooking") ?></div>
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
                                                            <?php $i=1; foreach( $v['raw_data'] as $k_list_date => $v_list_date){ ?>
                                                                <tr>
                                                                    <td>
                                                                        <?php esc_html_e("Night","wpbooking") ?> <?php echo esc_html($i) ?>
                                                                        <br>
                                                                        <span class="desc">( <?php echo date(get_option('date_format') , $k_list_date) ?> )</span>
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