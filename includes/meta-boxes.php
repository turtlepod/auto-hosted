<?php
/**
 * Meta boxes for Custom Post Type and Handle all meta data.
 * 
 * @package AutoUpdater
 * @subpackage Includes
 * @since 0.1.0 
 */

/* filter to create metabox */
add_filter( 'auto_hosted_metaboxes', 'auto_hosted_repo_metabox' );


/**
 * Rich Snippet Rating Review Metabox
 * Input visible in Custom Field metabox.
 * @since 0.1.0
 * @return array of meta boxes.
 */
function auto_hosted_repo_metabox( $meta_boxes ){

	/* Updater Data For Plugins Config */
	$meta_boxes[] = array(
		'id'			=> 'auto_hosted_repo_config',
		'title'			=> _x( 'Updater Config', 'metabox', 'auto-hosted' ),
		'pages'			=> array( 'plugin_repo', 'theme_repo' ),
		'context'		=> 'normal',
		'priority'		=> 'high',
		'show_names'	=> true,
		'fields'		=> array(
			array(
				'name'		=> 'repo_slug',
				'id'		=> 'repo_slug',
				'type'		=> 'slug',
				'desc'		=> _x( 'For your updater config, you can edit this from slug meta box.', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> 'repo_uri',
				'id'		=> 'repo_uri',
				'type'		=> 'home_url',
				'desc'		=> _x( 'For your updater config, this is your site home url.', 'metabox', 'auto-hosted' ),
			),
		),
	);

	/* Plugin Updates */
	$meta_boxes[] = array(
		'id'			=> 'plugin_repo_data',
		'title'			=> _x( 'Plugin Update Data', 'metabox', 'auto-hosted' ),
		'pages'			=> array( 'plugin_repo' ),
		'context'		=> 'normal',
		'priority'		=> 'default',
		'show_names'	=> true,
		'fields'		=> array(
			array(
				'name'		=> _x( 'Version', 'metabox', 'auto-hosted' ),
				'id'		=> 'version',
				'type'		=> 'text',
				'desc'		=> _x( 'Latest version of your plugin', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'Release Date', 'metabox', 'auto-hosted' ),
				'id'		=> 'last_updated',
				'type'		=> 'text',
				'desc'		=> _x( 'Latest version release date (YYYY-MM-DD)', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'WP required', 'metabox', 'auto-hosted' ),
				'id'		=> 'requires',
				'type'		=> 'text',
				'desc'		=> _x( 'Required WordPress Version', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'WP tested', 'metabox', 'auto-hosted' ),
				'id'		=> 'tested',
				'type'		=> 'text',
				'desc'		=> _x( 'Tested WordPress Version', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'Plugin ZIP', 'metabox', 'auto-hosted' ),
				'id'		=> 'download_link',
				'type'		=> 'file',
				'desc'		=> _x( 'Input URL to plugin zip file or upload it.', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'Changelog', 'metabox', 'auto-hosted' ),
				'id'		=> 'section_changelog',
				'type'		=> 'editor_section',
				'desc'		=> _x( 'Plugin Changelog', 'metabox', 'auto-hosted' ),
			),
		),
	);

	/* Theme Updates */
	$meta_boxes[] = array(
		'id'			=> 'theme_repo_data',
		'title'			=> _x( 'Theme Update Data', 'metabox', 'auto-hosted' ),
		'pages'			=> array( 'theme_repo' ),
		'context'		=> 'normal',
		'priority'		=> 'default',
		'show_names'	=> true,
		'fields'		=> array(
			array(
				'name'		=> _x( 'Version', 'metabox', 'auto-hosted' ),
				'id'		=> 'version',
				'type'		=> 'text',
				'desc'		=> _x( 'Latest version of your theme', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'Theme ZIP', 'metabox', 'auto-hosted' ),
				'id'		=> 'download_link',
				'type'		=> 'file',
				'desc'		=> _x( 'Input URL to plugin zip file or upload it.', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'Update detail', 'metabox', 'auto-hosted' ),
				'id'		=> 'theme_changelog_url',
				'type'		=> 'url',
				'desc'		=> _x( 'Theme Changelog URL, Will load in iframe, if empty will use Changelog content input below.', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'Changelog', 'metabox', 'auto-hosted' ),
				'id'		=> 'theme_changelog',
				'type'		=> 'editor',
				'desc'		=> _x( 'Theme Changelog, Will load in iframe, if empty will use your theme uri.', 'metabox', 'auto-hosted' ),
			),
		),
	);

	/* Restrict Plugin and Theme Update */
	$meta_boxes[] = array(
		'id'			=> 'auto_hosted_restrict_update',
		'title'			=> _x( 'Restrict Update', 'metabox', 'auto-hosted' ),
		'pages'			=> array( 'plugin_repo', 'theme_repo' ),
		'context'		=> 'normal',
		'priority'		=> 'low',
		'show_names'	=> true,
		'fields'		=> array(
			array(
				'name'		=> _x( 'Disable Update', 'metabox', 'auto-hosted' ),
				'id'		=> 'disable_update',
				'type'		=> 'checkbox',
				'desc'		=> _x( 'Disable update for this repo.', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'Activation Keys', 'metabox', 'auto-hosted' ),
				'id'		=> 'activation_key',
				'type'		=> 'textarea',
				'desc'		=> _x( 'Activation Key, separate by line.', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'Users email as Key', 'metabox', 'auto-hosted' ),
				'id'		=> 'user_role_email_key',
				'type'		=> 'multicheck',
				'options'	=> auto_hosted_role_options(),
				'desc'		=> _x( 'Select all registered users email in role(s) as Activation Key to get update.', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'Domains Whitelist', 'metabox', 'auto-hosted' ),
				'id'		=> 'domain_whitelist',
				'type'		=> 'textarea',
				'desc'		=> _x( 'Domain Whitelist, separate by line. Subdomain need to be added separately.<br/>Domains in this list will not be checked for Activation Key.', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'Domains Restrict', 'metabox', 'auto-hosted' ),
				'id'		=> 'domain_restrict',
				'type'		=> 'textarea',
				'desc'		=> _x( 'Restrict Domains, separate by line. Subdomain need to be added separately.<br/>Only domains in this list can use automatic update feature.', 'metabox', 'auto-hosted' ),
			),
		),
	);

	/* Plugin Repo Section Info */
	$meta_boxes[] = array(
		'id'			=> 'plugin_sections',
		'title'			=> _x( 'Plugin Sections', 'metabox', 'auto-hosted' ),
		'pages'			=> array( 'plugin_repo' ),
		'context'		=> 'normal',
		'priority'		=> 'low',
		'show_names'	=> true,
		'fields'		=> array(
			array(
				'name'		=> _x( 'Description', 'metabox', 'auto-hosted' ),
				'id'		=> 'section_description',
				'type'		=> 'editor_section',
				'desc'		=> _x( 'Plugin Description', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'FAQ', 'metabox', 'auto-hosted' ),
				'id'		=> 'section_faq',
				'type'		=> 'editor_section',
				'desc'		=> _x( 'Plugin FAQ', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'Screenshots', 'metabox', 'auto-hosted' ),
				'id'		=> 'section_screenshots',
				'type'		=> 'editor_section',
				'desc'		=> _x( 'Plugin Screenshots', 'metabox', 'auto-hosted' ),
			),
			array(
				'name'		=> _x( 'Other Notes', 'metabox', 'auto-hosted' ),
				'id'		=> 'section_other_notes',
				'type'		=> 'editor_section',
				'desc'		=> _x( 'Plugin Other Notes', 'metabox', 'auto-hosted' ),
			),
		),
	);

	/* return the metabox */
	return $meta_boxes;
}


/**
 * User Role Options for Registered User Email as Activation Key.
 * @since 0.1.0
 * @return array of user role available in the site
 */
function auto_hosted_role_options(){

	/* globalize wp_roles object */
	global $wp_roles;

	/* roles */
	$roles = array();
	$roles = $wp_roles->roles;

	/* get list of roles */
	$options = array();
	foreach ( $roles as $role => $role_data ){
		$options[$role] = $role_data['name'];
	}

	/* close sesame */
	return $options;
}