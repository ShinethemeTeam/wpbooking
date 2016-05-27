<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/5/2016
 * Time: 2:38 PM
 */

$email_content=wpbooking_get_option('email_to_partner');
echo do_shortcode($email_content);

