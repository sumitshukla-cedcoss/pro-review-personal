<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Proreview
 * @subpackage Proreview/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Proreview
 * @subpackage Proreview/admin
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Proreview_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string $hook      The plugin page slug.
	 */
	public function p_admin_enqueue_styles( $hook ) {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'makewebbetter_page_proreview_menu' == $screen->id ) {

			wp_enqueue_style( 'mwb-p-select2-css', PROREVIEW_DIR_URL . 'package/lib/select-2/proreview-select2.css', array(), time(), 'all' );

			wp_enqueue_style( 'mwb-p-meterial-css', PROREVIEW_DIR_URL . 'package/lib/material-design/material-components-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'mwb-p-meterial-css2', PROREVIEW_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'mwb-p-meterial-lite', PROREVIEW_DIR_URL . 'package/lib/material-design/material-lite.min.css', array(), time(), 'all' );

			wp_enqueue_style( 'mwb-p-meterial-icons-css', PROREVIEW_DIR_URL . 'package/lib/material-design/icon.css', array(), time(), 'all' );

			wp_enqueue_style( $this->plugin_name . '-admin-global', PROREVIEW_DIR_URL . 'admin/src/scss/proreview-admin-global.css', array( 'mwb-p-meterial-icons-css' ), time(), 'all' );

			wp_enqueue_style( $this->plugin_name, PROREVIEW_DIR_URL . 'admin/src/scss/proreview-admin.scss', array(), $this->version, 'all' );
			wp_enqueue_style( 'mwb-admin-min-css', PROREVIEW_DIR_URL . 'admin/css/mwb-admin.min.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string $hook      The plugin page slug.
	 */
	public function p_admin_enqueue_scripts( $hook ) {

		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'makewebbetter_page_proreview_menu' == $screen->id ) {
			wp_enqueue_script( 'mwb-p-select2', PROREVIEW_DIR_URL . 'package/lib/select-2/proreview-select2.js', array( 'jquery' ), time(), false );

			wp_enqueue_script( 'mwb-p-metarial-js', PROREVIEW_DIR_URL . 'package/lib/material-design/material-components-web.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-p-metarial-js2', PROREVIEW_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-p-metarial-lite', PROREVIEW_DIR_URL . 'package/lib/material-design/material-lite.min.js', array(), time(), false );

			wp_register_script( $this->plugin_name . 'admin-js', PROREVIEW_DIR_URL . 'admin/src/js/proreview-admin.js', array( 'jquery', 'mwb-p-select2', 'mwb-p-metarial-js', 'mwb-p-metarial-js2', 'mwb-p-metarial-lite' ), $this->version, false );

			wp_localize_script(
				$this->plugin_name . 'admin-js',
				'p_admin_param',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'reloadurl' => admin_url( 'admin.php?page=proreview_menu' ),
					'p_gen_tab_enable' => get_option( 'p_radio_switch_demo' ),
				)
			);

			wp_enqueue_script( $this->plugin_name . 'admin-js' );
		}
		wp_register_script( $this->plugin_name . 'custom-js', PROREVIEW_DIR_URL . 'admin/js/mwb-proreview-custom.js', array( 'jquery' ), $this->version, false );

			wp_localize_script(
				$this->plugin_name . 'custom-js',
				'p_custom_param',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'mwb_review_pin' ),
				)
			);

			wp_enqueue_script( $this->plugin_name . 'custom-js' );
	}

	/**
	 * Adding settings menu for proreview.
	 *
	 * @since    1.0.0
	 */
	public function p_options_page() {
		global $submenu;
		if ( empty( $GLOBALS['admin_page_hooks']['mwb-plugins'] ) ) {
			add_menu_page( __( 'MakeWebBetter', 'proreview' ), __( 'MakeWebBetter', 'proreview' ), 'manage_options', 'mwb-plugins', array( $this, 'mwb_plugins_listing_page' ), PROREVIEW_DIR_URL . 'admin/src/images/MWB_Grey-01.svg', 15 );
			$p_menus = apply_filters( 'mwb_add_plugins_menus_array', array() );
			if ( is_array( $p_menus ) && ! empty( $p_menus ) ) {
				foreach ( $p_menus as $p_key => $p_value ) {
					add_submenu_page( 'mwb-plugins', $p_value['name'], $p_value['name'], 'manage_options', $p_value['menu_link'], array( $p_value['instance'], $p_value['function'] ) );
				}
			}
		}
	}

	/**
	 * Removing default submenu of parent menu in backend dashboard
	 *
	 * @since   1.0.0
	 */
	public function mwb_p_remove_default_submenu() {
		global $submenu;
		if ( is_array( $submenu ) && array_key_exists( 'mwb-plugins', $submenu ) ) {
			if ( isset( $submenu['mwb-plugins'][0] ) ) {
				unset( $submenu['mwb-plugins'][0] );
			}
		}
	}


	/**
	 * proreview p_admin_submenu_page.
	 *
	 * @since 1.0.0
	 * @param array $menus Marketplace menus.
	 */
	public function p_admin_submenu_page( $menus = array() ) {
		$menus[] = array(
			'name'            => __( 'proreview', 'proreview' ),
			'slug'            => 'proreview_menu',
			'menu_link'       => 'proreview_menu',
			'instance'        => $this,
			'function'        => 'p_options_menu_html',
		);
		return $menus;
	}


	/**
	 * proreview mwb_plugins_listing_page.
	 *
	 * @since 1.0.0
	 */
	public function mwb_plugins_listing_page() {
		$active_marketplaces = apply_filters( 'mwb_add_plugins_menus_array', array() );
		if ( is_array( $active_marketplaces ) && ! empty( $active_marketplaces ) ) {
			require PROREVIEW_DIR_PATH . 'admin/partials/welcome.php';
		}
	}

	/**
	 * proreview admin menu page.
	 *
	 * @since    1.0.0
	 */
	public function p_options_menu_html() {

		include_once PROREVIEW_DIR_PATH . 'admin/partials/proreview-admin-dashboard.php';
	}


	/**
	 * proreview admin menu page.
	 *
	 * @since    1.0.0
	 * @param array $p_settings_general Settings fields.
	 */
	public function p_admin_general_settings_page( $p_settings_general ) {
		$mwb_prfw_attribute_data = wc_get_attribute_taxonomies();
		$data = array();
		foreach ( $mwb_prfw_attribute_data as $k => $v ) {
			$data[ $v->attribute_name ] = $v->attribute_name;
		}
		$p_settings_general = array(
			array(
				'title' => __( 'Enable plugin', 'proreview' ),
				'type'  => 'radio-switch',
				'description'  => __( 'Enable plugin to start the functionality.', 'proreview' ),
				'id'    => 'p_radio_switch_demo',
				'value' => get_option( 'p_radio_switch_demo' ),
				'class' => 'p-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'proreview' ),
					'no' => __( 'NO', 'proreview' ),
				),
			),
			array(
				'title' => __( 'Choose Attribute to show on review form', 'proreview' ),
				'type'  => 'multiselect',
				'description'  => __( 'Choose Attribute to show on review form.', 'proreview' ),
				'id'    => 'mwb_multiselect',
				'value' => get_option( 'mwb_multiselect' ),
				'class' => 'p-multiselect-class mwb-defaut-multiselect',
				'placeholder' => '',
				'name' => 'mwb_multiselect',
				'options' => $data,
			),
			array(
				'title' => __( 'Minimun attribute value', 'proreview' ),
				'type'  => 'number',
				'description'  => __( 'This is number field demo follow same structure for further use.', 'proreview' ),
				'id'    => 'mwb_min_attr_val',
				'value' => get_option( 'mwb_min_attr_val' ),
				'class' => 'p-number-class',
				'placeholder' => '',
			),
			array(
				'title' => __( 'Maximum attribute value', 'proreview' ),
				'type'  => 'number',
				'description'  => __( 'This is number field demo follow same structure for further use.', 'proreview' ),
				'id'    => 'mwb_max_attr_val',
				'value' => get_option( 'mwb_min_attr_val' ),
				'class' => 'p-number-class',
				'placeholder' => '',
			),
			array(
				'type'  => 'button',
				'id'    => 'p_button',
				'button_text' => __( 'Button Demo', 'proreview' ),
				'class' => 'p-button-class',
			),
		);
		return $p_settings_general;
	}

	/**
	 * proreview admin menu page.
	 *
	 * @since    1.0.0
	 * @param array $p_settings_template Settings fields.
	 */
	public function p_admin_template_settings_page( $p_settings_template ) {
		$p_settings_template = array(
			array(
				'title' => __( 'Text Field Demo', 'proreview' ),
				'type'  => 'text',
				'description'  => __( 'This is text field demo follow same structure for further use.', 'proreview' ),
				'id'    => 'p_text_demo',
				'value' => '',
				'class' => 'p-text-class',
				'placeholder' => __( 'Text Demo', 'proreview' ),
			),
			array(
				'title' => __( 'Number Field Demo', 'proreview' ),
				'type'  => 'number',
				'description'  => __( 'This is number field demo follow same structure for further use.', 'proreview' ),
				'id'    => 'p_number_demo',
				'value' => '',
				'class' => 'p-number-class',
				'placeholder' => '',
			),
			array(
				'title' => __( 'Password Field Demo', 'proreview' ),
				'type'  => 'password',
				'description'  => __( 'This is password field demo follow same structure for further use.', 'proreview' ),
				'id'    => 'p_password_demo',
				'value' => '',
				'class' => 'p-password-class',
				'placeholder' => '',
			),
			array(
				'title' => __( 'Textarea Field Demo', 'proreview' ),
				'type'  => 'textarea',
				'description'  => __( 'This is textarea field demo follow same structure for further use.', 'proreview' ),
				'id'    => 'p_textarea_demo',
				'value' => '',
				'class' => 'p-textarea-class',
				'rows' => '5',
				'cols' => '10',
				'placeholder' => __( 'Textarea Demo', 'proreview' ),
			),
			array(
				'title' => __( 'Select Field Demo', 'proreview' ),
				'type'  => 'select',
				'description'  => __( 'This is select field demo follow same structure for further use.', 'proreview' ),
				'id'    => 'p_select_demo',
				'value' => '',
				'class' => 'p-select-class',
				'placeholder' => __( 'Select Demo', 'proreview' ),
				'options' => array(
					'' => __( 'Select option', 'proreview' ),
					'INR' => __( 'Rs.', 'proreview' ),
					'USD' => __( '$', 'proreview' ),
				),
			),
			array(
				'title' => __( 'Multiselect Field Demo', 'proreview' ),
				'type'  => 'multiselect',
				'description'  => __( 'This is multiselect field demo follow same structure for further use.', 'proreview' ),
				'id'    => 'p_multiselect_demo',
				'value' => '',
				'class' => 'p-multiselect-class mwb-defaut-multiselect',
				'placeholder' => '',
				'options' => array(
					'default' => __( 'Select currency code from options', 'proreview' ),
					'INR' => __( 'Rs.', 'proreview' ),
					'USD' => __( '$', 'proreview' ),
				),
			),
			array(
				'title' => __( 'Checkbox Field Demo', 'proreview' ),
				'type'  => 'checkbox',
				'description'  => __( 'This is checkbox field demo follow same structure for further use.', 'proreview' ),
				'id'    => 'p_checkbox_demo',
				'value' => '',
				'class' => 'p-checkbox-class',
				'placeholder' => __( 'Checkbox Demo', 'proreview' ),
			),

			array(
				'title' => __( 'Radio Field Demo', 'proreview' ),
				'type'  => 'radio',
				'description'  => __( 'This is radio field demo follow same structure for further use.', 'proreview' ),
				'id'    => 'p_radio_demo',
				'value' => '',
				'class' => 'p-radio-class',
				'placeholder' => __( 'Radio Demo', 'proreview' ),
				'options' => array(
					'yes' => __( 'YES', 'proreview' ),
					'no' => __( 'NO', 'proreview' ),
				),
			),
			array(
				'title' => __( 'Enable', 'proreview' ),
				'type'  => 'radio-switch',
				'description'  => __( 'This is switch field demo follow same structure for further use.', 'proreview' ),
				'id'    => 'p_radio_switch_demo',
				'value' => '',
				'class' => 'p-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'proreview' ),
					'no' => __( 'NO', 'proreview' ),
				),
			),

			array(
				'type'  => 'button',
				'id'    => 'p_button_demo',
				'button_text' => __( 'Button Demo', 'proreview' ),
				'class' => 'p-button-class',
			),
		);
		return $p_settings_template;
	}

	/**
	* proreview save tab settings.
	*
	* @since 1.0.0
	*/
	public function p_admin_save_tab_settings() {
		global $p_mwb_p_obj;
		if ( isset( $_POST['p_button'] ) ) {

			$mwb_p_gen_flag = false;
			$p_genaral_settings = apply_filters( 'p_general_settings_array', array() );
			$p_button_index = array_search( 'submit', array_column( $p_genaral_settings, 'type' ) );
			if ( isset( $p_button_index ) && ( null == $p_button_index || '' == $p_button_index ) ) {
				$p_button_index = array_search( 'button', array_column( $p_genaral_settings, 'type' ) );
			}
			if ( isset( $p_button_index ) && '' !== $p_button_index ) {
				unset( $p_genaral_settings[$p_button_index] );
				if ( is_array( $p_genaral_settings ) && ! empty( $p_genaral_settings ) ) {
					foreach ( $p_genaral_settings as $p_genaral_setting ) {
						if ( isset( $p_genaral_setting['id'] ) && '' !== $p_genaral_setting['id'] ) {
							if ( isset( $_POST[$p_genaral_setting['id']] ) ) {
								update_option( $p_genaral_setting['id'], $_POST[$p_genaral_setting['id']] );
							} else {
								update_option( $p_genaral_setting['id'], '' );
							}
						}else{
							$mwb_p_gen_flag = true;
						}
					}
				}
				if ( $mwb_p_gen_flag ) {
					$mwb_p_error_text = esc_html__( 'Id of some field is missing', 'proreview' );
					$p_mwb_p_obj->mwb_p_plug_admin_notice( $mwb_p_error_text, 'error' );
				}else{
					$mwb_p_error_text = esc_html__( 'Settings saved !', 'proreview' );
					$p_mwb_p_obj->mwb_p_plug_admin_notice( $mwb_p_error_text, 'success' );
				}
			}
		}
	}
	/**
	 * Function name get_comment_data
	 * this fucntion is used to filter the comments
	 *
	 * @param object $query query object.
	 * @return void
	 */
	public function get_comment_data( &$query ) {

		if ( wp_doing_ajax() ) {
			if ( 'single' === $_REQUEST['mode'] ) {
				if ( isset( $query->query_vars['type__not_in'] ) && is_array( $query->query_vars['type__not_in'] ) ) {
					$query->query_vars['type__not_in'][] = 'mwb_qa';
				} else {
					$query->query_vars['type__not_in'] = array( 'mwb_qa' );
				}
				add_filter( 'comment_row_actions', array( $this, 'add_pin_action'), 20, 2 );
			}

		}
	}
	/**
	 * Function name add_pin_action.
	 * this function  will be used to pin the review.
	 *
	 * @param array  $actions  contains action array.
	 * @param object $comment contains comments object.
	 * @return array
	 */
	public function add_pin_action( $actions, $comment ) {
		$format = '<button type="button" data-comment-id="%d" data-post-id="%d" data-action="%s" class="%s button-link" aria-expanded="false" aria-label="%s">%s</button>';
		$id = $comment->comment_ID;
		$meta = get_comment_meta( $id, 'mwb_prfw_pinned', true );
		if ( 'true' === $meta ) {
			$actions['mwb_unpin'] = sprintf(
				$format,
				$comment->comment_ID,
				$comment->comment_post_ID,
				'Pin This Review',
				'mwb_prfw_un_pin_review',
				esc_attr__( 'Unpin' , 'proreview' ),
				__( 'Un-pin Review', 'proreview' )
			);
		} else {

			$actions['mwb_pin'] = sprintf(
				$format,
				$comment->comment_ID,
				$comment->comment_post_ID,
				'Pin This Review',
				'mwb_prfw_pin_review',
				esc_attr__( 'Pin' , 'proreview' ),
				__( 'Pin Review', 'proreview' )
			);
		}

		return $actions;
	}
	public function mwb_prfw_pin_review() {
		check_ajax_referer( 'mwb_review_pin', 'nonce' );
		$comment_id = isset( $_POST['cid'] ) ? sanitize_text_field( wp_unslash( $_POST['cid'] ) ) : '';
		$post_id = isset( $_POST['pid'] ) ? sanitize_text_field( wp_unslash( $_POST['pid'] ) ) : '';
		update_comment_meta( $comment_id, 'mwb_prfw_pinned', 'true' );
		$pinned = get_post_meta(  $post_id, 'mwb_prfw_pinned_count', true );
		update_post_meta( $post_id, 'mwb_prfw_pinned_count', $pinned + 1 );
		// update_option( 'sdfgsdfgs', get_the_ID( ));
		wp_die();
	}
	public function mwb_prfw_unpin_review() {
		check_ajax_referer( 'mwb_review_pin', 'nonce' );
		$comment_id = isset( $_POST['cid'] ) ? sanitize_text_field( wp_unslash( $_POST['cid'] ) ) : '';
		$post_id = isset( $_POST['pid'] ) ? sanitize_text_field( wp_unslash( $_POST['pid'] ) ) : '';

		update_comment_meta( $comment_id, 'mwb_prfw_pinned', 'false' );
		$pinned = get_post_meta(  $post_id, 'mwb_prfw_pinned_count', true );
		update_post_meta( $post_id, 'mwb_prfw_pinned_count', $pinned - 1 );
		wp_die();
	}

}
