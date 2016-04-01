<?php
/*if(!class_exists('Traveler_widget_form_search')){
    class Traveler_widget_form_search extends WP_Widget{
        public function __construct() {
            $widget_ops = array('classname' => 'st_wd_text', 'description' => __( "Text sidebar with traveler header style and can be shortcodes in content ",ST_TEXTDOMAIN) );
            parent::__construct('st_wd_text', __('ST Text',ST_TEXTDOMAIN), $widget_ops);
        }

        public function widget($args, $instance) {
            extract(wp_parse_args($instance , array('title'=>'','service_type'=>'','content'=>"")));
            $title = apply_filters( 'widget_title', empty( $title ) ? '' : $title, $instance, $this->id_base );
            echo $title;
        }

        public function update( $new_instance, $old_instance ) {
            return wp_parse_args($new_instance,$old_instance);
        }
        public function form( $instance ) {
            $instance = wp_parse_args((array) $instance, array( 'title' => '','content'=> '','style'=>''));
            extract($instance);
            */?><!--
            <p><label for="<?php /*echo $this->get_field_id('title'); */?>"><?php /*_e('Title:'); */?> <input class="widefat" id="<?php /*echo $this->get_field_id('title'); */?>" name="<?php /*echo $this->get_field_name('title'); */?>" type="text" value="<?php /*echo esc_attr($title); */?>" /></label></p>
            <p>
                <label for="<?php /*echo $this->get_field_id('service_type'); */?>"><?php /*_e('Service Type:'); */?>
                    <select name="<?php /*echo $this->get_field_name('service_type'); */?>" id="<?php /*echo $this->get_field_id('service_type'); */?>">
                        <?php
/*                        $data = Traveler_Service::inst()->get_service_types();
                        if(!empty($data)){
                            foreach($data as $k=>$v){
                                
                            }
                        }
                        */?>
                        <option <?php /*if (esc_attr($style) =='') echo "selected"; */?> value=""><?php /*echo __("Default" ,ST_TEXTDOMAIN) ;*/?></option>
                    </select>
                </label>
            </p>
            <p><label for="<?php /*echo $this->get_field_id('content'); */?>"><?php /*_e('Content:'); */?> <textarea class="widefat" id="<?php /*echo $this->get_field_id('content'); */?>" name="<?php /*echo $this->get_field_name('content'); */?>" type="text"><?php /*echo esc_attr($content); */?></textarea></label></p>
        --><?php
/*        }
    }

    function Traveler_widget_form_search() {
        register_widget( 'st_wd_text' );
    }

    add_action( 'widgets_init', 'st_wd_text' );
}*/
