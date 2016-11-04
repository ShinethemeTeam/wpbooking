<?php
/**
 * Created by ShineTheme.
 * User: NAZUMI
 * Date: 11/2/2016
 * Version: 1.0
 */
if(!defined('ABSPATH')){
    exit;
}
if(!class_exists('WPBooking_Comments')){
    class WPBooking_Comments{

        private static $_inst;

        public function __construct()
        {

            /**
             * Add notification email when changed comment status
             *
             * @author: tienhd
             * @since: 1.0
             */
            add_action('transition_comment_status', array($this,'_notification_email'), 10, 3);

        }

        /**
         * Notification email when approved and disapproved comment
         *
         * @author: tienhd
         * @since: 1.0
         *
         * @param $new_status
         * @param $old_status
         * @param $comment
         */
        public function _notification_email( $new_status, $old_status, $comment ) {

            if($new_status != $old_status) {
                //Send mail when approved
                if (!empty($new_status) && ($new_status == 'approved' || ($new_status != 'approved' && $old_status == 'approved'))) {
                    //Get email author
                    $send_to = $comment->comment_author_email;
                    //check mail
                    if (is_email($send_to)) {

                        if (is_multisite())
                            $blog_name = $GLOBALS['current_site']->site_name;
                        else
                            $blog_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

                        if ($new_status == 'approved') {
                            $subject = sprintf(esc_html__('[%s] Approved review'), $blog_name);
                            $subject = apply_filters('wpbooking_approved_comment_subject_email', $subject, $comment);
                        } else {
                            $subject = sprintf(esc_html__('[%s] Disapproved review'), $blog_name);
                            $subject = apply_filters('wpbooking_disapproved_comment_subject_email', $subject, $comment);
                        }

                        $message = $this->email_comment_template($new_status, $comment);

                        WPBooking_Email::inst()->send($send_to, $subject, $message);
                    }

                }
            }
        }

        /**
         * Email templates for approved comment
         *
         * @author: tienhd
         * @since: 1.0
         *
         * @param $status
         * @param $comment_data
         * @return mixed|string|void
         */
        public function email_comment_template($status,$comment_data){

            $html = wpbooking_load_view('emails/templates/approved_comment',array('status' => $status, 'comment_data' => $comment_data));
            $html = apply_filters('wpbooking_email_template_approved_comment_html',$html,$comment_data,$status);

            return $html;

        }

        static function _inst(){
            if(!self::$_inst){
                self::$_inst = new self();
            }

            return self::$_inst;
        }
    }
    WPBooking_Comments::_inst();
}