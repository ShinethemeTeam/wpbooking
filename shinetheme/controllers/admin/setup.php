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
                'page_title'=>esc_html__('Setup','wp-booking-management-system'),
                'menu_title'=>esc_html__('Setup','wp-booking-management-system'),
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
                        // Setup Plugin
                        global $wp_rewrite;
                        $wp_rewrite->set_permalink_structure( '/%postname%/' );

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

                        $list_taxonomy = array(
                            'wpbooking_amenity'=>array(
                                'fa-safari'=>'Air Conditioning',
                                'fa-briefcase'=>'Breakfast',
                                'fa-wifi'=>'Buzzer/Wireless Intercom',
                                'fa-tv'=>'Cable TV',
                            ),
                            'wb_hotel_room_facilities'=>array(
                                'fa-clock-o'=>'Alarm clock',
                                'fa-object-group'=>'Bathroom',
                                'fa-tripadvisor'=>'iPod dock',
                                'fa-laptop'=>'Laptop safe',
                            ),
                            'wpbooking_location'=>array(
                                'Italy',
                                'Germany',
                                'United States',
                                'Russia',
                                'Paris',
                            ),
                            'wb_tour_type'=>array(
                                'City trips',
                                'Ecotourism',
                                'Sightseeing',
                            ),
                            'wb_hotel_room_type'=>array(
                                'Apartment',
                                'Bed in Dormitory',
                                'Dormitory room',
                                'Double',
                                'Family',
                                'Single',
                                'Suite',
                                'Triple',
                                'Twin',
                                'Twin/Double',
                            ),
                        );
                        foreach($list_taxonomy as $taxonomy=>$terms){
                            foreach($terms as $icon=>$term){
                                $rs = wp_insert_term($term,$taxonomy);
                                if(!is_wp_error($rs)){
                                    $term_id = $rs['term_id'];
                                    if(!is_numeric($icon)){
                                        update_tax_meta($term_id,'wpbooking_icon',$icon);
                                    }
                                }
                            }
                        }

                        wp_redirect( add_query_arg( array('page'=>'wpbooking_setup_page_settings','wp_step'=>'wp_booking') , admin_url("admin.php") ) );
                        exit;
                        break;
                    case "wp_booking":
                        $page_check_out  = get_page_by_title( 'Wpbooking Checkout' );
                        if(empty($page_cart)){
                            $my_post = array(
                                'post_title'   => "Wpbooking Checkout" ,
                                'post_content' => "[wpbooking_checkout_page]" ,
                                'post_status'  => 'publish' ,
                                'post_type'    => 'page' ,
                            );
                            $post_id = wp_insert_post( $my_post );
                            update_option("wpbooking_checkout_page",$post_id);
                        }else{
                            update_option("wpbooking_checkout_page",$page_check_out->ID);
                        }
                        $key_request = "wpbooking_allow_captcha_google_checkout";
                        $value_request = WPBooking_Input::request($key_request);
                        update_option($key_request,$value_request);

                        $key_request = "wpbooking_google_key_captcha";
                        $value_request = WPBooking_Input::request($key_request);
                        update_option($key_request,$value_request);

                        $key_request = "wpbooking_google_secret_key_captcha";
                        $value_request = WPBooking_Input::request($key_request);
                        update_option($key_request,$value_request);


                        update_option("wpbooking_allow_guest_checkout",WPBooking_Input::request('wpbooking_allow_guest_checkout'));
                        wp_redirect( add_query_arg( array('page'=>'wpbooking_setup_page_settings','wp_step'=>'wp_email') , admin_url("admin.php") ) );
                        exit;
                        break;
                    case "wp_email":
                        update_option("wpbooking_email_from",WPBooking_Input::request('wpbooking_email_from'));
                        update_option("wpbooking_email_from_address",WPBooking_Input::request('wpbooking_email_from_address'));
                        update_option("wpbooking_system_email",WPBooking_Input::request('wpbooking_system_email'));

                        update_option("wpbooking_on_booking_email_customer",WPBooking_Input::request('wpbooking_on_booking_email_customer',0));
                        update_option("wpbooking_on_booking_email_admin",WPBooking_Input::request('wpbooking_on_booking_email_admin',0));

                        update_option("wpbooking_on_registration_email_customer",WPBooking_Input::request('wpbooking_on_registration_email_customer',0));
                        update_option("wpbooking_on_registration_email_admin",WPBooking_Input::request('wpbooking_on_registration_email_admin',0));

                        update_option("wpbooking_email_header",self::_get_template_default("email_header"));
                        update_option("wpbooking_email_footer",self::_get_template_default("email_footer"));

                        update_option("wpbooking_email_stylesheet",self::_get_template_default("css"));

                        update_option("wpbooking_email_to_customer",self::_get_template_default("booking_email_customer"));
                        update_option("wpbooking_email_to_admin",self::_get_template_default("booking_email_admin"));

                        update_option("wpbooking_registration_email_customer",self::_get_template_default("registration_email_customer"));
                        update_option("wpbooking_registration_email_admin",self::_get_template_default("registration_email_admin"));

                        wp_redirect( add_query_arg( array('page'=>'wpbooking_setup_page_settings','wp_step'=>'wp_payment') , admin_url("admin.php") ) );
                        exit;
                        break;
                    case "wp_payment":
                        $gateway=WPBooking_Payment_Gateways::inst();
                        $all=$gateway->get_gateways();
                        if(!empty($all)){
                            foreach($all as $key=>$value){
                                update_option("wpbooking_gateway_".$key."_enable",WPBooking_Input::request('wpbooking_gateway_'.$key.'_enable',0));
                                if($key == 'submit_form'){
                                    update_option("wpbooking_gateway_".$key."_title",esc_html__('Submit Form','wp-booking-management-system'));
                                    update_option("wpbooking_gateway_".$key."_desc",esc_html__('Submit Form','wp-booking-management-system'));
                                }
                                if($key == 'paypal'){
                                    update_option("wpbooking_gateway_".$key."_title",esc_html__('PayPal','wp-booking-management-system'));
                                    update_option("wpbooking_gateway_".$key."_desc",esc_html__('You will be redirect to paypal website to finish the payment process','wp-booking-management-system'));
                                }
                            }
                        }
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
                    $html = '    
.color_black{
    color:black;
}
.font_italic{
    font-style:italic;
}
.completed{
    border: 1px solid #669966;
    color: #669966;
    font-size: 12px;
    font-weight: bold;
    padding: 3px 10px;
    display: inline-block;
    margin-top: -3px;
}
.failed{
   border: 1px solid red;
    color: red;
    font-size: 12px;
    font-weight: bold;
    padding: 3px 10px;  
    display: inline-block;
    margin-top: -3px;
}
.on_hold{
     border: 1px solid #f0ad4e;
    color: #f0ad4e;
    font-size: 12px;
    font-weight: bold;
    padding: 3px 10px;
    display: inline-block;
    margin-top: -3px;
}
.col-10{
    float:left;
    width:100%;
} 
.col-5{
    float:left;
    width:50%;
} 
.col-7{
    float:left;
    width:70%;
} 
.col-3{
    float:left;
    width:30%;
} 
.col-2{
    float:left;
    width:20%;
} 
.float-right{
    float:right;
}
.bold{
    font-weight:bold;
}
.head-info{
    margin-bottom:20px;   
}
.head-info-content-hl{
     color: #f0ad4e;
}
.head-info-total-price{
    text-align:right;
    
}
.head-info-total-price .head-info-title{
     color: #666666;
    display: block;
    font-size: 15px;
    text-transform: uppercase;
    font-weight:bold;
}
.head-info-total-price .head-info-content{
    color: #333333;
    display: block;
    font-size: 24px;
    font-weight: bold;
}
.content-row {
	overflow:hidden;
}
table{
    width:100%;
    font-size: 13px;
}
h3{
    margin:0px;
    padding:0px 0px 10px 0px;
}
h4{
    margin:0px;
    padding:0px 0px 10px 0px;
}
.room-image img{
    width:50px;
}
.text-center{
    text-align:center;
}
.service_info td{
    vertical-align: top;
}
table {
    border-collapse: collapse;
}

table, th, td {
    border: 1px solid #ccc;
}
.extra-service{
    margin-top:10px;
}
.btn_detail_checkout {
    color: #f0ad4e;
    font-size: 13px;
    font-style: italic;
    font-weight: normal;
    margin-top: 20px;
}
.customer{
    margin-top:30px;
}
.customer label{
    font-weight: bold;
}
.btn_history{
    background: #f0ad4e none repeat scroll 0 0;
    border: 1px solid #ff9933 !important;
    border-radius: 0;
    color: white;
    display: inline-block;
    font-size: 15px;
    font-weight: normal;
    margin-bottom: 20px;
    margin-top: 20px;
    padding: 5px 20px;
    text-decoration: none;
    text-transform: none;
}
.color{
    color:#f0ad4e;
}
.content-total{
    width: 50%; float: right;
}
.total-title,.total-amount{
    display: inline-block; width: 50%;
    margin-bottom: 20px;
}
.total-amount{
    float:right;
}
.total-title{
    text-align:left;
}









.template {
	background: #F1F1F1;
	font-family: tahoma;
	padding: 50px 0px;
}

.email-template {
	width: 100%;
	text-align: center;
	background: #f2f2f2;
}

.email-header {
	padding-top: 50px;
	padding-bottom: 25px;
}

.email-header h2 {
	font-size: 30px;
}

.email-footer {
	padding-top: 50px;
	padding-bottom: 60px;
	font-size: 15px;
	font-style: italic;
	line-height: 25px;
}

.email-footer a {
	margin-left: 10px;
	margin-right: 10px;
}

.email-footer img {
	width: 22px;
	height: 22px;
}

.content {
	background: white;
	width: 600px;
	margin: 0px auto;
	border-radius: 4px;
	padding: 20px;
	padding: 20px 70px 50px;
	border-radius: 0;
	color: #000;
	font-size: 15px;
	text-align: left;
	overflow:auto;
}

.header {
	margin: -20px;
	background: #0073aa;
	padding: 15px 25px;
	margin-bottom: 40px;
}

.header h1 {
	color: red;
	text-transform: uppercase;
	font-size: 30px;
}

.footer {
	margin: -20px;
	background: #ECECEC;
	padding: 5px 10px;
	margin-top: 40px;
	color: #737373;
}

table, tr {
	border-top: 1px solid #ccc;
	border-left: 1px solid #ccc;
	background: white;
}

td, th {
	border-right: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
	
	padding: 8px;
}

.review-cart-total:before,
.review-cart-total:after {
	display: table;

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

a.label:hover, a.label:focus {
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

.label-default[href]:hover, .label-default[href]:focus {
	background-color: #5e5e5e;
}

.label-primary {
	background-color: #337ab7;
}

.label-primary[href]:hover, .label-primary[href]:focus {
	background-color: #286090;
}

.label-success {
	background-color: #5cb85c;
}

.label-success[href]:hover, .label-success[href]:focus {
	background-color: #449d44;
}

.label-info {
	background-color: #5bc0de;
}

.label-info[href]:hover, .label-info[href]:focus {
	background-color: #31b0d5;
}

.label-warning {
	background-color: #f0ad4e;
}

.label-warning[href]:hover, .label-warning[href]:focus {
	background-color: #ec971f;
}

.label-danger {
	background-color: #d9534f;
}

.label-danger[href]:hover, .label-danger[href]:focus {
	background-color: #c9302c;
}

/*email comment*/
.title {
	font-size: 28px;
	margin-bottom: 17px;
}

.title-approved {
	color: #6aa84f;
	font-size: 28px;
	margin-bottom: 17px;
}

.title-disapproved {
	color: #cc4125;
	font-size: 28px;
	margin-bottom: 17px;
}

.content-header {
	margin-bottom: 40px;
	text-align: center;
}

.content-header p {
	line-height: 25px;
	font-style: italic;
}

.content-center {
	background: #fafafa;
	padding: 20px 15px;
	text-align: center;
	font-style: italic;
}

.content .content-center a {
	color: #F0AD4E;
}

.content-center .icon {
	font-size: 45px;
	line-height: 1;
}

.content-center .comment {
	margin-top: 0px;
	margin-bottom: 22px;
	font-style: italic;
}

.content-center .review {
	font-style: italic;
}

.review-score {
	display: table;
	width: 50%;
	list-style: none;
	text-align: left;
	margin: 0 auto;
}

.review-score li {
	display: table-row;
	line-height: 2;
}

.review-score li span {
	display: table-cell;
}

.review-score li .score {
	color: #F0AD4E;
}

.content-footer {
	margin: 30px 30px 0;
	text-align: center;
}

.content-footer .btn.btn-default {
	padding: 15px;
	background: #F0AD4E;
	color: #FFF;
	text-decoration: none;
	display: inline-block;
}

.content-footer .comment_link {
	display: block;
	margin-top: 15px;
	font-style: italic;
}

.content-footer .comment_link a {
	color: #F0AD4E;
}

                                ';
                    break;
                case "booking_email_customer":
                    $html  = '
<div class=content>
    <h2> Hello [name_customer]!</h2>
    <h2> Your booking status is [order_status]</h2>
    <span class=font_italic> Below is your booking information</span>
    <br><br><br><br>
    <div class=content-row>
        <div class=col-7>
            <div class=head-info>
                <span class=head-info-title>Booking code:</span>
                <span class=head-info-content-hl>[order_id]</span>
            </div>
            <div class=head-info>
                <span class=head-info-title>Method of Payment:</span>
                <span class=head-info-content >[order_payment_gateway]</span>
            </div>
        </div>
        <div class=col-3>
            <div class=head-info-total-price>
                <span class=head-info-title>Total</span>
                <br>
                <span class=head-info-content>[order_total]</span>
            </div>
        </div>
    </div>
    <h2> YOUR BOOKING INFORMATION </h2>
    <div>[order_table]</div>
    <div class=customer>
        <div class=title>
            <h5>CUSTOMER INFORMATION:</h5>
        </div>
        [checkout_info]
    </div>
</div>';
                    break;
                case "booking_email_admin":
                    $html  = '
<div class=content>
    <h2> Hello Admin!</h2>
<h4>[name_customer] have just booked a room on your System. </h4>
 <span class=font_italic>Below is booking information:</span><br><br><br><br>
    <div class=content-row>
        <div class=col-7>
            <div class=head-info>
                <span class=head-info-title>Booking code:</span>
                <span class=head-info-content-hl>[order_id]</span>
            </div>
            <div class=head-info>
                <span class=head-info-title>Method of Payment:</span>
                <span class=head-info-content >[order_payment_gateway]</span>
            </div>
        </div>
        <div class=col-3>
            <div class=head-info-total-price>
                <span class=head-info-title>Total</span>
                <br>
                <span class=head-info-content>[order_total]</span>
            </div>
        </div>
    </div>
    <h2>BOOKING INFORMATION </h2>
    <div>[order_table]</div>
    <div class=customer>
        <div class=title>
           <h5>CUSTOMER INFORMATION:</h5>
        </div>
        [checkout_info]
    </div>
</div>';
                    break;
                case "registration_email_customer":
                    $html = '
<div class=content>
    <div class=content-header>
       <h3 class=title-approved >Registration Successful<h1>
       <p class=description>Hello <strong>[user_login]</strong>, Thank you for registration.<br>Here are your account information:</p>
     </div>
     <div class=content-center>
         Username: <strong>[user_login]</strong><br><br>
         Password: [user_pass]<br><br>
         Email: [user_email]<br><br>
         Profile URL: [profile_url]<br><br>
     </div>
</div>';
                    break;
                case "registration_email_admin":
                    $html = '<div class=content>
                                    <div class=content-header>
                                       <h3 class=title >New Registration<h1>
                                       <p class=description>Hello <strong>Administrator</strong>, There is a new user on your system.<br>Here are his(her) account information:</p>
                                     </div>
                                     <div class=content-center>
                                         Username: <strong>[user_login]</strong><br><br>
                                         Email: [user_email]<br><br>
                                         Edit User URL: [edit_user_url]<br><br>
                                     </div>
                                </div>';
                    break;
                case "email_header":
                    $html = '<div class=email-template>
                                <div class=email-header>
                                    <h2 class=email-title>WPBOOKING</h2>
                                </div>';
                    break;
                case "email_footer":
                    $html = '<div class=email-footer>
                                <a href="#"><img src="'.wpbooking_assets_url('images/facebook.png').'"/></a>
                                <a href="#"><img src="'.wpbooking_assets_url('images/twitter.png').'"/></a>
                                <a href="#"><img src="'.wpbooking_assets_url('images/google.png').'"/></a>
                                <a href="#"><img src="'.wpbooking_assets_url('images/linkedin.png').'"/></a>
                                <p>Copyright &copy; WP Booking. All rights reserved.</p>
                            </div>
                        </div>';
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