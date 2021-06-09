<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Proreview
 * @subpackage Proreview/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $p_mwb_p_obj;
$p_template_settings = apply_filters( 'p_template_settings_array', array() );
?>
<!--  template file for admin settings. -->
<div class="p-section-wrap">
	<?php
		$p_template_html = $p_mwb_p_obj->mwb_p_plug_generate_html( $p_template_settings );
		echo esc_html( $p_template_html );
	?>
</div>
