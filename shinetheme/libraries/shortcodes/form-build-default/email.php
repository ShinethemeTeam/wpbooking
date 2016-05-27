<?php
if(function_exists( 'wpbooking_add_field_form_builder' )) {
    wpbooking_add_field_form_builder( array(
            "title"    => __( "Email" , 'wpbooking' ) ,
            "name"     => 'wpbooking_booking_email' ,
            "category" => __('Standard Fields','wpbooking') ,
            "options"  => array(
                array(
                    "type"             => "required" ,
                    "title"            => __( "Set as <strong>required</strong>" , 'wpbooking' ) ,
                    "desc"             => "" ,
                    'edit_field_class' => 'wpbooking-col-md-12' ,
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Title" , 'wpbooking' ) ,
                    "name"             => "title" ,
                    "desc"             => __( "Title" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Name" , 'wpbooking' ) ,
                    "name"             => "name" ,
                    "desc"             => __( "Name" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "ID" , 'wpbooking' ) ,
                    "name"             => "id" ,
                    "desc"             => __( "ID" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Class" , 'wpbooking' ) ,
                    "name"             => "class" ,
                    "desc"             => __( "Class" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Value" , 'wpbooking' ) ,
                    "name"             => "value" ,
                    "desc"             => __( "Value" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Placeholder" , 'wpbooking' ) ,
                    "name"             => "placeholder" ,
                    "desc"             => __( "Placeholder" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Size" , 'wpbooking' ) ,
                    "name"             => "size" ,
                    "desc"             => __( "Size" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Maxlength" , 'wpbooking' ) ,
                    "name"             => "maxlength" ,
                    "desc"             => __( "Maxlength" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value'            => ""
                )
            )
        )
    );
}
if(!function_exists( 'wpbooking_booking_email_func' )) {
    function wpbooking_booking_email_func( $attr , $content = false )
    {
        $data = wp_parse_args(
			$attr,
            array(
                'is_required' => 'off' ,
                'title'        => '' ,
                'name'        => '' ,
                'id'          => '' ,
                'class'       => '' ,
                'value'       => '' ,
                'placeholder' => '' ,
                'size'        => '' ,
                'maxlength'   => '' ,
            )   );
        extract( $data );
        $required = "";
        $rule = array();
        if($is_required == "on") {
            $required = "required";
            $rule []= "required";
        }
        if(!empty($maxlength)){
            $rule []= "max_length[100]";
        }
		$rule[]='valid_email';

        WPBooking_Admin_Form_Build::inst()->add_form_field( $name,array('data'=>$data,'rule'=>implode('|',$rule)));

        return '<input type="text" name="' . $name . '" id="' . $id . '" class="' . $class . '" value="' . $value . '" placeholder="' . $placeholder . '"  maxlength="' . $maxlength . '" size="' . $size . '"  ' . $required . ' />';
    }
}
add_shortcode( 'wpbooking_booking_email' , 'wpbooking_booking_email_func' );

