<?php
	/**
	 * Template Tags hold functions to print out HTML
	 *
	 * @package madara
	 */

	use App\Madara;

	/**
	 * Get Thumbnail Image
	 *
	 * @return string - Image IMG tag
	 */
	if ( ! function_exists( 'madara_thumbnail' ) ) {
		function madara_thumbnail( $size = array(), $post_id = - 1, $source_sizes = '' ) {
			$thumbnail = new App\Views\ParseThumbnail();

			return $thumbnail->render( $size, $post_id, $source_sizes );
		}
	}

	/**
	 * Echo meta data tags
	 */
	function madara_meta_tags() {
		App\Views\ParseHead::meta_tags();
	}

	function madara_get_logo( $echo = true, $main_logo_only = false ) {
		$html = '<a class="logo" href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '">';

		$header_style = Madara::getOption( 'header_style', 1 );


		$default_logo_url = get_parent_theme_file_uri( '/images/logo.png' );

		$logo        = Madara::getOption( 'logo_image', '' ) == '' ? esc_url( $default_logo_url ) : Madara::getOption( 'logo_image', '' );
		$logo_sticky = Madara::getOption( 'sticky_logo', '' ) == '' ? ( $logo != $default_logo_url ? $logo : get_parent_theme_file_uri( '/images/logo.png' ) ) : Madara::getOption( 'sticky_logo', '' );

		$html .= '<img class="for-original" src="' . esc_url( $logo ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '"/>';

		if ( $main_logo_only == false ) {
			$html .= '<img class="for-sticky" src="' . esc_url( $logo_sticky ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '"/>';
		}

		$html .= '</a>';

		if ( $echo ) {
			echo esc_html( $html );
		} else {
			return $html;
		}
	}

	function madara_get_sticky_logo( $echo = true ) {
		$html = '<a class="logo" href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '">';

		$header_style = Madara::getOption( 'header_style', 1 );

		$default_logo_url = get_parent_theme_file_uri( '/images/logo.png' );

		$logo        = Madara::getOption( 'logo_image', '' ) == '' ? esc_url( $default_logo_url ) : Madara::getOption( 'logo_image', '' );
		$logo_sticky = Madara::getOption( 'sticky_logo', '' ) == '' ? $logo : Madara::getOption( 'sticky_logo', '' );

		$html .= '<img class="for-sticky" src="' . esc_url( $logo_sticky ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '"/>';

		$html .= '</a>';

		if ( $echo ) {
			echo esc_html( $html );
		} else {
			return $html;
		}
	}

	function madara_display_blog_post_formats_icon( $echo = 1 ) {

		$html = '<div class="icon-area">';

		if ( is_sticky() ) {
			$html .= '<span class="item-icon icon ion-md-pin"></span>';
		}

		$html .= '</div>';

		if ( $echo == 1 ) {
			echo esc_html( $html );
		} else {
			return $html;
		}

	}

	function madara_manga_title_badges_html( $post_id, $echo = 0 ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		$badges_html         = '';
		$title_badges        = get_post_meta( $post_id, 'manga_title_badges', true );
		$badges              = isset( $title_badges ) && $title_badges != '' ? $title_badges : 'no';
		$manga_adult_content = get_post_meta( $post_id, 'manga_adult_content', true );

		if ( ! empty( $manga_adult_content ) && $manga_adult_content[0] == 'yes' ) {
			$badges_html .= '<span class="manga-title-badges custom adult">';
			$badges_html .= esc_html__( '18+', 'madara' );
			$badges_html .= '</span>';
		}
		
		if($badges != 'no'){
			if ( $badges == 'custom' ) {
				$custom_badges = get_post_meta( $post_id, 'manga_custom_badges', true );
				if ( $custom_badges != '' ) {
					$badges_html .= '<span class="manga-title-badges custom ' . esc_attr(sanitize_title($custom_badges)) . '">';
					$badges_html .= esc_html( $custom_badges );
					$badges_html .= '</span>';
				}
			} else {
				$badges_html .= '<span class="manga-title-badges ' . $badges . '">';
				$badges_html .= strtoupper($badges);
				$badges_html .= '</span>';
			}
		}
		
		$badge_link = get_post_meta( $post_id, 'manga_custom_badge_link', true );
		$badge_link_target = get_post_meta($post_id, 'manga_custom_badge_link_target', true);
		
		if($badge_link != '') {
			$badges_html = '<a href="' . $badge_link . '" target="' . $badge_link_target . '">' . $badges_html . '</a>';
		}

		if ( $echo == 1 ) {
			echo wp_kses_post( $badges_html );
		} else {
			return $badges_html;
		}
	}

	function madara_ads_position( $ad_pos, $class = '', $echo = 0 ) {

		$ads = new App\Models\Entity\Ads();

		$ads = $ads->get_ad( $ad_pos, $class );

		if ( $echo == 1 ) {
			echo apply_filters( 'madara_ads_content', do_shortcode( $ads ), $ad_pos );
		} else {
			return apply_filters( 'madara_ads_content', do_shortcode( $ads ), $ad_pos );
		}

	}

	function madara_default_heading_icon( $echo = 1 ) {
		$def_icon = Madara::getOption( 'static_icon', 'icon ion-ios-star' );
		if ( $echo == 1 ) {
			echo esc_html( $def_icon );
		} else {
			return $def_icon;
		}
	}

	function madara_get_global_wp_query() {
		global $wp_query;

		return $wp_query;
	}

	function madara_get_global_wp_manga() {
		global $wp_manga;

		return $wp_manga;
	}

	function madara_get_global_wp_manga_functions() {
		global $wp_manga_functions;

		return $wp_manga_functions;
	}

	function madara_get_global_wp_manga_setting() {
		global $wp_manga_setting;

		return $wp_manga_setting;
	}

	function madara_get_global_wp_manga_template() {
		global $wp_manga_template;

		return $wp_manga_template;
	}

	function madara_get_global_wp_manga_chapter() {
		global $wp_manga_chapter;

		return $wp_manga_chapter;
	}

	function madara_get_global_wp_manga_volume() {
		global $wp_manga_volume;

		return $wp_manga_volume;
	}

	function madara_get_global_wp_manga_user_actions() {

		global $wp_manga_user_actions;

		return $wp_manga_user_actions;

	}