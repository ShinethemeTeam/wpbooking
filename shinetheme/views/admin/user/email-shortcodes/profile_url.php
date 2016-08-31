<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/24/2016
 * Time: 6:01 PM
 */
$user=WPBooking_User::inst();
printf('<a href="%s" target="_blank">%s</a>',$user->account_page_url(),esc_html__('View Profile','wpbooking'));

