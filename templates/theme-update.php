<?php
/**
 * Check Theme Update Data
 * @since 0.1.0
 */
/* get vars */
$qvar = get_query_var('ahtr_check');
$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? trim( $_SERVER['HTTP_USER_AGENT'] ) : '';
$userlogin = isset( $_POST['login'] ) ? trim( $_POST['login'] ) : '' ;
$key = isset( $_POST['key'] ) ? trim( $_POST['key'] ) : '' ;

/* validate request */
if ( auto_hosted_validate_request( $qvar, $user_agent, $userlogin, $key ) ){
	auto_hosted_theme_check();
}