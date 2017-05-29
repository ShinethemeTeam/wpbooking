<?php
if(function_exists( 'wpbooking_add_field_form_builder' )) {
    wpbooking_add_field_form_builder( array(
            "title"    => esc_html__( "Submit Button" , 'wpbooking' ) ,
            "name"     => 'wpbooking_form_submit_button' ,
            "category" => 'Standard Fields' ,
            "options"  => array(
                array(
                    "type"             => "text" ,
                    "title"            => esc_html__( "Label" , 'wpbooking' ) ,
                    "name"             => "label" ,
                    "desc"             => esc_html__( "Label" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-12' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => esc_html__( "Name" , 'wpbooking' ) ,
                    "name"             => "name" ,
                    "desc"             => esc_html__( "Name" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-4' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => esc_html__( "ID" , 'wpbooking' ) ,
                    "name"             => "id" ,
                    "desc"             => esc_html__( "ID" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-4' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => esc_html__( "Class" , 'wpbooking' ) ,
                    "name"             => "class" ,
                    "desc"             => esc_html__( "Class" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-4' ,
                    'value'            => ""
                ) ,
            )
        )
    );
}
if(!function_exists( 'wpbooking_sc_booking_submit_buttom' )) {
    function wpbooking_sc_booking_submit_buttom( $attr , $content = false )
    {
        $data = wp_parse_args(
			$attr,
            array(
                'label' => '' ,
                'name'  => '' ,
                'id'    => '' ,
                'class' => ' ' ,
            )  );
        extract( $data );
		$class.=' submit-button';
        $checkout=WPBooking_Order::inst()->get_checkout_url();
        return '<div class="wb-field clear"><button type="submit" name="' . $name . '" id="' . $id . '" class="' . $class . '" >' . $label . '</button><a class="wb-btn-to-checkout" href="'.$checkout.'">'.esc_html__('Checkout','wpbooking').'</a></div>';
    }
}
add_shortcode( 'wpbooking_form_submit_button' , 'wpbooking_sc_booking_submit_buttom' );