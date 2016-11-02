<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(!class_exists('WPBooking_Admin_Setup'))
{
    class WPBooking_Admin_Setup extends WPBooking_Controller
    {
        private static $_inst;

        function __construct()
        {
            WPBookingConfig()->load('settings');

            add_action( 'admin_menu', array($this,"register_wpbooking_sub_menu_page") );

            add_action( 'admin_init', array($this,"_save_setup_demo") );
        }
        function register_wpbooking_sub_menu_page() {
            $is_setup_demo = wpbooking_get_option("setup_demo",'true');
            if($is_setup_demo == "true"){
                $menu_page=$this->get_menu_page();
                add_submenu_page(
                    $menu_page['parent_slug'],
                    $menu_page['page_title'],
                    $menu_page['menu_title'],
                    $menu_page['capability'],
                    $menu_page['menu_slug'],
                    $menu_page['function']
                );
            }
        }
        function get_menu_page()
        {
            $menu_page=WPBooking()->get_menu_page();
            $page=array(
                'parent_slug'=>$menu_page['menu_slug'],
                'page_title'=>__('Setup','wpbooking'),
                'menu_title'=>__('Setup','wpbooking'),
                'capability'=>'manage_options',
                'menu_slug'=>'wpbooking_setup_page_settings',
                'function'=> array($this,'callback_wpbooking_sub_menu')
            );
            return apply_filters('wpbooking_setting_menu_args',$page);
        }
        function callback_wpbooking_sub_menu() {
            echo ($this->admin_load_view('setup_demo'));
        }
        function _save_setup_demo()
        {
            if(!empty( $_POST[ 'wpbooking_save_setup' ] ) and wp_verify_nonce( $_REQUEST[ 'wpbooking_save_setup_demo' ] , "wpbooking_action" )) {
                $tab = WPBooking_Input::request("is_tab");
                switch($tab){
                    case "wp_general":
                        $value_request = WPBooking_Input::request("setup_demo");
                        $key_request = "wpbooking_currency";
                        update_option($key_request,$value_request['currency']);
                        $page_archive  = get_page_by_title( 'Wpbooking Archive' );
                        if(empty($page_archive)){
                            $my_post = array(
                                'post_title'   => "Wpbooking Archive" ,
                                'post_content' => "" ,
                                'post_status'  => 'publish' ,
                                'post_type'    => 'page' ,
                            );
                            $post_id = wp_insert_post( $my_post );
                            update_option("wpbooking_archive-page",$post_id);
                        }else{
                            update_option("wpbooking_archive-page",$page_archive->ID);
                        }
                        $page_term_condition  = get_page_by_title( 'Wpbooking Term & Condition' );
                        if(empty($page_term_condition)){
                            $my_post = array(
                                'post_title'   => "Wpbooking Term & Condition" ,
                                'post_content' => "" ,
                                'post_status'  => 'publish' ,
                                'post_type'    => 'page' ,
                            );
                            $post_id = wp_insert_post( $my_post );
                            update_option("wpbooking_term-page",$post_id);
                        }else{
                            update_option("wpbooking_term-page",$page_term_condition->ID);
                        }
                        $page_my_acount  = get_page_by_title( 'Wpbooking My Account' );
                        if(empty($page_my_acount)){
                            $my_post = array(
                                'post_title'   => "Wpbooking My Account" ,
                                'post_content' => "[wpbooking-myaccount]" ,
                                'post_status'  => 'publish' ,
                                'post_type'    => 'page' ,
                            );
                            $post_id = wp_insert_post( $my_post );
                            update_option("wpbooking_myaccount-page",$post_id);
                        }else{
                            update_option("wpbooking_myaccount-page",$page_my_acount->ID);
                        }
                        wp_redirect( add_query_arg( array('page'=>'wpbooking_setup_page_settings','wp_step'=>'wp_booking') , admin_url("admin.php") ) );
                        exit;
                        break;
                    case "wp_booking":
                        $page_cart  = get_page_by_title( 'Wpbooking Cart' );
                        if(empty($page_cart)){
                            $my_post = array(
                                'post_title'   => "Wpbooking Cart" ,
                                'post_content' => "[wpbooking_cart_page]" ,
                                'post_status'  => 'publish' ,
                                'post_type'    => 'page' ,
                            );
                            $post_id = wp_insert_post( $my_post );
                            update_option("wpbooking_cart_page",$post_id);
                        }else{
                            update_option("wpbooking_cart_page",$page_cart->ID);
                        }
                        $page_check_out  = get_page_by_title( 'Wpbooking Check Out' );
                        if(empty($page_cart)){
                            $my_post = array(
                                'post_title'   => "Wpbooking Check Out" ,
                                'post_content' => "[wpbooking_checkout_page]" ,
                                'post_status'  => 'publish' ,
                                'post_type'    => 'page' ,
                            );
                            $post_id = wp_insert_post( $my_post );
                            update_option("wpbooking_checkout_page",$post_id);
                        }else{
                            update_option("wpbooking_checkout_page",$page_check_out->ID);
                        }
                        $page_check_form  = get_page_by_title( 'Wpbooking Check Out Form 1' ,'OBJECT' , "wpbooking_form" );
                        if(empty($page_check_form)){
                            $content =  self::_get_template_default("check_out_form");
                            $my_post = array(
                                'post_title'   => "Wpbooking Check Out Form 1" ,
                                'post_content' => $content ,
                                'post_status'  => 'publish' ,
                                'post_type'    => 'wpbooking_form' ,
                            );
                            $post_id = wp_insert_post( $my_post );
                            update_option("wpbooking_checkout_form",$post_id);
                        }else{
                            update_option("wpbooking_checkout_form",$page_check_form->ID);
                        }
                        update_option("wpbooking_allow_guest_checkout",WPBooking_Input::request('wpbooking_allow_guest_checkout'));
                        wp_redirect( add_query_arg( array('page'=>'wpbooking_setup_page_settings','wp_step'=>'wp_email') , admin_url("admin.php") ) );
                        exit;
                        break;
                    case "wp_email":
                        update_option("wpbooking_email_from",WPBooking_Input::request('wpbooking_email_from'));
                        update_option("wpbooking_email_from_address",WPBooking_Input::request('wpbooking_email_from_address'));
                        update_option("wpbooking_on_booking_email_customer",WPBooking_Input::request('wpbooking_on_booking_email_customer',0));
                        update_option("wpbooking_on_booking_email_author",WPBooking_Input::request('wpbooking_on_booking_email_author',0));
                        update_option("wpbooking_on_booking_email_admin",WPBooking_Input::request('wpbooking_on_booking_email_admin',0));
                        update_option("wpbooking_on_registration_email_customer",WPBooking_Input::request('wpbooking_on_registration_email_customer',0));
                        update_option("wpbooking_on_registration_email_admin",WPBooking_Input::request('wpbooking_on_registration_email_admin',0));
                        update_option("wpbooking_email_stylesheet",self::_get_template_default("css"));
                        update_option("wpbooking_email_to_customer",self::_get_template_default("booking_email_customer"));
                        update_option("wpbooking_email_to_partner",self::_get_template_default("booking_email_author"));
                        update_option("wpbooking_email_to_admin",self::_get_template_default("booking_email_admin"));
                        update_option("wpbooking_registration_email_customer",self::_get_template_default("registration_email_customer"));
                        update_option("wpbooking_registration_email_admin",self::_get_template_default("registration_email_admin"));

                        wp_redirect( add_query_arg( array('page'=>'wpbooking_setup_page_settings','wp_step'=>'wp_service') , admin_url("admin.php") ) );
                        exit;
                        break;
                    case "wp_service":
                        ///////////////////
                        /////Room//////////
                        ///////////////////
                        update_option("wpbooking_service_type_room_enable_review",WPBooking_Input::request('wpbooking_service_type_room_enable_review',0));
                        update_option("wpbooking_service_type_room_review_without_booking",WPBooking_Input::request('wpbooking_service_type_room_review_without_booking',0));
                        update_option("wpbooking_service_type_room_show_rate_review_button",WPBooking_Input::request('wpbooking_service_type_room_show_rate_review_button',0));
                        update_option("wpbooking_service_type_room_allowed_review_on_own_listing",WPBooking_Input::request('wpbooking_service_type_room_allowed_review_on_own_listing',0));
                        update_option("wpbooking_service_type_room_allowed_vote_for_own_review",WPBooking_Input::request('wpbooking_service_type_room_allowed_vote_for_own_review',0));
                        update_option("wpbooking_service_type_room_review_stats",array(
                            array(
                                'title'=>'Location'
                            ),
                            array(
                                'title'=>'View'
                            ),
                            array(
                                'title'=>'Meal'
                            ),
                            array(
                                'title'=>'Sleep'
                            ),
                            array(
                                'title'=>'Cleaness'
                            ),
                            array(
                                'title'=>'Quality Service'
                            )
                        ));
                        update_option("wpbooking_service_type_room_maximum_review",1);
                        $page_order_form  = get_page_by_title( 'Wpbooking Order Form 1' ,'OBJECT' , "wpbooking_form" );
                        if(empty($page_order_form)){
                            $content =   self::_get_template_default("order_form");
                            $my_post = array(
                                'post_title'   => "Wpbooking Order Form 1" ,
                                'post_content' => $content ,
                                'post_status'  => 'publish' ,
                                'post_type'    => 'wpbooking_form' ,
                            );
                            $post_id = wp_insert_post( $my_post );
                            update_option("wpbooking_service_type_room_order_form",$post_id);
                        }else{
                            update_option("wpbooking_service_type_room_order_form",$page_order_form->ID);
                        }
                        update_option("wpbooking_service_type_room_posts_per_page",10);
                        wp_redirect( add_query_arg( array('page'=>'wpbooking_setup_page_settings','wp_step'=>'wp_payment') , admin_url("admin.php") ) );
                        exit;
                        break;
                    case "wp_payment":
                        update_option("wpbooking_gateway_bank_transfer_enable",WPBooking_Input::request('wpbooking_gateway_bank_transfer_enable',0));
                        update_option("wpbooking_gateway_paypal_enable",WPBooking_Input::request('wpbooking_gateway_paypal_enable',0));
                        update_option("wpbooking_setup_demo","false");
                        wp_redirect( add_query_arg( array('page'=>'wpbooking_page_settings') , admin_url("admin.php") ) );
                        exit;
                        break;
                }

                do_action('wpbooking_do_setup',$tab);
            }
        }
        static function _get_template_default($style){
            $html = "";
            switch($style){
                case "css":
                    $html = '.template{
                                    background:#F1F1F1;
                                    font-family:tahoma;
                                    padding:50px 0px;

                                }
                                .content{
                                    background:white;
                                    width:600px;
                                    margin:0px auto;
                                    border-radius:4px;
                                    padding:20px;
                                    border:1px solid #dcdcdc;
                                }
                                .header{
                                    margin:-20px;
                                    background:#0073aa;
                                    padding:15px 25px;
                                    margin-bottom:40px;
                                }
                                .header h1{
                                    color:white;
                                    text-transform:uppercase;
                                    font-size:30px;
                                }
                                .footer{
                                    margin:-20px;
                                    background:#ECECEC;
                                    padding:5px 10px;
                                    margin-top:40px;
                                    color:#737373;
                                }
                                table,tr{
                                    border-top:1px solid #ccc;
                                    border-left:1px solid #ccc;
                                    background:white;
                                }
                                td,th{
                                    border-right:1px solid #ccc;
                                    border-bottom:1px solid #ccc;
                                    color:#737373;
                                    padding:12px;
                                }


                                .review-cart-total:before,
                                .review-cart-total:after {
                                  display: table;
                                  content: \"\";
                                }
                                .review-cart-total:after {
                                  clear: both;
                                }
                                .review-cart-total .total-title {
                                  clear: both;
                                  font-size: 14px;
                                  float: left;
                                  margin-top: 10px;
                                }
                                .review-cart-total .total-amount {
                                  font-size: 14px;
                                  color: #666666;
                                  float: right;
                                  margin-top: 10px;
                                }
                                .review-cart-total .total-amount.big {
                                  font-size: 20px;
                                  margin-top: 5px;
                                }
                                .review-cart-total .total-line {
                                  clear: both;
                                  height: 1px;
                                  width: 100%;
                                  background: #333;
                                  margin: 10px 0px;
                                  display: block;
                                  margin-top: 15px;
                                }

                                .label {
                                    display: inline;
                                    padding: .2em .6em .3em;
                                    font-size: 75%;
                                    font-weight: bold;
                                    line-height: 1;
                                    color: #fff;
                                    text-align: center;
                                    white-space: nowrap;
                                    vertical-align: baseline;
                                    border-radius: .25em;
                                }
                                a.label:hover,
                                a.label:focus {
                                    color: #fff;
                                    text-decoration: none;
                                    cursor: pointer;
                                }
                                .label:empty {
                                    display: none;
                                }
                                .btn .label {
                                    position: relative;
                                    top: -1px;
                                }
                                .label-default {
                                    background-color: #777;
                                }
                                .label-default[href]:hover,
                                .label-default[href]:focus {
                                    background-color: #5e5e5e;
                                }
                                .label-primary {
                                    background-color: #337ab7;
                                }
                                .label-primary[href]:hover,
                                .label-primary[href]:focus {
                                    background-color: #286090;
                                }
                                .label-success {
                                    background-color: #5cb85c;
                                }
                                .label-success[href]:hover,
                                .label-success[href]:focus {
                                    background-color: #449d44;
                                }
                                .label-info {
                                    background-color: #5bc0de;
                                }
                                .label-info[href]:hover,
                                .label-info[href]:focus {
                                    background-color: #31b0d5;
                                }
                                .label-warning {
                                    background-color: #f0ad4e;
                                }
                                .label-warning[href]:hover,
                                .label-warning[href]:focus {
                                    background-color: #ec971f;
                                }
                                .label-danger {
                                    background-color: #d9534f;
                                }
                                .label-danger[href]:hover,
                                .label-danger[href]:focus {
                                    background-color: #c9302c;
                                }
                                .wp-email-content-wrap{
                                    text-align: center;
                                    padding: 20px 70px;
                                    border-radius: 0;
                                    color: #000;
                                }
                                .wp-email-content-wrap .title{
                                    font-size: 25px;
                                    color: #6aa84f;
                                    margin-bottom: 17px;
                                }
                                .wp-email-content-wrap .title.disapproved{
                                    color: #cc4125;
                                }
                                .wp-email-content-wrap .content-header{
                                    margin-bottom: 40px;
                                }
                                .wp-email-content-wrap .content-header p{
                                    line-height: 25px;
                                }
                                .wp-email-content-wrap .content-center{
                                    background: #fafafa;
                                    padding: 20px 15px;
                                }
                                .wp-email-content-wrap .content-center .icon{
                                    font-size: 45px;
                                    line-height: 1;
                                }
                                .wp-email-content-wrap .content-center .comment{
                                    margin-top: 0px;
                                    margin-bottom: 22px;
                                    font-style: italic;
                                    font-size: 15px;
                                }
                                .wp-email-content-wrap .content-center .review{
                                    font-style: italic;
                                    font-size: 15px;
                                }
                                .review-score{
                                    display: table;
                                    width: 50%;
                                    list-style: none;
                                    text-align: left;
                                    margin: 0 auto;
                                }
                                .review-score li{
                                    display: table-row;
                                    line-height: 2;
                                }
                                .review-score li span{
                                    display: table-cell;
                                }
                                .review-score li .score{
                                    color: #F0AD4E;
                                }
                                .wp-email-content-wrap .content-footer{
                                    margin: 30px;
                                }
                                .wp-email-content-wrap .content-footer .btn.btn-default{
                                    padding: 15px;
                                    background: #F0AD4E;
                                    color: #FFF;
                                    text-decoration: none;
                                    font-size: 15px;
                                    display: inline-block;
                                }
                                .wp-email-content-wrap .content-footer .comment_link{
                                    display: block;
                                    margin-top: 15px;
                                    font-style: italic;
                                    font-size: 15px;
                                }
                                .wp-email-content-wrap .content-footer .comment_link a{
                                    color: #F0AD4E;
                                }
                                ';
                    break;
                case "booking_email_customer":
                    $html = '<div class=template>
                               <div class=content>
                                    <div class=header>
                                       <h1>Order Information</h1>
                                     </div>
                            <h2>Dear <strong>[checkout_form_field name=first_name]</h2>
                            <p>          Here are information of your booking</p>
                            <p>Order ID: [order_id]</p>
                            <p>Order Status:[order_status]</p>
                            <p>Booking Date:[order_date]</p>
                            <p>Payment Gateway:[order_payment_gateway]</p>
                            <p>Total:[order_total]</p>
                            <br><br>
                                     [order_table]
                                       <br>
                                       <br>
                                       [checkout_info]
                                       <br>
                                       <br>
                                       <h3>Seperate Checkout Field</h3>
                                     <p>Name: <strong>[checkout_form_field name=first_name]</strong></p>
                            <p>Email: <strong>[checkout_form_field name=user_email]</strong></p>
                            <p>Your Phone: <strong>[checkout_form_field name=phone]</strong></p>
                                     <div class=footer>
                                       <p>Generated by <a href="#">WPBooking</a> Plugin</p>
                                     </div>
                                </div>
                            </div>';
                    break;
                case "booking_email_author":
                    $html = '<div class=template>
                               <div class=content>
                                    <div class=header>
                                       <h1>Order Information</h1>
                                     </div>
                            <h2>Dear shop manager<h2>
                            <p>          You have received an order from [checkout_form_field name="first_name"]. This is booking information </p>
                            <p>Order ID: [order_id]</p>
                            <p>Order Status:[order_status]</p>
                            <p>Booking Date:[order_date]</p>
                            <p>Payment Gateway:[order_payment_gateway]</p>
                            <p>Total:[order_total]</p>
                                     [order_table]
                                       <br>
                                       <br>
                                       [checkout_info]
                                       <br>
                                       <br>
                                       <h3>Seperate Checkout Field</h3>
                                     <p>Name: <strong>[checkout_form_field name=first_name] [checkout_form_field name=last_name]</strong></p>
                            <p>Email: <strong>[checkout_form_field name=user_email]</strong></p>
                            <p>Phone: <strong>[checkout_form_field name=phone]</strong></p>
                                     <div class=footer>
                                       <p>Generated by <a href="#">WPBooking</a> Plugin</p>
                                     </div>
                                </div>
                            </div>';
                    break;
                case "booking_email_admin":
                    $html  = '<div class=template>
                               <div class=content>
                                    <div class=header>
                                       <h1>Order Information</h1>
                                     </div>
                            <h2>Dear Admin <h2>
                            <p>          Here are information of your booking</p>
                            <p>Order ID: [order_id]</p>
                            <p>Order Status:[order_status]</p>
                            <p>Booking Date:[order_date]</p>
                            <p>Payment Gateway:[order_payment_gateway]</p>
                            <p>Total:[order_total]</p>
                                     [order_table]
                                       <br>
                                       <br>
                                       [checkout_info]
                                       <br>
                                       <br>
                                       <h3>Seperate Checkout Field</h3>
                                     <p>Name: <strong>[checkout_form_field name=first_name] [checkout_form_field name=last_name]</strong></p>
                            <p>Email: <strong>[checkout_form_field name=user_email]</strong></p>
                            <p>Phone: <strong>[checkout_form_field name=phone]</strong></p>
                                     <div class=footer>
                                       <p>Generated by <a href="#">WPBooking</a> Plugin</p>
                                     </div>
                                </div>
                            </div>';
                    break;
                case "registration_email_customer":
                    $html = '<div class=template>
                               <div class=content>
                                    <div class=header>
                                       <h1>New Registration<h1>
                                     </div>
                            <h2>Dear Customer<h2>
                            <p>          Thank you for registration. Here are your account information:</p>
                                     <strong>Username</strong>: [user_login]
                            <br>
                                     <strong>Password</strong>: [user_pass]
                            <br>
                                     <strong>Email</strong>:[user_email]
                            <br>
                                     <strong>Profile URL</strong>:[profile_url]<br>
                                     <div class=footer>
                                       <p>Generated by <a href="#">WPBooking</a> Plugin</p>
                                     </div>
                                </div>
                            </div>';
                    break;
                case "registration_email_admin":
                    $html = '<div class=template>
                               <div class=content>
                                    <div class=header>
                                       <h1>New Registration<h1>
                                     </div>
                            <h2>Dear Admin<h2>
                            <p>          Thank you for registration. Here are your account information:</p>
                                     <strong>Username</strong>: [user_login]
                            <br>
                                     <strong>Email</strong>:[user_email]
                            <br>
                                     <strong>Edit User URL</strong>:[edit_user_url]
                            <br>
                                     <div class=footer>
                                       <p>Generated by <a href="#">WPBooking</a> Plugin</p>
                                     </div>
                                </div>
                            </div>';
                    break;
                case "check_out_form":
                    $html = '<div class="row">
                                            <div class="col-sm-6">[wpbooking_form_first_name is_required="on" title="First name" ]</div>
                                            <div class="col-sm-6">[wpbooking_form_last_name is_required="on" title="Last name" ]</div>
                                        </div>

                                        [wpbooking_form_text title="Company Name" name="company-name" ]

                                        <div class="row">
                                            <div class="col-sm-6">[wpbooking_form_user_email is_required="on" title="Email address" ]</div>
                                            <div class="col-sm-6">[wpbooking_form_text is_required="on" title="Phone" name="phone" ]</div>
                                        </div>

                                        [wpbooking_form_country_dropdown is_required="on" title="Country" name="country" ]

                                        [wpbooking_form_text is_required="on" title="Address" name="address" ]

                                        <div class="row">
                                            <div class="col-sm-6">[wpbooking_form_text  title="Postcode / ZIP" name="postcode" ]</div>
                                            <div class="col-sm-6">[wpbooking_form_text  title="Apt/ Unit" name="apt_unit" ]</div>
                                        </div>

                                        [wpbooking_form_textarea title="Order Note" name="order_note" placeholder="Notes about your order, e.g special note for delivery" ]';
                    break;
                case "order_form":
                    $html = '[wpbooking_form_extra_services title="Extra Services" ]

                            [wpbooking_form_check_in is_required="on" title="Check In" ]

                            [wpbooking_form_check_out is_required="on" title="Check Out" ]

                            [wpbooking_form_guest is_required="on" title="Guest" ]

                            [wpbooking_form_submit_button label="Book Now" ]';
                    break;
            }
            return $html;

        }
        static function inst()
        {
            if(!self::$_inst){
                self::$_inst=new self();
            }
            return self::$_inst;
        }
    }
    WPBooking_Admin_Setup::inst();
}