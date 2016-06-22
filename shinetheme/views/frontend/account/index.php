<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/22/2016
 * Time: 3:32 PM
 */
?>
<div class="wpbooking-myaccount-wrap">
	<?php
	if(is_user_logged_in()){

	}else{
		?>
		<div class="row">
			<div class="col-sm-6"><?php echo wpbooking_load_view('account/login') ?></div>
			<div class="col-sm-6"><?php echo wpbooking_load_view('account/register') ?></div>
		</div>
		<?php
	}
	?>
</div>
