<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/22/2016
 * Time: 11:05 AM
 */

?>
<div class="price-wrapper" itemprop="offers" itemscope itemtype="http://schema.org/Offer">

	<p class="price"><?php echo traveler_service_price_html() ?></p>

	<meta itemprop="price" content="<?php echo traveler_service_price() ?>" />
	<meta itemprop="priceCurrency" content="<?php echo get_post_meta(get_the_ID(),'currency',true) ?>" />
</div>
