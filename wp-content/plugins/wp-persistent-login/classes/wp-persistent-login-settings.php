<?php


// If this file is called directly, abort.
defined( 'WPINC' ) || die( 'Well, get lost.' );

/**
 * Class WP_Persistent_Login_Settings
 *
 * @since 2.0.0
 */
class WP_Persistent_Login_Settings {

    public $post;
    public $message;
    public $type;
    public $message_key;
    public $type_key;

    /**
	 * Initialize the class and set its properties.
	 *
	 * We register all our common hooks here.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function __construct() {

        // update settings if a form has been submitted
        $this->post = $_REQUEST;
        
        // if the request has a method set, attempt to run the method
        if( !empty($this->post) && isset($this->post['wppl_method']) ) {
            add_action('admin_init', array($this, 'handle_settings'));
        }

        // display messages to the user if a message is set
        if( isset($_GET['wppl-msg']) ) {
            $this->message = $_GET['wppl-msg'];
            $this->message_key = 'wppl-msg';
        }
        if( isset( $_GET['type'] ) ) {
            $this->type = $_GET['type'];
            $this->type_key = 'type';
        }        

        if( isset($this->message) && isset($this->type) ) {
            add_action('admin_init', array($this, 'show_message'));
        }
        
		
	}

    
    /**
     * redirect_with_message
     *
     * @param  string $url
     * @param  int $status_code
     * @param  string $message
     * @param  string $type
     * @return void
     */
    public function redirect_with_message($url = '', $status_code = 302, $message = '', $type = 'updated') {

        // check if the url already has query strings - ?
        $query_string_search = strpos($url, '?');
        if( $query_string_search === false ) {
            $query_string = '?'; // if it doesn't, use it to being our query string
        } else {
            $query_string = '&'; // if it does, add ours onto the end
        }

        $message = urlencode($message);
        $redirect_url = "$url$query_string$this->message_key=$message&$this->type_key=$type";
        
        if ( wp_safe_redirect( $redirect_url, $status_code ) ) {
            exit;
        }

    }


    /**
	 * show_message
	 *
	 * @param  string $message
	 * @param  string $type
	 * @return void
	 */
	public function show_message() {
 
		if ( isset($this->message) && isset($this->type) ) {

			add_settings_error(
				uniqid('wppl'),
				esc_attr( 'wp_persistent_login_message' ),
				$this->message,
				$this->type
			);

		}
	 
	}

    
    /**
     * handle_settings
     *
     * @return void
     */
    public function handle_settings() {

        $post_data = $this->post;
        $method = sanitize_text_field($post_data['wppl_method']);

        if( method_exists($this, $method) ) {

            if( wp_verify_nonce($post_data[$method.'_nonce'], $method.'_action') ) {

                $this->$method($post_data);
            
            } else {
            
                // redirect the user and notify an error
                $redirect_url = esc_url_raw($post_data['_wp_http_referer']);
                $code = 302;
                $message = 'Failed to update settings, your security nonce was invalid.';
                $type = 'error';
                $this->redirect_with_message($redirect_url, $code, $message, $type);
            
            }
        
        }

    }

	
	/**
	 * end_sessions
	 *
	 * @param  array $post_data
	 * @return void
	 */
	protected function end_sessions($post_data) {
			
        $value = sanitize_text_field($post_data['value']);

        if( $value === 'true' ) {

            // end all sessions
            $wp_session_token = WP_Session_Tokens::get_instance(get_current_user_id());
            $wp_session_token->destroy_all_for_all_users();

			// clear the login user count
			$user_count = get_option('persistent_login_user_count');
			foreach( $user_count as $key => $value ) {
				$user_count[$key] = 0;
			}
			$update_roles = update_option('persistent_login_user_count', $user_count);

			if( $update_roles ) {
				// redirect the user and notify setting updated
				$redirect_url = esc_url_raw($post_data['_wp_http_referer']);
				$code = 302;
				$message = 'All users have been logged out! You will have to log back in now.';
				$this->redirect_with_message($redirect_url, $code, $message);
			}
    
            

        }
     

	}

    
    /**
     * update_general_settings
     *
     * @return void
     */
    protected function update_general_settings($post_data) {

        // update control for hiding dashboard stats
        $this->update_dashboard_stats($post_data);

        // update control for plugin specific logic
        $this->update_duplicate_sessions($post_data);

    }

	
	/**
	 * update_active_login_settings
	 *
	 * @param  mixed $post_data
	 * @return void
	 */
	protected function update_active_login_settings($post_data) {

		// update user preferences for active logins
		$this->update_limit_active_logins($post_data);

		// update the logic when login limit is reached
        $this->update_limit_reached_logic($post_data);
		
	}

    
    /**
     * update_dashboard_stats
     *
     * @param  string $setting
     * @return bool
     */
    protected function update_dashboard_stats($post_data) {

        if( isset($post_data['hidedashboardstats']) ) {
            $hide_dashboard_stats = sanitize_text_field($post_data['hidedashboardstats']);
        } else {
            $hide_dashboard_stats = '0';
        }

        if( $hide_dashboard_stats === '1' ) :								
            update_option('persistent_login_dashboard_stats', $hide_dashboard_stats);
        else : 
             update_option('persistent_login_dashboard_stats', '0');	 	
        endif;

        return true;

    }

    
    /**
     * get_dashboard_stats
     *
     * @return string
     */
    public function get_dashboard_stats() {

        $dashboard_stats = get_option('persistent_login_dashboard_stats');
        return $dashboard_stats;

    }


	/**
     * get_persistent_login_options
     *
     * @return array
     */
    protected function get_persistent_login_options() {

        $options = get_option('persistent_login_options');
        return $options;

    }


    
    /**
     * update_duplicate_sessions
     *
     * @param  array $post_data
     * @return void
     */
    protected function update_duplicate_sessions($post_data) {

        $options = $this->get_persistent_login_options();

        // duplicate sessions
        if( isset($post_data['duplicateSessions']) ) {
            $duplicate_sessions = sanitize_text_field($post_data['duplicateSessions']);
        } else {
            $duplicate_sessions = '0';
        }
        $options['duplicateSessions'] = $duplicate_sessions;
                
        return update_option('persistent_login_options', $options);

    }

    
    
    /**
     * get_duplicate_sessions
     *
     * @return string
     */
    public function get_duplicate_sessions() {

        $options = $this->get_persistent_login_options();
        if( isset($options['duplicateSessions']) ) {
            return $options['duplicateSessions'];
        } else {
            return '0';
        }

    }



	/**
     * update_limit_active_logins
     *
     * @param  array $post_data
     * @return bool
     */
    protected function update_limit_active_logins($post_data) {

        $options = $this->get_persistent_login_options();

        // duplicate sessions
        if( isset($post_data['limitActiveLogins']) ) {
            $limit_active_logins = sanitize_text_field($post_data['limitActiveLogins']);
        } else {
            $limit_active_logins = '0';
        }
        $options['limitActiveLogins'] = $limit_active_logins;
                
        return update_option('persistent_login_options', $options);

    }


	    
    /**
     * get_limit_active_logins
     *
     * @return string
     */
    public function get_limit_active_logins() {

        $options = $this->get_persistent_login_options();
        if( isset($options['limitActiveLogins']) ) {
            return $options['limitActiveLogins'];
        } else {
            return '0';
        }

    }



	/**
     * get_limit_reached_logic
     *
     * @return string
     */
    public function get_limit_reached_logic() {
        
        $options = $this->get_persistent_login_options();
        if( isset( $options['activeLoginLogic'] ) ) {
            $active_login_logic = $options['activeLoginLogic'];
        } else {
            $active_login_logic = 'automatic';
        }

        return $active_login_logic;

    }



    /**
     * update_limit_reached_logic
     *
     * @param  array $post_data
     * @return bool
     */
    private function update_limit_reached_logic($post_data) {

        if( isset($post_data['activeLoginLogic']) ) : 
							    
            $logic = $post_data['activeLoginLogic'];
            $options = $this->get_persistent_login_options();
            $options['activeLoginLogic'] = $logic;

			return update_option('persistent_login_options', $options);

        endif;

    }



    /**
	 * output_login_count_meta_box
	 *
	 * @param  bool $premium
	 * @return string
	 */
	public function output_login_count_meta_box() {

		?>

		<div class="postbox-container" style="max-width: 500px;">
			<div class="metabox-holder"> 
						
				<div class="postbox" style="margin-bottom: 1rem;">
					<div class="inside">
						
						<h3><?php _e('Usage', 'wp-persistent-login' ); ?></h3>

						<?php
							$count = new WP_Persistent_Login_User_Count();
							if( $count->is_user_count_running() ) {
								echo sprintf('<p>%s</p>', $count->output_current_counting_role());
							}
							echo sprintf('<p>%s</p>', $count->output_loggedin_user_count());
						?>
						
						<strong style="margin-bottom: 5px; display: block;">
							<?php _e('Usage Breakdown:', 'wp-persistent-login' ); ?>
						</strong>
						<?php echo $count->output_user_count_breakdown(); ?>
						
						<?php if( WPPL_PR === false ) : ?>
							<p style="clear: both; display: block;">
								<small>
									<?php 
										_e(
											'Did you know you can control which user roles are kept logged in by upgrading?', 'wp-persistent-login' ); 
									?>
								</small>
							</p>
						<?php endif; ?>
						
						<?php echo $count->output_next_count(); ?>
																								
					</div>
				</div>

				<!-- end all sessions -->
				<form method="POST">

					<?php wp_nonce_field( 'end_sessions_action', 'end_sessions_nonce' ); ?>
					<input type="hidden" name="wppl_method" value="end_sessions" />
					<input type="hidden" name="value" value="true" />

					<input type="submit" name="sessions" id="sessions" value="End all sessions" class="button"><br/>
					<p style="margin-top: 0;">
						<small>
							<?php 
								_e(
									'If you end all sessions, all users will be logged out of the website (including you).', 
									 'wp-persistent-login' ); 
							?>
						</small>
					</p>

				</form>
				<!-- END end all sessions -->
				
			</div>
		</div>
		
		<div style="display: block; clear: both;"></div>

		<?php

	}


	

		
	/**
	 * persistent_login_options_display
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function persistent_login_options_display() {


		if( isset($_GET['view']) ) {
			
			// updated db version
			if( $_GET['view'] == 'update' ) {
				$message = __('WordPress Persistent Login has been updated to the latest database version!', 'wp-persistent-login' );
				$class = 'notice updated';
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
			}
		
		}


		?>

	
		<div class="wrap">
			
			<h1><?php _e('WordPress Persistent Login', 'wp-persistent-login' ); ?></h1>
			<h2 style="float: left; margin-top: 0;"><?php _e('Free Forever Plan', 'wp-persistent-login' ); ?></h2>
			
			<div style="float: right;">
				<p>
					<a href="<?php echo WPPL_ACCOUNT_PAGE; ?>" class="button">
						<?php _e('My Account', 'wp-persistent-login' ); ?>
					</a>
					<a href="<?php echo WPPL_UPGRADE_PAGE; ?>" class="button">
						<?php _e('Manage my plan', 'wp-persistent-login' ); ?>
					</a>
					<a href="<?php echo WPPL_SUPPORT_PAGE; ?>" class="button">
						<?php _e('Support', 'wp-persistent-login' ); ?>
					</a>
				</p>
			</div>
			<div class="clear"></div>	
			
			<div class="main-content" style="width: calc(100% - 270px); float: left;">
						
				<?php
					$default_tab = NULL;
					$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
				?>
				<nav class="nav-tab-wrapper">
					<a 
						href="<?php echo WPPL_SETTINGS_PAGE; ?>" 
						class="nav-tab <?php echo ( $tab === NULL ) ? 'nav-tab-active' : ''; ?>"
					>
						<?php _e('Dashboard', 'wp-persistent-login' ); ?>
					</a>
					<a 
						href="<?php echo WPPL_SETTINGS_PAGE; ?>&tab=persistent-login" 
						class="nav-tab <?php echo ( $tab === 'persistent-login' ) ? 'nav-tab-active' : ''; ?>"
					>
						<?php _e('Persistent Login', 'wp-persistent-login' ); ?>
					</a>
					<a 
						href="<?php echo WPPL_SETTINGS_PAGE; ?>&tab=active-logins" 
						class="nav-tab <?php echo ( $tab === 'active-logins' ) ? 'nav-tab-active' : ''; ?>"
					>
						<?php _e('Active Logins', 'wp-persistent-login' ); ?>
					</a>
				</nav>

				<div class="tab-content">
					<?php if( !isset($tab) ) : ?>

						<h1>Dashboard</h1>
						<p>
							<?php 
								_e(
									'Persistent login will keep all users logged in automatically. For free. Forever.', 
									 'wp-persistent-login' ); 
							?>
						</p>
						<?php $this->output_login_count_meta_box(); ?>

					
					<?php elseif( $tab === 'persistent-login' ) : ?>
					
						<h1>
							<?php 
								_e(
									'Persistent Login Settings', 
									 'wp-persistent-login' ); 
							?>
						</h1>
						<p>
							<?php 
								_e(
									'Control how users are kept logged into your website over time.', 
									 'wp-persistent-login' ); 
							?>
						</p>
						<form method="POST">
					
							<input type="hidden" name="wppl_method" value="update_general_settings" />
							<?php wp_nonce_field( 'update_general_settings_action', 'update_general_settings_nonce' ); ?>
							
							<table class="form-table">
								<tbody>   

									<!-- logged in time -->						
									<tr style="border-bottom: 1px solid #dfdfdf;">
									
										<th>
											<?php _e('Keep users logged in for', 'wp-persistent-login' ); ?>
										</th>
										<td>
											<?php _e('365 days', 'wp-persistent-login' ); ?>
											<p class="description">
												<small>
													<?php _e('To change the remember me duration and which roles it applies to, please consider upgrading.', 'wp-persistent-login' ); ?>
												</small>
											</p>
										</td>
									</tr>
									<!-- END loggedin time -->
							
							
									<!-- dashboard at a glance screen -->						
									<tr style="border-bottom: 1px solid #dfdfdf;">
									
										<th><br/>
											<?php _e('Dashboard panel options', 'wp-persistent-login' ); ?><br/>
										</th>
										<td>
											<br/>
											<label style="width: auto; display: inline-block;">
												<?php $hide_dashboard_stats = $this->get_dashboard_stats(); ?> 
												<input 
													name="hidedashboardstats" id="hidedashboardstats" type="checkbox" value="1" 
													class="regular-checkbox" <?php echo ($hide_dashboard_stats !== '0') ? 'checked' : ''; ?>
												/> 
												<?php _e('Hide \'At a glance\' dashboard stats', 'wp-persistent-login' ); ?>
											</label><br/>
											<br/>
										</td>
									</tr>
									<!-- END dashboard at a glance screen -->							
							
									<!-- allow duplicate sessions -->
									<?php $duplicate_sessions = $this->get_duplicate_sessions(); ?> 
									<tr style="border-bottom: 1px solid #dfdfdf;">
										<th>
											<br/> 
											<?php _e('Duplicate sessions', 'wp-persistent-login' ); ?><br/>
										</th>
										<td>
											<br/>
											<label style="width: auto; display: inline-block;">
												<input 
													name="duplicateSessions" id="duplicateSessions" type="checkbox" value="1" 
													class="regular-checkbox" <?php echo ($duplicate_sessions === '0' || $duplicate_sessions === NULL ) ? '' : 'checked'; ?>
												/>
												<?php _e('Allow duplicate sessions', 'wp-persistent-login' ); ?>
											</label><br/>
											<p class="description">
												<small>
													<?php _e('(select if you\'re having trouble staying logged in on multiple devices)', 'wp-persistent-login' ); ?>
												</small>
											</p>
										</td> 
									</tr>
									<!-- END allow duplicate sessions -->										              
						
								</tbody>
							</table>
							<p class="submit">
								<input 
									type="submit" name="submit" id="submit" class="button button-primary" 
									value="<?php _e('Save Persistent Login Settings', 'wp-persistent-login' ); ?>"
								>
							</p>
						</form>
					
					<?php elseif( $tab === 'active-logins' ) : ?>
					
						<h1>
							<?php 
								_e(
									'Active Login Settings', 
									 'wp-persistent-login' ); 
							?>
						</h1>
						<p>
							<?php 
								_e(
									'Control how many active logins users can have at any one time.', 
									 'wp-persistent-login' ); 
							?>
						</p>

						<form method="POST">
					
							<input type="hidden" name="wppl_method" value="update_active_login_settings" />
							<?php wp_nonce_field( 'update_active_login_settings_action', 'update_active_login_settings_nonce' ); ?>
						
							<table class="form-table">
								<tbody>

									<!-- enable active login limit -->
									<?php $limit_active_logins = $this->get_limit_active_logins(); ?> 
									<tr style="border-bottom: 1px solid #dfdfdf;">
										<th>
											<?php _e('Limit active logins', 'wp-persistent-login' ); ?><br/>
										</th>
										<td>
											<label style="width: auto; display: inline-block;">
												<input 
													name="limitActiveLogins" id="limitActiveLogins" type="checkbox" value="1" 
													class="regular-checkbox" <?php echo ($limit_active_logins === '0' || $limit_active_logins === NULL ) ? '' : 'checked'; ?>
												/>
												<?php _e('Limit users to <strong>1 active login</strong>', 'wp-persistent-login' ); ?>
											</label><br/>
											<p style="padding-top: 0.5rem;">
												<?php _e('When a user reaches the active login limit, they will automatically be logged out from their oldest session.', 'wp-persistent-login' ); ?>
											</p>
											<br/>
											<p class="description">
												<small>
													<?php _e('To change the active logins limit, which roles it applies to and let users select which session to end, please consider upgrading.', 'wp-persistent-login' ); ?>
												</small>
											</p>
											
										</td>
									</tr>
									<!-- END enable active login limit -->

									<!-- limit reached -->
                                    <?php 
                                        $limit_reached_logic = $this->get_limit_reached_logic(); 
                                        $limit_reached_options = array(
                                            array(
                                                'label' => __('Automatically end the oldest active login for the user.', 'wp-persistent-login' ),
                                                'value' => 'automatic'
                                            ),
                                            array(
                                                'label' => __( 'Block new logins if active login limit reached.', 'wp-persistent-login' ),
                                                'value' => 'block'
                                            )
                                        );
                                    ?>
                                    <tr style="border-bottom: 1px solid #dfdfdf;">
                                        <th>
                                            <?php _e('Limit reached logic', 'wp-persistent-login' ); ?><br/>
                                        </th>
                                        <td>
                                            <?php foreach( $limit_reached_options as $option ) : ?>
                                                <label style="width: auto; display: inline-block; margin-bottom: 10px;">
                                                    <input 
                                                        name="activeLoginLogic" 
                                                        id="<?php echo $option['value']; ?>" 
                                                        type="radio" 
                                                        value="<?php echo $option['value']; ?>" 
                                                        class="regular-radio js-maximum-logins-logic" 
                                                        <?php echo ( $limit_reached_logic === $option['value'] ) ? ' checked' : ''; ?>
                                                    /> 
                                                    <?php echo $option['label']; ?>
                                                </label><br/>
                                            <?php endforeach; ?>
                                        </td>
                                    </tr>
                                    <!-- END limit reached -->

									<!-- manage active logins -->
									<tr style="border-bottom: 1px solid #dfdfdf;">
										<th>
											<?php _e('Manage Active Logins', 'wp-persistent-login' ); ?><br/>
										</th>
										<td>
											<p>
												<?php _e('You can manage your own active logins from your profile page in the dashboard.', 'wp-persistent-login' ); ?>
											</p>
											<br/>
											<p>
												<a href="<?php echo admin_url(); ?>profile.php#sessions" class="button button-primary">
													<?php _e('Manage your active logins', 'wp-persistent-login' ); ?>
												</a>
												&nbsp;<?php _e('or', 'wp-persistent-login' ); ?>&nbsp;
												<a href="<?php echo persistent_login()->get_upgrade_url(); ?>&trial=true" class="button ">
													<?php _e('Upgrade', 'wp-persistent-login' ); ?>
												</a>
											</p>
											<br/>
											<p class="description">
												<small>
													<?php _e('To manage all active logins & allow users to manage their own active logins from the front-end, please consider upgrading.', 'wp-persistent-login' ); ?>
												</small>
											</p>
										</td>
									</tr>
									<!-- END manage sessions -->

								</tbody>
							</table>
							<p class="submit">
								<input 
									type="submit" name="submit" id="submit" class="button button-primary" 
									value="<?php _e('Save Active Login Settings', 'wp-persistent-login' ); ?>"
								>
							</p>
						</form>
					
					<?php endif; ?>	
				</div>

			</div>

			<div class="postbox-container sidebar" style="max-width: 250px; float: right;">
				<div class="metabox-holder"> 
					<div class="postbox">
						<div class="inside">
							<h3 style="margin-top: 1rem; cursor: auto;">Want a new feature?</h3>
							<p>If you'd like to see a new feature on WordPress Persistent Login, just request it by clicking the button below and <strong>choose the Feature Request option</strong>.</p>
							<a href="<?php echo admin_url(); ?>options-general.php?page=wp-persistent-login-contact" class="button">
								Request a Feature
							</a>
						</div>
					</div>
					<div class="postbox">
						<div class="inside">
							<h3>Try premium for 7 days, free</h3>
							<p>Persistent Login is great, but we've made it even better!</p>
							<p>If you love Persistent Login, but want more control, have a look at the <a href="<?php echo persistent_login()->get_upgrade_url(); ?>">features in our premium version</a>. </p>
							<p style="line-height: 40px;">	    	
								<a href="<?php echo persistent_login()->get_upgrade_url(); ?>&trial=true" class="button button-primary">
									7 Day Free Trial
								</a>
								&nbsp; or &nbsp;
								<a href="<?php echo persistent_login()->get_upgrade_url(); ?>" class="button">
									Purchase Premium
								</a>
							</p>
						</div>
					</div>
				</div>
			</div>

			<div style="clear:both; display: block;"></div>
							
		</div>

		<style>
			.tab-content {
				padding: 2.5%;
			}
			@media all and ( max-width: 1100px ) {
				.main-content {
					width: 100%;
					float: none;
				}
				.sidebar {
					float: none;
				}
			}
		</style>
		<?php
	
		
	} // end persistent_login_options_display	 
	

}

?>