<?php
$tabs=WPBooking_User::inst()->get_tabs();
?>
<!-- Nav tabs -->
<ul class="wb-account-nav" role="tablist">
	<?php
	if(!empty($tabs)){
		$i=1;
		foreach($tabs as $k=>$tab){
			$class=FALSE;

			if($current_tab=get_query_var('tab') and $current_tab==$k) $class='active';

			if(!get_query_var('tab') and $i==1) $class='active';

			$url=get_permalink(wpbooking_get_option('myaccount-page')).'tab/'.$k;

			switch($k){
				case "logout":
                    $url = wp_logout_url();
					break;
			}

			$children=FALSE;
			if(!empty($tab['children']) and is_array($tab['children'])){
				$children.='<ul class="children">';
					foreach($tab['children'] as $child_key=>$child){
						$child_class=FALSE;
						if($current_tab=get_query_var('tab') and $current_tab==$child_key){
							$child_class='active';
							$class='active'; /// Parent Active
						}
						$child_url=get_permalink(wpbooking_get_option('myaccount-page')).'tab/'.$child_key;
						$children.=sprintf('<li role="presentation" class="%s"><a href="%s">%s</a></li>',$child_class,$child_url,$child['label']);
					}
				$children.='</ul>';
			}

			if($children){
				$tab['label'].=' <i class="fa fa-caret-down"></i>';
			}
			printf('<li role="presentation" class="%s"><a href="%s">%s</a>%s</li>',$class,$url,$tab['label'],$children);
			$i++;
		}
	}
	?>

</ul>
