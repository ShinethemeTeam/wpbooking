<?php
/**
 * Created by WpBooking Team.
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
        <p><?php echo wp_kses(esc_html__('We are supporting you <strong>with four main settings</strong> to create a booking system.', 'wpbooking'), array('strong' => array()))?></p>
        <p><?php echo esc_html__('Including:','wpbooking'); ?></p>
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
                <h3><?php echo esc_html__('Create a new accommodation','wpbooking')?></h3>
                <p><?php echo esc_html__('Accommodations are the core of your Booking site. You will not really have a Booking site without them, so it is extremely important to set them up properly and make them easy for booking.','wpbooking');?></p>
                <ul>
                   <li><?php echo esc_html__('Create contact information of accommodation','wpbooking')?></li>
                   <li><?php echo esc_html__('Create location of accommodation','wpbooking')?></li>
                   <li><?php echo esc_html__('Set time for check-in/ check-out','wpbooking')?></li>
                   <li><?php echo esc_html__('Set amenities of accommodation','wpbooking')?></li>
                   <li><?php echo esc_html__('Create room of accommodation','wpbooking')?></li>
                   <li><?php echo esc_html__('Set facilities of accommodation','wpbooking')?></li>
                   <li><?php echo esc_html__('Set policies of accommodation such as tax, cancellation policies...','wpbooking')?></li>
                   <li><?php echo esc_html__('Set photos of accommodation','wpbooking')?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url('images/accommodation-final.png')?>" class="img-step"/>
        </div>
        <div class="step">
            <div class="left">
                <h3><?php echo esc_html__('Create accommodation’s room','wpbooking')?></h3>
                <p><?php echo esc_html__('Each accommodation often has multiple rooms. These rooms are created in a accommodation. Including:','wpbooking');?></p>
                <ul>
                    <li><?php echo esc_html__('Basic information: name, number of rooms','wpbooking')?></li>
                    <li><?php echo esc_html__('Create and set extra services','wpbooking')?></li>
                    <li><?php echo esc_html__('Set price of room','wpbooking')?></li>
                    <li><?php echo esc_html__('Set available status of room','wpbooking')?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url('images/room-final.png')?>" class="img-step"/>
        </div>
        <div class="step">
            <div class="left">
                <h3><?php echo esc_html__('Create a new tour','wpbooking')?></h3>
                <p><?php echo esc_html__('Tour is another part of WpBooking plugin. You can use it for tourism, events or workshops, etc. You need to set tour according to the following information:','wpbooking');?></p>
                <ul>
                    <li><?php echo esc_html__('Create contact information of tour','wpbooking')?></li>
                    <li><?php echo esc_html__('Set location of tour','wpbooking')?></li>
                    <li><?php echo esc_html__('Set pricing and status of tour','wpbooking')?></li>
                    <li><?php echo esc_html__('Set policies of tour: tax, cancellation policies','wpbooking')?></li>
                    <li><?php echo esc_html__('Set photos of tour','wpbooking')?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url('images/tour-final.png')?>" class="img-step"/>
        </div>
        <div class="step">
            <div class="left">
                <h3><?php echo esc_html__('Manage your bookings','wpbooking')?></h3>

                <ul>
                    <li><?php echo esc_html__('After users book successfully, you can replace booking status of user booking(s) easily to use Booking Admin Panel','wpbooking')?></li>
                    <li><?php echo esc_html__('You can view booking report by the chart','wpbooking')?></li>
                </ul>

            </div>
            <div class="full">
                <img src="<?php echo wpbooking_admin_assets_url('images/allbooking-final.png')?>" class="img-step"/>
                <img src="<?php echo wpbooking_admin_assets_url('images/report-final.png')?>" class="img-step"/>
            </div>
        </div>
        <div class="step">
            <div class="left">
                <h3><?php echo esc_html__('Configure different settings','wpbooking')?></h3>

                <ul>
                    <li><?php echo esc_html__('Currency of booking system','wpbooking')?></li>
                    <li><?php echo esc_html__('Booking/register notification email','wpbooking')?></li>
                    <li><?php echo esc_html__('Edit notification email easy','wpbooking')?></li>
                    <li><?php echo esc_html__('Setting payment methods','wpbooking')?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url('images/setting-general-final.png')?>" class="img-step"/>
        </div>
    </div>
    <div class="footer">
        <h3 class="question"><?php echo esc_html__('Make questions?', 'wpbooking'); ?></h3>
        <div class="link">
            <a href="https://wpbooking.org" target="_blank"><?php echo esc_html__('Need any Help?','wpbooking')?></a>
            <a href="https://wpbooking.org/pricing-fqa" target="_blank"><?php echo esc_html__('FAQ','wpbooking')?></a>
            <a href="https://wpbooking.org" target="_blank"><?php echo esc_html__('Submit a Ticket?','wpbooking')?></a>
        </div>
    </div>
</div>
