<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/4/2016
 * Time: 3:23 PM
 */
if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}

?>

<div  itemscope itemtype="http://schema.org/Product" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php echo traveler_load_view('single/order-form')?>
	<meta itemprop="url" content="<?php the_permalink(); ?>" />
</div>

