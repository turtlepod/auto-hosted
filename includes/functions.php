<?php
/**
 * AutoHosted Functions.
 * 
 * @package AutoUpdater
 * @subpackage Includes
 * @since 0.1.0
 */


/* Add query variable so wordpress recognize it */
add_filter( 'query_vars', 'auto_hosted_add_query_variable' );


/**
 * Add Query Vars, so WordPress could recognize it.
 * 
 * @since 0.1.0
 */
function auto_hosted_add_query_variable( $vars ){

	/* to get plugin update data for transient */
	$vars[] = 'ahpr_check';

	/* to get plugin information data */
	$vars[] = 'ahpr_info';

	/* to get theme update data for transient */
	$vars[] = 'ahtr_check';

	/* to get theme information data */
	$vars[] = 'ahtr_info';

	/* to check valid activation key */
	$vars[] = 'ahr_check_key';

	return $vars;
}


/* Override Singular Plugin and Theme Repo */
add_filter( 'template_include', 'auto_hosted_template_include' ) ;


/**
 * Replace Singular Template for Plugin Repo based on custom query var.
 * 
 * @since 0.1.0
 */
function auto_hosted_template_include( $template ){

	/* get plugin check var */
	$plugin_check = get_query_var( 'ahpr_check' );

	/* get plugin info var */
	$plugin_info = get_query_var( 'ahpr_info' );

	/* get theme check var */
	$theme_check = get_query_var( 'ahtr_check' );

	/* get theme info var */
	$theme_info = get_query_var( 'ahtr_info' );

	/* get plugin activation key */
	$check_key = get_query_var( 'ahr_check_key' );

	/* get theme download var */
	$download = get_query_var( 'ahr_download' );

	/* in singular plugin repo */
	if ( is_singular('plugin_repo') ){

		/* if update check requested */
		if ( $plugin_check ){

			/* use "check-update" template  */
			$template = AUTOHOSTED_PATH . 'templates/plugin-update.php';
		}

		/* if plugin information requested */
		elseif( $plugin_info ){

			/* use "plugin-information" template  */
			$template = AUTOHOSTED_PATH . 'templates/plugin-information.php';
		}

		/* if validation activation key requested */
		elseif( $check_key ){

			/* use "check-key" template  */
			$template = AUTOHOSTED_PATH . 'templates/check-key.php';
		}

		/* redirect to home page */
		else{
			$template = AUTOHOSTED_PATH . 'templates/redirect.php';
		}
	}

	/* in singular theme repo */
	if ( is_singular('theme_repo') ){

		/* if update check requested */
		if ( $theme_check ){

			/* use "check-update" template  */
			$template = AUTOHOSTED_PATH . 'templates/theme-update.php';
		}

		/* if plugin information requested */
		elseif( $theme_info ){

			/* use "plugin-information" template  */
			$template = AUTOHOSTED_PATH . 'templates/theme-information.php';
		}

		/* if validation activation key requested */
		elseif( $check_key ){

			/* use "check-key" template  */
			$template = AUTOHOSTED_PATH . 'templates/check-key.php';
		}

		/* redirect to home page */
		else{
			$template = AUTOHOSTED_PATH . 'templates/redirect.php';
		}
	}

	/* close sesame */
	return $template;
}


/**
 * Initial Check for data request.
 * this will check for input and zip package input.
 * 
 * @since 0.1.0
 * @return bool
 */
function auto_hosted_initial_check(){

	/* default output */
	$output = false;

	/* get id */
	$id = get_queried_object_id();

	/* only if id isset, and not empty */
	if ( isset( $id ) && !empty ( $id ) ){

		/* zip file input */
		$zip = get_post_meta( $id, 'download_link', true );

		/* version */
		$version = get_post_meta( $id, 'version', true );

		/* disable */
		$disable = get_post_meta( $id, 'disable_update', true );

		/* only pass this check if minimum data exist */
		if ( $zip && $version && !$disable )
			$output = true;
	}

	/* output */
	return apply_filters( 'auto_hosted_initial_check', $output );
}




/**
 * Version Compare. Initial check for available update.
 * Compare current version and latest version of theme/plugin.
 * Also check if plugin zip is available and if update is not disable.
 * 
 * @since 0.1.0
 */
function auto_hosted_version_compare( $current_version ){

	/* default output */
	$output = false;

	/* get id */
	$id = get_queried_object_id();

	/* only if id isset, and not empty */
	if ( isset( $id ) && !empty ( $id ) ){

		/* get latest version from plugin data */
		$latest_version = get_post_meta( $id, 'version', true );

		/* compare plugin version version */
		if ( version_compare( $current_version, $latest_version, '<' ) ) {
			$output = true;
		}
	}

	/* output */
	return apply_filters( 'auto_hosted_version_compare', $output);
}



/**
 * Validate domain request from domain restrict using user agent.
 * 
 * @since 0.1.0
 */
function auto_hosted_validate_domain( $user_agent ){

	/* default */
	$output = false;

	/* "WordPress" need to be in user agent string */
	if ( stristr( $user_agent, 'WordPress' ) == TRUE ) {

		/* domain restrict */
		$whitelist_domains = auto_hosted_restrict_domain();

		/* get user agent domain */
		$resp = explode( ";" , $user_agent );

		/* domain request */
		$get_domain = esc_url_raw( trim( $resp[1] ) );

		/* parse url */
		$parse_domain = parse_url( $get_domain );

		/* domain name only */
		$domain_from = $parse_domain['host'];

		/* check if it's in array of whitelist domain */
		if ( empty( $whitelist_domains ) || in_array( $domain_from, $whitelist_domains ) ){
			$output = true;
		}
	}

	return $output;
}


/**
 * Validate Activation Keys.
 * 
 * @since 0.1.0
 */
function auto_hosted_validate_key( $key, $user_agent ){

	/* default */
	$output = true;

	/* list of activation keys */
	$keys = auto_hosted_activation_keys();

	/* false, if activation key list is not empty, or key is not valid */
	if ( $keys && !in_array( $key, $keys ) ){
		$output = false;
	}

	/* get list of whitelist domain */
	$whitelist_domains = auto_hosted_whitelist_domain();

	/* if whitelist domain is not empty */
	if ( $whitelist_domains ){

		/* get user agent domain */
		$resp = explode( ";" , $user_agent );

		/* domain request */
		$get_domain = esc_url_raw( trim( $resp[1] ) );

		/* parse url */
		$parse_domain = parse_url( $get_domain );

		/* domain name only */
		$domain_from = $parse_domain['host'];

		/* true, if domain from is exist in list */
		if ( in_array( $domain_from, $whitelist_domains ) ){
			$output = true;
		}
	}

	return $output;
}


/**
 * Download URL
 * 
 * @since 0.1.0
 */
function auto_hosted_download_url(){

	/* get id */
	$id = get_queried_object_id();

	/* empty default */
	$download_url = '';

	/* only if id isset, and not empty */
	if ( isset( $id ) && !empty ( $id ) ){

		/* download url */
		$download_link = get_post_meta( $id, 'download_link', true );

		/* check if zip package exist */
		if ( $download_link ) {
			$download_url = $download_link;
		}
	}
	return $download_url;
}


/**
 * Update Check. This data is for "update_plugins" transient data. 
 * The data needed is only "plugin_slug", "new_version", and "package" url.
 * 
 * @since 0.1.0
 */
function auto_hosted_plugin_check(){

	/* get id */
	$id = get_queried_object_id();

	/* only if id isset, and not empty */
	if ( isset( $id ) && !empty ( $id ) ){

		/* create object */
		$update_info = new stdClass;

		/* latest plugin version */
		$update_info->new_version = get_post_meta( $id, 'version', true );

		/* zip package url */
		$update_info->package = auto_hosted_download_url();

		/* print the data */
		print serialize( $update_info );
	}
}


/**
 * Plugin Information
 * 
 * @since 0.1.0
 */
function auto_hosted_plugin_information(){

	/* get id */
	$id = get_queried_object_id();

	/* only if id isset, and not empty */
	if ( isset( $id ) && !empty ( $id ) ){

		/* create object */
		$info = new stdClass;

		/* get latest plugin version */
		$info->version = get_post_meta( $id, 'version', true );

		/* latest updated date */
		$info->last_updated = get_post_meta( $id, 'last_updated', true );

		/* zip url */
		$info->download_link = auto_hosted_download_url();

		/* wp version requires */
		$info->requires = get_post_meta( $id, 'requires', true );

		/* wp version tested */
		$info->tested = get_post_meta( $id, 'tested', true );

		/* sections */
		$info->sections = auto_hosted_plugin_sections();

		/* print the data */
		print serialize( $info );
	}
}


/**
 * Update Check. This data is for "update_themes" transient data. 
 * The data needed is only "plugin_slug", "new_version", and "package" url.
 * 
 * @since 0.1.0
 */
function auto_hosted_theme_check(){

	/* get id */
	$id = get_queried_object_id();

	/* only if id isset, and not empty */
	if ( isset( $id ) && !empty ( $id ) ){

		/* create object */
		$update_info = array();

		/* latest theme version */
		$update_info['new_version'] = get_post_meta( $id, 'version', true );

		/* zip package url */
		$update_info['package'] = auto_hosted_download_url();

		/* update detail url */
		$update_info['url'] = auto_hosted_theme_detail_url();

		/* print the data */
		print serialize( $update_info );
	}
}


/**
 * Auto Hosted Theme Detail URL
 * 
 * @since 0.1.0
 */
function auto_hosted_theme_detail_url(){

	/* get id */
	$id = get_queried_object_id();

	/* empty as open sesame. */
	$url = '';

	/* only if id isset, and not empty */
	if ( isset( $id ) && !empty ( $id ) ){

		/* check if theme changelog url input exist */
		if ( get_post_meta( $id, 'theme_changelog_url', true ) ) {
			$url = esc_url_raw( get_post_meta( $id, 'theme_changelog_url', true ) );
		}

		/* check changelog input */
		elseif ( get_post_meta( $id, 'theme_changelog', true ) ) {

			/* construct url */
			$extrapostdata = get_post(get_the_ID(), ARRAY_A);
			$slug = $extrapostdata['post_name'];
			$url = add_query_arg( array( 'theme_repo' => $slug, 'ahtr_info' => 'changelog' ), trailingslashit( home_url() ) );
		}
	}
	return $url;
}


/**
 * Theme Information (changelog)
 * 
 * @since 0.1.0
 */
function auto_hosted_theme_information(){

	/* get id */
	$id = get_queried_object_id();

	/* empty */
	$info = '';

	/* only if id isset, and not empty */
	if ( isset( $id ) && !empty ( $id ) ){

		/* check if theme changelog editor input exist */
		if ( get_post_meta( $id, 'theme_changelog', true ) ) {

			$info .= '<div class="container">';
			$info .= get_post_meta( $id, 'theme_changelog', true );
			$info .= '</div>';

		}
	}
	echo apply_filters( 'auto_hosted_theme_information', $info );
}