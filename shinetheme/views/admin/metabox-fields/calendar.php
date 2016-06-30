<?php
/**
* @since 1.0.0
**/
$service_type = isset( $data['service_type'] ) ? esc_html( $data['service_type'] ) : 'room';

if( empty( $service_type ) ) $service_type = 'room';

$file = 'metabox-fields/calendar/calendar-' . $service_type;

echo wpbooking_admin_load_view( $file, array( 'data' => $data,'post_id'=>$post_id) );