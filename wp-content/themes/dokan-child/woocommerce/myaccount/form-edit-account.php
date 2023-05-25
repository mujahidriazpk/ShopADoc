<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
global $current_user;
/*echo date_default_timezone_get();
echo "==";
echo date('Y-m-d g:i A');
echo "==";
date_default_timezone_set('America/Los_Angeles');
echo $today_date_time = date('Y-m-d g:i A');*/
do_action( 'woocommerce_before_edit_account_form' ); ?>
<?php if((isset($_GET['redirect'])&&$_GET['redirect']=='checkout')){?>
<a href="<?php echo home_url('/checkout/')?>" style="font-size:17px !important;" class="dokan-btn dokan-btn-theme btn-primary" title="back">Back to Checkout</a>
<?php }?>
 <?php 
  $btn_text = 'Save changes';
 if($user->roles[0] =='advanced_ads_user'){
	 $btn_text = 'Save';
	 ?>
 	<style type="text/css">
		header.entry-header{display:none !important;}
	</style>
 	<div class="entry-header">
        <h1 class="entry-title">Change Password<span id="not_print" class="right-align-txt">ShopADoc<span class="TM_title">®</span></span></h1>
    </div>
 <?php }?>
<?php if(isset($_GET['mode']) && $_GET['mode']=='update'){?>
<div class="woocommerce-notices-wrapper">
	<?php if($user->roles[0] =='customer'){
		$plan_status = get_plan_status();
		if($plan_status == 'inactive' || $plan_status == ''){
		?>
			<div class="woocommerce-message" role="alert">You’ve successfully edited your contact information.</div>
		<?php }else{?>
			<div class="woocommerce-message" role="alert">You’ve successfully edited your contact information. Changes reflected in the following auction cycle.</div>
		<?php }?>
	<?php }else{?>
		<?php 
			date_default_timezone_set('America/Los_Angeles');
			$monday_next_week = date("Y-m-d",strtotime( "monday next week" ))." 08:30";
			$flash_cycle_end = date('Y-m-d', strtotime( 'friday this week' ) )." 10:30";
			$today_date_time = date('Y-m-d H:i');
			if ($today_date_time > $flash_cycle_end && $today_date_time < $monday_next_week) {?>
				<div class="woocommerce-message" role="alert">You’ve successfully edited your contact information.</div>
			<?php }else{?>
				<div class="woocommerce-message" role="alert">You’ve successfully edited your contact information. Changes reflected in the following auction cycle.</div>
			<?php }?>
		<?php }?>
</div>
<?php }?>

<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> autocomplete="off">

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>
    <?php /*?>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_first_name"><?php esc_html_e( 'ID', 'woocommerce' ); ?>:&nbsp;<?php echo $user->ID?></label>
	</p>
	<?php */?>
    <?php if($user->roles[0] =='advanced_ads_user'){
		$display_class = ' hide';
	}else{
		$display_class = '';
	}?>
	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first<?php echo $display_class;?>">
		<label for="account_first_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" readonly="readonly" />
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last<?php echo $display_class;?>">
		<label for="account_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" readonly="readonly" />
	</p>
	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide hide">
		<label for="account_display_name"><?php esc_html_e( 'Display name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->first_name ).' '.esc_attr( $user->last_name ); ?>" /> <span><em><?php esc_html_e( 'This will be how your name will be displayed in the account section and in reviews', 'woocommerce' ); ?></em></span>
	</p>
	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide<?php echo $display_class;?>">
		<?php if($current_user->roles[0]=='customer'){?>
			<label for="account_email"><?php esc_html_e( 'Personal email', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<?php }else{?>
			<label for="account_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<?php }?>
		<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
	</p>
	<fieldset>
    <?php if($user->roles[0] !='advanced_ads_user'){?>
		<legend class="blue_text"><?php esc_html_e( 'Change Password', 'woocommerce' ); ?></legend>
	<?php }?>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row form-row-first">
			<label for="password_current"><?php esc_html_e( 'Current password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current12" autocomplete="new-password"/>
            <span toggle="#password_current" class="myacount fa fa-fw fa-eye field-icon toggle-password"></span>
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row form-row-first clear">
			<label for="password_1"><?php esc_html_e( 'New password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="off" />
            <span toggle="#password_1" class="myacount fa fa-fw fa-eye field-icon toggle-password"></span>
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row form-row-first clear">
			<label for="password_2"><?php esc_html_e( 'Confirm new password', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
            <span toggle="#password_2" class="myacount fa fa-fw fa-eye field-icon toggle-password"></span>
		</p>
	</fieldset>
	<div class="clear"></div>
   
	<?php
	
	?>
	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="woocommerce-Button button" name="save_account_details" value="<?php echo $btn_text; ?>"><?php echo $btn_text; ?></button>
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
<script type="text/javascript">
jQuery(document).ready(function() {
	window.history.pushState(null, "", window.location.href);        
	window.onpopstate = function() {
		window.history.pushState(null, "", window.location.href);
	};
});
jQuery('#post-46 h1.entry-title').append('&nbsp;<span class="tooltips" style="display:inline-block;float:none !important;" title="Any changes to your information will become effective the following auction cycle.">i</span>');
</script>