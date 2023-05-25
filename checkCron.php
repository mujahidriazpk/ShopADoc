<?php
define('WP_USE_THEMES', true);
require('/home/642855.cloudwaysapps.com/gddwwykpfm/public_html/wp-load.php');
//mail("mujahidriazpk@gmail.com","My subject",'this is test email '.date('Y-m-d g:i A'));
$to = 'mujahidriazpk@gmail.com';
$subject = 'The subject';
$body = 'this is test email '.date('Y-m-d g:i A');
$headers = array('Content-Type: text/html; charset=UTF-8');

wp_mail( $to, $subject, $body, $headers );
?>