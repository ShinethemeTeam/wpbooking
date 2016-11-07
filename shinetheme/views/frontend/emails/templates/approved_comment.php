<?php
/**
 * Created by ShineTheme.
 * User: NAZUMI
 * Date: 11/4/2016
 * Version: 1.0
 */
if(!empty($comment_data)) {
    $review_html = '';
    $approve_str = 'approved';
    if ($status != 'approved') {
        $approve_str = 'disapproved';
    }

    //review score
    $review_detail = get_comment_meta($comment_data->comment_ID, 'wpbooking_review_detail', true);
    if (!empty($review_detail) and is_array($review_detail)) {
        $review_html = '<ul class="review-score">';
        foreach ($review_detail as $key => $value) {
            $review_html .= '<li><span class="rv-title">' . $value['title'] . '</span><span class="score">' . $value['rate'] . '/5 ' . esc_html__('points') . '</span></li>';
        }
        $review_html .= '</ul>';
    }

    if (is_multisite())
        $blog_name = $GLOBALS['current_site']->site_name;
    else
        $blog_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    ?>
    <div class="wp-email-content-wrap content">
        <div class="content-header">
            <h3 class="title-<?php echo esc_attr($approve_str); ?>"><?php echo sprintf(esc_html__('Your review is %s', 'wpbooking'), $approve_str); ?></h3>
            <p class="description"><?php echo sprintf(wp_kses(__('Hello <strong>%s</strong>, Your review is %s by %s administrator.<br> at %s', 'wpbooking'), array('br' => array(), 'strong' => array())), $comment_data->comment_author, $approve_str, $blog_name, date('Y/m/d H:i a')) ?></p>
        </div>
        <div class="content-center">
            <i class="icon">&ldquo;</i>
            <p class="comment"><?php echo do_shortcode($comment_data->comment_content); ?></p>
            <div class="review"><?php echo do_shortcode($review_html); ?></div>
        </div>
        <?php if($status == 'approved'){ ?>
        <div class="content-footer">
            <a class="btn btn-default" href="<?php echo esc_attr(get_comment_link($comment_data->comment_ID)); ?>"><?php echo esc_html__('Show comment', 'wpbooking'); ?></a>
            <span class="comment_link"><?php echo esc_html__('Can\'t see the button? Try this '); ?><a href="<?php echo esc_url(get_comment_link($comment_data->comment_ID)); ?>"><?php echo esc_html__('Link', 'wpbooking'); ?></a></span>
        </div>
        <?php } ?>
    </div>

<?php
}


