<?php
/**
 * Created by ShineTheme.
 * User: NAZUMI
 * Date: 12/1/2016
 * Version: 1.0
 */

?>
<div class="wb-control">
    <?php
    if(!empty($tabs[$is_key-1])){
        if(!empty($tabs[$is_key-1]['url'])){
            $url = $tabs[$is_key-1]['url'];
        }else {
            $url = add_query_arg(array("page" => $slug_page_menu, "wb_tab" => $tabs[$is_key-1]['id']), admin_url("admin.php"));
        }
        ?>
        <div class="wb-left">
            <a href="<?php echo esc_url($url); ?>" class="prev button button-primary"><?php echo esc_attr($tabs[$is_key-1]['name']) ?></a>
        </div>
    <?php } ?>

    <div class="wb-desc <?php echo(count($tabs) >1)?'':'full-width'; ?>">
        <p><?php echo wp_kses(__('We are spending you <strong>four main settings</strong> to create a booking system.', 'wpbooking'), array('strong' => array()))?></p>
        <p><?php echo esc_html__('There are below:','wpbooking'); ?></p>
    </div>
    <?php
    if(!empty($tabs[$is_key+1])){
        if(!empty($tabs[$is_key+1]['url'])){
            $url = $tabs[$is_key+1]['url'];
        }else {
            $url = add_query_arg(array("page" => $slug_page_menu, "wb_tab" => $tabs[$is_key+1]['id']), admin_url("admin.php"));
        }
        ?>
        <div class="wb-right">
            <a href="<?php echo esc_url($url); ?>" class="prev button button-primary"><?php echo esc_attr($tabs[$is_key+1]['name']) ?></a>
        </div>
    <?php } ?>
</div>
<div class="wb-content">
    <div class="header">
        <h2 class="title"><?php echo esc_html__('Get Started','wpbooking')?></h2>
    </div>

    <div class="content">
        <div class="step">
            <div class="left">
                <h3><?php echo esc_html__('Create new accommodation','wpbooking')?></h3>
                <p><?php echo esc_html__('Accommodations are the core of your Booking site. Without them, you do not really have a Booking site, so setting them up properly and making them easy to booking is extremely important.','wpbooking');?></p>
                <ul>
                   <li><?php echo esc_html__('Create contact information of accommodation','wpbooking')?></li>
                   <li><?php echo esc_html__('Create location of accommodation','wpbooking')?></li>
                   <li><?php echo esc_html__('Setting check-in/ check-out time','wpbooking')?></li>
                   <li><?php echo esc_html__('Setting amenities of accommodation','wpbooking')?></li>
                   <li><?php echo esc_html__('Create room of accommodation','wpbooking')?></li>
                   <li><?php echo esc_html__('Setting facilities of accommodation','wpbooking')?></li>
                   <li><?php echo esc_html__('Setting policies of accommodation: tax, cancelation policies','wpbooking')?></li>
                   <li><?php echo esc_html__('Setting photos of accommodation','wpbooking')?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url('images/wb-step.png')?>" class="img-step img-right"/>
        </div>
        <div class="step">
            <div class="left">
                <h3><?php echo esc_html__('Create accommodation’s room','wpbooking')?></h3>
                <p><?php echo esc_html__('Per accommodation usually have multiple rooms. They are created in the accommodation. Includes:','wpbooking');?></p>
                <ul>
                    <li><?php echo esc_html__('Base information: name, number of rooms','wpbooking')?></li>
                    <li><?php echo esc_html__('Create and setting extra service','wpbooking')?></li>
                    <li><?php echo esc_html__('Setting price of room','wpbooking')?></li>
                    <li><?php echo esc_html__('Setting available status of room','wpbooking')?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url('images/wb-step.png')?>" class="img-step img-left"/>
        </div>
        <div class="step">
            <div class="left">
                <h3><?php echo esc_html__('Check and manage your bookings','wpbooking')?></h3>

                <ul>
                    <li><?php echo esc_html__('After user booking success, you can replace booking status of user booking(s) easy to use Booking Admin Panel','wpbooking')?></li>
                    <li><?php echo esc_html__('You can view booking report by the chart','wpbooking')?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url('images/wb-step.png')?>" class="img-step img-right"/>
        </div>
        <div class="step">
            <div class="left">
                <h3><?php echo esc_html__('Configure different settings','wpbooking')?></h3>

                <ul>
                    <li><?php echo esc_html__('Currency of booking system','wpbooking')?></li>
                    <li><?php echo esc_html__('Booking/register notification email','wpbooking')?></li>
                    <li><?php echo esc_html__('Edit notification email easy','wpbooking')?></li>
                    <li><?php echo esc_html__('Setting payment methods','wpbooking')?></li>
                    <li><?php echo esc_html__('Setting review options','wpbooking')?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url('images/wb-step.png')?>" class="img-step img-left"/>
        </div>
    </div>
    <div class="footer">
        <h3 class="question"><?php echo esc_html__('Have a questions?', 'wpbooking'); ?></h3>
        <div class="link">
            <a href="#"><?php echo esc_html__('Need Help?','wpbooking')?></a>
            <a href="#"><?php echo esc_html__('FAQ','wpbooking')?></a>
            <a href="#"><?php echo esc_html__('Submit a Ticket?','wpbooking')?></a>
        </div>
    </div>
</div>
