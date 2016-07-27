<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/27/2016
 * Time: 4:29 PM
 */
if(!$my_query->have_posts()) return;
?>

<div class="wpbooking-loop-header">
	<div class="row">
		<div class="col-sm-7">
			<h2 class="post-found-count"><?php printf(esc_html__('Found %d room(s)','wpbooking'),$my_query->found_posts) ?></h2>
			<p class="post-query-desc">
				<?php
					echo wpbooking_post_query_desc();
				?>
			</p>
		</div>
	</div>
</div>
