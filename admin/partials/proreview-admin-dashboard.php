<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Proreview
 * @subpackage Proreview/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit(); // Exit if accessed directly.
}

global $p_mwb_p_obj;
$p_active_tab   = isset( $_GET['p_tab'] ) ? sanitize_key( $_GET['p_tab'] ) : 'proreview-general';
$p_default_tabs = $p_mwb_p_obj->mwb_p_plug_default_tabs();
?>
<header>
	<div class="mwb-header-container mwb-bg-white mwb-r-8">
		<h1 class="mwb-header-title"><?php echo esc_attr( strtoupper( str_replace( '-', ' ', $p_mwb_p_obj->p_get_plugin_name() ) ) ); ?></h1>
		<a href="https://docs.makewebbetter.com/" target="_blank" class="mwb-link"><?php esc_html_e( 'Documentation', 'proreview' ); ?></a>
		<span>|</span>
		<a href="https://makewebbetter.com/contact-us/" target="_blank" class="mwb-link"><?php esc_html_e( 'Support', 'invoice-system-for-woocommerce' ); ?></a>
	</div>
</header>

<main class="mwb-main mwb-bg-white mwb-r-8">
	<nav class="mwb-navbar">
		<ul class="mwb-navbar__items">
			<?php
			if ( is_array( $p_default_tabs ) && ! empty( $p_default_tabs ) ) {

				foreach ( $p_default_tabs as $p_tab_key => $p_default_tabs ) {

					$p_tab_classes = 'mwb-link ';

					if ( ! empty( $p_active_tab ) && $p_active_tab === $p_tab_key ) {
						$p_tab_classes .= 'active';
					}
					?>
					<li>
						<a id="<?php echo esc_attr( $p_tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=proreview_menu' ) . '&p_tab=' . esc_attr( $p_tab_key ) ); ?>" class="<?php echo esc_attr( $p_tab_classes ); ?>"><?php echo esc_html( $p_default_tabs['title'] ); ?></a>
					</li>
					<?php
				}
			}
			?>
		</ul>
	</nav>

	<section class="mwb-section">
		<div>
			<?php 
				do_action( 'mwb_p_before_general_settings_form' );
						// if submenu is directly clicked on woocommerce.
				if ( empty( $p_active_tab ) ) {
					$p_active_tab = 'mwb_p_plug_general';
				}

						// look for the path based on the tab id in the admin templates.
				$p_tab_content_path = 'admin/partials/' . $p_active_tab . '.php';

				$p_mwb_p_obj->mwb_p_plug_load_template( $p_tab_content_path );

				do_action( 'mwb_p_after_general_settings_form' ); 
			?>
		</div>
	</section>
