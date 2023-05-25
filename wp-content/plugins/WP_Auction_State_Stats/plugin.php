<?php

/*
Plugin Name: WP_Auction_State_Stats
Plugin URI: https://grossiweb.com
Description: 
Version: 1.0
Author: stefano
Author URI:  https://grossiweb.com
*/

if ( ! class_exists( 'WP_Auction_State_Stats' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Auction_State_Stats_List extends WP_List_Table {
	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Auction State Stats', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Auction State Stats', 'sp' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}
	public static function get_dates_of_quarter($quarter = 'current', $year = null, $format = 'Y-m-d')
{

    if ( !is_int($year) ) {        
       $year = (new DateTime)->format('Y');
    }
    $current_quarter = ceil((new DateTime)->format('n') / 3);
    switch (  strtolower($quarter) ) {
    case 'this':
    case 'current':
       $quarter = ceil((new DateTime)->format('n') / 3);
       break;

    case 'previous':
       $year = (new DateTime)->format('Y');
       if ($current_quarter == 1) {
          $quarter = 4;
          $year--;
        } else {
          $quarter =  $current_quarter - 1;
        }
        break;

    case 'first':
        $quarter = 1;
        break;

    case 'last':
        $quarter = 4;
        break;

    default:
        $quarter = (!is_int($quarter) || $quarter < 1 || $quarter > 4) ? $current_quarter : $quarter;
        break;
    }
    if ( $quarter === 'this' ) {
        $quarter = ceil((new DateTime)->format('n') / 3);
    }
    $start = new DateTime($year.'-'.(3*$quarter-2).'-1');
    $end = new DateTime($year.'-'.(3*$quarter).'-'.($quarter == 1 || $quarter == 4 ? 31 : 30) .'');

    return array(
        'start' => $format ? $start->format($format) : $start,
        'end' => $format ? $end->format($format) : $end,
    );
}
public static function getAuctionCustomFunc($auction_state,$auction_zip_code,$mishaDateFrom,$mishaDateTo,$period){
	global $wpdb,$demo_listing;
	$post_statuses = array('publish');
	$ids = array();
	$args = array(
					'post__not_in' => array($demo_listing),
					//'post_status'         => $post_statuses,
					'ignore_sticky_posts' => 1,
					'meta_key' => '_auction_dates_from',
					'orderby' => 'meta_value',
					'order'               => 'asc',
					'posts_per_page'      => -1,
					'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
					'meta_query' => array(array('key' => '_auction_closed','operator' => 'EXISTS',)),
					'auction_archive'     => TRUE,
					'show_past_auctions'  => TRUE,
				);
	if(isset($auction_state) && $auction_state !="" ){
		$args['meta_query'][] =    array(
					'key'   => 'auction_state',
					'compare'   => 'LIKE',
					'value'   => $auction_state,
		
				);	
		$result['auction_state'] = $auction_state;
	}
	if(isset($auction_zip_code) && $auction_zip_code !="" ){
		$args['meta_query'][] =    array(
					'key'   => 'auction_zip_code',
					'compare'   => 'LIKE',
					'value'   => $auction_zip_code,
		
				);	
		$result['auction_zip_code'] = $auction_zip_code;
	}
	if(isset($period) && $period =="custom"){
		if(isset($mishaDateFrom) && $mishaDateFrom !="" && $mishaDateTo ==""){
			$args['meta_query'][] =    array(
						'key'   => '_auction_dates_from_org',
						'compare'   => '>=',
						'value'   => date('Ymd',strtotime($mishaDateFrom)),
						'type'        => 'date'

					);	

		}elseif($mishaDateFrom =="" && isset($mishaDateTo) && $mishaDateTo !=""){
			$args['meta_query'][] = array(   
					array(
						'key'   => '_auction_dates_to',
						'compare'   => '<=',
						'value'   => date('Ymd',strtotime($mishaDateTo)),
						'type'        => 'date'

					));	
		}elseif(isset($mishaDateFrom) && $mishaDateFrom !="" && isset($mishaDateTo) && $mishaDateTo !=""){
			$args['meta_query'][] = array( 
					'relation' => 'AND',              
					array(
						'key'   => '_auction_dates_from_org',
						'compare'   => '>=',
						'value'   =>date('Ymd',strtotime($mishaDateFrom)),
						'type'        => 'date'

					),
					array(
						'key'   => '_auction_dates_to',
						'compare'   => '<=',
						'value'   => date('Ymd',strtotime($mishaDateTo)),
						'type'        => 'date'

					));	
		}
	}else{
		$start = date("Y-m-d")." 00:00:00";
		//$end = date('Y-m-d', strtotime( 'friday this week' ) ).' 23:59:59';
		$end = date('Y-m-d').' 23:59:59';
		if($period=='yesterday'){
			$start =date('Y-m-d',strtotime("-1 days"))." 00:00:00";
			$end = date('Y-m-d',strtotime("-1 days")).' 23:59:59';
		}elseif($period=='7days'){
			$start = date("Y-m-d",strtotime( "monday this week" ));
			$end = date('Y-m-d', strtotime( 'friday this week' ) );
		}elseif($period=='last7days'){
			$start = date("Y-m-d",strtotime( "monday last week" ));
			$end = date('Y-m-d', strtotime( 'friday last week' ) );
		}elseif($period=='lastmonth'){
			$start = date("Y-m-d", strtotime("first day of previous month"));
			$end = date("Y-m-d", strtotime("last day of previous month"));
		}elseif($period=='lastyear'){
			$start = date("Y",strtotime("-1 year"))."-01-01";
			$end = date("Y",strtotime("-1 year"))."-12-31";
		}elseif($period=="quarter"){
			$quarter = Auction_State_Stats_List::get_dates_of_quarter();
			$start = $quarter['start'];
			$end = $quarter['end'];
		}
		//echo $start."==".$end."<br />";
		if($period=='today' || $period=='yesterday'){
			if(in_array(date("l"),array('Thursday','Friday','Saturday','Sunday'))){
				$start = date("Y-m-d",strtotime( "monday this week" ));
				$end = date('Y-m-d', strtotime( 'friday this week' ) );
				$args['meta_query'][] = array( 
				'relation' => 'AND',              
				array(
					'key'   => '_auction_dates_from_org',
					'compare'   => '>=',
					'value'   =>date('Ymd',strtotime($start)),
					'type'        => 'date'
		
				),
				array(
					'key'   => '_auction_dates_to',
					'compare'   => '<=',
					'value'   => date('Ymd',strtotime($end)),
					'type'        => 'date'
		
				));
			}else{
				$args['meta_query'][] = array( 
				'relation' => 'AND',              
				array(
					'key'   => '_auction_dates_from_org',
					'compare'   => '>=',
					'value'   =>date('Ymd',strtotime($start)),
					'type'        => 'date'
				));
			}
		}else{
			$args['meta_query'][] = array( 
				'relation' => 'AND',              
				array(
					'key'   => '_auction_dates_from_org',
					'compare'   => '>=',
					'value'   =>date('Ymd',strtotime($start)),
					'type'        => 'date'
		
				),
				array(
					'key'   => '_auction_dates_to',
					'compare'   => '<=',
					'value'   => date('Ymd',strtotime($end)),
					'type'        => 'date'
		
				));	
		}
	}
	$product_query = new WP_Query( $args );
	//echo $product_query->request;
	$no_auctions = $product_query->found_posts;
	$posts = $product_query->posts;
	$success_count = '0';
	$relist_count ='0';
	if($no_auctions > 0){
		foreach($posts as $post){
			$_auction_relist_expire = get_post_meta($post->ID, '_auction_relist_expire',TRUE);
			if($_auction_relist_expire){
				$relist_count++;
			}else{
				$success_count++;
			}
		}
	}
	if($success_count==0) $success_count ='--';
	if($relist_count==0) $relist_count ='--';
	$result['success'] = $success_count;
	$result['relist'] = $relist_count;
	$result['no_auctions'] = $no_auctions;
	return $result;	
}
public static function getUserCustomFunc($user_state,$user_zip_code,$mishaDateFrom,$mishaDateTo,$period){
		global $wpdb;
		if(isset($period) && $period =="custom"){
			$start = $mishaDateFrom;
			$end = $mishaDateTo;
		}else{
			$start = date("Y-m-d");
			$end = date('Y-m-d');
			if($period=='yesterday'){
				$start =date('Y-m-d',strtotime("-1 days"));
				$end = date('Y-m-d',strtotime("-1 days"));
			}elseif($period=='7days'){
				$start = date("Y-m-d",strtotime( "monday this week" ));
				$end = date('Y-m-d', strtotime( 'friday this week' ) );
			}elseif($period=='last7days'){
				$start = date("Y-m-d",strtotime( "monday last week" ));
				$end = date('Y-m-d', strtotime( 'friday last week' ) );
			}elseif($period=='lastmonth'){
				$start = date("Y-m-d", strtotime("first day of previous month"));
				$end = date("Y-m-d", strtotime("last day of previous month"));
			}elseif($period=='lastyear'){
				$start = date("Y",strtotime("-1 year"))."-01-01";
				$end = date("Y",strtotime("-1 year"))."-12-31";
			}elseif($period=="quarter"){
				$quarter = Auction_State_Stats_List::get_dates_of_quarter();
				$start = $quarter['start'];
				$end = $quarter['end'];
			}
		}
	
		 $where = '';
		if(isset($user_state) && $user_state !="" ){
			 $where .= " AND state='".$user_state."'";		
		}
		if(isset($user_zip_code) && $user_zip_code !="" ){
			$where .= " AND zipcode='".$user_zip_code."'";
		}
		$query_client = "select SUM(count_total) from wp_session_log where type= 'client' and (dated BETWEEN '".$start."' AND '".$end."') ".$where;
		$total_client = $wpdb->get_var( $query_client );
		$query_dentist = "select SUM(count_total) from wp_session_log where type= 'dentist' and (dated BETWEEN '".$start."' AND '".$end."') ".$where;
		$total_dentist = $wpdb->get_var( $query_dentist );
		
		$result['client'] = $total_client;
		$result['dentist'] = $total_dentist;
		$result['total'] =$total_client + $total_dentist;
		return $result;
	}
	/**
	 * Retrieve stats data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	 public static function get_stats_User( $per_page = 20, $page_number = 1 ) {
		global $wpdb;
		$result = array();
		return $result;
	}

	public static function get_stats( $per_page = 20, $page_number = 1 ) {
		global $wpdb;
		$result = array();
		return $result;
	}


	/**
	 * Delete a stat record.
	 *
	 * @param int $id stat ID
	 */
	public static function delete_stat( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}customers",
			[ 'ID' => $id ],
			[ '%d' ]
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {}


	/** Text displayed when no stat data is available */
	public function no_items() {
		_e( 'No stats avaliable.', 'sp' );
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		global $wpdb;
		$where = '';
		if(isset($_POST['mishaDateFrom']) && $_POST['mishaDateFrom'] !="" && isset($_POST['mishaDateTo']) && $_POST['mishaDateTo'] !=""){
				$from = date('Y-m-d',strtotime($_POST['mishaDateFrom']));
				$to = date('Y-m-d',strtotime($_POST['mishaDateTo']));
				$where  = " and dated >= '".$from."' AND dated <= '".$to."'";
		}
		switch ( $column_name ) {
			case 'no_auctions':
				return $item[ $column_name ];
			case 'auction_city':
				$auction_city = ( isset($item[ $column_name ]) && $item[ $column_name ]) ? $item[ $column_name ]: '-';
				return $auction_city;
				//return get_post_meta($item['post_id'],'business_name',true);
			case 'auction_state':
				$auction_state = ( isset($item[ $column_name ]) && $item[ $column_name ]) ? $item[ $column_name ]: '-';
				return $auction_state;
			case 'auction_zip_code':
    			$auction_zip_code = ( isset($item[ $column_name ]) && $item[ $column_name ]) ? $item[ $column_name ]: '-';
				return $auction_zip_code;
			case 'service':
				$service = ( isset($item[ $column_name ]) && $item[ $column_name ]) ? $item[ $column_name ]: '-';
				return $service;
			case 'success':
				$success = ( isset($item[ $column_name ]) && $item[ $column_name ]) ? $item[ $column_name ]: '-';
				return $success;
			case 'relist':
				$relist = ( isset($item[ $column_name ]) && $item[ $column_name ]) ? $item[ $column_name ]: '-';
				return $relist;	
			default:
				return '-'; //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		/*return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
		);*/
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'sp_delete_stat' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&stat=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			//'cb'      => '<input type="checkbox" />',
			'no_auctions'    => __( '# of Auctions', 'sp' ),
			'auction_city'    => __( 'City', 'sp' ),
			'auction_state'    => __( 'State', 'sp' ),
			'auction_zip_code'    => __( 'Zip', 'sp' ),
			'service'    => __( 'Service', 'sp' ),
			'outcome'    => __( 'Outcome', 'sp' ),
			'success'    => __( 'Success %', 'sp' ),
			'relist'    => __( 'Relist %', 'sp' ),
		];

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'post_title' => array( 'post_title', true ),
			'company' => array( 'company', true ),
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		/*$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;*/
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		//$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'stats_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_stats( $per_page, $current_page );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_delete_stat' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_stat( absint( $_GET['stat'] ) );

		                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		                // add_query_arg() return the current url
		                wp_redirect( esc_url_raw(add_query_arg()) );
				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_stat( $id );

			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		        // add_query_arg() return the current url
		        wp_redirect( esc_url_raw(add_query_arg()) );
			exit;
		}
	}

}


class SP_Plugin_Auction_State_Stat {

	// class instance
	static $instance;

	// stat WP_List_Table object
	public $stats_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
	}


	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function plugin_menu() {

		$hook = add_menu_page(
			'Auction State Performance',
			'Auction State Performance',
			'shopadoc_admin_cap',
			'auction_state_performance',
			[ $this, 'plugin_settings_page' ]
		);
	//	add_submenu_page( 'performance_auction', 'Total # of Auctions', 'Auction','shopadoc_admin_cap', 'admin.php?page=auction_performance');

		add_action( "load-$hook", [ $this, 'screen_option' ] );

	}

// Converts a number into a short version, eg: 1000 -> 1k
// Based on: http://stackoverflow.com/a/4371114
public function number_format_short( $n, $precision = 1 ) {
	if ($n < 900) {
		// 0 - 900
		$n_format = number_format($n, $precision);
		$suffix = '';
	} else if ($n < 900000) {
		// 0.9k-850k
		$n_format = number_format($n / 1000, $precision);
		$suffix = 'K';
	} else if ($n < 900000000) {
		// 0.9m-850m
		$n_format = number_format($n / 1000000, $precision);
		$suffix = 'M';
	} else if ($n < 900000000000) {
		// 0.9b-850b
		$n_format = number_format($n / 1000000000, $precision);
		$suffix = 'B';
	} else {
		// 0.9t+
		$n_format = number_format($n / 1000000000000, $precision);
		$suffix = 'T';
	}

  // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
  // Intentionally does not affect partials, eg "1.50" -> "1.50"
	if ( $precision > 0 ) {
		$dotzero = '.' . str_repeat( '0', $precision );
		$n_format = str_replace( $dotzero, '', $n_format );
	}

	return $n_format . $suffix;
}
	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		$map_id = ( isset( $_GET['map_id'] ) && $_GET['map_id']) ? $_GET['map_id'] : '';
		
		$period_vistor = ( isset( $_POST['period_vistor'] ) && $_POST['period_vistor'] ) ? $_POST['period_vistor'] : 'today';
		if($_POST['period_vistor']=='custom'){
			$from_vistor = ( isset( $_POST['mishaDateFrom_vistor'] ) && $_POST['mishaDateFrom_vistor'] ) ? $_POST['mishaDateFrom_vistor'] : '';
			$to_vistor = ( isset( $_POST['mishaDateTo_vistor'] ) && $_POST['mishaDateTo_vistor'] ) ? $_POST['mishaDateTo_vistor'] : '';
		}else{
			$from_vistor ='';
			$to_vistor = '';
		}
		$from_vistor = '';
		$to_vistor = '';
		if($period_vistor=='today'){
			$from_vistor =date('m/d/y');
			$to_vistor = date('m/d/y');
		}elseif($period_vistor=='yesterday'){
			$from_vistor =date('m/d/y',strtotime("-1 days"));
			$to_vistor = date('m/d/y',strtotime("-1 days"));
		}elseif($period_vistor=='7days'){
			$from_vistor = date("m/d/y",strtotime( "monday this week" ));
			$to_vistor = date('m/d/y', strtotime( 'friday this week' ) );
		}elseif($period_vistor=='last7days'){
			$from_vistor = date("m/d/y",strtotime( "monday last week" ));
			$to_vistor = date('m/d/y', strtotime( 'friday last week' ) );
		}elseif($period_vistor=='lastmonth'){
			$from_vistor = date("m/d/y", strtotime("first day of previous month"));
			$to_vistor = date("m/d/y", strtotime("last day of previous month"));
		}elseif($period_vistor=='lastyear'){
			$from_vistor = "01/01/".date("y",strtotime("-1 year"));
			$to_vistor = "12/31/".date("y",strtotime("-1 year"));
		}elseif($period_vistor=="quarter"){
			$quarter_vistor = Auction_State_Stats_List::get_dates_of_quarter('','','m/d/y');
			$from_vistor = $quarter_vistor['start'];
			$to_vistor = $quarter_vistor['end'];
		}
		/*if($period_vistor=='today' || $period_vistor=='yesterday')
		{$to_vistor = '';}*/
		if($_POST['period_vistor']=='custom'){
			$from_vistor = ( isset( $_POST['mishaDateFrom_vistor'] ) && $_POST['mishaDateFrom_vistor'] ) ? $_POST['mishaDateFrom_vistor'] : '';
			$to_vistor = ( isset( $_POST['mishaDateTo_vistor'] ) && $_POST['mishaDateTo_vistor'] ) ? $_POST['mishaDateTo_vistor'] : '';
		}
		
		$period = ( isset( $_POST['period'] ) && $_POST['period'] ) ? $_POST['period'] : 'today';
		if($_POST['period']=='custom'){
			$from = ( isset( $_POST['mishaDateFrom'] ) && $_POST['mishaDateFrom'] ) ? $_POST['mishaDateFrom'] : '';
			$to = ( isset( $_POST['mishaDateTo'] ) && $_POST['mishaDateTo'] ) ? $_POST['mishaDateTo'] : '';
		}else{
			$from ='';
			$to = '';
		}
		$from = '';
		$to = '';
		
		if($period=='today'){
			$from =date('m/d/y');
			$to = date('m/d/y');
		}elseif($period=='yesterday'){
			$from =date('m/d/y',strtotime("-1 days"));
			$to = date('m/d/y',strtotime("-1 days"));
		}elseif($period=='7days'){
			$from = date("m/d/y",strtotime( "monday this week" ));
			$to = date('m/d/y', strtotime( 'friday this week' ) );
		}elseif($period=='last7days'){
			$from = date("m/d/y",strtotime( "monday last week" ));
			$to = date('m/d/y', strtotime( 'friday last week' ) );
		}elseif($period=='lastmonth'){
			$from = date("m/d/y", strtotime("first day of previous month"));
			$to = date("m/d/y", strtotime("last day of previous month"));
		}elseif($period=='lastyear'){
			$from = "01/01/".date("y",strtotime("-1 year"));
			$to = "12/31/".date("y",strtotime("-1 year"));
		}elseif($period=="quarter"){
			$quarter = Auction_State_Stats_List::get_dates_of_quarter('','','m/d/y');
			$from = $quarter['start'];
			$to = $quarter['end'];
		}
		
		if($_POST['period']=='custom'){
			$from = ( isset( $_POST['mishaDateFrom'] ) && $_POST['mishaDateFrom'] ) ? $_POST['mishaDateFrom'] : '';
			$to = ( isset( $_POST['mishaDateTo'] ) && $_POST['mishaDateTo'] ) ? $_POST['mishaDateTo'] : '';
		}
		$post = get_post($map_id); 
		?>

<div class="wrap"> 
  <!--<h2>Sales</h2>-->
<style type="text/css">
	#toplevel_page_admin-page-auction_performance a {
	  background: #2271b1 !important;
	  color: #fff !important;
	}
	#toplevel_page_admin-page-auction_performance a:after {
	  right: 0;
	  border: solid 8px transparent;
	  content: " ";
	  height: 0;
	  width: 0;
	  position: absolute;
	  pointer-events: none;
	  border-right-color: #f0f0f1;
	  top: 50%;
	  margin-top: -8px;
	}
	/*th#order_city{width:25%;}*/
	/*body{overflow:hidden !important;}*/
	th, td {
	  font-size: 12px !important;
	}
	.font-22 {
	  font-size: 22px !important;
	}
	.error, .notice {
	  display: none;
	}
	th {
	  cursor: pointer;
	}
	table.wp-list-table img {
	  position: relative;
	  right: -3px;
	  top: 0;
	  display: flex;
	  margin: 0 !important;
	  align-items: center;
	  align-content: center;
	}
	svg:not(:root) {
	  position: fixed !important;
	  top: auto !important;
	  left: auto !important;
	  width: 50%;
	}
	.mapsvg-btn-zoom-reset svg {
	  position: relative !important;
	}
	.widefat tbody tr {
	  float: left;
	  width: 100%;
	}
	table.scroll {
	  /* width: 100%; */ /* Optional */
	  /* border-collapse: collapse; */
	  border-spacing: 0;
	  width: 100%;
	}
	table.scroll tbody, table.scroll thead {
	  display: block;
	}
	table.scroll thead tr th {
	  height: 40px;
	  line-height: 12px;
	  display: flex;
	  /* text-align: left; */
	  align-content: center;
	  align-items: center;
	  padding-left: 0;
	}
	table.scroll tbody {
	  height: 630px;
	  overflow-y: auto;
	  overflow-x: hidden;
	}
	table.scroll tbody { /*border-top: 2px solid black; */
	}
	table.scroll thead tr {
	  display: inline-table;
	  width: 100%;
	}
	table.scroll thead tr th:first-child {
	  padding-left: 5px !important;
	}
	table.scroll tbody td:first-child {
	  text-align: left;
	  padding-left: 5px;
	  padding-right: 0px;
	  width: 17% !important;
	}
	/*table.scroll tbody td:first-child {
						width: 28% !important;
					}
					table.scroll tbody td:last-child {
						width: 12% !important;
					}*/
	table.scroll tbody td {
	  float: left !important;
	  width: 16.6% !important;
	  text-align: center;
	}
	table.scroll thead th {
	  float: left !important;
	  width: 16.6% !important;
	  position: relative;
	}
	table.scroll tbody td:last-child, table.scroll thead th:last-child {
	  border-right: none;
	}
	table.scroll thead tr td, table.scroll thead tr th {
	  color: #000;
	  background: #fff;
	}
	#map {
	  width: 60%;
	  height: 630px;
	  float: left
	}
	#details {
	  width: 40%;
	  float: left;
	  background: #fff;
	  padding: 1%;
	  margin-top: -20px;
	}
	.tooltip {
	  position: absolute;
	  /*border:1px solid black;*/
	  background: #fff;
	  color: #000;
	  font-size: 1.5 em;
	  padding: 2px 8px;
	  opacity: 1;
	  border-radius: 2px;
	  width: 300px;
	  box-shadow: 0px 1px 8px 1px #888;
	  -moz-box-shadow: 0px 1px 8px 1px #888;
	  -webkit-box-shadow: 0px 1px 8px 1px #888;
	  z-index: 100001;
	}
	/*text,rect{display:none;}*/
	.striped > tbody > :nth-child(2n+1), ul.striped > :nth-child(2n+1) {
	  background-color: #fff;
	}
	.active {
	  background-color: #ccc !important;
	}
	#filter_date {
	  float: left;
	  border: solid 3px #000;
	  padding: 10px;
	  margin-bottom: 10px;
	  border-radius: 3px;
	}
	input[name="mishaDateFrom"], input[name="mishaDateTo"], input[name="mishaDateFrom_vistor"], input[name="mishaDateTo_vistor"] {
	  line-height: 28px;
	  height: 28px;
	  margin: 0;
	  /*width: 37%;*/
	  font-weight: bold;
	  font-size: 12px;
	  background: url(calendar.png);
	  background-repeat: no-repeat;
	  background-size: 20px 20px;
	  background-position: right;
	}
	.period, .period_vistor {
	  line-height: 28px;
	  height: 28px;
	  margin: 0 0 0 8px;
	  width: 25%;
	  font-weight: bold;
	  font-size: 12px;
	  background: url(calendar.png);
	  background-repeat: no-repeat;
	  background-size: 20px 20px;
	  background-position: right;
	  background: #0A7BE2 !important;
	  color: #fff !important;
	  font-weight: normal !important;
	  border: 1px solid #F5F5F5 !important;
	  padding: 0 5px 0 5px !important;
	  min-height:31px !important;
	}
	.pink {
	  color: #DB2D69 !important;
	}
	.blue {
	  color: #479CE9 !important;
	}
	.amt {
	  font-size: 20px;
	}
	.wrap {
	  margin: 10px 0 0 2px;
	}
	.top_panel {
	  background: #572D91;
	  float: left;
	  width: 100%;
	  padding: 10px;
	  margin-top: 10px;
	}
	.table_bg strong, .table_bg th {
	  color: #fff;
	}
	.widefat td, .widefat th {
	  /*padding: 8px 0;*/
	}
	.heading {
	  float: left;
	  width: 100%;
	  font-weight: bold;
	  padding-bottom: 10px;
	}
	.heading .heading1 {
	  float: left;
	  font-size: 14px;
	  line-height: 24px;
	}
	.heading .heading2 {
	  float: left;
	  font-size: 18px;
	  line-height: 24px;
	}
	.font-10 {
	  font-size: 10px !important;
	}
	.font-12 {
	  font-size: 12px !important;
	}
	.font-18 {
	  font-size: 18px !important;
	}
	.font-13 {
	  font-size: 13px !important;
	}
	/*table.scroll tbody td, table.scroll thead th{font-size:12px !important;}*/
	/*table.wp-list-table img {
						margin: 1px -1px !important;
					}	*/
	.font-10 {
	  font-size: 10px !important;
	  font-weight: bold !important;
	}
	/*table.wp-list-table img {
						margin: 1px 4px !important;
					}*/
	.back {
	  color: #AAAAAA !important;
	  font-size: 14px !important;
	  margin-bottom: 5px;
	  text-decoration: none !important;
	  float: left;
	  width: auto;
	}
	#load {
	  width: 50%;
	  height: 100%;
	  position: fixed;
	  z-index: 9999;
	  /*background:url("/Spinner-2.gif") no-repeat center center rgba(0,0,0,0.25)*/
	  background: url("/ajax-loader.gif") no-repeat center center;
	}
	.heading {
	  float: left;
	  width: 100%;
	  font-weight: bold;
	  padding-bottom: 10px;
	}
	.heading .heading1 {
	  float: left;
	  font-size: 14px;
	  line-height: 24px;
	}
	.heading .heading2 {
	  float: left;
	  font-size: 18px;
	  line-height: 24px;
	}
	.filter label {
	  float: left;
	  width: 10%;
	  text-align: center;
	  line-height: 25px;
	}
	.datefield {
	  float: left;
	  width: 33%;
	}
	.filter_main {
	  float: left;
	  width: 100%;
	}
	.filter_div1 {
	  float: left;
	  width: 100%;
	}
	.filter_div2 {
	  float: left;
	  width: 42%;
	  margin-left: 14%;
	}
	.filter {
	  float: left;
	  width: 100%;
	  margin: 0 0 10px 0 !important;
	}
	.top_panel_main th {
	  text-align: center;
	}
	.top_panel_main {
	  float: left;
	  width: 100%;
	  text-align: center;
	}
	.top_panel_left {
	  width: 100%;
	  float: left;
	  margin-right: 0%;
	}
	.top_panel_right {
	  width: 55%;
	  float: left;
	  margin-left: 1%;
	}
	.amt {
	  text-align: center;
	  float: left;
	  width: 100%;
	}
	.s_lab{background: #572D91;color: #fff;}
</style>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder">
      <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable">
          <form method="post" id="filterForm">
            <script src="/wp-content/plugins/WP_Sale_Graph/lib/raphael.js"></script> 
            <!-- <script src="scale.raphael.js"></script> --> 
            <script src="/wp-content/plugins/WP_Sale_Graph/example/color.jquery.js"></script> 
            <!--<script src="/wp-content/plugins/WP_Sale_Graph/sorttable.js"></script>--> 
            <script type="text/javascript">
					jQuery(document).ready(function() {;
						jQuery('#map svg').attr('width','100%');
						jQuery('#map svg').attr('height','100%');
						//jQuery("#dest").addSortWidget();
						jQuery("table.scroll tr:odd").css({"background-color":"#F6F7F7","color":"#000"});
					});
					
					var $table = jQuery('table.scroll'),
					$bodyCells = $table.find('tbody tr:first').children(),
					colWidth;
					
  					
					// Adjust the width of thead cells when window resizes
					jQuery(window).resize(function() {
					// Get the tbody columns width array
					colWidth = $bodyCells.map(function() {
					return jQuery(this).width();
					}).get();
					// Set the width of thead columns
					$table.find('thead tr').children().each(function(i, v) {
						jQuery(v).width(colWidth[i]);
					});    
					}).resize();
					
					jQuery( function($) {
						var from = $('input[name="mishaDateFrom"]'),
						to = $('input[name="mishaDateTo"]');
						$.datepicker.setDefaults({
							dateFormat: "mm/dd/y"
						});
						$( 'input[name="mishaDateFrom"], input[name="mishaDateTo"]' ).datepicker();
						from.on( 'change', function() {
								to.datepicker( 'option', 'minDate', from.val());
								if(to.val()!=""){
									$("#filterForm").submit();
								}
							});
							to.on( 'change', function() {
								from.datepicker( 'option', 'maxDate', to.val());
								if(from.val()!=""){
									$("#filterForm").submit();
								}
							});
							var img_asc="/wp-content/plugins/WP_Sale_Graph/img/desc_sort.gif";	
							var img_desc="/wp-content/plugins/WP_Sale_Graph/img/asc_sort.gif";	
							var img_nosort="/wp-content/plugins/WP_Sale_Graph/img/no_sort.gif";
							$('#dest th').append('<img src="/wp-content/plugins/WP_Sale_Graph/img/no_sort.gif" class="sorttable_img" style="cursor: pointer; margin-left: 10px;">');
							$('th').click(function(){
								var table = $(this).parents('table').eq(0)
								var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
								this.asc = !this.asc;
								$('th').find("img").attr('src',img_nosort);
								if (!this.asc){rows = rows.reverse();$(this).find("img").attr('src',img_asc);}else{$(this).find("img").attr('src',img_desc);}
								for (var i = 0; i < rows.length; i++){table.append(rows[i])}
							})
							function comparer(index) {
								return function(a, b) {
									var valA = getCellValue(a, index), valB = getCellValue(b, index)
									return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB)
								}
							}
							function getCellValue(row, index){ return $(row).children('td').eq(index).text() }
							$(".period").on("change",function(){
							if(jQuery(this).val()=='custom'){
								if(to.val()!="" && from.val()!=""){
									$("#filterForm").submit();
								}
							}else{
								$("#filterForm").submit();
							}
							
						});
							$(".period").val('<?php echo $period;?>');
					});
					jQuery( function($) {
						var from_vistor = $('input[name="mishaDateFrom_vistor"]'),
						to_vistor = $('input[name="mishaDateTo_vistor"]');
						$.datepicker.setDefaults({
							dateFormat: "mm/dd/y"
						});
						$( 'input[name="mishaDateFrom_vistor"], input[name="mishaDateTo_vistor"]' ).datepicker();
						from_vistor.on( 'change', function() {
								to_vistor.datepicker( 'option', 'minDate', from_vistor.val());
								if(to_vistor.val()!="" && $(".period_vistor").val() == 'custom'){
									$("#filterForm").submit();
								}
							});
							to_vistor.on( 'change', function() {
								from_vistor.datepicker( 'option', 'maxDate', to_vistor.val());
								if(from_vistor.val()!="" && $(".period_vistor").val() == 'custom'){
									$("#filterForm").submit();
								}
							});
						var img_asc="/wp-content/plugins/WP_Sale_Graph/img/desc_sort.gif";	
						var img_desc="/wp-content/plugins/WP_Sale_Graph/img/asc_sort.gif";	
						var img_nosort="/wp-content/plugins/WP_Sale_Graph/img/no_sort.gif";
						function comparer(index) {
							return function(a, b) {
								var valA = getCellValue(a, index), valB = getCellValue(b, index)
								return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB)
							}
						}
						function getCellValue(row, index){ return $(row).children('td').eq(index).text() }
						$(".period_vistor").on("change",function(){
							if(jQuery(this).val()=='custom'){
								if(to_vistor.val()!="" && from_vistor.val()!=""){
									$("#filterForm").submit();
								}
							}else{
								$("#filterForm").submit();
							}
							
						});
						$(".period_vistor").val('<?php echo $period_vistor;?>');
					});
			</script>
             <div id="load"></div>
            <div id="map" style=""><?php echo do_shortcode('[mapsvg id="'.$map_id.'"]');?></div>
            <div id="details" >
            <?php global $US_State_2;?>
            <a href="<?php echo home_url();?>/wp-admin/admin.php?page=auction_performance" class="back"> &lt;&lt; Back </a><!-- / <strong><?php echo $post->post_title;?></strong><br />-->
              <div id="filter_date" >
                <div class="heading"><span class="heading1">PERFORMANCE -</span><span class="heading2">&nbsp;<?php echo strtoupper($US_State_2[$post->post_title]);?></span></div>
                <?php 
					global $US_state;
					$types = array('126'=>'Auction Listing Fee','1141'=>'Registration Fee','948'=>'Subscription Fee','942'=>'Auction Cycle fee','1642'=>'Auction Relisting Fee',);
				?>
                <form method="post" id="filterForm">
              	  <div class="filter_main">
					<div class="filter_div1" >
						<div class="filter filter4">
						  <label>Start</label>
						  <input type="text" name="mishaDateFrom" class="datefield" placeholder="Start Date" value="<?php echo $from;?>" autocomplete="off">
						</div>
						<div class="filter filter5" style="margin: 0 !important;">
						  <label>End</label>
						  <input type="text" name="mishaDateTo" class="datefield" placeholder="End Date" value="<?php echo $to;?>" autocomplete="off"/>
						  <select name="period" class="period pull-left" >
						   <option value="today">Today</option>
							<!--<option value="7days">This Week</option>-->
							<option value="last7days">Last Week</option>
							<option value="lastmonth">Last Month</option>
							<option value="lastyear">Last Year</option>
							<option value="custom">Custom</option>
						  </select>
						</div>
						<!--<div class="filter filter1">
						  <label>Start</label>
						  <input type="text" name="mishaDateFrom_vistor" class="datefield" placeholder="Start Date" value="<?php echo $from_vistor;?>" autocomplete="off">
						</div>
						<div class="filter filter1">
						  <label>End</label>
						  <input type="text" name="mishaDateTo_vistor" class="datefield" placeholder="End Date" value="<?php echo $to_vistor;?>" autocomplete="off"/>
						</div>
						<div class="filter filter3">
						  <select name="period_vistor" class="period_vistor pull-right" >
							<option value="today">Today</option>
							<option value="7days">This Week</option>
							<option value="last7days">Last Week</option>
							<option value="lastmonth">Last Month</option>
							<option value="lastyear">Last Year</option>
							<option value="custom">Custom</option>
						  </select>
						</div>-->
					</div>
					<div class="filter_div2">

						<div class="filter filter6">
						  
						</div>
					</div>
				  </div>
              	</form>
                <?php 
					$item = Auction_State_Stats_List::getAuctionCustomFunc($post->post_title,'',$from,$to,$period);
					$item_User = Auction_State_Stats_List::getUserCustomFunc($post->post_title,'',$from_vistor,$to,$period);
				?>
            <div class="top_panel_main">
              <div class="top_panel top_panel_left">
                <table width="100%" style="margin-top:0;" class="table_bg">
                  <thead>
					 <tr>
						 <th colspan="2">Session&nbsp;&nbsp;<small><a href="javascript:" title="Google Analytics tracks each engagement. Returning after 30 minutes remaining logged in counts as 1 session." style="color: red !important;">(i)</a></small></th>
                    </tr>
                    <tr>
                      <th>Client</th>
                      <th>Dentist</th>
					  <th>Auctions</th>
					  <th>Relists&nbsp;&nbsp;<small ><a href="javascript:" title="Relists 1x only discounted 2nd runs" style="color: red !important;">(i)</a></small></th>
                      <th>Success</th>
                    </tr>
                  </thead>
                  <tbody >
                    <tr>
                      <td><strong class="amt font-22"><?php echo SP_Plugin_Post_Stat::number_format_short($item_User['client']);?></strong></td>
                      <td><strong class="amt font-22"><?php echo SP_Plugin_Post_Stat::number_format_short($item_User['dentist']);?></strong></td>
					   <td><strong class="amt font-22"><?php echo $item['no_auctions']; ?></strong></td>
                      <td><strong class="amt font-22"><?php echo $item['relist'];?></strong></td>
                      <td><strong class="amt font-22"><?php echo $item['success']; ?></strong></td>
                    </tr>
                  </tbody>
                </table>
              </div>
			<?php /*?>
              <div class="top_panel top_panel_right">
                <table width="100%" style="margin-top:0;" class="table_bg">
                  <thead>
					 <tr>
                      <th colspan="3">&nbsp;</th>
                    </tr>
                    <tr>
                      <th>Auctions</th>
					  <th>Relists&nbsp;&nbsp;<small ><a href="javascript:" title="Relists 1x only discounted 2nd runs" style="color: red !important;">(i)</a></small></th>
                      <th>Success</th>
                    </tr>
                  </thead>
                  <tbody >
                    <tr>
                      <td><strong class="amt font-22"><?php echo $item['no_auctions']; ?></strong></td>
                      <td><strong class="amt font-22"><?php echo $item['relist'];?></strong></td>
                      <td><strong class="amt font-22"><?php echo $item['success']; ?></strong></td>
                    </tr>
                  </tbody>
                </table>
              </div>
			  <?php */?>
              </div>
              </div>
              <table width="100%" class="scroll wp-list-table widefat fixed striped table-view-list posts" id="dest" style="width:100%;height:630px;overflow:scroll;">
                <thead>
                  <tr>
                  <th class="font-10">State</th>
                  <th class="font-10"><span class="s_lab">S</span>Client</th>
				  <th class="font-10" style="width: 16.5% !important;"><span class="s_lab">S</span>Dentist</th>
                  <th class="font-10">Auctions</th>
                  <th class="font-10">Relists</th>
                  <th class="font-10" style="width: 14.5% !important;">Success</th>
                </tr>
                </thead>
                <tbody style="display:inline-block;width:100%;">
                  <?php 
					global $wpdb;
					if($map_id=='4920' && 1==2){
						$query = "SELECT id FROM wp_mapsvg_regions_".$map_id." where status_text = 'Enabled' and id='90031' order by id asc ";
					}else{
						$query = "SELECT id FROM wp_mapsvg_regions_".$map_id." where status_text = 'Enabled' order by id asc ";
					}
					$results = $wpdb->get_results($query, OBJECT);

					foreach($results as $row){
								$v = $row->id;
								$item='';
								$item_User='';
								$item = Auction_State_Stats_List::getAuctionCustomFunc('',$v,$from,$to ,$period);
								$item_User = Auction_State_Stats_List::getUserCustomFunc('',$v,$from,$to ,$period);
								$total_client = $item_User['client'];
						
								echo '<span id="tooltip-html-'.strtoupper($v).'" style="display:none;"><p><strong>PERFORMANCE - <span class="State_name">'.strtoupper($v).'</span></strong></p><p><p style="float:left;width:20%;"><strong><span class="s_lab">S</span>Client</strong><br /><strong class="amt">'.SP_Plugin_Post_Stat::number_format_short($item_User['client']).'</strong></p><p style="float:left;width:22%;"><strong><span class="s_lab">S</span>Dentist</strong><br /><strong class="amt">'.SP_Plugin_Post_Stat::number_format_short($item_User['dentist']).'</strong></p><p style="float:left;width:22%;"><strong>Auctions</strong><br /><strong class="amt">'.SP_Plugin_Post_Stat::number_format_short($item['no_auctions']).'</strong></p><p style="float:left;width:18%;"><strong>Relists</strong><br /><strong class="amt">'.$item['relist'].'</strong></p><p style="float:left;width:18%;"><strong>Success</strong><br /><strong class="amt">'.$item['success'].'</strong></p></p></span>';
								
								echo '<tr id="'.str_replace(" ","-",$v).'">';
									echo '<td align="left"><strong>'.$v.'</strong></td>';
									echo '<td align="left" class=""><strong>'.SP_Plugin_Post_Stat::number_format_short($item_User['client']).'</strong></td>';echo '<td align="left" class=""><strong>'.SP_Plugin_Post_Stat::number_format_short($item_User['dentist']).'</strong></td>';
									echo '<td align="left" class=""><strong>'.SP_Plugin_Post_Stat::number_format_short($item['no_auctions']).'</strong></td>';
									echo '<td align="left" class=""><strong>'.$item['relist'].'</strong></td>';
									echo '<td align="left" class=""><strong>'.$item['success'].'</strong></td>';
								echo '</tr>';
							?>
                  <script>
										 jQuery('#<?php echo $v;?>').click(function() {
												if(jQuery(this).hasClass('active')){
													
												}else{
													if(jQuery('#dest tbody tr').hasClass('active')){
														jQuery('<tr id="'+jQuery(this).attr("id")+'_selected" class="active">'+jQuery(this).html()+"</tr>").insertAfter( "#dest tbody tr.active:last" );
													}else{
														jQuery( "#dest" ).prepend('<tr id="'+jQuery(this).attr("id")+'_selected" class="active">'+jQuery(this).html()+"</tr>");
													}
													//jQuery(this).addClass('active');
													jQuery(this).hide();
													jQuery("svg path#<?php echo $v;?>").addClass("mapsvg-region-hover");
													jQuery("svg path#<?php echo $v;?>").addClass("mapsvg-region-active");
													jQuery("svg path#<?php echo $v;?>").attr("style","font-size: 12px; fill: #12A94C; fill-rule: nonzero; stroke: rgb(0, 0, 0); stroke-width: 0.233106px; stroke-linecap: butt; stroke-linejoin: bevel; stroke-miterlimit: 4; stroke-opacity: 1; stroke-dasharray: none; marker-start: none;");
													
													 jQuery('#<?php echo $v;?>_selected').click(function() {
														jQuery(this).remove();
														 jQuery('#dest tbody tr#<?php echo $v;?>').show();
														jQuery("svg path#<?php echo $v;?>").removeClass("mapsvg-region-hover");
														jQuery("svg path#<?php echo $v;?>").removeClass("mapsvg-region-active");
														jQuery("svg path#<?php echo $v;?>").attr("style","opacity: 1; cursor: pointer; stroke-opacity: 1; stroke-linejoin: round; fill-opacity: 1; fill:#d1dbdd; stroke: rgb(255, 255, 255); stroke-width: 0.360052px;");
													});
												}
										});
										
									</script>
                  <?php }?>
                </tbody>
              </table>
            </div>
          </form>
        </div>
      </div>
    </div>
    <br class="clear">
  </div>
</div>
<script type="text/javascript">
document.onreadystatechange = function () {
	  var state = document.readyState
	  if (state == 'interactive') {
		   document.getElementById('map').style.visibility="hidden";
	  } else if (state == 'complete') {
		  setTimeout(function(){
			 document.getElementById('interactive');
			 document.getElementById('load').style.visibility="hidden";
			 document.getElementById('map').style.visibility="visible";
		  },1000);
	  }
	}
</script>
<?php
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Stats',
			'default' => 20,
			'option'  => 'stats_per_page'
		];

		add_screen_option( $option, $args );

		$this->stats_obj = new Auction_State_Stats_List();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}


add_action( 'plugins_loaded', function () {
	SP_Plugin_Auction_State_Stat::get_instance();
} );

