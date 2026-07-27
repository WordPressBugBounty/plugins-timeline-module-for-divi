<?php

// phpcs:disable WordPress.Security.NonceVerification.Recommended ,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
/**
 * Timeline Module for Divi — global header screen check and hook wiring.
 *
 * @package TimelineModuleForDivi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'tmdivi_is_timeline_header_page' ) ) {
	/**
	 * Whether the current admin screen should show the TMDIVI global header.
	 *
	 * @return bool
	 */
	function tmdivi_is_timeline_header_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only screen detection.
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

		return 'tmdivi-getting-started' === $page;
	}
}

require_once __DIR__ . '/timeline-global-header.php';

add_action(
	'admin_enqueue_scripts',
	static function () {
		if ( ! tmdivi_is_timeline_header_page() ) {
			return;
		}

		cp_timeline_header_enqueue_styles( defined( 'TMDIVI_V' ) ? TMDIVI_V : '1.0.0' );
	}
);

add_filter(
	'admin_body_class',
	static function ( $classes ) {
		if ( tmdivi_is_timeline_header_page() ) {
			$classes .= ' cph-timeline-addon-page';
		}

		return $classes;
	}
);

add_action(
	'in_admin_header',
	static function () {
		if ( ! tmdivi_is_timeline_header_page() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only screen detection.
		if ( isset( $_GET['page'], $_GET['mode'] ) && 'tmdivi-getting-started' === $_GET['page'] && 'onboarding' === $_GET['mode'] ) {
			return;
		}

		$utm_params = '?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=docs&utm_content=global-header';

		cp_timeline_header_render(
			array(
				'heading'       => __( 'Timeline Addons', 'timeline-module-for-divi' ),
				'icon_url'      => TMDIVI_URL . 'assets/image/timeline-icon.svg',
				'docs_url'      => 'https://cooltimeline.com/docs/timeline-module-pro-for-divi/' . $utm_params,
				'support_url'   => 'https://coolplugins.net/support/' . $utm_params,
				'docs_label'    => __( 'Check Docs', 'timeline-module-for-divi' ),
				'support_label' => __( 'Get Support', 'timeline-module-for-divi' ),
				'text_domain'   => 'timeline-module-for-divi',
			)
		);
	}
);
