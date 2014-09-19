<?php
/**
 * Manage Custom Column in Edit Screen.
 *
 * @package AutoHosted
 * @subpackage Includes
 * @since 0.1.0
 * @author David Chandra Purnama <david.warna@gmail.com>
 * @copyright Copyright (c) 2013, David Chandra Purnama
 * @link http://autohosted
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Set up the admin functionality. */
add_action( 'admin_menu', 'auto_hosted_admin_setup' );

/**
 * Adds actions where needed for setting up the plugin's admin functionality.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function auto_hosted_admin_setup() {

	/* Custom columns on the edit plugin_repo screen. */
	add_filter( 'manage_plugin_repo_posts_columns', 'auto_hosted_edit_plugin_repo_columns' );
	add_action( 'manage_plugin_repo_posts_custom_column', 'autohosted_manage_repo_columns', 10, 2 );

	/* Custom columns on the edit theme_repo screen. */
	add_filter( 'manage_theme_repo_posts_columns', 'auto_hosted_edit_theme_repo_columns' );
	add_action( 'manage_theme_repo_posts_custom_column', 'autohosted_manage_repo_columns', 10, 2 );

	/* head css and script */
	add_action( 'admin_head', 'auto_hosted_admin_head_style' );
	add_action( 'admin_head-edit.php', 'auto_hosted_admin_head_script' );

	/* css */
	add_action( 'admin_enqueue_scripts', 'auto_hosted_manage_column_style' );
}


/**
 * Sets up custom columns on the plugin_repo edit screen.
 *
 * @since  0.1.0
 */
function auto_hosted_edit_plugin_repo_columns( $columns ) {

	unset( $columns['title'] );
	unset( $columns['taxonomy-plugin_repo_cat'] );
	unset( $columns['taxonomy-plugin_repo_tag'] );

	$new_columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => _x( 'Plugins', 'edit', 'auto-hosted' )
	);

	$new_columns['update_info'] = _x( 'Info', 'edit', 'auto-hosted' );
	$new_columns['taxonomy-plugin_repo_cat'] = _x( 'Categories', 'edit', 'auto-hosted' );
	$new_columns['taxonomy-plugin_repo_tag'] = _x( 'Tags', 'edit', 'auto-hosted' );

	return array_merge( $new_columns, $columns );
}


/**
 * Sets up custom columns on the plugin_repo edit screen.
 *
 * @since  0.1.0
 */
function auto_hosted_edit_theme_repo_columns( $columns ) {

	unset( $columns['title'] );
	unset( $columns['taxonomy-theme_repo_cat'] );
	unset( $columns['taxonomy-theme_repo_tag'] );

	$new_columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => _x( 'Themes', 'edit', 'auto-hosted' )
	);

	$new_columns['update_info'] = _x( 'Info', 'edit', 'auto-hosted' );
	$new_columns['taxonomy-theme_repo_cat'] = _x( 'Categories', 'edit', 'auto-hosted' );
	$new_columns['taxonomy-theme_repo_tag'] = _x( 'Tags', 'edit', 'auto-hosted' );

	return array_merge( $new_columns, $columns );
}


/**
 * Displays the content of repo columns on the edit screen.
 *
 * @since  0.1.0
 */
function autohosted_manage_repo_columns( $column, $post_id ) {
	global $post;

	/* ID */
	$id = get_the_ID();

	/* version */
	$version_meta = get_post_meta( $id, 'version', true );
	$version = ( $version_meta ) ? $version_meta : _x( 'N/A', 'edit', 'auto-hosted' ) ;

	/* download */
	$download_meta = get_post_meta( $id, 'download_link', true );
	$protect_download = get_post_meta( $id, 'protect_download', true);
	$download_class = ( $protect_download ) ? '-protected' : '' ;
	$download = ( $download_meta ) ? '<a href="'.$download_meta.'"><span class="download' . $download_class .'">' . _x( 'Download', 'edit', 'auto-hosted' ) . '</span></a>' : _x( 'Download', 'edit', 'auto-hosted' ) ;

	/* status */
	$status_class = '';
	$status = _x( 'incomplete', 'edit', 'auto-hosted' );
	$status_class = 'incomplete';
	if ( $version_meta && $download_meta ){
		$status = _x( 'available', 'edit', 'auto-hosted' );
		$status_class = 'available';
	}
	if ( get_post_meta( $id, 'disable_update', true ) ){
		$status = _x( 'disabled', 'edit', 'auto-hosted' );
		$status_class = 'disabled';
	}

	/* slug */
	$extrapostdata = get_post( $id, ARRAY_A );
	$slug = $extrapostdata['post_name'];

	/* other info */
	$key_info = ( get_post_meta( $id, 'activation_key', true) ) ? '<span class="key-needed" title="Activation key"></span>' : '';
	$user_info = ( get_post_meta( $id, 'user_role_email_key', true) ) ? '<span class="user-key" title="Registered user selected"></span>' : '';
	$domain_info = ( get_post_meta( $id, 'domain_restrict', true) ) ? '<span class="domain-restricted" title="Domain restricted"></span>' : '';

	switch( $column ) {

		case 'update_info' :
			/* version */
			echo '<strong>' . $version . '</strong>';
			/* download link */
			echo ' ( '. $download . ' )';
			/* update status */
			echo '<div class="update-status update-status-'.$status_class.'">'. $status . '</div>';
			/* slug + other info */
			echo '<div class="extra-update-data">';
			echo '<input type="text" readonly="readonly" class="update-data-slug" id="updater-slug-'.$id.'" onClick="SelectAll(\'updater-slug-'.$id.'\');" value="'.$slug.'" />';
			echo $key_info;
			echo $user_info;
			echo $domain_info;
			echo '</div>';
			break;

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}


/**
 * Admin head style for Screen Icon
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function auto_hosted_admin_head_style() {
	global $post_type;
	if ( 'plugin_repo' === $post_type || 'theme_repo' === $post_type ) { ?>
	<style type="text/css">
		#icon-edit.icon32-posts-plugin_repo{
			background: transparent url( '<?php echo AUTOHOSTED_URI . 'images/screen-icon-plugin.png'; ?>' ) no-repeat;
		}
		#icon-edit.icon32-posts-theme_repo{
			background: transparent url( '<?php echo AUTOHOSTED_URI . 'images/screen-icon-theme.png'; ?>' ) no-repeat;
		}
	</style>
	<?php }
}

/**
 * Admin head script for select input and home url input 
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function auto_hosted_admin_head_script() {
	global $post_type;
	if ( 'plugin_repo' === $post_type || 'theme_repo' === $post_type ) { ?>
	<script type="text/javascript">
	function SelectAll(id){
		document.getElementById(id).focus();
		document.getElementById(id).select();
	}
	jQuery(document).ready(function($){
	if ( $('h2').length > 0 ) {
		jQuery("h2").after('<p class="updater-home-url"><input type="text" value="<?php echo trailingslashit( home_url() );?>" onClick="SelectAll(\'updater-repo-uri\');" id="updater-repo-uri" readonly="readonly"></p>');
	}
	});
	</script>
	<?php }
}


/**
 * Style and Script
 * 
 * @since 0.1.0
 */
function auto_hosted_manage_column_style(){
	global $pagenow, $post_type;

	/* check post type */
	if ( $post_type == 'plugin_repo' ||  $post_type == 'theme_repo' ) {

		/* only in column admin screen */
		if ($pagenow == 'edit.php'){

			/* style */
			wp_enqueue_style( 'auto-hosted-column', AUTOHOSTED_URI . 'css/edit.css', false, AUTOHOSTED_VERSION );
		}
	}
}