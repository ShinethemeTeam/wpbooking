<?php
/**
 * Created by WpBooking Team.
 * User: NAZUMI
 * Date: 11/7/2016
 * Version: 1.0
 */
?>
<div class="content">
    <div class="content-header">
        <h3 class="title"><?php echo esc_html__('Change password successfully','wp-booking-management-system'); ?></h3>
        <p class="description"><?php echo sprintf(esc_html__('Hello %s, ','wp-booking-management-system'),$user['user_login']). esc_html__('You changed password a few minutes ago,','wp-booking-management-system')?><br>
            <?php echo esc_html__('Currently, here is your account information:','wp-booking-management-system'); ?>
        </p>
    </div>
    <div class="content-center">
        <?php echo esc_html__('Username','wp-booking-management-system'); ?>: <strong><?php echo esc_attr($user['user_login']); ?></strong><br><br>
        <?php echo esc_html__('Password','wp-booking-management-system'); ?>: <?php echo WPBooking_Session::get('new_changed_pass'); ?><br><br>
        <?php echo esc_html__('Email','wp-booking-management-system'); ?>: <?php echo esc_attr($user['user_email']); ?><br><br>
        <?php echo esc_html__('Profile URL','wp-booking-management-system'); ?>: <a href="<?php echo WPBooking_User::inst()->account_page_url(); ?>"><?php echo WPBooking_User::inst()->account_page_url(); ?></a><br><br>
    </div>
</div>

