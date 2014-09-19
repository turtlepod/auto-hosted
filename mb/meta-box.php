<?php
/**
 * Auto Hosted Meta Box Class
 *
 * Helper Class to easily create meta boxes
 * Based on Custom Metaboxes and Fields v.0.9 by:
 * - Andrew Norcross (@norcross / andrewnorcross.com)
 * - Jared Atchison (@jaredatch / jaredatchison.com)
 * - Bill Erickson (@billerickson / billerickson.net)
 * 
 * @link https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
 * 
 * @package 	AutoHosted
 * @copyright	Copyright (c) 2012, David Chandra Purnama
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @version		0.1.0
 */
if ( ! class_exists( 'Auto_Hosted_Metabox_Validate' ) ){

/**
 * Create Metaboxes.
 * @since 0.1.0
 */
add_action('init','auto_hosted_metabox_create' );
function auto_hosted_metabox_create(){
	static $meta_boxes = null;

	if (!$meta_boxes)
		$meta_boxes = array();

	$meta_boxes = apply_filters ( 'auto_hosted_metaboxes' , $meta_boxes );

	foreach ( (array) $meta_boxes as $meta_box ) {
		$my_box = new Auto_Hosted_Metabox( $meta_box );
	}
}

/**
 * Validate value of meta fields
 *
 * Define ALL validation methods inside this class and use the names of these 
 * methods in the definition of meta boxes (key 'validate_func' of each field)
 */
class Auto_Hosted_Metabox_Validate {
	function check_text( $text ) {
		if ( $text != 'hello' ) {
			return false;
		}
		return true;
	}
}


/**
 * Auto Hosted Meta Box Class
 * Helper Class to easily create meta boxes
 */
class Auto_Hosted_Metabox {

	protected $_meta_box;

	function __construct( $meta_box ) {

		if ( !is_admin() ) return;

		$this->_meta_box = $meta_box;

		$upload = false;

		foreach ( $meta_box['fields'] as $field ) {
			if ( $field['type'] == 'file' ) {
				$upload = true;
				break;
			}
		}

		global $pagenow;

		if ( $upload && in_array( $pagenow, array( 'page.php', 'page-new.php', 'post.php', 'post-new.php' ) ) ) {
			add_action( 'admin_head', array( &$this, 'add_post_enctype' ) );
		}

		add_action( 'admin_menu', array( &$this, 'add' ) );
		add_action( 'save_post', array( &$this, 'save' ) );

		/* only if WordPress at least version 3.5 */
		global $wp_version;

		if ( version_compare( round( $wp_version, 1 ), '3.5' ) >= 0  ){

			add_action( 'add_attachment', array( &$this, 'save' ), 1 );
			add_action( 'edit_attachment', array( &$this, 'save' ), 1 );
		}
	}

	/* For uploads */
	function add_post_enctype() {

		echo '
		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery("#post").attr("enctype", "multipart/form-data");
			jQuery("#post").attr("encoding", "multipart/form-data");
		});
		</script>';
	}

	/* Add metaboxes */
	function add() {

		$this->_meta_box['context'] = empty($this->_meta_box['context']) ? 'normal' : $this->_meta_box['context'];

		$this->_meta_box['priority'] = empty($this->_meta_box['priority']) ? 'high' : $this->_meta_box['priority'];

		$this->_meta_box['show_on'] = empty( $this->_meta_box['show_on'] ) ? array('key' => false, 'value' => false) : $this->_meta_box['show_on'];

		foreach ( $this->_meta_box['pages'] as $page ) {
			add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'show'), $page, $this->_meta_box['context'], $this->_meta_box['priority']) ;
		}
	}

	/* Show fields */
	function show() {

		global $post;

		/* Use nonce for verification */
		echo '<input type="hidden" name="wp_meta_box_nonce" value="', wp_create_nonce( basename(__FILE__) ), '" />';
		echo '<table class="form-table ahs_metabox">';

		foreach ( $this->_meta_box['fields'] as $field ) {

			/* Set up blank or default values for empty ones */
			if ( !isset( $field['name'] ) ) $field['name'] = '';
			if ( !isset( $field['desc'] ) ) $field['desc'] = '';
			if ( !isset( $field['std'] ) ) $field['std'] = '';
			if ( 'file' == $field['type'] && !isset( $field['allow'] ) ) $field['allow'] = array( 'url', 'attachment' );
			if ( 'file' == $field['type'] && !isset( $field['save_id'] ) )  $field['save_id']  = false;
			if ( 'multicheck' == $field['type'] ) $field['multiple'] = true;  
			$meta = get_post_meta( $post->ID, $field['id'], 'multicheck' != $field['type'] );

			/* open sesame */
			echo '<tr>';

			if( $this->_meta_box['show_names'] == true ) {
				echo '<th style="width:18%"><label for="', $field['id'], '">', $field['name'], '</label></th>';
			}

			echo '<td>';

			switch ( $field['type'] ) {

				case 'text':
					echo '<input class="ahs_text_small" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? $meta : $field['std'], '" /><span class="ahs_metabox_description">', $field['desc'], '</span>';
					break;
				case 'slug':
					$extrapostdata = get_post(get_the_ID(), ARRAY_A);
					$slug = $extrapostdata['post_name'];
					echo '<input class="ahs_text_medium" type="text" value="' . $slug . '" readonly="readonly"/><span class="ahs_metabox_description">', $field['desc'], '</span>';
					break;
				case 'home_url':
					$home_url = trailingslashit( home_url() );
					echo '<input class="ahs_text_medium" type="text" value="' . $home_url . '" readonly="readonly"/><span class="ahs_metabox_description">', $field['desc'], '</span>';
					break;
				case 'url':
					echo '<input class="ahs_text" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? $meta : $field['std'], '" /><span class="ahs_metabox_description">', $field['desc'], '</span>';
					break;
				case 'textarea':
					echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="10">', '' !== $meta ? $meta : $field['std'], '</textarea>','<p class="ahs_metabox_description">', $field['desc'], '</p>';
					break;
				case 'checkbox':
					echo '<input type="checkbox" value="1" name="'.$field['id'].'" '. checked( ! empty( $meta ),true ,false ) .' />';
					echo '<span class="ahs_metabox_description">', $field['desc'], '</span>';
					break;
				case 'multicheck':
					echo '<ul>';
					$i = 1;
					foreach ( $field['options'] as $value => $name ) {
						echo '<li><input type="checkbox" name="', $field['id'], '[]" id="', $field['id'], $i, '" value="', $value, '"', in_array( $value, $meta ) ? ' checked="checked"' : '', ' /><label for="', $field['id'], $i, '">', $name, '</label></li>';
						$i++;
					}
					echo '</ul>';
					echo '<span class="ahs_metabox_description">', $field['desc'], '</span>';
					break;
				case 'editor':
					wp_editor( $meta ? $meta : $field['std'], $field['id'], isset( $field['options'] ) ? $field['options'] : array() );
			        echo '<p class="ahs_metabox_description">', $field['desc'], '</p>';
					break;
				case 'editor_section':
					wp_editor( $meta ? $meta : $field['std'], $field['id'], isset( $field['options'] ) ? $field['options'] : array() );
			        echo '<p class="ahs_metabox_description">', $field['desc'], '</p>';
					break;
				case 'file':
					wp_enqueue_script( 'ahs-meta-box-uploader-scripts' );
					$input_type_url = "hidden";
					if ( 'url' == $field['allow'] || ( is_array( $field['allow'] ) && in_array( 'url', $field['allow'] ) ) )
						$input_type_url="text";
					echo '<input class="ahs_upload_file" type="' . $input_type_url . '" size="45" id="', $field['id'], '" name="', $field['id'], '" value="', $meta, '" />';
					echo '<input class="ahs_upload_button button" type="button" value="Upload File" />';
					echo '<input class="ahs_upload_file_id" type="hidden" id="', $field['id'], '_id" name="', $field['id'], '_id" value="', get_post_meta( $post->ID, $field['id'] . "_id",true), '" />';
					echo '<p class="ahs_metabox_description">', $field['desc'], '</p>';
					echo '<div id="', $field['id'], '_status" class="ahs_upload_status">';	
						if ( $meta != '' ) { 
							$check_zip = preg_match( '/(^.*\.zip|ZIP*)/i', $meta );
							if ( $check_zip ) {
								$parts = explode( '/', $meta );
								for( $i = 0; $i < count( $parts ); ++$i ) {
									$title = $parts[$i];
								}
								echo 'File: <strong>', $title, '</strong>&nbsp;&nbsp;&nbsp; (<a href="', $meta, '" rel="external">Download</a> / <a href="#" class="ahs_remove_file_button" rel="', $field['id'], '">Remove</a>)';
							}
							else {
								echo 'File: <strong>not supported</strong>, only zip file supported.';
							}
						}
					echo '</div>'; 
				break;
				default:
					do_action('ahs_render_' . $field['type'] , $field, $meta);
			}

			/* close sesame */
			echo '</td>' . '</tr>';
		}
		echo '</table>';
	}

	/* Save data from metabox */
	function save( $post_id)  {

		/* verify nonce */
		if ( ! isset( $_POST['wp_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['wp_meta_box_nonce'], basename(__FILE__) ) ) {
			return $post_id;
		}

		/* check autosave */
		if ( defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		/* check permissions */
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} elseif ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		foreach ( $this->_meta_box['fields'] as $field ) {

			$name = $field['id'];
			if ( ! isset( $field['multiple'] ) ) $field['multiple'] = ( 'multicheck' == $field['type'] ) ? true : false;
			$old = get_post_meta( $post_id, $name, !$field['multiple'] );
			$new = isset( $_POST[$field['id']] ) ? $_POST[$field['id']] : null;

			if ( ($field['type'] == 'textarea') ) {
				$new = htmlspecialchars( $new );
			}

			$new = apply_filters( 'ahs_validate_' . $field['type'], $new, $post_id, $field );

			/* validate meta value */
			if ( isset( $field['validate_func']) ) {
				$ok = call_user_func( array( 'Auto_Hosted_Metabox_Validate', $field['validate_func']), $new );
				if ( $ok === false ) continue;
			}
			elseif ( $field['multiple'] ) {
				delete_post_meta( $post_id, $name );
				if ( !empty( $new ) ) {
					foreach ( $new as $add_new ) {
						add_post_meta( $post_id, $name, $add_new, false );
					}
				}
			}
			elseif ( '' !== $new && $new != $old  ) {
				update_post_meta( $post_id, $name, $new );
			}
			elseif ( '' == $new ) {
				delete_post_meta( $post_id, $name );
			}

			if ( 'file' == $field['type'] ) {
				$name = $field['id'] . "_id";
				$old = get_post_meta( $post_id, $name, !$field['multiple'] );
				if ( isset( $field['save_id'] ) && $field['save_id'] ) {
					$new = isset( $_POST[$name] ) ? $_POST[$name] : null;
				} else {
					$new = "";
				}
				if ( $new && $new != $old ) {
					update_post_meta( $post_id, $name, $new );
				} elseif ( '' == $new && $old ) {
					delete_post_meta( $post_id, $name, $old );
				}
			}
		}
	}
}

/**
 * Register and Enqueque Script and Style For Metaboxes
 * This will only loaded in admin, in post screen.
 */
add_action( 'admin_enqueue_scripts', 'ahs_scripts', 10 );

function ahs_scripts( $hook ) {

	global $post_type;

	if ( $post_type == 'plugin_repo' ||  $post_type == 'theme_repo' ) {

		if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'page-new.php' || $hook == 'page.php' ) {

			/* uploader js */
			wp_register_script( 'ahs-meta-box-uploader-scripts', trailingslashit( plugin_dir_url( __FILE__) ) . 'js/meta-box-uploader.js', array( 'jquery', 'jquery-ui-core', 'media-upload', 'thickbox' ), 'ahs.'. AUTOHOSTED_VERSION );

			/* css */
			wp_enqueue_style( 'ahs-meta-box-styles', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/meta-box.css', array( 'thickbox', 'farbtastic' ), 'ahs.'. AUTOHOSTED_VERSION );
		}
	}
}

/**
 * Editor Footer Script
 */
add_action( 'admin_print_footer_scripts', 'ahs_editor_footer_scripts', 99 );

function ahs_editor_footer_scripts() { ?>
	<?php
	if ( isset( $_GET['ahs_force_send'] ) && 'true' == $_GET['ahs_force_send'] ) { 
		$label = $_GET['ahs_send_label']; 
		if ( empty( $label ) ) $label="Select File";
		?>	
		<script type="text/javascript">
		jQuery(function($) {
			$('td.savesend input').val('<?php echo $label; ?>');
		});
		</script>
		<?php 
	}
}

/**
 * Force 'Insert into Post' button from Media Library 
 * 
 */
add_filter( 'get_media_item_args', 'ahs_force_send' );

function ahs_force_send( $args ) {

	// if the Gallery tab is opened from a custom meta box field, add Insert Into Post button
	if ( isset( $_GET['ahs_force_send'] ) && 'true' == $_GET['ahs_force_send'] )
		$args['send'] = true;

	// if the From Computer tab is opened AT ALL, add Insert Into Post button after an image is uploaded
	if ( isset( $_POST['attachment_id'] ) && '' != $_POST["attachment_id"] ) {

		$args['send'] = true;

	}

	// change the label of the button on the From Computer tab
	if ( isset( $_POST['attachment_id'] ) && '' != $_POST["attachment_id"] ) {

		echo '
			<script type="text/javascript">
				function ahsGetParameterByNameInline(name) {
					name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
					var regexS = "[\\?&]" + name + "=([^&#]*)";
					var regex = new RegExp(regexS);
					var results = regex.exec(window.location.href);
					if(results == null)
						return "";
					else
						return decodeURIComponent(results[1].replace(/\+/g, " "));
				}

				jQuery(function($) {
					if (ahsGetParameterByNameInline("ahs_force_send")=="true") {
						var ahs_send_label = ahsGetParameterByNameInline("ahs_send_label");
						$("td.savesend input").val(ahs_send_label);
					}
				});
			</script>
		';
	}
	return $args;
}
// End. That's it, folks! //


/**
 * Validation Functions
 * 
 * @since 0.2.2
 */
add_filter( 'ahs_validate_file', 'ahs_validate_file' );
add_filter( 'ahs_validate_text', 'ahs_validate_text' );
add_filter( 'ahs_validate_url', 'ahs_validate_url' );
add_filter( 'ahs_validate_textarea', 'ahs_validate_textarea' );
add_filter( 'ahs_validate_editor_section', 'ahs_validate_editor_section' );
add_filter( 'ahs_validate_editor', 'ahs_validate_editor' );


/* File Uploader */
function ahs_validate_file( $input ) {
	$filetype = wp_check_filetype($input);
	if ( $filetype["ext"] == 'zip' ) 
		$output = esc_url_raw( $input );
	else
		$output = '';
	return $output;
}

/* Text */
function ahs_validate_text( $input ){
	$output = strip_tags( $input );
	return $output;
}

/* URL */
function ahs_validate_url( $input ){
	$output = esc_url_raw( $input );
	return $output;
}

/* Text Area */
function ahs_validate_textarea( $input ){
	$output = strip_tags( $input );
	return $output;
}

/* Plugin Sections */
function ahs_validate_editor_section( $input ){

	/* allowed tags */
	$plugins_allowedtags = array(
		'a' => array( 'href' => array(), 'title' => array(), 'target' => array() ),
		'abbr' => array( 'title' => array() ), 'acronym' => array( 'title' => array() ),
		'code' => array(), 'pre' => array(), 'em' => array(), 'strong' => array(),
		'div' => array(), 'p' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(),
		'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(),
		'img' => array( 'src' => array(), 'class' => array(), 'alt' => array() )
	);

	$output = wp_kses( wpautop( $input ), $plugins_allowedtags );
	return $output;
}

/* Editor */
function ahs_validate_editor( $input ) {
	global $allowedtags;
	if ( current_user_can( 'unfiltered_html' ) )
		$output = wpautop( $input );
	else
		$output = wp_kses( wpautop( $input ), $allowedtags);
	return $output;
}

} //class exist check
