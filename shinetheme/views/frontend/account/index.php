<?php
global $wp_query;
$other_filter_tab_account = apply_filters('wpbbooking_other_tab_content_account',array());
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
	}elseif(isset($wp_query->query_vars['register'])){
        echo '<div class="row"><div class="col-sm-12">';
        echo wpbooking_load_view('account/register');
        echo '</div></div>';
    }else{
		$show_tab_login  = true;
		if(!empty($other_filter_tab_account)){
			foreach($other_filter_tab_account as $k=>$v){
				if(isset($wp_query->query_vars[$v])){
					echo '<div class="row"><div class="col-sm-12">';
					echo wpbooking_load_view('account/'.$v);
					echo '</div></div>';
					$show_tab_login = false;
				}
			}
		}
		if($show_tab_login == true) {
			?>
			<div class="row">
				<div class="col-sm-12"><?php echo wpbooking_load_view( 'account/login' ) ?></div>
			</div>
			<?php
		}
	}
	?>
</div>
