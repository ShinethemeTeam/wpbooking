<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/22/2016
 * Time: 3:32 PM
 */
global $wp_query;
?>
<div class="wpbooking-myaccount-wrap">
	<?php
	if(get_query_var('tab') == "profile" and $user_id = get_query_var('profile')){
		if(!$tab=get_query_var('tab')) $tab='dashboard';
		echo "<div class='wpbooking-account-tab ".$tab."'>";
		echo wpbooking_load_view('account/tabs/'.$tab);
		echo "</div>";
	}else if(is_user_logged_in()){
		echo wpbooking_load_view('account/nav');
		if(!$tab=get_query_var('tab')) $tab='dashboard';
		echo "<div class='wpbooking-account-tab ".$tab."'>";
		echo wpbooking_load_view('account/tabs/'.$tab);
		echo "</div>";
	}elseif(isset($wp_query->query_vars['lost-password'])){
        echo '<div class="wb-account-reset-pass">';
		echo wpbooking_load_view('account/lost-password');
        echo '</div';
    }elseif(isset($wp_query->query_vars['reset-password'])){
		echo '<div class="wb-account-reset-pass">';
		echo wpbooking_load_view('account/reset-password');
		echo '</div';
	}else{
		?>
		<div class="row">
			<div class="<?php echo (wpbooking_is_any_register()?'col-sm-6':'col-sm-12')?>"><?php echo wpbooking_load_view('account/login') ?></div>
            <?php if(wpbooking_is_any_register()){ ?>
			<div class="col-sm-6"><?php echo wpbooking_load_view('account/register') ?></div>
            <?php } ?>
		</div>
		<?php
	}
	?>
</div>
