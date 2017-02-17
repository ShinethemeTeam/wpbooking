<?php
if(!function_exists('wpbooking_form_search_func'))
{
    function wpbooking_form_search_func($attr=array(),$content=FALSE)
    {
        $data = wp_parse_args($attr,array( 'sidebar_id' => ''));
        extract($data);
        $html = '';
        if(!empty($sidebar_id)){
            if ( is_active_sidebar( $sidebar_id ) ) :
                $html .= '<div id="widget-search-form" class="widget-area">';
                ob_start();
                    dynamic_sidebar( $sidebar_id );
                    $html .= ob_get_contents();
                ob_end_clean();
                $html .= ' </div>';
            endif;
        }
        return $html;
    }
    add_shortcode('wpbooking_form_search','wpbooking_form_search_func');
}