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
        <h3 class="title"><?php echo esc_html__('Changed Password Successful','wpbooking'); ?></h3>
        <p class="description"><?php echo sprintf(esc_html__('Hello %s, ','wpbooking'),$user['user_login']). esc_html__('You have changed password a few minutes ago,','wpbooking')?><br>
            <?php echo esc_html__('Currently, here are your account information: ','wpbooking'); ?>
        </p>
    </div>
    <div class="content-center">
        <?php echo esc_html__('Username','wpbooking'); ?>: <strong><?php echo esc_attr($user['user_login']); ?></strong><br><br>
        <?php echo esc_html__('Password','wpbooking'); ?>: <?php echo WPBooking_Session::get('new_changed_pass'); ?><br><br>
        <?php echo esc_html__('Email','wpbooking'); ?>: <?php echo esc_attr($user['user_email']); ?><br><br>
        <?php echo esc_html__('Profile URL','wpbooking'); ?>: <a href="<?php echo WPBooking_User::inst()->account_page_url(); ?>"><?php echo WPBooking_User::inst()->account_page_url(); ?></a><br><br>
    </div>
</div>

