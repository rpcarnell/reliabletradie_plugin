<?php
/**
 * Pagination - Show numbered pagination for catalog pages.
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wp_query;
$max_num_pages = $wp_query->max_num_pages;
//echo "bere ".$wp_query->max_num_pages;
//$max_num_pages = 2;
if ( $max_num_pages == 0 )
	return;
//print_r($_POST);
?>
<div id='pageResponse'></div>
<div style='display: none'>
    <div id='src_rtdata'><?php echo $_POST['rtdata'];?></div>
    <div id='src_rtcty'><?php echo $_POST['city'];?></div>
    <div id='src_postcode'><?php echo $_POST['postcode'];?></div>
</div>
<nav class="reliable-pagination" data-perPage='<?php echo PER_RESULT; ?>' data-pageNum='<?php echo PER_RESULT; ?>'>Show More</nav>