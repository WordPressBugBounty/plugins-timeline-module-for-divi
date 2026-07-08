<?php
/**
 * D4 pipe-separated font field → D5 font object conversion helpers.
 *
 * @package TMDIVI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Parse Divi 4 pipe-separated font string into properties.
 *
 * @param string $font_string D4 font field value.
 * @return array<string, string>
 */
if ( ! function_exists( 'tmdivi_free_parse_d4_font_string' ) ) {
function tmdivi_free_parse_d4_font_string( $font_string ) {
	$parts = explode( '|', (string) $font_string );

	$font_style = ( ! empty( $parts[2] ) && 'off' !== $parts[2] ) ? 'italic' : 'normal';

	if ( ! empty( $parts[3] ) && 'on' === $parts[3] ) {
		$text_transform = 'uppercase';
	} elseif ( ! empty( $parts[5] ) && 'on' === $parts[5] ) {
		$text_transform = 'capitalize';
	} else {
		$text_transform = 'none';
	}

	if ( ! empty( $parts[4] ) && 'on' === $parts[4] && ! empty( $parts[6] ) && 'on' === $parts[6] ) {
		$text_decoration = 'line-through';
	} elseif ( ! empty( $parts[4] ) && 'on' === $parts[4] ) {
		$text_decoration = 'underline';
	} elseif ( ! empty( $parts[6] ) && 'on' === $parts[6] ) {
		$text_decoration = 'line-through';
	} else {
		$text_decoration = 'none';
	}

	return array(
		'fontFamily'              => isset( $parts[0] ) ? $parts[0] : '',
		'fontWeight'              => isset( $parts[1] ) ? $parts[1] : '',
		'fontStyle'               => $font_style,
		'textTransform'           => $text_transform,
		'textDecoration'          => $text_decoration,
		'textDecorationLineColor' => isset( $parts[7] ) ? $parts[7] : '',
		'textDecorationStyle'     => isset( $parts[8] ) ? $parts[8] : '',
	);
}
}

/**
 * Convert D4 label font fields (label_date / sub_label / label_text) for migration.
 *
 * Colors are mapped directly in conversion-outline.js.
 *
 * @param mixed $value        D4 attribute value.
 * @param array $extra_params Conversion context.
 * @return array<string, string>
 */
if ( ! function_exists( 'tmdivi_free_convert_d4_font_field' ) ) {
function tmdivi_free_convert_d4_font_field( $value, $extra_params = array() ) {
	if ( empty( $value ) || ! is_string( $value ) ) {
		return array();
	}

	$viewport = ! empty( $extra_params['viewport'] ) ? $extra_params['viewport'] : 'desktop';
	$parsed   = tmdivi_free_parse_d4_font_string( $value );
	$prefix   = "font.{$viewport}.value.";

	$result = array(
		"{$prefix}family" => $parsed['fontFamily'],
		"{$prefix}weight" => $parsed['fontWeight'],
	);

	if ( 'italic' === $parsed['fontStyle'] ) {
		$result[ "{$prefix}style" ] = 'italic';
	}

	if ( 'none' !== $parsed['textTransform'] ) {
		$result[ "{$prefix}textTransform" ] = $parsed['textTransform'];
	}

	return $result;
}
}

add_filter(
	'divi.moduleLibrary.conversion.valueExpansionFunctionMap',
	function ( $map ) {
		$map['tmdivi_free_convert_d4_font_field'] = 'tmdivi_free_convert_d4_font_field';
		return $map;
	}
);
