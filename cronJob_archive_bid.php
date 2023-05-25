<?php
define('WP_USE_THEMES', true);
require('/var/www/html/shopadoc/wp-load.php');
global $wpdb,$demo_listing,$today_date_time;
function CheckImage($image){
		global $wpdb;
		$results = $wpdb->get_results("SELECT Distinct(post_id) FROM wp_postmeta WHERE (meta_key =  '_thumbnail_id' and meta_value ='".$image."')  or (meta_key =  '_product_image_gallery' and meta_value like '%".$image."%') ");
		foreach ($results as $result) {
			//print_r($result);
			$status = get_post_status($result->post_id);
			if($status=='publish'){
				return 'in_use';
				break;
			}else{
			}
		}
		return '';
}
$args = array(
				'post__not_in' => array($demo_listing),
				'post_status'         => array('publish','pending'),
				'posts_per_page'      => -1,
				'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
				//'meta_query' => array(array('key' => '_auction_closed','operator' => 'EXISTS',)),
				//'auction_archive'     => TRUE,
				//'show_past_auctions'  => TRUE,
              );
		$query = new WP_Query($args );
		$posts = $query->posts;
foreach($posts as $post) {
   	$_auction_current_bid = get_post_meta($post->ID, '_auction_current_bid', true );
	$_auction_dates_to = get_post_meta($post->ID, '_auction_dates_to', true );
	if($_auction_current_bid && strtotime($today_date_time) > strtotime($_auction_dates_to)){
		$my_post = array('ID' =>$post->ID,'post_status'   => 'private',);
		wp_update_post( $my_post );
		//Delete Thumbnails
		/*
		$_thumbnail_id = get_post_meta($post->ID, '_thumbnail_id', true );
		$_product_image_gallery = get_post_meta($post->ID, '_product_image_gallery', true );
		$images = array();
		if($_product_image_gallery !=""){
			$images = explode(",",$_product_image_gallery);
		}
		array_push($images,$_thumbnail_id);
		foreach($images as $image){
				$image_status = CheckImage($image);
				if($image_status !='in_use'){
					//wp_delete_attachment($image,true);
				}
		}
		*/
	}
	
}
?>
