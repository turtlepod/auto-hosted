<?php
/**
 * Check Activation Key
 * @since 0.1.0
 */

/* get vars needed */
$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? trim( $_SERVER['HTTP_USER_AGENT'] ) : '';
$userlogin = isset( $_POST['login'] ) ? trim( $_POST['login'] ) : '' ;
$key = isset( $_POST['key'] ) ? trim( $_POST['key'] ) : '' ;

/* validate request for key  */
if ( auto_hosted_validate_check_key( $user_agent, $userlogin, $key ) ){
	print 'valid';
} else {
	print 'invalid';
}