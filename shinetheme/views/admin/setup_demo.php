<div class="wrap ">
    <div id="icon-tools" class="icon32"></div>
    <h2 class="text-center" ><?php echo esc_html__("Setup Wpbooking plugin",'wp-booking-management-system') ?></h2>
</div>
<?php $is_tab = WPBooking_Input::request('wp_step','wp_general'); ?>
<div class="wrap">
    <div class="wp-setup-demo">
        <ol class="setup-steps">
            <li class="<?php if($is_tab == "wp_general") echo "active"; ?>"><?php echo esc_html__("General Setup","wp-booking-management-system") ?></li>
            <li class="<?php if($is_tab == "wp_booking") echo "active"; ?>"><?php echo esc_html__("Booking Setup","wp-booking-management-system") ?></li>
            <li class="<?php if($is_tab == "wp_email") echo "active"; ?>"><?php echo esc_html__("Email Setup","wp-booking-management-system") ?></li>
            <li class="<?php if($is_tab == "wp_payment") echo "active"; ?>"><?php echo esc_html__("Payment Setup","wp-booking-management-system") ?></li>
        </ol>
        <?php
        echo wpbooking_admin_load_view("/setup_demo/".$is_tab);
        ?>
    </div>

</div>

