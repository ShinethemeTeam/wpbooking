<div class="wrap ">
    <div id="icon-tools" class="icon32"></div>
    <h2 class="text-center" ><?php _e("Setup Wpbooking plugin",'wpbooking') ?></h2>
</div>
<?php $is_tab = WPBooking_Input::request('wp_step','wp_general'); ?>
<div class="wrap">
    <div class="wp-setup-demo">
        <ol class="setup-steps">
            <li class="<?php if($is_tab == "wp_general") echo "active"; ?>"><?php esc_html_e("General Setup","wpbooking") ?></li>
            <li class="<?php if($is_tab == "wp_booking") echo "active"; ?>"><?php esc_html_e("Booking Setup","wpbooking") ?></li>
            <li class="<?php if($is_tab == "wp_email") echo "active"; ?>"><?php esc_html_e("Email Setup","wpbooking") ?></li>
            <li class="<?php if($is_tab == "wp_service") echo "active"; ?>"><?php esc_html_e("Service Setup","wpbooking") ?></li>
            <li class="<?php if($is_tab == "wp_payment") echo "active"; ?>"><?php esc_html_e("Payment Setup","wpbooking") ?></li>
        </ol>
        <?php
        echo wpbooking_admin_load_view("/setup_demo/".$is_tab);
        ?>
    </div>

</div>

