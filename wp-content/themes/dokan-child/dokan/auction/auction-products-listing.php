<style type="text/css">
ul.subsubsub {
	font-size:15px;
	margin-top:40px;
}
</style>
<?php ?>
<link rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/dokan-child/nanoscroller.css">
<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/dokan-child/jquery.nanoscroller.js"></script>
<style type="text/css">
.dokan-alert{height:auto !important;}
/*@media only screen and (min-width: 800px) {*/
.nano {
	width: 100%;
	height: 167px;
}
.nano .nano-content {
	padding: 10px;
}
.nano .nano-pane {
	background: #f0f0f0;
}
/*.my-listing-custom::-webkit-scrollbar {
 display: none;
}*/
.nano > .nano-pane > .nano-slider img{
	/*display:none;*/
}
.nano > .nano-pane > .nano-slider{
	border:solid 2px #444;
}
/* Hide scrollbar for IE, Edge add Firefox */
/*.my-listing-custom {
  -ms-overflow-style: none;
  scrollbar-width: none; 
}*/
/*@media only screen and (max-width: 448px) {
.nano > .nano-pane{
	background:transparent;
}
}*/
/*}*/
@media only screen and (max-width: 800px) {
.nano > .nano-pane {
	width:5px !important;
}
.nano > .nano-pane > .nano-slider img {
	display:none;
}
.nano > .nano-pane > .nano-slider {
	background-color: rgba(0, 0, 0, .5) !important;
	height:68px !important;
	border-radius:8px !important;
}
.nano .nano-pane {
	background: transparent;
}
/*.my-listing-custom::-webkit-scrollbar {
    -webkit-appearance: none;
}

.my-listing-custom::-webkit-scrollbar:vertical {
    width: 11px;
}

.my-listing-custom::-webkit-scrollbar:horizontal {
    height: 11px;
}

.my-listing-custom::-webkit-scrollbar-thumb {
    border-radius: 8px;
    border: 2px solid white; 
    background-color: rgba(0, 0, 0, .5);
}*/
/*.my-listing-custom{
	padding-right:0 !important;
}*/

}
</style>
<script type="text/javascript">
jQuery(function(){
	var windowsize = jQuery(window).width();
	//if(windowsize > 800){
		var heightThead = parseInt(jQuery(".my-listing-custom table thead").height());
		//console.log(heightThead);
		jQuery('.nano').nanoScroller({
			preventPageScrolling: true,
			sliderMaxHeight: 25,
			alwaysVisible: true,
			contentClass: 'nano-content',
			scrollTop: 10
		});
	//}
});
</script>
<?php 
/*$user = wp_get_current_user();
if($user->roles[0]=='seller'){
	wp_redirect( home_url( '/shopadoc-auction-activity/' ) );
	exit();
}*/
?>
<?php global $wpdb,$post,$today_date_time,$today_date_time_seconds;

			$flash_cycle_end_this = date('Y-m-d', strtotime( 'friday this week' ) )." 10:30";
			//$flash_cycle_end_this = date('Y-m-d', strtotime( 'tuesday this week' ) )." 06:50";
			/*$expire_relist='yes';
			if(strtotime($today_date_time) >= strtotime($flash_cycle_end_this)){
				$newtimestamp = strtotime($flash_cycle_end_this.' + 3 minute');
				$to_date = date('Y-m-d H:i:s', $newtimestamp);
				if(strtotime($today_date_time) >= strtotime($to_date)){
					$expire_relist='yes';
				}else{
					$expire_relist='no';
				}
			}*/
			$expire_relist='yes';
			?>
<div class="dokan-dashboard-wrap">
  <?php
	makeFlashLive();
    do_action( 'dokan_dashboard_content_before' );
    do_action( 'dokan_auction_product_listing_content_before' );
    ?>
  <div class="dokan-dashboard-content dokan-product-listing">
    <?php

            /**
             *  dokan_auction_product_listing_inside_before hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_auction_product_listing_inside_before' );
        ?>
    <?php do_action( 'dokan_before_listing_auction_product' ); ?>
    <article class="dokan-product-listing-area">
      <div class="product-listing-top dokan-clearfix">
        <?php  //dokan_auction_product_listing_status_filter(); ?>
        <?php 
					/*
						 global $post;
                        $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
                        $post_statuses = array( 'publish', 'draft', 'pending');
						$args1 = array(
								'post_status'         => $post_statuses,
								'ignore_sticky_posts' => 1,
								'orderby'             => 'post_date',
								'author'              => dokan_get_current_user_id(),
								'order'               => 'DESC',
								'posts_per_page'      => -1,
								'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
								'meta_query' => array(array('key' => '_auction_closed','operator' => 'EXISTS',)),
								'auction_archive'     => TRUE,
								'show_past_auctions'  => TRUE,
								'paged'               => $pagenum
							);
						 $product_query = new WP_Query( $args1 );
						$count1 = $product_query->found_posts;	
						
						$args2 = array(
								'post_status'         => $post_statuses,
								'ignore_sticky_posts' => 1,
								'orderby'             => 'post_date',
								'author'              => dokan_get_current_user_id(),
								'order'               => 'DESC',
								'posts_per_page'      => -1,
								'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
								'meta_query' => array(array('key' => '_auction_closed','compare' => 'NOT EXISTS',)),
								'auction_archive'     => TRUE,
								'show_past_auctions'  => TRUE,
								'paged'               => $pagenum
							);
						
						$product_query = new WP_Query( $args2 );
						$count2 = $product_query->found_posts;
                       
					?>
					<ul class="dokan-listing-filter dokan-left subsubsub">
                      <li <?php if(isset($_GET['auction_status']) && $_GET['auction_status']=='past'){}else{ echo 'class="active"';}?>> <a href="<?php echo dokan_get_navigation_url('auction');?>">Current (<?php echo $count2;?>)</a> </li>
                      <li <?php if(isset($_GET['auction_status']) && $_GET['auction_status']=='past'){ echo 'class="active"';}else{ }?>> <a href="<?php echo add_query_arg( array('auction_status' => 'past' ), dokan_get_navigation_url('auction') );?>">Past (<?php echo $count1;?>)</a> </li>
                    </ul>
<?php */?>
        <?php if ( current_user_can( 'dokan_add_auction_product' ) ): ?>
        <span class="dokan-add-product-link"> <a href="<?php echo dokan_get_navigation_url( 'new-auction-product' ); ?>" class="dokan-btn dokan-btn-theme dokan-right"><i class="fa fa-briefcase">&nbsp;</i>
        <?php _e( 'Add New Auction Product', 'dokan-auction' ); ?>
        </a> </span>
        <?php endif ?>
        <?php if($expire_relist =='no'):?>
        <span class="dokan-add-product-link"> 
        <a href="javascript:SubmitRelist();" class="dokan-btn dokan-btn-theme dokan-right"><i class="fa fa-briefcase">&nbsp;</i>Relist Auction(s)</a> 
        </span>
        <?php endif?>
      </div>
      <?php dokan_product_dashboard_errors(); ?>
      <?php if (isset($_GET['action']) && $_GET['action'] =='relist_error') { ?>
      <div class="dokan-alert dokan-alert-danger" style="height:auto;"> <a class="dokan-close" data-dismiss="alert">&times;</a> <strong>
        <?php _e( 'Error!', 'dokan-auction' ); ?>
        </strong> You can not relist this auction.<br>
      </div>
      <?php }elseif (isset($_GET['action']) && $_GET['action'] =='update_success' && 1==2) { ?>
      <div class="dokan-alert dokan-alert-success"> <a class="dokan-close" data-dismiss="alert">&times;</a> <strong>
        <?php _e( 'Success!', 'dokan-auction' ); ?>
        </strong> Ask fee has been successfully updated.<br>
      </div>
      <?php }elseif (isset($_GET['action']) && $_GET['action'] =='update_error') {?>
      <div class="dokan-alert dokan-alert-danger"> <a class="dokan-close" data-dismiss="alert">&times;</a> <strong>
        <?php _e( 'Error!', 'dokan-auction' ); ?>
        </strong> Ask fee should be greater than or equal to current price.<br>
      </div>
      <?php }?>
      <style type="text/css">
			.table > thead > tr > th {
				vertical-align: bottom;
				border-bottom: 2px solid #dddddd;
				line-height: 27px;
				font-size:17px;
			}
			/*.my-listing-custom{
				float: left;
				width: 100%;
				height: 167px;
				overflow-y: scroll;
			}*/
			.dokan-dashboard .dokan-dash-sidebar article, .dokan-dashboard .dokan-dashboard-content article{
				overflow:hidden !important;
			}
			table tbody:nth-of-type(1) tr:nth-of-type(1) td {
  border-top: none !important;
}

table thead th {
  border-top: none !important;
  border-bottom: none !important;
  /*box-shadow: inset 0 2px 0 #dddddd, inset 0 -2px 0 #dddddd;*/
  box-shadow:  inset 0 -2px 0 #dddddd;
  padding: 2px 0;
}
/* and one small fix for weird FF behavior, described in https://stackoverflow.com/questions/7517127/ */
table thead th {
  background-clip: padding-box
}
#posts_table{position:relative;} 
.table > thead > tr > th {
    position: sticky;
	position: -webkit-sticky;
    top:0;
    background: #F2F2F2;
}
			
			@media (max-width: 448px) {
				#main .container.content-wrap{
					padding-left:5px;
					padding-right:5px;
				}
				.image_col{display:none !important;}
				.dokan-product-listing .table > thead > tr > td, .dokan-product-listing .table > tbody > tr > td, .dokan-product-listing .table > tfoot > tr > td{
					padding:8px !important;
				}
				#content{
					margin-top:7px;
				}
			}
			</style>
            
      <div class="nano">
        <!--<div class="my-listing-custom nano-content table-wrap" style="padding:0;">-->
        <div class="nano-content table-wrap" style="padding:0;">
        <form id="product-filter" method="POST" class="my-listing-custom dokan-form-inline">
          <table id="posts_table" class="table table-striped product-listing-table">
            <thead>
              <tr>
              	<?php if($expire_relist =='no'):?>
                <th id="cb" class="manage-column column-cb check-column"> <label for="cb-select-all"></label>
                  <input id="cb-select-all" class="dokan-checkbox" type="checkbox">
                </th>
                <?php endif?>
                <th class="image_col"><?php _e( 'IMAGE', 'dokan-auction' ); ?></th>
                <th><?php _e( 'SERVICE', 'dokan-auction' ); ?></th>
                <th class="center ask_fee"><?php _e( 'ASK FEE', 'dokan-auction' ); ?></th>
                <th class="center"><?php _e( 'STATUS', 'dokan-auction' ); ?></th>
                <th class="center"><?php _e( 'START DATE', 'dokan-auction' ); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php
                        global $post;
                        $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
						$auctionids = array();
                        $post_statuses = array( 'publish', 'draft', 'pending');
						$post_statuses = array( 'publish');
						if(isset($_GET['auction_status']) && $_GET['auction_status']=='past'){
							$args = array(
								'post_status'         => $post_statuses,
								'ignore_sticky_posts' => 1,
								'orderby'             => 'post_date',
								'author'              => dokan_get_current_user_id(),
								'order'               => 'DESC',
								'posts_per_page'      => -1,
								'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
								'meta_query' => array(array('key' => '_auction_closed','operator' => 'EXISTS',)),
								'auction_archive'     => TRUE,
								'show_past_auctions'  => TRUE,
								'paged'               => $pagenum
							);
	
						}else{
							$args = array(
								'post_status'         => $post_statuses,
								'ignore_sticky_posts' => 1,
								//'orderby'             => 'post_date',
								'meta_key' => '_auction_dates_from',
                   			 	'orderby' => 'meta_value',
								'order'               => 'asc',
								'author'              => dokan_get_current_user_id(),
								'posts_per_page'      => -1,
								'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
								//'meta_query' => array(array('key' => '_auction_closed','compare' => 'NOT EXISTS',)),
								'auction_archive'     => TRUE,
								'show_past_auctions'  => TRUE,
								'paged'               => $pagenum
							);
						}
						

                        if ( isset( $_GET['post_status'] ) && in_array( $_GET['post_status'], $post_statuses ) ) {
                            $args['post_status'] = $_GET['post_status'];
                        }

                        // $original_post = $post;
                        $product_query = new WP_Query( $args );
						//_auction_dates_from
						$count = $product_query->found_posts;
                        if ( $product_query->have_posts() ) {
                            while ($product_query->have_posts()) {
                                $product_query->the_post();
								array_push($auctionids,$post->ID);
                                $tr_class = ($post->post_status == 'pending' ) ? ' class="danger"' : '';
                                $product = dokan_wc_get_product( $post->ID );
                                $edit_url = add_query_arg( array('product_id' => $post->ID, 'action' => 'edit' ), dokan_get_navigation_url('auction') );
								$Relist_url = add_query_arg( array('product_id' => $post->ID, 'action' => 'relist' ), dokan_get_navigation_url('auction') );
								$delete_url = add_query_arg( array('product_id' => base64_encode($post->ID), 'action' => 'delete_list' ), dokan_get_navigation_url('auction') );
								$_auction_start_price = get_post_meta($post->ID, '_auction_start_price',TRUE);

								$_auction_dates_from =  get_post_meta($post->ID, '_auction_dates_from_org', true );
								
								$product_cats_ids = wc_get_product_term_ids($post->ID, 'product_cat' );
								$sub_title = '';
								if(in_array(119,$product_cats_ids)){
									$sub_title = '&nbsp;-&nbsp;<i>locators & retrofit service only</i>';
								}
								if(in_array(76,$product_cats_ids)){
									$sub_title = '&nbsp;-&nbsp;<i>abutments & denture only</i>';
								}
								if(in_array(77,$product_cats_ids)){
									$sub_title = '&nbsp;-&nbsp;<i>abutments & dentures only</i>';
								}
								$bid_count = $wpdb->get_var($wpdb->prepare("SELECT count(id) as count FROM wp_simple_auction_log WHERE auction_id = %d LIMIT 1",$post->ID));
								$_auction_dates_to = get_post_meta($post->ID, '_auction_dates_to', true );
								$_flash_cycle_start = get_post_meta( $post->ID, '_flash_cycle_start' , TRUE);
								$_flash_cycle_end = get_post_meta( $post->ID, '_flash_cycle_end' , TRUE);
						?>
              <tr<?php echo $tr_class; ?>>
              <?php if($expire_relist =='no'){?>
				  <?php if(strtotime($_auction_dates_to) < strtotime($today_date_time_seconds) && strtotime($today_date_time_seconds) > strtotime($_flash_cycle_end) && ($bid_count == '' || $bid_count == 0)){?>
                  	<?php
                    	$newtimestamp = strtotime($_flash_cycle_end.' + 3 minute');
						$to_date = date('Y-m-d H:i:s', $newtimestamp);
						if(strtotime($today_date_time_seconds) > strtotime($to_date)){?>
							 <th class="dokan-product-select check-column"> <label for="cb-select-<?php echo esc_attr( $post->ID ); ?>"></label>
                         &nbsp;
                         </th>
						<?php }else{?>
                        <th class="dokan-product-select check-column"> <label for="cb-select-<?php echo esc_attr( $post->ID ); ?>"></label>
                          <input class="cb-select-items dokan-checkbox" type="checkbox" data-product-name="<?php echo esc_attr( $product->get_title() ); ?>" name="bulk_products[]" value="<?php echo esc_attr( $post->ID ); ?>">
                        </th>
                    <?php }?>
                    <?php }else{ ?>
                    				<th class="dokan-product-select check-column"> <label for="cb-select-<?php echo esc_attr( $post->ID ); ?>"></label> &nbsp;</th>
                    <?php }?>
                <?php } ?>
                <td class="image_col"><?php if ( current_user_can( 'dokan_edit_auction_product' ) && 1==2): ?>
                  <a href="<?php echo $edit_url ?>"><?php echo $product->get_image(); ?></a>
                  <?php else: ?>
                  <a href="#"><?php echo $product->get_image(); ?></a>
                  <?php endif ?></td>
                <td width="30%" class="service_title"><?php if ( current_user_can( 'dokan_edit_auction_product' ) ): ?>
                  <p><a href="<?php echo get_permalink( $product->get_id() ); ?>"><?php echo str_replace("*","",$product->get_title()); ?><?php echo $sub_title;?></a></p>
                  <?php else: ?>
                  <p><a href=""><?php echo str_replace("*","",$product->get_title()); ?><?php echo $sub_title;?></a></p>
                  <?php endif ?>
                  <div class="row-actions">
                    <?php 
												
												$bid_count = $wpdb->get_var($wpdb->prepare("SELECT count(id) as count FROM wp_simple_auction_log WHERE auction_id = %d LIMIT 1",$post->ID));
											?>
                    <?php if(strtotime($_auction_dates_to) < strtotime($today_date_time) && strtotime($today_date_time) > strtotime($_flash_cycle_end) && $bid_count == 0 && 1==2):?>
                    <?php 
												//code to make auction pending
												$postData = array('ID' =>$post->ID, 'post_status' =>'pending');
												wp_update_post( $postData );
											?>
                    <span class="relist"><a href="<?php echo $Relist_url; ?>">
                    <?php _e( 'Relist', 'dokan-auction' ); ?>
                    </a> | </span>
                    <?php endif ?>
                    <?php if ( current_user_can( 'dokan_edit_auction_product' ) && 1==2): ?>
                    <span class="edit"><a href="<?php echo $edit_url; ?>">
                    <?php _e( 'Edit', 'dokan-auction' ); ?>
                    </a> | </span>
                    <?php endif ?>
                    <?php if ( current_user_can( 'dokan_delete_auction_product' ) && 1==2): ?>
                    <span class="delete"><a onclick="return confirm('Are you sure?');" href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'dokan-delete-auction-product', 'product_id' => $post->ID ), dokan_get_navigation_url('auction') ), 'dokan-delete-auction-product' ); ?>">
                    <?php _e( 'Delete Permanently', 'dokan-auction' ); ?>
                    </a> | </span>
                    <?php endif ?>
                    
                    <!--<span class="view"><a href="<?php echo get_permalink( $product->get_id() ); ?>" rel="permalink"><?php _e( 'View', 'dokan-auction' ); ?></a></span>--> 
                  </div></td>
                <td class="post-status center"><label class="dokan-label">
                    <?php
                                        if ( $product->get_price_html() ) {
                                            //echo str_replace("Current bid:","",str_replace("Ask Fee:","",$product->get_price_html()));
											echo str_replace(".00","",wc_price(abs($_auction_start_price)));
											//echo wc_price($_auction_start_price);
                                        } else {
                                            echo '<span class="na">&ndash;</span>';
                                        }
                                        ?>
                  </label></td>
                <?php 
										$_auction_current_bid = get_post_meta($product->get_id(), '_auction_current_bid', true );
										$_auction_closed = get_post_meta($product->get_id(), '_auction_closed', true );
										$_auction_dates_extend = get_post_meta($product->get_id(), '_auction_dates_extend', true );
										$_auction_extend_counter = get_post_meta($product->get_id(), '_auction_extend_counter', true );
										$_flash_status = get_post_meta($product->get_id(), '_flash_status', true );
										$customer_winner = get_post_meta($product->get_id(),'_auction_current_bider', true);
										//if(!$_auction_current_bid && $_auction_closed==1){
										if($product->is_closed() === TRUE){	
										global $today_date_time;
										$_flash_cycle_start = get_post_meta( $product->get_id() , '_flash_cycle_start' , TRUE);
										$_flash_cycle_end = get_post_meta( $product->get_id() , '_flash_cycle_end' , TRUE);
											if(strtotime($today_date_time) < strtotime($_flash_cycle_start) && strtotime($_flash_cycle_end) > strtotime($today_date_time)){
												if(!$_auction_current_bid){
													$status = '<span>countdown to Flash Bid Cycle<span class="TM_flash">®</span></span>';
													$class = " ended";
												}else{
													if($customer_winner !=""){
														$status = '<span class="red">✓ Email (Spam)</span>';
													}else{
														$status = '<span>ended</span>';
													}
													$class = " ended";
												}
											}elseif(strtotime($today_date_time) >= strtotime($_flash_cycle_start) && strtotime($_flash_cycle_end) > strtotime($today_date_time)){
												$status = '<span style="color:red;">Flash Bid Cycle<span class="TM_flash">®</span> live</span>';
												$class = " live";
											}else{
												if($customer_winner !=""){
													$status = '<span class="red">✓ Email (Spam)</span>';
												}else{
													$status = '<span>ended</span>';
												}
												$class = " ended";
											}
											/*$status = '<span>Ended</span>';
												$class = " ended";*/
										}else{
											if(($product->is_closed() === FALSE ) and ($product->is_started() === FALSE )){
												if($post->post_status=='pending'){
													$status = '<span>countdown to auction</span>';
													$class = " upcoming-pending";
												}else{
													$status = '<span>countdown to auction</span>';
													$class = " upcoming";
												}
											}else{
												if($post->post_status=='pending'){
													$status = '<span>Live: Pending Review</span>';
													$class = " live";
												}else{
													if ($_auction_dates_extend == 'yes' && $_auction_extend_counter == 'no') {
														$status = '<span>extended</span>';
														$class = " extended";
													}else{
														if($_flash_status == 'yes'){
															$status = '<span style="color:red;">Flash Bid Cycle<span class="TM_flash">®</span> live</span>';
															$class = " live";
														}else{
															$status = '<span style="color:red;">auction live</span>';
															$class = " live";
														}
													}
												}
											}
										}
										global $class_lang;
										$width = '';
										if($class_lang=='lang_es'){
											$width = 'width="30%"';
										}
									?>
                <td class="post-status center status_col" <?php echo $width;?> id="status_<?php echo $product->get_id();?>"><label class="dokan-label <?php //echo $post->post_status.$class; ?>" id="status_label_<?php echo $product->get_id();?>"><?php echo $status;//dokan_get_post_status( $post->post_status ); ?></label></td>
                <td width="20%" class="post-status center date_col" valign="top"><label class="dokan-label"><?php echo trim(date_i18n('l m/d/Y',strtotime($_auction_dates_from)));?></label></td>
              </tr>
              <?php } ?>
              <?php } else { ?>
              <tr>
                <td colspan="7"><?php _e( 'No product found', 'dokan-auction' ); ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
          </form>
        </div>
      </div>
      <?php
                wp_reset_postdata();

                $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

                if ( $product_query->max_num_pages > 1 ) {
                    echo '<div class="pagination-wrap">';
                    $page_links = paginate_links( array(
                        'current'   => $pagenum,
                        'total'     => $product_query->max_num_pages,
                        'base'      => add_query_arg( 'pagenum', '%#%' ),
                        'format'    => '',
                        'type'      => 'array',
                        'prev_text' => __( '&laquo; Previous', 'dokan-auction' ),
                        'next_text' => __( 'Next &raquo;', 'dokan-auction' )
                    ) );

                    echo '<ul class="pagination"><li>';
                    echo join("</li>\n\t<li>", $page_links);
                    echo "</li>\n</ul>\n";
                    echo '</div>';
                }
                ?>
    </article>
    <?php //echo do_shortcode('[ad_section]');?>
    <?php do_action( 'dokan_after_listing_auction_product' ); ?>
    <?php

            /**
             *  dokan_auction_product_listing_inside_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_auction_product_listing_inside_after' );
        ?>
  </div>
  <!-- #primary .content-area -->
  
  <?php
        /**
         *  dokan_dashboard_content_after hook
         *  dokan_withdraw_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_auction_product_listing_content_after' );
    ?>
</div>
<!-- .dokan-dashboard-wrap -->
</div>
<script type="text/javascript">
function SubmitRelist(){
	/*var checkList = jQuery('input[name="bulk_products[]"]:checked').serialize();
	console.log(checkList);*/
	/*var product_ids ='';
	jQuery('input[name="bulk_products[]"]:checked').each(function() {
	   product_ids +=this.value+","
	});*/
	var product_ids = jQuery.map(jQuery(':checkbox[name=bulk_products\\[\\]]:checked'), function(n, i){
									  return n.value;
								}).join(',');
	if(product_ids ==''){
		alert('Please select Auction to Relist!');
	}else{							
	 	window.location.replace("<?php echo get_site_url();?>/?action=multi-relist&mode=discount&product_id="+product_ids);	
	}
}
function checkAuctionStatus(){
	var auctionids = '<?php echo implode(",",$auctionids);?>';
	jQuery.ajax({	
						url:'/ajax.php',	
						type:'POST',
						cache : false,
						data:{'mode':'checkAuctionStatus','auctionids':auctionids},
						beforeSend: function() {},
						complete: function() {},
						success:function (data){
							var json =jQuery.parseJSON(data);
							//console.log(json);
							jQuery.each(json, function(index, item) {
								//console.log(item.auctionid+"=="+item.stausTxt);
								if(jQuery("#status_label_"+item.auctionid).html() != item.stausTxt){
									jQuery("#status_label_"+item.auctionid).html(item.stausTxt);
								}
							});
						}
						});
}
<?php if(!empty($auctionids)){?>
//var auctionStatus = setInterval(checkAuctionStatus,5000);
<?php }?>
</script>
