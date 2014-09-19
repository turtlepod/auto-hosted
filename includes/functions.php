<?php
/**
 * AutoHosted Functions.
 * 
 * @package AutoUpdater
 * @subpackage Includes
 * @since 0.1.0
 */

/* QUERY VAR
--------------------------------------------------------------- */

/* Add query variable so wordpress recognize it */
add_filter( 'query_vars', 'auto_hosted_add_query_variable' );


/**
 * Add Query Vars, so WordPress could recognize it.
 * @since 0.1.0
 * @return array of WordPress query var.
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



/* TEMPLATE INCLUDES
--------------------------------------------------------------- */

/* Override Singular Plugin and Theme Repo */
add_filter( 'template_include', 'auto_hosted_template_include' ) ;


/**
 * Replace Singular Template for Plugin Repo based on custom query var.
 * @since 0.1.0
 * @return string of template used by the repo.
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


/* PLUGIN CHECK DATA
--------------------------------------------------------------- */

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
		$update_info->package = get_post_meta( $id, 'download_link', true );

		/* print the data */
		print serialize( $update_info );
	}
}


/* PLUGIN INFORMATION
--------------------------------------------------------------- */


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
		$info->download_link = esc_url( get_post_meta( $id, 'download_link', true ) );

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
 * Plugin Sections
 * @since 0.1.0
 * @return array of plugin sections
 */
function auto_hosted_plugin_sections(){

	/* get id */
	$id = get_queried_object_id();

	/* build sections */
	$sections = array();

	/* descriptions */
	if ( get_post_meta( $id, 'section_description', true ) )
		$sections['description'] = auto_hosted_sanitize_section( get_post_meta( $id, 'section_description', true ) );

	/* faq */
	if ( get_post_meta( $id, 'section_faq', true ) )
		$sections['faq'] = auto_hosted_sanitize_section( get_post_meta( $id, 'section_faq', true ) );

	/* screenshots */
	if ( get_post_meta( $id, 'section_screenshots', true ) )
		$sections['screenshots'] = auto_hosted_sanitize_section( get_post_meta( $id, 'section_screenshots', true ) );

	/* changelog */
	if ( get_post_meta( $id, 'section_changelog', true ) )
		$sections['changelog'] = auto_hosted_sanitize_section( get_post_meta( $id, 'section_changelog', true ) );

	/* other_notes */
	if ( get_post_meta( $id, 'section_other_notes', true ) )
		$sections['other_notes'] = auto_hosted_sanitize_section( get_post_meta( $id, 'section_other_notes', true ) );

	return $sections;
}


/**
 * Sanitize Plugin Sections Datam just to make sure it's proper data.
 * This is a helper function to sanitize plugin sections data to send to user site.
 * @since 0.1.0
 * @return string of sanitized section data
 */
function auto_hosted_sanitize_section( $input ){

	/* allowed tags */
	$sections_allowedtags = array(
		'a' => array( 'href' => array(), 'title' => array(), 'target' => array() ),
		'abbr' => array( 'title' => array() ), 'acronym' => array( 'title' => array() ),
		'code' => array(), 'pre' => array(), 'em' => array(), 'strong' => array(),
		'div' => array(), 'p' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(),
		'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(),
		'img' => array( 'src' => array(), 'class' => array(), 'alt' => array() )
	);

	$output = wp_kses( $input, $sections_allowedtags);
	return $output;
}


/* THEME CHECK
--------------------------------------------------------------- */


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
		$update_info['package'] = get_post_meta( $id, 'download_link', true );

		/* update detail url */
		$update_info['url'] = auto_hosted_theme_detail_url( $id );

		/* print the data */
		print serialize( $update_info );
	}
}


/**
 * Auto Hosted Theme Detail URL
 * 
 * @since 0.1.0
 */
function auto_hosted_theme_detail_url( $id ){

	/* empty as open sesame. */
	$url = '';

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

	return $url;
}


/* THEME INFORMATION
--------------------------------------------------------------- */


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