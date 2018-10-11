<?php
 if(!class_exists('wpbooking_shortcode_controller')){
     class wpbooking_shortcode_controller{

         static $_inst;
         function __construct() {
             add_action('init',[$this,'register_shortcode_list_service']);
             add_action('init',[$this,'register_shortcode_form_search']);
             add_action('init',[$this,'register_shortcode_list_room']);
             add_action('init',[$this,'register_shortcode_tab_search']);
             add_action('init',[$this,'register_shortcode_tour_type']);
             add_action('init',[$this,'register_shortcode_room_type']);
         }

         function register_shortcode_list_service(){
             add_shortcode('wpbooking_list_services',[$this,'_render_list_service_shortcode']);
         }

         function _render_list_service_shortcode($atts){
             $atts = shortcode_atts(
                 array(
                     'service_type' => '',
                     'number_per_page' => '6',
                     'order'       => 'DESC',
                     'orderby'     => 'date',
                     'layout'      => 'grid',
                     'post_per_row'      => '2',
                     'location_id' => '',
                     'service_id' => '',
                 ),$atts
             );
             return wpbooking_load_view('shortcode/services/services',array( 'atts' => $atts ));
         }

         function register_shortcode_form_search(){
             add_shortcode('wpbooking_search_form',[$this,'_render_search_form_shortcode']);
         }
         function _render_search_form_shortcode($atts){
             $atts = shortcode_atts(array(
                 'id' => '',
                 'services_in' => ''
             ),$atts);
             return wpbooking_load_view('shortcode/form-search/form-search',array(
                 'atts' => $atts,
             ));
         }

         function register_shortcode_list_room(){
             add_shortcode('wpbooking_list_rooms',[$this,'_render_list_room_shortcode']);
         }
         function _render_list_room_shortcode($atts){
             $atts = shortcode_atts(array(
                 'layout'  => 'grid',
                 'orderby' => 'date',
                 'order'   => 'desc',
                 'number_per_page' => '6',
                 'post_per_row' => '2',
                 'hotel_id' => '',
             ),$atts);
             return wpbooking_load_view('shortcode/rooms/room',array(
                'atts' => $atts
             ));
         }

         /* tab search */
         function register_shortcode_tab_search(){
             add_shortcode('wpbooking_search_service',[$this,'_render_tab_form_search_service_shortcode']);
         }
         function _render_tab_form_search_service_shortcode($atts){

             $atts = shortcode_atts(array(
                 'id'      => '',
             ),$atts);

             return wpbooking_load_view('shortcode/tabs/tab',array(
                 'atts' => $atts
             ));
         }


         function register_shortcode_tour_type(){
             add_shortcode('wpbooking_tour_type',[$this,'_render_tour_type_shortcode']);
         }
         function _render_tour_type_shortcode($atts){
             $atts = shortcode_atts(array(
                 'tag_id' => '',
                 'col' => '',
             ),$atts);
             return wpbooking_load_view('shortcode/tour-type/tour-type',array(
                 'atts' => $atts,
             ));
         }

         function register_shortcode_room_type(){
             add_shortcode('wpbooking_room_type',[$this,'_render_room_type_shortcode']);
         }
         function _render_room_type_shortcode($atts){
             $atts = shortcode_atts(array(
                 'tag_id' => '',
                 'col' => '',
             ),$atts);
             return wpbooking_load_view('shortcode/room-type/room-type',array(
                 'atts' => $atts,
             ));
         }


         static function inst(){
             if(!self::$_inst){
                 self::$_inst = new self();
             }
             return self::$_inst;
         }
     }
     wpbooking_shortcode_controller::inst();
 }