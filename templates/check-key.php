<?php
/**
 * Check Activation Key
 * 
 * @since 0.1.0
 */

/* get user agent */
$user_agent = $_SERVER['HTTP_USER_AGENT'];

/* check domain whitelist */
if ( auto_hosted_validate_domain( $user_agent ) ){

	/* check activation key */
	if ( auto_hosted_validate_key( $_POST['key'], $user_agent ) ){
		print 'valid';
	}
	/* key is not valid */
	else{
		print 'invalid';
	}
}