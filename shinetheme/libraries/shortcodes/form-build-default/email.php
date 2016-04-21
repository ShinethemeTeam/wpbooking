<?php
if(function_exists( 'traveler_add_field_form_builder' )) {
    traveler_add_field_form_builder( array(
            "title"    => __( "Email" , 'traveler-booking' ) ,
            "name"     => 'traveler_booking_email' ,
            "category" => __('Standard Fields','traveler-booking') ,
            "options"  => array(
                array(
                    "type"             => "required" ,
                    "title"            => __( "Set as <strong>required</strong>" , 'traveler-booking' ) ,
                    "desc"             => "" ,
                    'edit_field_class' => 'traveler-col-md-12' ,
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Title" , 'traveler-booking' ) ,
                    "name"             => "title" ,
                    "desc"             => __( "Title" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Name" , 'traveler-booking' ) ,
                    "name"             => "name" ,
                    "desc"             => __( "Name" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "ID" , 'traveler-booking' ) ,
                    "name"             => "id" ,
                    "desc"             => __( "ID" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Class" , 'traveler-booking' ) ,
                    "name"             => "class" ,
                    "desc"             => __( "Class" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Value" , 'traveler-booking' ) ,
                    "name"             => "value" ,
                    "desc"             => __( "Value" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Placeholder" , 'traveler-booking' ) ,
                    "name"             => "placeholder" ,
                    "desc"             => __( "Placeholder" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Size" , 'traveler-booking' ) ,
                    "name"             => "size" ,
                    "desc"             => __( "Size" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Maxlength" , 'traveler-booking' ) ,
                    "name"             => "maxlength" ,
                    "desc"             => __( "Maxlength" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-6' ,
                    'value'            => ""
                )
            )
        )
    );
}
if(!function_exists( 'traveler_booking_email_func' )) {
    function traveler_booking_email_func( $attr , $content = false )
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
        if(!empty(!$maxlength)){
            $rule []= "max_length[100]";
        }
		$rule[]='valid_email';

        Traveler_Admin_Form_Build::inst()->add_form_field($title , $name,array('data'=>$data,'rule'=>implode('|',$rule)));



        return '<input type="text" name="' . $name . '" id="' . $id . '" class="' . $class . '" value="' . $value . '" placeholder="' . $placeholder . '"  maxlength="' . $maxlength . '" size="' . $size . '"  ' . $required . ' />';
    }
}
add_shortcode( 'traveler_booking_email' , 'traveler_booking_email_func' );

