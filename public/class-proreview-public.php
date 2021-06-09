<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Proreview
 * @subpackage Proreview/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * namespace proreview_public.
 *
 * @package    Proreview
 * @subpackage Proreview/public
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Proreview_Public {

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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function p_public_enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, PROREVIEW_DIR_URL . 'public/src/scss/proreview-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function p_public_enqueue_scripts() {

		wp_register_script( $this->plugin_name, PROREVIEW_DIR_URL . 'public/src/js/proreview-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'p_public_param', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( $this->plugin_name );

	}
	public function show_pin() {

		// echo 'hello';
		$post_id = get_the_ID();
		$comments_query = new WP_Comment_Query();
		$args = array(
			'type__in' => 'review',
			'status' => 'approve',
			'post_id' => $post_id,
		);
		$result = $comments_query->query( $args );
		if ( count( $result ) > 0 ) {
			$comment_exclude_ids = array();
			foreach ( $result as $key => $value ) {
				$comment_id = $value->comment_ID;
				$meta = get_comment_meta( $comment_id, 'mwb_prfw_pinned', true );
				if( 'true' === $meta ) {
					$this->show_reviews_mwb_customized( $comment_id, $value );
				} else {
					$this->show_reviews_mwb_customized( $comment_id, $value );
				}
			}

			echo '<br/>';
		}
	}
	public function show_reviews_mwb_customized( $comment_id, $value ) {
					$title      = get_comment_meta( $comment_id, 'mwb_add_review_title', true );
					$dynamic_feature = get_comment_meta( $comment_id, 'mwb_prfw_dynamic_review_features', true );
					$rating = get_comment_meta( $comment_id, 'rating', true );
					$que_comment_date = $value->comment_date;
						$q_date_time = strtotime( $que_comment_date );
						$final_q_date = gmdate( 'd/m/Y', $q_date_time );
					echo wc_get_rating_html( $rating );
					echo esc_html( $value->comment_author );
					echo '-';
					echo esc_html( $final_q_date );
					echo '<br/>';

					echo esc_html( $value->comment_content );
					if ( $dynamic_feature ) {
						echo "<div class='mwb_prfw-main_review_extra'>";
						foreach ( $dynamic_feature as $key => $value ) {
							$v = str_replace( '_', ' ', trim( $key ) );
							echo '<span>' . esc_html( $v ) . '</span>';
							echo '<span>' . wc_get_rating_html( $value ) . '</span>';
						}
						echo '</div>';
					}
					if ( $title ) {
						echo "<div class='mwb_prfw-main_review_title'>". esc_html( $title ) . '</div>';
					}
					$img = get_comment_meta( $comment_id, 'mwb_review_image', true );
					if ( $img ) {
						echo "<div class='mwb_prfw-main_review_img'>";
						foreach ( $img as $k => $value ) {

							echo "<img src='" . $value . "' width='100px' height='100px' > "; // phpcs:ignore
						}
						echo '</div>';

					}
			$dynamic_attr = get_comment_meta( $comment_id, 'mwb_prfw_attribute_features', true );

			if ( $dynamic_attr ) {
				echo "<div class='mwb_prfw-main_review_extra'>";
				foreach ( $dynamic_attr as $k => $va ) {
					$v = str_replace( '_', ' ', trim( $k ) );
					echo '<span>' . esc_html( $v ) . '</span>';
					echo '<span>' .  $va . '</span>';
				}
				echo '</div>';
			}
	}

	public function add_attributes() {
		$mwb_attributes = get_option( 'mwb_multiselect', array() );
		foreach ( $mwb_attributes as $k => $val ) {
			if ( '' !== $val ) {
		$min_val = get_option( 'mwb_min_attr_val', array() );
		$max_val = get_option( 'mwb_max_attr_val', array() );

				echo '<label class="mwb_prfw-extra_feature">' . esc_html( $val ) . '</label>';
				for ( $i = $min_val; $i <= $max_val; $i++ ) {
					echo '<input type="radio" name="' . str_replace( " ", "_", trim( esc_html( $val ) ) ) . '" id="'. esc_html( $val ) . esc_html( $i ) . '" value="'. esc_html( $i ) . '"';
					echo ' /><span class="mwb_prfw-extra_feature_count">'. esc_html( $i ) . '</span>';
				}
			}
		}
	}
	public function mwb_save_data( $comment_id ){
		$dynamic_fields = get_option( 'mwb_multiselect', array() );
					$review_arr = array();
					foreach( $dynamic_fields as $k => $val ) {
						if ( '' !== $val ) {
							$v = str_replace( " ", "_", trim( $val ) );
							$data = array_key_exists( $v, $_POST ) ? $_POST[ $v ] : '';
							if( '' !== $data ) {
								$review_arr[ $v ] = $data;
							}
						}
					}
		update_comment_meta( $comment_id, 'mwb_prfw_attribute_features', $review_arr );
		// die;

	}
	public function show_attr_filter() {
		$tab = '#tab-reviews';
		$product_id = get_the_ID();
		$mwb_attributes = get_option( 'mwb_multiselect', array() );
		foreach ( $mwb_attributes as $k => $val ) {

			echo '<a data-rating="5" href="' . esc_url( add_query_arg( 'mwb_attr_filter', $val, get_permalink( $product_id ) ) ) . $tab . '" title="' . $val . '">' . $val . '</a>';
			echo '<br>';
		}
	}

	public function apply_filter_action() {
		if( isset( $_GET['mwb_attr_filter'] ) ) {
			$filter = $_GET['mwb_attr_filter'];

			$post_id = get_the_ID();
		$comments_query = new WP_Comment_Query();
		$args = array(
			'type__in' => 'review',
			'status' => 'approve',
			'post_id' => $post_id,
		);
		$result = $comments_query->query( $args );
		// echo '<pre>'; print_r( $result ); echo '</pre>';

		if( $result > 0 ) {
			foreach ( $result as $k => $v ) {
				// echo '<pre>'; print_r( $v ); echo '</pre>';
					$comment_id = $v->comment_ID;
					// echo $comment_id;
				$meta = get_comment_meta( $comment_id, 'mwb_prfw_attribute_features', true );
				// echo $meta;
				// echo '<pre>'; print_r( $meta ); echo '</pre>';
				if( $meta ) {

					if( array_key_exists( $filter, $meta ) ) {
						$this->show_reviews_mwb_customized( $comment_id, $v );
					}
				}
				// if( 'true' === $meta ) {
				// 	$this->show_reviews_mwb_customized( $comment_id, $value );
				// }
			}
		}
		}
	}
	public function mwb_change_text_filter( $reviews_title, $count, $product ) {
	
		// var_dump( $review_title );
		// echo '<pre>'; print_r( $count ); echo '</pre>';
		// echo '<pre>'; print_r( $product ); echo '</pre>';
		if( isset( $_GET['mwb_attr_filter'] ) ) {
			$filter = $_GET['mwb_attr_filter'];
			$reviews_title = __('Showing Reviews on Filtered by', 'proreview') . $filter . '<a href="'. esc_url( get_permalink( get_the_ID() ) ) . '#tab-reviews">' . __('See All Reviews', 'proreview') .'</a>';
			return $reviews_title;
		} else {
			return $reviews_title;

		}
	}

}
