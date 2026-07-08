<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared utilities for D4 and D5 timeline modules.
 */
class TmdiviHelper {

	public static function enqueue_google_font( $font_family ) {
		$font_parts       = explode( '|', $font_family );
		$font_family_name = isset( $font_parts[0] ) ? trim( $font_parts[0] ) : '';
		if ( $font_family_name === '' ) {
			return;
		}
		$family_param = rawurlencode( $font_family_name );
		$font_url     = 'https://fonts.googleapis.com/css2?family=' . $family_param . '&display=swap';
		wp_enqueue_style(
			'tmdivi-gfonts-' . sanitize_key( $font_family_name ),
			esc_url( $font_url ),
			array(),
			TMDIVI_V,
			'all'
		);
	}

	public static function extractFontProperties( $font_string ) {
		$font_parts = explode( '|', $font_string );
		$font_family = $font_parts[0];
		$font_weight = ! empty( $font_parts[1] ) ? $font_parts[1] : '';
		$font_style  = ! empty( $font_parts[2] ) ? 'italic' : 'normal';

		if ( ! empty( $font_parts[3] ) ) {
			$text_transform = 'uppercase';
		} elseif ( ! empty( $font_parts[5] ) ) {
			$text_transform = 'capitalize';
		} else {
			$text_transform = 'none';
		}

		if ( ! empty( $font_parts[4] ) && ! empty( $font_parts[6] ) ) {
			$text_decoration = 'line-through';
		} elseif ( ! empty( $font_parts[4] ) ) {
			$text_decoration = 'underline';
		} elseif ( ! empty( $font_parts[6] ) ) {
			$text_decoration = 'line-through';
		} else {
			$text_decoration = 'none';
		}

		$text_decoration_line_color = ( ! empty( $font_parts[7] ) ) ? $font_parts[7] : '';
		$text_decoration_style      = ( ! empty( $font_parts[8] ) ) ? $font_parts[8] : '';

		return array(
			'fontFamily'              => $font_family,
			'fontWeight'              => $font_weight,
			'fontStyle'               => $font_style,
			'textTransform'           => $text_transform,
			'textDecoration'          => $text_decoration,
			'textDecorationLineColor' => $text_decoration_line_color,
			'textDecorationStyle'     => $text_decoration_style,
		);
	}
}
