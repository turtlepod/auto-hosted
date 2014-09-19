<?php
/**
 * Validate request
 * 
 * @param $current_version string to get version from install and compare with repo version
 * @param $user_agent string to get domain and it's from WP
 * @param $login_name string to get user login name, for activation by user role
 * @param $key string to get activation key
 * 
 * @since 0.1.1
 * @return bool
 */
function auto_hosted_validate_request( $current_version, $user_agent, $login_name = '', $key = '' ){

	/**
	 * Validate by available repository.
	 * if there's no repo, return false
	 * ----------------------------------------------------------
	 */
	$id = get_queried_object_id();
	if ( !isset( $id ) || empty ( $id ) ){
		return false;
	}


	/**
	 * Validate user agent.
	 * if the request is not from WordPress (admin) return false
	 * ----------------------------------------------------------
	 */
	if ( stristr( $user_agent, 'WordPress' ) == false ) {
		return false;
	}
	/**
	 * If request is valid, get the domain where the request is from
	 */
	else{
		/* get user agent domain */
		$user_agent = explode( ";" , $user_agent );

		/* domain request */
		$requested_from = esc_url_raw( trim( $user_agent[1] ) );

		/* parse url */
		$requested_from = parse_url( $requested_from );

		/* domain name only */
		$requested_from = $requested_from['host'];
	}


	/**
	 * Validate by available repository data.
	 * If the repo is disable or if the minimum data not available
	 * return false
	 * ----------------------------------------------------------
	 */
	$zip_available = get_post_meta( $id, 'download_link', true );
	$version_available = get_post_meta( $id, 'version', true );
	$disable_update = get_post_meta( $id, 'disable_update', true );

	/* only pass this check if minimum data exist, or not disable */
	if ( !$zip_available || !$version_available || $disable_update ){
		return false;
	}


	/**
	 * Validate by version requested.
	 * if no newer version avalilable, return false.
	 * ------------------------------------------------------------
	 */
	if ( version_compare( $current_version, $version_available, '>=' ) ) {
		return false;
	}


	/**
	 * Validate by whitelisted domain name
	 * If the request is from white listed domain, no need to check for activation key.
	 * ---------------------------------------------------------------------------
	 */
	$whitelisted_domains = array();
	$whitelisted_domains_meta = get_post_meta( $id, 'domain_whitelist', true );

	/* if repo input for restrict domain available */
	if ( $whitelisted_domains_meta ){

		/* make domains as array */
		$whitelisted_domains_meta = explode( "\n", $whitelisted_domains_meta );

		/* trim it */
		$whitelisted_domains_meta = array_map( 'trim', $whitelisted_domains_meta );

		/* make it's an url */
		$whitelisted_domains_meta = array_map( 'esc_url_raw', $whitelisted_domains_meta );

		/* get whitelist domain name from meta input */
		foreach ( $whitelisted_domains_meta as $whitelisted_domain_meta ){

			/* get the domain from list */
			$parse_whitelisted_domain_meta = parse_url( $whitelisted_domain_meta );
			$whitelisted_domains[] = $parse_whitelisted_domain_meta['host'];
		}
		
		/* if the request is from whitelisted domain, return true  */
		if ( in_array( $requested_from, $whitelisted_domains ) ){
			return true;
		}
	}


	/**
	 * Validate by restricted domain name.
	 * only domain in this list can do automatic update.
	 * ------------------------------------------------------------
	 */
	$restricted_domains = array();
	$restricted_domains_meta = get_post_meta( $id, 'domain_restrict', true );

	/* if repo input for restrict domain available */
	if ( $restricted_domains_meta ){

		/* make domains as array */
		$restricted_domains_meta = explode( "\n", $restricted_domains_meta );

		/* trim it */
		$restricted_domains_meta = array_map( 'trim', $restricted_domains_meta );

		/* make it's an url */
		$restricted_domains_meta = array_map( 'esc_url_raw', $restricted_domains_meta );

		/* get whitelist domain name from meta input */
		foreach ( $restricted_domains_meta as $restricted_domain_meta ){

			/* get the domain from list */
			$parse_restricted_domain_meta = parse_url( $restricted_domain_meta );
			$restricted_domains[] = $parse_restricted_domain_meta['host'];
		}
		
		/* if the request is from restricted domain, return true  */
		if ( !in_array( $requested_from, $restricted_domains ) ){
			return false;
		}
	}


	/* set output to false */
	$output = false;

	/**
	 * Validate by activation key from the list
	 * if it's using activation key from activation key list.
	 * ---------------------------------------------------------------------------
	 */
	$restricted_by_keys_meta = get_post_meta( $id, 'activation_key', true );

	/* default keys in md5 */
	$md5_keys = array();

	/* if this repo using activation key by user role */
	if ( $restricted_by_keys_meta ){

		/* make keys as array */
		$restricted_by_keys_meta = explode( "\n", $restricted_by_keys_meta );

		/* trim it */
		$restricted_by_keys_meta = array_map( 'trim', $restricted_by_keys_meta );

		/* md5 it */
		$md5_keys = array_map( 'md5', $restricted_by_keys_meta );

		/* make sure the key is available, and activation key match */
		if ( in_array( $key, $md5_keys ) ){
			return true;
		}

	} // end validate by activation key
	else{
		$output = true;
	}


	/**
	 * Validate by activation key by user role
	 * If it's using activation key by user role
	 * check if user email is the same as user login received from request.
	 * ---------------------------------------------------------------------------
	 */
	$restricted_by_roles_meta = get_post_meta( $id, 'user_role_email_key' );

	/* if this repo using activation key by user role */
	if ( $restricted_by_roles_meta ){

		/* set output to false */
		$output = false;

		/* get user id by login name */
		$get_user = validate_username( $login_name ) ? get_user_by( 'login', $login_name ) : false ;

		/* only if user data is available */
		if ( $get_user ){

			/* default emails in md5 */
			$md5_email = '';

			if ( in_array( $get_user->roles[0], $restricted_by_roles_meta ) ){

				/* user email in md5 */
				$md5_email = md5( trim( $get_user->data->user_email ) );
			}

			/* make sure the email is found, and activation key match */
			if ( $md5_email == $key ){
				return true;
			}

		} // end user data check

	} // end validate by user role check
	else{
		$output = true;
	}

	return $output;
}