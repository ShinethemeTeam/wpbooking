<?php
$service=new WB_Service();
$service_type=$service->get_type();
$related = $service->get_related_query();
if(!$related or !$related->have_posts()) return FALSE;
?>
<div class="service-content-section">
	<h5 class="service-info-title"><?php echo esc_html__('Related ','wp-booking-management-system').$service_type.'s'; ?></h5>
	<div class="wpbooking-loop-wrap">
	<?php
	echo wpbooking_load_view('archive/loop',array('my_query'=>$related,'service_type'=>$service_type));
	 ?>
	</div>
</div>
<?php wp_reset_query();