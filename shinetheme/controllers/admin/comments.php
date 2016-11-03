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
         * Email template for approved comment
         *
         * @author: tienhd
         * @since: 1.0
         *
         * @param $status
         * @param $comment_data
         * @return mixed|string|void
         */
        public function email_comment_template($status,$comment_data){

            $header = $footer = $html = '';

            if(!empty($comment_data)) {
                $review_html = '5 score';

                $approve_str = 'approved';
                if($status != 'approved') {
                    $approve_str = 'disapproved';
                }

                //review score
                $review_detail = get_comment_meta($comment_data->comment_ID,'wpbooking_review_detail',true);
                if(!empty($review_detail) and is_array($review_detail)){
                    $review_html = '<ul class="review-score">';
                    foreach($review_detail as $key => $value){
                        $review_html .= '<li><span class="rv-title">'.$value['title'].'</span><span class="score">'.$value['rate'].'/5 '.esc_html__('points').'</span></li>';
                    }
                    $review_html .= '</ul>';
                }

                if ( is_multisite() )
                    $blog_name = $GLOBALS['current_site']->site_name;
                else
                    $blog_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

                $desc = sprintf(wp_kses(__('Hello <strong>%s</strong>, Your review is %s by %s administrator.<br> at %s','wpbooking'),array('br'=>array(),'strong' => array())), $comment_data->comment_author,$approve_str, $blog_name, date('Y/m/d H:i a'));

                $header = apply_filters('wpbooking_header_email_template_html', $header);
                $html .= str_replace('\"','"',$header);
                $html .= '<div class="wp-email-content-wrap content">
                        <div class="content-header">
                            <h3 class="title '.$approve_str.'">' . sprintf(esc_html__('Your review is %s','wpbooking'),$approve_str) . '</h3>
                            <p class="description">' . $desc . '</p>
                        </div>
                        <div class="content-center">
                            <i class="icon">&ldquo;</i>
                            <p class="comment">'.$comment_data->comment_content.'</p>
                            <div class="review">'.$review_html.'</div>
                        </div>
                        <div class="content-footer">
                            <a class="btn btn-default" href="'.get_comment_link($comment_data->comment_ID).'">'.esc_html__('Show comment','wpbooking').'</a>
                            <span class="comment_link">'.esc_html__('Can\'t see the button? Try this ').'<a href="'.get_comment_link($comment_data->comment_ID).'">'.esc_html__('Link','wpbooking').'</a></span>
                        </div>
                    </div>';
                $footer = apply_filters('wpbooking_footer_email_template_html', $header);
                $html .= str_replace('\"','"',$footer);

                $html = apply_filters('wpbooking_email_template_approved_comment_html',$html,$comment_data,$status);

            }
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