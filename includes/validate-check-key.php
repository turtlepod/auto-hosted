<?php
/**
 * Validate Activation key
 * 
 * @param $user_agent string to get domain and it's from WP
 * @param $login_name string to get user login name, for activation by user role
 * @param $key string to get activation key
 * 
 * @since 0.1.1
 * @return bool
 */
function auto_hosted_validate_check_key( $user_agent, $login_name = '', $key = '' ){

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


	/* set output to true */
	$output = true;

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

		/* set output to false */
		$output = false;

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
	} // end restrict by user role

	return $output;
}