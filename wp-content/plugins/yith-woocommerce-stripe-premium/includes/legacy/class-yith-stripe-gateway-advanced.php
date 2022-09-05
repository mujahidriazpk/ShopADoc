<?php
/**
 * Main class
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.0.0
 */

use \Stripe\Error;

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCStripe_Gateway_Advanced' ) ) {
	/**
	 * WooCommerce Stripe gateway class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCStripe_Gateway_Advanced extends YITH_WCStripe_Gateway {

		/** @var \Stripe\Customer */
		protected $_current_customer = null;

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCStripe_Gateway_Advanced
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			// gateway properties
			$this->order_button_text  = $this->get_option( 'button_label', __( 'Place order', 'yith-woocommerce-stripe' ) );
			$this->supports           = array(
				'products',
				'default_credit_card_form',
				'refunds'
			);

			// Add premium options
			$this->init_premium_fields();

			// Define user set variables
			$this->mode              = $this->get_option( 'mode', 'standard' );
			$this->debug             = strcmp( $this->get_option( 'debug' ), 'yes' ) == 0;
			$this->save_cards        = strcmp( $this->get_option( 'save_cards', 'yes' ), 'yes' ) == 0;
			$this->capture           = strcmp( $this->get_option( 'capture', 'no' ), 'yes' ) == 0;
			$this->bitcoin           = strcmp( $this->get_option( 'enable_bitcoin', 'no' ), 'yes' ) == 0 && strcmp( WC()->countries->get_base_country(), 'US' ) == 0 && strcmp( get_woocommerce_currency(), 'USD' ) == 0 && $this->mode == 'hosted';
			$this->elements_show_zip = strcmp( $this->get_option( 'elements_show_zip', 'yes' ), 'yes' ) == 0;

			// Logs
			if ( $this->debug ) {
				$this->log = new WC_Logger();
			}

			// hooks
			add_filter( 'woocommerce_credit_card_form_fields', array( $this, 'credit_form_add_fields' ), 10, 2 );
			add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
			add_filter( 'woocommerce_stripe_hosted_args', array( $this, 'advanced_stripe_checkout_args' ), 10, 2 );
		}

		/**
		 * Initialize form fields for the admin
		 *
		 * @since 1.0.0
		 */
		public function init_premium_fields() {

			$this->add_form_field( array(
				'capture' => array(
					'title'       => __( 'Capture', 'yith-woocommerce-stripe' ),
					'type'        => 'select',
					'description' => sprintf( __( 'Decide whether to immediately capture the charge or not. When "Authorize only & Capture later" is selected, the charge issues an authorization (or pre-authorization), and will be captured later. Uncaptured charges expire in %2$s7 days%3$s. %1$sFor further information, see %4$sauthorizing charges and settling later%5$s.', 'yith-woocommerce-stripe' ),
						'<br />',
						'<b>',
						'</b>',
						'<a href="https://support.stripe.com/questions/can-i-authorize-a-charge-and-then-wait-to-settle-it-later" target="_blank">',
						'</a>'
					),
					'default'     => 'no',
					'options'     => array(
						'no'  => __( 'Authorize only & Capture later', 'yith-woocommerce-stripe' ),
						'yes' => __( 'Authorize & Capture immediately', 'yith-woocommerce-stripe' )
					)
				),

				'mode' => array(
					'title'       => __( 'Payment Mode', 'yith-woocommerce-stripe' ),
					'type'        => 'select',
					'description' => sprintf( __( 'Standard will display credit card fields on your store (SSL required). %s Stripe checkout will redirect the user to the checkout page hosted in Stripe.', 'yith-woocommerce-stripe' ), '<br />' ),
					'default'     => 'standard',
					'options'     => array(
						'standard'  => __( 'Standard', 'yith-woocommerce-stripe' ),
						'hosted'    => __( 'Stripe Checkout', 'yith-woocommerce-stripe' ),
						'elements' => __( 'Stripe Elements', 'yith-woocommerce-stripe' )
					)
				),

				'save_cards' => array(
					'title'       => __( 'Save cards', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable "Remember cards"', 'yith-woocommerce-stripe' ),
					'description' => __( "Save users' credit cards to let them use them for future payments.", 'yith-woocommerce-stripe' ),
					'default'     => 'yes'
				),

				'add_billing_fields' => array(
					'title'       => __( 'Add billing fields', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'description' => __( "If you have installed any WooCommerce extension to edit checkout fields, this option allows you require some necessary information associated to the credit card, in order to further reduce the risk of fraudulent transactions.", 'yith-woocommerce-stripe' ),
					'default'     => 'no'
				),

				'enable_bitcoin' => array(
					'title'       => __( 'Accepting Bitcoin', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'label'       => __( 'Accepting Bitcoin', 'yith-woocommerce-stripe' ),
					'description' => __( 'Option available only for "Stripe Checkout" payment mode. Stripe supports accepting Bitcoin alongside payments with credit cards.', 'yith-woocommerce-stripe' ) . '<br />'
				                     . __( 'You currently need a <b>United States bank account</b> to accept Bitcoin payments.', 'yith-woocommerce-stripe' ),
					'default'     => 'no',
					'disabled'    => strpos( 'US', WC()->countries->get_base_country() ) === false
				),

				'show_name_on_card' => array(
					'title'       => __( 'Show Name on Card', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'description' => __( 'Show Name on Card field in Elements and Standard form; Name will be sent within card data, to let Stripe perform additional check over user and better evaluate risk', 'yith-woocommerce-stripe' ),
					'default'     => 'yes'
				),

				'elements_show_zip' => array(
					'title'       => __( 'Show Zip Field', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'description' => __( 'Show Zip field in Elements form; ZIP code will be sent within card data, to let Stripe perform additional check over user and better evaluate risk', 'yith-woocommerce-stripe' ),
					'default'     => 'yes'
				)
			), 'after', 'description' );

			$this->add_form_field( array(
				'button_label' => array(
					'title'       => __( 'Button label', 'yith-woocommerce-stripe' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => __( 'Define the label for the button on checkout.', 'yith-woocommerce-stripe' ),
					'default'     => __( 'Placeholder.', 'yith-woocommerce-stripe' )
				),
			), 'after', 'customization' );

			$this->add_form_field( array(
				'debug' => array(
					'title'       => __( 'Debug Log', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable logging', 'yith-woocommerce-stripe' ),
					'default'     => 'no',
					'description' => sprintf( __( 'Log Stripe events inside <code>%s</code>', 'yith-woocommerce-stripe' ), wc_get_log_file_path( 'stripe' ) ) . '<br />' . sprintf( __( 'You can also consult the logs in your <a href="%s">Logs Dashboard</a>, without checking this option.', 'yith-woocommerce-stripe' ), 'https://dashboard.stripe.com/logs' )
				),
			), 'after', 'enabled_test_mode' );

			$this->add_form_field( array(
				'webhooks'        => array(
					'title'       => __( 'Config Webhooks', 'yith-woocommerce-stripe' ),
					'type'        => 'title',
					'description' => sprintf( __( 'You can configure the webhook url %s in your <a href="%s">application settings</a>. All the webhooks for all your connected users will be sent to this endpoint.', 'yith-woocommerce-stripe' ), '<code>' . esc_url( add_query_arg( 'wc-api', 'stripe_webhook', site_url( '/' ) ) ) . '</code>', 'https://dashboard.stripe.com/account/applications/settings' ) . '<br /><br />'
					                 . __( "It's important to note that while only test webhooks will be sent to your development webhook url, <b>both live and test</b> webhooks will be sent to your production webhook url. This is due to the fact that you can create both live and test objects under a production application", 'yith-woocommerce-stripe' ) . ' — ' . __( "we'd recommend that you check the livemode when receiving an event webhook.", 'yith-woocommerce-stripe' ) . '<br /><br />'
									 . sprintf( __( 'For more information about webhooks, see the <a href="%s">webhook documentation</a>', 'yith-woocommerce-stripe' ), 'https://stripe.com/docs/webhooks' ),
				),
			), 'after', 'live_publishable_key' );

			$this->add_form_field( array(
				'security'         => array(
					'title'       => __( 'Security', 'yith-woocommerce-stripe' ),
					'type'        => 'title',
					'description' => __( 'Enable here the testing mode, to debug the payment system before going into production', 'yith-woocommerce-stripe' ),
				),
				'enable_blacklist'    => array(
					'title'   => __( 'Enable Blacklist', 'yith-woocommerce-stripe' ),
					'type'    => 'checkbox',
					'label'   => __( 'Hide gateway payment on frontend if the same user or the same IP address have already failed a payment. The blacklist table is available on WooCommerce -> Stripe Blacklist', 'yith-woocommerce-stripe' ),
					'default' => 'no'
				),
			), 'after', 'modal_image' );

		}

		/**
		 * Handling payment and processing the order.
		 *
		 * @param int $order_id
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );
			$this->_current_order = $order;
			$this->log( 'Generating payment form for order ' . $order->get_order_number() . '.' );

//			if ( 'hosted' == $this->mode ) {
//				return $this->process_hosted_payment();
//			} else {
				return $this->process_standard_payment();
//			}
		}

		/**
		 * Method to check blacklist (only for premium)
		 *
		 * @since 1.1.3
		 *
		 * @param bool $user_id
		 * @param bool $ip
		 *
		 * @return bool
		 */
		public function is_blocked( $user_id = false, $ip = false ) {
			if ( $this->get_option( 'enable_blacklist', 'no' ) == 'no' ) {
				return false;
			}

			global $wpdb;

			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $ip ) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			$res = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM {$wpdb->yith_wc_stripe_blacklist} WHERE ( user_id = %d OR ip = %s ) AND unbanned = 0", $user_id, $ip ) );

			return $res > 0 ? true : false;
		}

		/**
		 * Check if the user is unbanned by admin
		 *
		 * @param bool $user_id
		 * @param bool $ip
		 *
		 * @return bool
		 */
		public function is_unbanned( $user_id = false, $ip = false ) {
			if ( $this->get_option( 'enable_blacklist', 'no' ) == 'no' ) {
				return false;
			}

			global $wpdb;

			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $ip ) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			$res = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->yith_wc_stripe_blacklist} WHERE ( user_id = %d OR ip = %s ) AND unbanned = %d", $user_id, $ip, 1 ) );

			return $res > 0 ? true : false;
		}

		/**
		 * Say if the user in parameter have already purchased properly previously
		 *
		 * @since 1.1.3
		 *
		 * @param bool $user_id
		 *
		 * @return bool
		 */
		public function have_purchased( $user_id = false ) {
			global $wpdb;

			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status IN ( %s, %s ) AND post_author = %d", 'wc-completed', 'wc-processing', $user_id ) );
			return $count > 0 ? true : false;
		}

		/**
		 * Register the block on blacklist
		 *
		 * @since 1.1.3
		 *
		 * @param array $args
		 *
		 * @return bool
		 *
		 */
		public function add_block( $args = array() ) {
			extract( wp_parse_args( $args,
				array(
					'user_id' => get_current_user_id(),
					'ip' => $_SERVER['REMOTE_ADDR'],
					'order_id' => 0,
					'ua' => ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : ''
				)
			) );

			if ( $this->get_option( 'enable_blacklist', 'no' ) == 'no' || $this->have_purchased( $user_id ) || $this->is_blocked( $user_id, $ip ) || $this->is_unbanned( $user_id, $ip ) ) {
				return false;
			}

			global $wpdb;

			// add the user and the ip
			$wpdb->insert( $wpdb->yith_wc_stripe_blacklist, array(
				'user_id' => $user_id,
				'ip' => $ip,
				'order_id' => $order_id,
				'ua' => $ua,
				'ban_date' => current_time( 'mysql' ),
				'ban_date_gmt' => current_time( 'mysql', 1 )
			) );

			return true;
		}

		/**
		 * Handling payment and processing the order.
		 *
		 * @param WC_Order $order
		 *
		 * @return array
		 * @throws Error\Api
		 * @since 1.0.0
		 */
		protected function process_standard_payment( $order = null ) {
			if ( empty( $order ) ) {
				$order = $this->_current_order;
			}

			try {

				// Initializate SDK and set private key
				$this->init_stripe_sdk();

				// Card selected during payment
				$selected_card = $this->get_credit_card_num();

				// Set the token with card ID selected
				if ( $this->save_cards && 'new' != $selected_card && empty( $this->token ) ) {
					$this->token = $selected_card;
				}

				if ( empty( $this->token ) ) {
					$error_msg = __( 'Please make sure that your card details have been entered correctly and that your browser supports JavaScript.', 'yith-woocommerce-stripe' );

					if ( 'test' == $this->env ) {
						$error_msg .= ' ' . __( 'Developers: Please make sure that you\'re including jQuery and that there are no JavaScript errors in the page.', 'yith-woocommerce-stripe' );
					}

					$this->log( 'Wrong token ' . $this->token . ': ' . print_r( $_POST, true ) );

					throw new Error\Api( $error_msg );
				}

				// pay
				$response = $this->pay( $order );

				if ( $response === true ) {
					$response = array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order )
					);

				} elseif ( is_a( $response, 'WP_Error' ) ) {
					throw new Error\Api( $response->get_error_message( 'stripe_error' ) );
				}

				return $response;

			} catch ( Error\Base $e ) {
				$body = $e->getJsonBody();
				$message = $e->getMessage();

				if ( $body ) {
					$err     = $body['error'];
					if ( isset( $this->errors[ $err['code'] ] ) ) {
						$message = $this->errors[ $err['code'] ];
					}

					$this->log( 'Stripe Error: ' . $e->getHttpStatus() . ' - ' . print_r( $e->getJsonBody(), true ) );

					// add order note
                    $order->add_order_note( yith_wcstripe_error_message_order_note_call( $e, $err ) );

					// add block if there is an error on card
					if ( $err['type'] == 'card_error' ) {
						$this->add_block( "order_id={$order->id}" );
						WC()->session->refresh_totals = true;
					}
				}

				wc_add_notice( $message, 'error' );

				return array(
					'result'   => 'fail',
					'redirect' => ''
				);

			}
		}

		/**
		 * Get credit card number from post
		 *
		 * @access protected
		 * @return string
		 * @author Francesco Licandro
		 */
		protected function get_credit_card_num() {

			$card = isset( $_POST['wc-yith-stripe-payment-token'] ) ? $_POST['wc-yith-stripe-payment-token'] : 'new';

			return apply_filters( 'yith_stripe_selected_card', $card );
		}

		/**
		 * Performs the payment on Stripe
		 *
		 * @param $order  WC_Order
		 *
		 * @return array|WP_Error
		 * @since 1.0.0
		 */
		protected function pay( $order = null, $amount = null ) {
			// Initializate SDK and set private key
			$this->init_stripe_sdk();

			// get amount
			$amount = ! is_null( $amount ) ? $amount : $order->get_total();

			if ( 0 == $amount ) {
				// Payment complete
				$order->payment_complete();

				return true;
			}

			if ( $amount * 100 < 50 ) {
				return new WP_Error( 'stripe_error', __( 'Sorry, the minimum allowed order total is 0.50 to use this payment method.', 'yith-woocommerce-stripe' ) );
			}

			$params = array(
				'amount'      => YITH_WCStripe::get_amount( $amount ), // Amount in cents!
				'currency'    => strtolower( $order->get_order_currency() ? $order->get_order_currency() : get_woocommerce_currency() ),
				'source'      => $this->token,
				'description' => apply_filters( 'yith_wcstripe_charge_description', sprintf( __( '%s - Order %s', 'yith-woocommerce-stripe' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ),
				'capture'     => $this->capture,
				'metadata'    => apply_filters( 'yith_wcstripe_metadata', array(
					'order_id'    => $order->id,
					'order_email' => $order->billing_email,
					'instance'    => $this->instance,
				), 'charge' )
			);

			// set customer if there is one
			if ( $this->save_cards && is_user_logged_in() ) {
				$customer = $this->get_customer( $order );
				$params['customer'] = $customer->id;

				// Card selected during payment
				$selected_card = $this->get_credit_card_num();

				// If new credit card, set it
				if ( 'new' == $selected_card ) {
					$params['source'] = $this->token;
				}
			}

			$this->log( 'Stripe Request: ' . print_r( $params, true ) );

			$charge = $this->api->charge( $params );

			$this->log( 'Stripe Response: ' . print_r( $charge, true ) );

			// Payment complete
			$order->payment_complete( $charge->id );

			// Add order note
			$order->add_order_note( sprintf( __( 'Stripe payment approved (ID: %s)', 'yith-woocommerce-stripe' ), $charge->id ) );

			// Remove cart
			WC()->cart->empty_cart();

			// update post meta
			update_post_meta( $order->id, '_captured', ( $charge->captured ? 'yes' : 'no' ) );

			// Return thank you page redirect
			return true;
		}

		/**
		 * Extend arguments for Stripe checkout
		 *
		 * @since 1.0.0
		 *
		 * @param $args
		 * @param $order_id
		 *
		 * @return
		 */
		public function advanced_stripe_checkout_args( $args, $order_id ) {
			if ( $this->bitcoin ) {
				$args['bitcoin'] = 'true';
			}

			return $args;
		}

		/**
		 * Process refund
		 *
		 * Overriding refund method
		 *
		 * @access      public
		 * @param       int $order_id
		 * @param       float $amount
		 * @param       string $reason
		 * @return      mixed True or False based on success, or WP_Error
		 */
		public function process_refund( $order_id, $amount = null, $reason = '' ) {
			$order = wc_get_order( $order_id );
			$transaction_id = $order->get_transaction_id();
			$captured = strcmp( $order->captured, 'yes' ) == 0;

			if ( ! $transaction_id ) {
				return new WP_Error( 'yith_stripe_no_transaction_id',
					sprintf(
						__( "There isn't any charge linked to this order", 'yith-woocommerce-stripe' )
					)
				);
			}

			if ( isset( $order->bitcoin_inbound_address ) || isset( $order->bitcoin_uri ) ) {
				return new WP_Error( 'yith_stripe_no_bitcoin',
					sprintf(
						__( "Refund not supported for bitcoin", 'yith-woocommerce-stripe' )
					)
				);
			}

			try {

				// Initializate SDK and set private key
				$this->init_stripe_sdk();

				$params = array();

				// get the last refund object created before to process this method, to get own object
				$refund = array_shift( $order->get_refunds() );

				// If the amount is set, refund that amount, otherwise the entire amount is refunded
				if ( $amount ) {
					$params['amount'] = YITH_WCStripe::get_amount( $amount );
				}

				// If a reason is provided, add it to the Stripe metadata for the refund
				if ( $reason AND in_array( $reason, array( 'duplicate', 'fraudulent', 'requested_by_customer' ) ) ) {
					$params['reason'] = $reason;
				}

				$this->log( 'Stripe Refund Request: ' . print_r( $params, true ) );

				// Send the refund to the Stripe API
				$stripe_refund = $this->api->refund( $transaction_id, $params );
				update_post_meta( $refund->id, '_refund_stripe_id', $stripe_refund->id );

				$this->log( 'Stripe Refund Response: ' . print_r( $stripe_refund, true ) );

				return true;

			} catch ( Error\Card $e ) {
				$body = $e->getJsonBody();
				$err  = $body['error'];
				$message = isset( $this->errors[ $err['code'] ] ) ? $this->errors[ $err['code'] ] : $err['message'];

				$order->add_order_note(
					sprintf(
						__( 'Stripe Credit Card Refund Failed with message: "%s"', 'yith-woocommerce-stripe' ),
						$message
					)
				);

				// Something failed somewhere, send a message.
				return new WP_Error( 'yith_stripe_refund_error', $e->getMessage() );
			}
		}

		/**
		 * Get customer ID of Stripe account from user ID
		 *
		 * @param $user_id
		 *
		 * @return integer
		 * @since 1.0.0
		 */
		public function get_customer_id( $user_id ) {
			$customer = YITH_WCStripe()->get_customer()->get_usermeta_info( $user_id );

			if ( ! isset( $customer['id'] ) ) {
				return 0;
			}

			return $customer['id'];
		}

		/**
		 * Get customer of Stripe account or create a new one if not exists
		 *
		 * @param $order WC_Order
		 * @return \Stripe\Customer
		 * @since 1.0.0
		 */
		public function get_customer( $order ) {
			if ( is_int( $order ) ) {
				$order = wc_get_order( $order );
			}

			if ( $this->_current_order->id == $order->id && ! empty( $this->_current_customer ) ) {
				return $this->_current_customer;
			}

			$user_id = $order->get_user_id();
			$customer = YITH_WCStripe()->get_customer()->get_usermeta_info( $user_id );

			// get existing
			if ( $customer ) {
				$customer = $this->api->get_customer( $customer['id'] );
				$selected_card = $this->get_credit_card_num();

				if ( 'new' == $selected_card ) {
					$user = $order->get_user();

					$card = $this->api->create_card( $customer, $this->token );
					$this->token = $card->id;

					try {
						$customer = $this->api->update_customer( $customer, array(
							'email'       => $order->billing_email,
							'description' => $user->user_login . ' (#' . $order->user_id . ' - ' . $user->user_email . ') ' . $order->billing_first_name . ' ' . $order->billing_last_name
						) );

					} catch( Exception $e ) {
						YITH_WCStripe()->customer->delete_usermeta_info( $user_id );
						$this->get_customer( $order );
					}

					// update user meta
					YITH_WCStripe()->get_customer()->update_usermeta_info( $user_id, array(
						'id'             => $customer->id,
						'cards'          => $customer->sources->data,
						'default_source' => $customer->default_source
					) );

					do_action( 'yith_wcstripe_created_card', $card->id, $customer );
				}

				if ( $this->_current_order->id == $order->id ) {
					$this->_current_customer = $customer;
				}

				return $customer;

			}

			// create new one
			else {

				$user = $order->get_user();

				$params = array(
					'source' => $this->token,
					'email' => $order->billing_email,
					'description' => $user->user_login . ' (#' . $order->user_id . ' - ' . $user->user_email . ') ' . $order->billing_first_name . ' ' . $order->billing_last_name,
					'metadata' => apply_filters( 'yith_wcstripe_metadata', array(
						'user_id' => $order->get_user_id(),
						'instance' => $this->instance
					), 'create_customer' )
				);

				$customer = $this->api->create_customer( $params );
				$this->token = $customer->default_source;

				// update user meta
				YITH_WCStripe()->get_customer()->update_usermeta_info( $user_id, array(
					'id'             => $customer->id,
					'cards'          => $customer->sources->data,
					'default_source' => $customer->default_source
				) );

				if ( $this->_current_order->id == $order->id ) {
					$this->_current_customer = $customer;
				}

				return $customer;

			}

		}

		/**
		 * Javascript library
		 *
		 * @since 1.0.0
		 */
		public function payment_scripts() {
			if ( ! $this->is_available() || ! is_checkout() && ! YITH_WCStripe()->is_add_new_card_endpoint() ) {
				return;
			}

			$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$wc_assets_path       = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

			// style
			if( in_array( $this->mode, array( 'standard', 'elements' ) ) ){
				wp_register_style( 'stripe-css', YITH_WCSTRIPE_URL . 'assets/css/stripe.css', array(), YITH_WCSTRIPE_VERSION );
				wp_enqueue_style( 'stripe-css' );
			}

			if ( 'standard' == $this->mode ) {
				wp_enqueue_style( 'woocommerce_prettyPhoto_css', $wc_assets_path . 'css/prettyPhoto.css' );
				wp_enqueue_script( 'prettyPhoto', $wc_assets_path . 'js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.5', true );
			}

			// scripts
			if ( 'hosted' == $this->mode ){
				wp_register_script( 'stripe-js', 'https://checkout.stripe.com/checkout.js', array('jquery'), YITH_WCSTRIPE_VERSION, true );
				wp_register_script( 'yith-stripe-js', YITH_WCSTRIPE_URL . 'assets/js/stripe-checkout.js', array('jquery', 'stripe-js'), YITH_WCSTRIPE_VERSION, true );
				wp_enqueue_script( 'yith-stripe-js' );

				wp_localize_script( 'yith-stripe-js', 'yith_stripe_info', array(
					'public_key'      => $this->public_key,
					'mode'            => $this->mode,
					'amount'          => YITH_WCStripe::get_amount( WC()->cart->total ),
					'currency'        => strtolower( get_woocommerce_currency() ),
					'name'            => esc_html( get_bloginfo( 'name' ) ),
					'description'     => '',
					'image'           => $this->modal_image,
					'bitcoin'         => esc_attr( $this->bitcoin ? 'true' : 'false' ),
					'capture'         => esc_attr( $this->capture ? 'true' : 'false' ),
					'locale'          => apply_filters( 'yith_stripe_locale', substr( get_locale(), 0, 2 ) ),
					'billing_email'   => wp_get_current_user()->billing_email,
					'refresh_details' => wp_create_nonce( 'refresh-details' ),
					'ajaxurl'               => admin_url( 'admin-ajax.php' ),
					'billing_address' => esc_attr( $this->hosted_billing ? 'true' : 'false' ),
					'shipping_address' => esc_attr( $this->hosted_shipping ? 'true' : 'false' ),
				) );
			}
            elseif( 'standard' == $this->mode ) {
				if ( 'test' == $this->env ) {
					wp_register_script( 'stripe-js', 'https://js.stripe.com/v2/stripe-debug.js', array( 'jquery' ), false, true );
				} else {
					wp_register_script( 'stripe-js', 'https://js.stripe.com/v2/', array( 'jquery' ), false, true );
				}

				wp_register_script( 'yith-stripe-js', YITH_WCSTRIPE_URL . 'assets/js/yiths.js', array( 'jquery', 'stripe-js' ), YITH_WCSTRIPE_VERSION, true );
				wp_enqueue_script( 'yith-stripe-js' );

				wp_localize_script( 'yith-stripe-js', 'yith_stripe_info', array(
					'public_key'  => $this->public_key,
					'mode'        => $this->mode,
					'card.name'   => __( 'A valid Name on Card is required.', 'yith-woocommerce-stripe' ),
					'card.number' => __( 'The credit card number appears to be invalid.', 'yith-woocommerce-stripe' ),
					'card.cvc'    => __( 'The CVC number appears to be invalid.', 'yith-woocommerce-stripe' ),
					'card.expire' => __( 'The expiration date appears to be invalid.', 'yith-woocommerce-stripe' ),
					'billing.fields' => __( 'You have to add extra information to checkout.', 'yith-woocommerce-stripe' ),
				) );
			}
            elseif( 'elements' == $this->mode ) {
				wp_register_script( 'stripe-js', 'https://js.stripe.com/v3/', array( 'jquery' ), YITH_WCSTRIPE_VERSION, true );
				wp_register_script( 'yith-stripe-js', YITH_WCSTRIPE_URL . 'assets/js/stripe-elements.js', array('jquery', 'stripe-js'), YITH_WCSTRIPE_VERSION, true );
				wp_enqueue_script( 'yith-stripe-js' );

				wp_localize_script( 'yith-stripe-js', 'yith_stripe_info', array(
					'public_key'            => $this->public_key,
					'mode'                  => $this->mode,
					'elements_container_id' => '#' . esc_attr( $this->id ) . '-card-elements',
					'currency'              => strtolower( get_woocommerce_currency() ),
					'show_zip'              => $this->elements_show_zip
				) );
			}
		}

		/**
		 * Payment form on checkout page
		 *
		 * @since 1.0.0
		 */
		public function payment_fields() {
			$description = $this->get_description();

			if ( 'test' == $this->env ) {
				$description .= ' ' . sprintf( __( 'TEST MODE ENABLED. Use a test card: %s', 'yith-woocommerce-stripe' ), '<a href="https://stripe.com/docs/testing">https://stripe.com/docs/testing</a>' );
			}

			if ( $description ) {
				echo wpautop( wptexturize( trim( $description ) ) );
			}

			if ( in_array( $this->mode, array( 'standard', 'elements' ) ) ) {
				$card_saved = false;

				if ( $this->mode == 'standard' && $this->bitcoin ) { ?>

					<div class="payment-mode-choise">
						<label class="stripe-mode">
							<input type="radio" name="yith-stripe-mode" id="yith-stripe-mode-card" value="card"<?php checked(true) ?> />
							<?php _e( 'Card', 'yith-woocommerce-stripe' ); ?>
						</label>

						<label>
							<input type="radio" name="yith-stripe-mode" id="yith-stripe-mode-bitcoin" value="bitcoin" />
							<?php _e( 'Bitcoin', 'yith-woocommerce-stripe' ); ?>
						</label>
					</div>

					 <?php
				}

				?>
				<div class="yith-stripe-mode-card"><?php

				// List saved cards
				if ( $this->save_cards && is_user_logged_in() ) {

					$this->init_stripe_sdk();
					$customer = YITH_WCStripe()->get_customer()->get_usermeta_info( get_current_user_id() );

					if ( ! empty( $customer['cards'] ) ) {
						$cards = array();

						foreach ( $customer['cards'] as $the ) {
							$card            = new stdClass();
							$card->id        = $the->id;
							$card->brand     = $the->brand;
							$card->slug      = array_values( array_keys( $this->cards, $card->brand ) );
							$card->slug      = array_shift( $card->slug );
							$card->icon      = WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/' . $card->slug . '.png' );
							$card->last4     = $the->last4;
							$card->exp_month = str_pad( $the->exp_month, 2, '0', STR_PAD_LEFT );
							$card->exp_year  = str_pad( substr( $the->exp_year, - 2 ), 2, '0', STR_PAD_LEFT );

							$cards[] = $card;
						}

						wc_get_template( 'stripe-checkout-cards.php', array(
							'cards'    => $cards,
							'customer' => $customer
						), WC()->template_path() . 'checkout/', YITH_WCSTRIPE_DIR . 'templates/' );

						$card_saved = true;

					}

				}

				if ( ! $card_saved ) {
					?><input type="radio" value="new" name="wc-yith-stripe-payment-token" class="input-radio"
					         id="wc-yith-stripe-payment-token-new" checked="checked" style="display:none;"/><?php
				}

				$this->credit_card_form( array( 'fields_have_names' => false ) );

				?></div><?php

				if ( $this->bitcoin ) {

					?><div class="yith-stripe-mode-bitcoin" style="display:none;">

						<input type="hidden" name="bitcoin-amount" value="<?php echo YITH_WCStripe::get_amount( $this->get_order_total() ) ?>" />
						<input type="hidden" name="bitcoin-signature" value="<?php echo strtoupper( md5( YITH_WCStripe::get_amount( $this->get_order_total() ) . $this->private_key ) ) ?>" />
						<input type="hidden" name="bitcoin-currency" value="<?php echo get_woocommerce_currency() ?>" />

					</div><?php

				}

				wp_enqueue_script( 'prettyPhoto' );
			}
		}

		/**
		 * Add checkbox to choose if save credit card or not
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function credit_form_add_fields( $fields, $id ) {
			if ( $id != $this->id ) {
				return $fields;
			}

			$fields = array( 'fields-container' => '<div class="' . esc_attr( $this->id ) . '-form-container ' . $this->mode . '">' );

			$form_row_first = ! wp_is_mobile() ? 'form-row-first' : '';
			$form_row_last = ! wp_is_mobile() ? 'form-row-last' : '';

			if ( 'standard' == $this->mode ) {
				$fields = array_merge( $fields, array(
					'card-name-field' => '<p class="form-row' . $form_row_first . '">
					<label for="' . esc_attr( $this->id ) . '-card-name">' . __( 'Name on Card', 'yith-woocommerce-stripe' ) . ' <span class="required">*</span></label>
					<input id="' . esc_attr( $this->id ) . '-card-name" class="input-text wc-credit-card-form-card-name" type="text" autocomplete="off" placeholder="' . __( 'Name on Card', 'yith-woocommerce-stripe' ) . '" name="" />
				</p>',

				'card-number-field' => '<p class="form-row ' . $form_row_last . '">
					<label for="' . esc_attr( $this->id ) . '-card-number">' . __( 'Card Number', 'yith-woocommerce-stripe' ) . ' <span class="required">*</span></label>
					<input id="' . esc_attr( $this->id ) . '-card-number" class="input-text wc-credit-card-form-card-number" type="text" maxlength="20" autocomplete="off" placeholder="•••• •••• •••• ••••" name="" />
				</p>',

				'card-expiry-field' => '<p class="form-row ' . $form_row_first . '">
					<label for="' . esc_attr( $this->id ) . '-card-expiry">' . __( 'Expiration Date (MM/YY)', 'yith-woocommerce-stripe' ) . ' <span class="required">*</span></label>
					<input id="' . esc_attr( $this->id ) . '-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="' . __( 'MM / YY', 'yith-woocommerce-stripe' ) . '" name="" />
				</p>',

				'card-cvc-field' => '<p class="form-row  ' . $form_row_last . ' ">
					<label for="' . esc_attr( $this->id ) . '-card-cvc">' . __( 'Security Code', 'yith-woocommerce-stripe' ) . ' <span class="required">*</span> <a href="#cvv-suggestion" class="cvv2-help" rel="prettyPhoto">' . __( 'What is my CVV code?', 'yith-woocommerce-stripe' ) . '</a></label>
					<input id="' . esc_attr( $this->id ) . '-card-cvc" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" placeholder="' . __( 'Security Code (CVV2)', 'yith-woocommerce-stripe' ) . '" name="" />
				</p>
				<div id="cvv-suggestion">
					<p style="font-size: 13px;">
						<strong>' . __( 'Visa&reg;, Mastercard&reg;, and Discover&reg; cardholders:', 'yith-woocommerce-stripe' ) . '</strong><br>
						<a href="//www.cvvnumber.com/" target="_blank"><img height="192" src="//www.cvvnumber.com/csc_1.gif" width="351" align="left" border="0" alt="cvv" style="width: 220px; height:auto;"></a>
						' . __( 'Turn your card over and look at the signature box. You should see either the entire 16-digit credit card number or just the last four digits followed by a special 3-digit code. This 3-digit code is your CVV number / Card Security Code.', 'yith-woocommerce-stripe' ) . '
					</p>
					<p>&nbsp;</p>
					<p style="font-size: 13px;">
						<strong>' . __( 'American Express&reg; cardholders:', 'yith-woocommerce-stripe' ) . '</strong><br>
						<a href="//www.cvvnumber.com/" target="_blank"><img height="140" src="//www.cvvnumber.com/csc_2.gif" width="200" align="left" border="0" alt="cid" style="width: 220px; height:auto;"></a>
						' . __( 'Look for the 4-digit code printed on the front of your card just above and to the right of your main credit card number. This 4-digit code is your Card Identification Number (CID). The CID is the four-digit code printed just above the Account Number.', 'yith-woocommerce-stripe' ) . '
					</p>
				</div>',

//				'card-save-field' => '<div class="create-account"><p class="form-row form-row-wide">
//					<label for="' . esc_attr( $this->id ) . '-card-save">
//						<input id="' . esc_attr( $this->id ) . '-card-save" class="wc-credit-card-form-card-save" type="checkbox" name="' . esc_attr( $this->id ) . '-card-save"' . checked( true, true, false ) . ' />
//						' . __( 'Save card for future payment.', 'yith-woocommerce-stripe' ) . '
//					</label>
//				</p></div>'
				) );
			}
            elseif ( 'elements' == $this->mode ){
	            $fields = array_merge( $fields, array(
		            'card-name-field' => '<p class="form-row form-row-full">
                                <label for="' . esc_attr( $this->id ) . '-card-name">' . apply_filters( 'yith_wcstripe_name_on_card_label', __( 'Name on Card', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span></label>
                                <input id="' . esc_attr( $this->id ) . '-card-name" class="input-text wc-credit-card-form-card-name" type="text" autocomplete="off" placeholder="' . __( 'Name on Card', 'yith-woocommerce-stripe' ) . '" name="" />
                            </p>',

		            'card-elements' => '
                                <label for="' . esc_attr( $this->id ) . '-card-elements">' . apply_filters( 'yith_wcstripe_name_on_card_label', __( 'Card Details', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span></label>
                                <div id="' . esc_attr( $this->id ) . '-card-elements"></div>
                            ',
	            ) );
            }

			// add checkout fields for credit cart
			if ( 'standard' == $this->mode && 'yes' == $this->get_option( 'add_billing_fields' ) ) {
				$fields_to_check = array( 'billing_country', 'billing_city', 'billing_address_1', 'billing_address_2', 'billing_state', 'billing_postcode' );
				$original_fields = WC()->countries->get_address_fields( WC()->customer->get_country(), 'billing_' );
				$shown_fields = is_checkout() ? WC()->checkout()->checkout_fields['billing'] : array();

				$fields['separator'] = '<hr style="clear: both;" />';

				foreach ( $fields_to_check as $i => $field_name ) {
					if ( isset( $shown_fields[ $field_name ] ) ) {
						unset( $fields_to_check[ $i ] );
						continue;
					}

					if ( is_checkout() ) {
						$value = WC()->checkout()->get_value( str_replace( array( 'billing_', 'address_1' ), array( '', 'address' ), $field_name ) );
					} else {
						$value = get_user_meta( get_current_user_id(), $field_name, true );
					}
					$fields[ $field_name ] = woocommerce_form_field( $field_name, array_merge( array( 'return' => true ), $original_fields[ $field_name ] ), $value );

				}

				if ( empty( $fields_to_check ) ) {
					unset( $fields['separator'] );
				}

			}

			$fields = array_merge(
				$fields,
				array(
					'fields-container-end' => '</div>'
				)
			);

			return $fields;
		}

		/**
		 * Log to txt file
		 *
		 * @param $message
		 * @since 1.0.0
		 */
		public function log( $message ) {
			if ( isset( $this->log, $this->debug ) && $this->debug ) {
				$this->log->add( 'stripe', $message );
			}
		}

		/**
		 * Give ability to add options to $this->form_fields
		 *
		 * @param $field
		 * @param string $where (first, last, after, before) (optional, default: last)
		 * @param string $who (optional, default: empty string)
		 *
		 * @since  2.0.0
		 */
		private function add_form_field( $field, $where = 'last', $who = '' ) {
			switch ( $where ) {

				case 'first':
					$this->form_fields = array_merge( $field, $this->form_fields );
					break;

				case 'last':
					$this->form_fields = array_merge( $this->form_fields, $field );
					break;

				case 'before':
				case 'after' :
					if ( array_key_exists( $who, $this->form_fields ) ) {

						$who_position = array_search( $who, array_keys( $this->form_fields ) );

						if ( $where == 'after' ) {
							$who_position = ( $who_position + 1 );
						}

						$before = array_slice( $this->form_fields, 0, $who_position );
						$after  = array_slice( $this->form_fields, $who_position );

						$this->form_fields = array_merge( $before, $field, $after );
					}
					break;
			}
		}
	}
}