<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://makewebbetter.com/
 * @since             1.0.0
 * @package           Proreview
 *
 * @wordpress-plugin
 * Plugin Name:       proreview
 * Plugin URI:        https://makewebbetter.com/product/proreview/
 * Description:       pro review plugin
 * Version:           1.0.0
 * Author:            makewebbetter
 * Author URI:        https://makewebbetter.com/
 * Text Domain:       proreview
 * Domain Path:       /languages
 *
 * Requires at least: 4.6
 * Tested up to:      4.9.5
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Define plugin constants.
 *
 * @since             1.0.0
 */
function define_proreview_constants() {

	proreview_constants( 'PROREVIEW_VERSION', '1.0.0' );
	proreview_constants( 'PROREVIEW_DIR_PATH', plugin_dir_path( __FILE__ ) );
	proreview_constants( 'PROREVIEW_DIR_URL', plugin_dir_url( __FILE__ ) );
	proreview_constants( 'PROREVIEW_SERVER_URL', 'https://makewebbetter.com' );
	proreview_constants( 'PROREVIEW_ITEM_REFERENCE', 'proreview' );
}


/**
 * Callable function for defining plugin constants.
 *
 * @param   String $key    Key for contant.
 * @param   String $value   value for contant.
 * @since             1.0.0
 */
function proreview_constants( $key, $value ) {

	if ( ! defined( $key ) ) {

		define( $key, $value );
	}
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-proreview-activator.php
 */
function activate_proreview() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-proreview-activator.php';
	Proreview_Activator::proreview_activate();
	$mwb_p_active_plugin = get_option( 'mwb_all_plugins_active', false );
	if ( is_array( $mwb_p_active_plugin ) && ! empty( $mwb_p_active_plugin ) ) {
		$mwb_p_active_plugin['proreview'] = array(
			'plugin_name' => __( 'proreview', 'proreview' ),
			'active' => '1',
		);
	} else {
		$mwb_p_active_plugin = array();
		$mwb_p_active_plugin['proreview'] = array(
			'plugin_name' => __( 'proreview', 'proreview' ),
			'active' => '1',
		);
	}
	update_option( 'mwb_all_plugins_active', $mwb_p_active_plugin );
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-proreview-deactivator.php
 */
function deactivate_proreview() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-proreview-deactivator.php';
	Proreview_Deactivator::proreview_deactivate();
	$mwb_p_deactive_plugin = get_option( 'mwb_all_plugins_active', false );
	if ( is_array( $mwb_p_deactive_plugin ) && ! empty( $mwb_p_deactive_plugin ) ) {
		foreach ( $mwb_p_deactive_plugin as $mwb_p_deactive_key => $mwb_p_deactive ) {
			if ( 'proreview' === $mwb_p_deactive_key ) {
				$mwb_p_deactive_plugin[ $mwb_p_deactive_key ]['active'] = '0';
			}
		}
	}
	update_option( 'mwb_all_plugins_active', $mwb_p_deactive_plugin );
}

register_activation_hook( __FILE__, 'activate_proreview' );
register_deactivation_hook( __FILE__, 'deactivate_proreview' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-proreview.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_proreview() {
	define_proreview_constants();

	$p_plugin_standard = new Proreview();
	$p_plugin_standard->p_run();
	$GLOBALS['p_mwb_p_obj'] = $p_plugin_standard;

}
run_proreview();


// Add settings link on plugin page.
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'proreview_settings_link' );

/**
 * Settings link.
 *
 * @since    1.0.0
 * @param   Array $links    Settings link array.
 */
function proreview_settings_link( $links ) {

	$my_link = array(
		'<a href="' . admin_url( 'admin.php?page=proreview_menu' ) . '">' . __( 'Settings', 'proreview' ) . '</a>',
	);
	return array_merge( $my_link, $links );
}

/**
 * Adding custom setting links at the plugin activation list.
 *
 * @param array  $links_array array containing the links to plugin.
 * @param string $plugin_file_name plugin file name.
 * @return array
*/
function proreview_custom_settings_at_plugin_tab( $links_array, $plugin_file_name ) {
	if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
		$links_array[] = '<a href="#" target="_blank"><img src="' . esc_html( PROREVIEW_DIR_URL ) . 'admin/image/Demo.svg" class="mwb-info-img" alt="Demo image">'.__( 'Demo', 'proreview' ).'</a>';
		$links_array[] = '<a href="#" target="_blank"><img src="' . esc_html( PROREVIEW_DIR_URL ) . 'admin/image/Documentation.svg" class="mwb-info-img" alt="documentation image">'.__( 'Documentation', 'proreview' ).'</a>';
		$links_array[] = '<a href="#" target="_blank"><img src="' . esc_html( PROREVIEW_DIR_URL ) . 'admin/image/Support.svg" class="mwb-info-img" alt="support image">'.__( 'Support', 'proreview' ).'</a>';
	}
	return $links_array;
}
add_filter( 'plugin_row_meta', 'proreview_custom_settings_at_plugin_tab', 10, 2 );
// add_filter( 'gettext', 'my_custom_column_function', 10, 2 );

// function my_custom_column_function($text, $domain=""){
//  if($domain === 'woocommerce' && $text === 'Order by'){
//    $text = $text . "</th><th>My Column Name";
//  } else if($domain === 'woocommerce' && ($text === 'Name' || $text === 'Name (numeric)' || 
//  $text === 'Term ID' || $text === 'Custom ordering')){
//    // Some code here to create the output as text string
//    $example = 1 + 2;
//    $text = $text . "</td><td>" . $example;
//  }
//  return $text;
// }
// add_filter( 'manage_edit-product_attributes_columns', 'mishafunction' );
// function mishafunction( $columns ){
// 	// do something with $columns array
// 	$columns['dsasd'] = 'sdfadsfas';
// 	return $columns;
// }


// function myplugin_plugin_path() {

// 	// gets the absolute path to this plugin directory
   
// 	return untrailingslashit( plugin_dir_path( __FILE__ ) );
//    }
//    add_filter( 'woocommerce_locate_template', 'myplugin_woocommerce_locate_template', 10, 3 );
   
   
   
//    function myplugin_woocommerce_locate_template( $template, $template_name, $template_path ) {
// 	global $woocommerce;
   
// 	$_template = $template;
   
// 	if ( ! $template_path ) $template_path = $woocommerce->template_url;
   
// 	$plugin_path  = myplugin_plugin_path() . '/woocommerce/';
   
// // 	echo $template_name;
// 	// Look within passed path within the theme - this is priority
// 	$template = locate_template(
   
// 	  array(
// 	    $template_path . $template_name,
// 	    $template_name
// 	  )
// 	);
   
// 	echo '<pre>'; print_r( $template ); echo '</pre>';
// 	die;

// 	// Modification: Get the template from this plugin, if it exists
// 	if ( ! $template && file_exists( $plugin_path . $template_name ) )
// 	  $template = $plugin_path . $template_name;
   
// 	// Use default template
// 	if ( ! $template )
// 	  $template = $_template;
   
// 	// Return what we found
// 	return $template;
//    }

function mwb_pro_review_override_template( $template ) {
	global $woocommerce;
	if ( get_post_type() == 'product' && file_exists( untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/woocommerce/single-product-reviews.php' ) ) {
		return plugin_dir_path( __FILE__ ) . '/woocommerce/single-product-reviews.php';
	}
	return $woocommerce->comments_template_loader( $template );
}
add_filter( 'comments_template', 'mwb_pro_review_override_template', 100, 1 );

// add_action( 'init','dfdasf' );
// function dfdasf() {
// 	global $wpdb;

// $results = $wpdb->get_results("SELECT  post_title, CASE WHEN  meta_key = '_price' THEN ( wp_postmeta.meta_value ) END AS PRICE , (SELECT meta_value from wp_postmeta where meta_key = '_sku' AND wp_postmeta.post_id  = wp_posts.ID
// ) AS SKU FROM wp_postmeta LEFT JOIN  wp_posts ON wp_postmeta.post_id  = wp_posts.ID where post_type = 'product' HAVING PRICE");
// echo '<pre>'; print_r( $results ); echo '</pre>';
// die;
// }

