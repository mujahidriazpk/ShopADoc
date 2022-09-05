=== YITH WooCommerce Stripe ===

Contributors: yithemes
Tags: stripe, simple stripe checkout, stripe checkout, credit cards, online payment, payment, payments, recurring billing, subscribe, subscriptions, bitcoin, gateway, yithemes, woocommerce, shop, ecommerce, e-commerce
Requires at least: 4.5
Tested up to: 5.2
Stable tag: 1.9.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Changelog ==
= 1.9.1 - Released on May 29, 2019 =
* New: Support to WordPress 5.2
* Update: Plugin Framework
* Tweak: get_home_url() to get_site_url()
* Tweak: remove protocol from url before performing security check
* Dev: Added new filter yith_wcstripe_charge_params

= 1.9.0 - Released on Apr 11, 2019 =

* New: WooCommerce 3.6 support
* New: retry renews when a fail occurs
* New: added check over site url, to set Test Mode when plugin is enabled on a staging installation
* Tweak: disabled Make default button for expired cards
* Tweak: improved card form on mobile devices
* Tweak: avoid duplicated cards in card expiration reminder queue
* Tweak: updated card expiration reminder email, to provide correct information when card is already expired
* Tweak: improved error reporting system
* Tweak: system now register card fingerprint during webhook handling
* Update: internal plugin framework
* Update: dutch translation
* Fix: issue when trying to pay with a previously registered card (default card was applied)
* Fix: stripe do not execute code when customer tries to delete/set as default tokens created with other gateways
* Fix: avoid to register token twice, when the same card_id is already saved for the same customer
* Fix: avoid saving cards when related option is disabled

= 1.8.1 - Released on Feb 11, 2019 =

* New: WooCommerce 3.5.4 support
* New: button to automatically set webhook on Stripe
* New: added reminder email for card expiration
* Update: internal plugin framework
* Update: Stripe php library to version 6.29.3
* Update: Dutch language
* Fix: processing renew instead of new order when reactivating subscriptions

= 1.8.0 - Released on Dec 19, 2018 =

* New: WordPress 5.0 support
* New: WooCommerce 3.5.2 support
* New: support to latest API version (2018-11-08)
* New: admin can now choose whether they want to automatically store card reference, or ask customer which cards to save (for appropriate payment modes only)
* Tweak: prevent stripe to execute subscriptions methods, when payment method for the subscription is not stripe
* Tweak: added check over gateway existence before filtering YWSBS from list
* Tweak: improved Webhook error messages
* Tweak: check over configurable properties in update_subscription method
* Update: internal plugin framework
* Update: dutch translation
* Fix: error in js to trigger elements handling
* Fix: solved issue occurring when billing state select is not visible
* Fix: implicit casting when registering a new failed invoice
* Fix: call to undefined endpoint, causing a 404 error in API; Limited labels accordingly to new limit set for the API
* Dev: added filter yith_wcstripe_error_message_order_note to let third party code filter error messages stored in order notes

= 1.7.2 - Released on Oct 24, 2018 =

* New: WooCommerce 3.5 support
* Tweak: updated plugin framework

= 1.7.1 - Released on Oct 15, 2018 =

* Fix: wrong links on admin page
* Fix: restored plugin panel under YITH menu
* Update: Italian language

= 1.7.0 - Released on Oct 09, 2018 =

* New: added WordPress 4.9.8 compatibility
* New: added WooCommerce 3.5-RC compatibility
* New: updated Stripe API version to 2018-09-24
* New: updated plugin framework
* New: added pt_PT translation (thanks to Ricardo A.)
* Tweak: plugin now registers card even for subscription only orders;
* Tweak: set default card when processing subscriptions
* Tweak: removed create_function for php 7.2 compatibility
* Tweak: removed usage of deprecated WC function from gateway class
* Tweak: improved error handling for invoices: skip useless checks, and add notes to renew order, instead of parent one
* Fix: order total when paying from order-pay endpoint
* Fix: notices when registering a card
* Fix: check over subscription expiration date
* Fix: get_plan now does not create new plans any longer when just used to check plan existance (this mainly happens when checking if order has active subscription)
* Fix: SSL error notification on admin pages
* Fix: warning when failed attempts is not an array
* Dev: added yith_wcstripe_before_create_token trigger
* Dev: added yith_wcstripe_add_payment_method_result filter
* Dev: added yith_wcstripe_gateway_us_icons filter
* Dev: added yith_wcstripe_gateway_default_icons filter
* Dev: added yith_wcstripe_gateway_icon filter
* Dev: added yith_wcstripe_use_plugin_error_codes filter to show original API error messages
* Dev: added yith_wcstripe_error_message filter to let third party code filter error messages

= 1.6.0 - Released on May 28, 2018 =

* New: WooCommerce 3.4.0 support
* New: WordPress 4.9.6 support
* New: updated plugin fw
* New: GDPR compliance
* New: Stripe library 6.7.1 (Requires API update on Stripe Dashboard)
* New: trial period is now added only when Subscription Product has trial set
* New: added billing and shipping information on Stripe Checkout payment mode
* New: added Hosted mode, similar to free version
* Tweak: added description to renew charges after successful charge webhook
* Tweak: improved customer handling when registering cards
* Tweak: moved notify_failed_renewal to avoid issues when adding a new card from My Account endpoint
* Fix: plan creation, for newer version of API
* Fix: now subscription is cancelled after end date
* Fix: improved js to avoid implict conversions
* Dev: added yith_wcstripe_plan_trial_period filter
* Dev: added filter yith_wcstripe_gateway_enabled to programmatically enable/disable Stripe Gateway
* Dev: added filter yith_wcstripe_gateway_id to let developers filter gateway ID (Use this filter at your own risk; after filtering gateway ID you will need to configure gateway again)

= 1.5.0 - Released on Feb 08, 2018 =

* New: WooCommerce 3.3.1 support
* New: WordPress 4.9.4 support
* New: updated Stripe library to 6.0 revision
* New: updated plugin-fw library

= 1.4.0 - Released on Jan 09, 2018 =

* New: WooCommerce 3.2.6 support
* New: updated plugin-fw to version 3.0
* New: updated Stripe library to 5.8 revision
* Tweak: added check over save_cards flag before token creation; this way cards won't be actually saved if admin disable related option
* Fix: check on captured flag on payment_complete
* Fix: stripe script not being loaded in Add Payment Method page
* Fix: token error when remember card functionality is disabled
* Fix: trial start/end time when pausing/resuming subscriptions
* Dev: added yith_wcstripe_subscription_amount to let third party plugin to change plan amount
* Dev: added yith_wcstripe_card_number_dots filter to let dev change "dots" in cc number
* Dev: added filters to change default CC form labels
* Dev: added yith_wcstripe_environment filter
* Dev: added yith_wcstripe_metadata filter to let third party developers change metadata sent to Stripe servers

= 1.3.0 - Released on Apr 04, 2017 =

* New: WordPress 4.7.3 compatibility
* New: WooCommerce 3.0.0-RC2 compatibility
* New: added italian - ITALY translation
* Fix: plan amount with recurring shipping payment, for YITH WooCommerce Subscription plugin
* Fix: added ajax to refresh amount when hosted checkout needs to be refreshed
* Fix: "Renewal failed" message repeated on my-account page
* Fix: subscription renew link inside MyAccount message
* Fix: guest checkout when purchasing subscription
* Tweak: added check over gateway existence
* Tweak: updated Stripe library to 3.23.0
* Tweak: improved failed renew message, when YITH WooCommerce Subscription active
* Tweak: changed text domain to yith-woocommerce-stripe
* Dev: added yith_wcstripe_capture_payment filter
* Dev: added yith_stripe_locale filter to change locale used in hosted checkout

= 1.2.10 - Released on Jun 16, 2016 =

* Added: ufficial support to WC 2.6
* Fixed: minor bug fixes

= 1.2.9.1 - Released on Jun 13, 2016 =

* Added: notification for failed and success renewal, with yith subscription plugin
* Fixed: bugs for final release of WC 2.6

= 1.2.9 - Released on May 31, 2016 =

* Added: support to WC 2.6 Beta 3
* Tweak: improved exception catcher
* Fixed: bug on Stripe Checkout mode when pay order create manually by admin

= 1.2.8 - Released on Apr 27, 2016 =

* Fixed: amount doesn't shown on stripe checkout
* Fixed: fatal error on card validation on checkout
* Fixed: duplicate cancel notification when triggered "cancel" action from my account
* Fixed: payment due date duplicate on renew

= 1.2.7 - Released on Mar 29, 2016 =

* Tweak: hash on plan name, on avoid subscription configuration no product (like changing price, interval, trial period, etc..)
* Fixed: improved webhooks on payment succedeed
* Fixed: credit card form isn't shown if selected "New card" on checkout page
* Fixed: fatal error with Stripe\Error\API
* Fixed: wrong cart total on hosted checkout
* Fixed: internal server error if the import is lower then .50 cent
* Fixed: a refund from website is marked double, dued an error from webhook
* Fixed: can't create blacklist table and feature not working
* Fixed: total without tax in plan amount

= 1.2.6 - Released on Feb 16, 2016 =

* Added: ability to add new credit card by my account
* Fixed: localization for "Stripe checkout"

= 1.2.5 - Released on Feb 16, 2016 =

* Added: "Stripe checkout" mode directly on checkout page, without button on second page.
* Added: 'order_email' parameter in metadata of Stripe charge
* Added: order note when there is an error during the payment (card declined or card validation by stripe)
* Fixed: stripe library loading causing fatal error in some servers
* Fixed: ccv2 help box not opening on checkout
* Fixed: validation of extra billing fields below credit card form 
* Fixed: bitcoin option didn't work
* Fixed: better response for webhooks, because they remains in pending in some cases

= 1.2.4 - Released on Jan 19, 2016 =

* Added: compatibility with WooCommerce 2.5
* Added: compatibility with YITH WooCommerce Subscriptions and YITH WooCommerce Membership, so now ability to open and manage new subscriptions with Stripe (available only for "Standard" mode of checkout)
* Added: language support for "Stripe checkout" mode
* Added: ability to show extra address fields below credit card info, if you are using any plugin that change fields on checkout, to reduce fraudolent payment risk
* Updated: Stripe API library with latest version

= 1.2.3 - Released on Dec 14, 2015 =

* Fixed: no errors for wrong cards during checkout

= 1.2.2 - Released on Dec 10, 2015 =

* Added: compatibility to multi currency plugin
* Added: compatibility with one-click checkout
* Fixed: bug on refunds for orders not captured yet
* Fixed: localization of CVV suggestion text
* Fixed: bitcoin receivers errors on logs

= 1.2.1 - Released on Aug 19, 2015 =

* Fixed: Minor bug

= 1.2.0 - Released on Aug 12, 2015 =

* Added: Support to WooCommerce 2.4
* Updated: Plugin core framework
* Updated: Language pot file

= 1.1.4 - Released on Jul 24, 2015 =

* Fixed: blacklist table not created on database
* Fixed: blacklist table on admin without pagination

= 1.1.3 - Released on Jul 21, 2015 =

* Added: ability to ban automatically the users with errors during the payment and ability to manage them in a blacklist page

= 1.1.2 - Released on Jun 09, 2015 =

* Fixed: localization of cvv help popup content

= 1.1.1 - Released on Apr 24, 2015 =

* Fixed: creation on-hold orders and flushing checkout session after card error on checkout

= 1.1.0 - Released: Apr 22, 2015 =

* Added: support to WordPress 4.2
* Added: CVV Card Security Code suggestion
* Fixed: bug on checkout

= 1.0.4 - Released: Apr 21, 2015 =

* Added: languages pot catalog

= 1.0.3 - Released: Apr 15, 2015 =

* Added: Name on Card field on Credit Card form of checkout
* Fixed: bug with customer profile creating during purchase

= 1.0.2 - Released: Mar 04, 2015 =

* Updated: Plugin core framework

= 1.0.1 - Released: Mar 03, 2015 =

* Fixed: minor bugs

= 1.0.0 =

* Initial release
