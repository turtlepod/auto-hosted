<?php
/**
 * Update Data
 * 
 * @since 0.1.0
 */

/* get user agent */
$user_agent = $_SERVER['HTTP_USER_AGENT'];

/* check version and zip package */
if ( auto_hosted_version_compare( get_query_var('ahpr_check') ) ){

	/* check domain restrict */
	if ( auto_hosted_validate_domain( $user_agent ) ){

		/* check activation key */
		if ( auto_hosted_validate_key( $_POST['key'], $user_agent ) ){

			/* display data */
			auto_hosted_plugin_check();
		}
	}
}