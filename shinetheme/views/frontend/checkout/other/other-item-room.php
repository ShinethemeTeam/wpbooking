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
                ?>
                <tr class="room-<?php echo esc_attr($k) ?> ">
                    <td width="5%"  class="text-center">
                        <a class="delete-cart-item" onclick="return confirm('<?php esc_html_e('Do you want to delete it?','wpbooking') ?>')" href="<?php echo esc_url(add_query_arg(array('delete_cart_item'=>$k),$booking->get_checkout_url())) ?>">
                            <i class="fa fa-trash-o"></i>
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
                                        if(!empty($v['extra_fees'])){ ?>
                                            <?php
                                            foreach($v['extra_fees'] as $k=>$extra_service){
                                                if(!empty($extra_service['data'])){
                                                    ?>
                                                    <div class="extra-service">
                                                        <div class="title"><?php echo esc_html($extra_service['title']) ?></div>
                                                        <div class="extra-item">
                                                            <?php
                                                            foreach($extra_service['data'] as $key=>$value){
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
                                        <div class="title price">$1000</div>
                                        <div class="sub-title">&nbsp</div>
                                        <?php
                                        if(!empty($v['extra_fees'])){ ?>
                                            <?php
                                            foreach($v['extra_fees'] as $k=>$extra_service){
                                                if(!empty($extra_service['data'])){
                                                    ?>
                                                    <div class="extra-service">
                                                        <div class="title">&nbsp</div>
                                                        <div class="extra-item price">
                                                            <?php
                                                            foreach($extra_service['data'] as $key=>$value){
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
                                <div class="col-md-12">$10000</div>
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