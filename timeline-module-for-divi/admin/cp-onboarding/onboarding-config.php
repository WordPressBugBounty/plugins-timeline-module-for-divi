<?php
// phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain , WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
/**
 * Timeline Module for Divi — onboarding wiring.
 *
 * Single-method onboarding via the shared cp-onboarding framework. The CTA
 * creates a draft page with an empty Timeline module and opens the Divi Visual
 * Builder (see tmdivi_onboarding_create_page below).
 *
 * @package TimelineModuleForDivi
 */

use CoolPlugins\Onboarding\Config;
use CoolPlugins\Onboarding\Framework;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the onboarding Config array for Timeline Module for Divi.
 */
final class TMDIVI_Onboarding_Config {

	/**
	 * Plugin text domain.
	 *
	 * @var string
	 */
	private const TEXT_DOMAIN = 'timeline-module-for-divi';

		/**
		 * Timeline Module Pro plugin slug / basename.
		 *
		 * @var string
		 */
		private const PRO_SLUG        = 'cp-timeline-module-pro-for-divi';
		private const PRO_PLUGIN_FILE = self::PRO_SLUG . '/' . self::PRO_SLUG . '.php';

	/**
	 * Cached Pro plugin state (active, installed, activate URL).
	 *
	 * @return array{active:bool,installed:bool,activate:string}
	 */
	private function pro_plugin_api() {
		static $api = null;

		if ( null !== $api ) {
			return $api;
		}

		if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$api = array(
			'active'    => is_plugin_active( self::PRO_PLUGIN_FILE ),
			'installed' => isset( get_plugins()[ self::PRO_PLUGIN_FILE ] ),
			'activate'  => wp_nonce_url(
				admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( self::PRO_PLUGIN_FILE ) ),
				'activate-plugin_' . self::PRO_PLUGIN_FILE
			),
		);

		return $api;
	}

	/**
	 * Build the full config array passed to CoolPlugins\Onboarding\Config.
	 *
	 * @param int  $telemetry_data CTA click count.
	 * @param bool $is_onboarding  Whether onboarding mode is active.
	 * @return array
	 */
	public function build( $telemetry_data, $is_onboarding ) {
		return array_merge(
			$this->identity(),
			array(
				'methods' => array( 'module' => $this->method_divi( $telemetry_data, $is_onboarding ) ),
				'addons'  => $this->addons(),
				'links'   => array( 'footer' => $this->footer_cards( $telemetry_data, $is_onboarding ) ),
			)
		);
	}

	/**
	 * Core plugin identity and page copy.
	 *
	 * @return array
	 */
	private function identity() {
		$td = self::TEXT_DOMAIN;

		return array(
			'slug'            => 'tmdivi',
			'prefix'          => 'tmdivi',
			'text_domain'     => $td,
			'version'         => defined( 'TMDIVI_V' ) ? TMDIVI_V : '1.0.0',
			'plugin_dir'      => defined( 'TMDIVI_DIR' ) ? TMDIVI_DIR : plugin_dir_path( __FILE__ ),
			'plugin_url'      => defined( 'TMDIVI_URL' ) ? TMDIVI_URL : plugin_dir_url( __FILE__ ),
			// Orphan page → admin.php?page=tmdivi-getting-started. When CTL is
			// inactive, Settings → Timeline Addons redirects here (main plugin).
			'parent_slug'     => '',
			'edition'         => 'full',
			'tier'            => 'free',
			'new_user_option' => 'tmdivi_is_new_user',
			'show_chooser'    => false,
			'colors'          => array(
				'primary'      => '#2e9e9d',
				'primary_dark' => '#257f7e',
			),
			'page'            => array(
				'menu_title' => __( 'Getting Started', $td ),
				'heading'    => __( 'Welcome to Timeline Module For Divi!', $td ),
				'subheading' => __( 'Start Building Timelines Inside Divi', $td ),
				'chooser'    => '',
			),
		);
	}

	/**
	 * Single Divi module onboarding method.
	 *
	 * @param int  $telemetry_data CTA click count.
	 * @param bool $is_onboarding  Whether onboarding mode is active.
	 * @return array
	 */
	private function method_divi( $telemetry_data, $is_onboarding ) {
		$td = self::TEXT_DOMAIN;

		$utm_params = $is_onboarding
			? '?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=demo&utm_content=onboarding'
			: '?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard';

		$arr_method = array(
			'type'          => 'divi-based',
			'title'         => __( 'Divi Module', $td ),
			'badge'         => __( 'Recommended', $td ),
			'content_badge' => __( 'Best for Divi Users', $td ),
			'description'   => __( 'Create Timeline Layouts In Divi.', $td ),
			'best_for'      => __( 'Sites built with Divi', $td ),
			'editions'      => array( 'full' ),
			'video'         => array(
				'id'       => 'V9dEoN0PvFI',
				// hqdefault always exists; maxresdefault 404s for this video.
				'thumb'    => 'https://img.youtube.com/vi/V9dEoN0PvFI/hqdefault.jpg',
				'title'    => __( 'Create a Timeline in Divi', $td ),
				'duration' => __( '3:43', $td ),
			),
			'steps'         => array(
				array(
					'title' => __( 'Open Any Page or Post', $td ),
					'desc'  => __( 'Go to Pages → Add Page (or Posts → Add Post), or edit an existing page/post with Divi.', $td ),
				),
				array(
					'title' => __( 'Add Timeline Module', $td ),
					'desc'  => __( 'Click "+", and search for the "Timeline" module.', $td ),
				),
				array(
					'title' => __( 'Add Timeline Items', $td ),
					'desc'  => __( 'Add the story title, date, description, media, and icons.', $td ),
				),
				array(
					'title' => __( 'Customize the Design', $td ),
					'desc'  => __( 'Adjust your timeline layout, colors, typography, and other settings according to your website.', $td ),
				),
			),
			'secondary'     => array(
				'label' => __( 'View Demo', $td ),
				'url'   => 'https://cooltimeline.com/divi/' . $utm_params,
			),
		);

		if ( empty( $telemetry_data ) ) {
			$arr_method['cta'] = array( 'label' => __( 'Create Sample Timeline', $td ) );
		}

		return $arr_method;
	}

	/**
	 * Plugin asset base URL.
	 *
	 * @return string
	 */
	private function plugin_url() {
		return defined( 'TMDIVI_URL' ) ? TMDIVI_URL : plugin_dir_url( __FILE__ );
	}

	/**
	 * Cross-sell addon cards.
	 *
	 * @return array
	 */
	private function addons() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only check, no state change.
		$is_onboarding = isset( $_GET['mode'] ) && 'onboarding' === $_GET['mode'];
		$addons        = array();

		if ( false === $is_onboarding && ! $this->pro_plugin_api()['active'] ) {
			$pro_addon = $this->addon_pro();
			if ( is_array( $pro_addon ) && ! empty( $pro_addon ) ) {
				$addons[] = $pro_addon;
			}
		}

		return $addons;
	}

	/**
	 * Pro Timeline Module cross-sell card (optional).
	 *
	 * @return array
	 */
	private function addon_pro() {
		$td  = self::TEXT_DOMAIN;
		$pro = $this->pro_plugin_api();

		if ( $pro['active'] ) {
			return array();
		}

		$icon = $this->plugin_url() . 'assets/image/timeline-module-pro.png';

		if ( $pro['installed'] ) {
			return array(
				'slug'           => self::PRO_SLUG,
				'type'           => 'pro',
				'group'          => 'divi-based',
				'install_method' => 'manually',
				'title'          => __( 'Timeline Module Pro For Divi', $td ),
				'description'    => __( 'The Pro plugin is already installed. Activate it to unlock premium layouts and features.', $td ),
				'icon'           => $icon,
				'label_text'     => __( 'Need advanced layouts and designs?', $td ),
				'upgrade_label'  => __( 'Activate', $td ),
				'upgrade_url'    => $pro['activate'],
			);
		}

		return array(
			'slug'           => self::PRO_SLUG,
			'type'           => 'pro',
			'group'          => 'divi-based',
			'install_method' => 'manually',
			'title'          => __( 'Timeline Module Pro For Divi', $td ),
			'description'    => __( 'Unlock horizontal layouts, premium designs, and advanced settings.', $td ),
			'icon'           => $icon,
			'label_text'     => __( 'Need advanced layouts and designs?', $td ),
			'upgrade_label'  => __( 'Buy Timeline Module Pro', $td ),
			'upgrade_url'    => 'https://cooltimeline.com/plugin/timeline-module-for-divi/?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=dashboard',
			'learn_more'     => 'https://cooltimeline.com/divi/?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
		);
	}

	/**
	 * Footer link cards for the onboarding page.
	 *
	 * @param int  $telemetry_data CTA click count.
	 * @param bool $is_onboarding  Whether onboarding mode is active.
	 * @return array
	 */
	private function footer_cards( $telemetry_data, $is_onboarding ) {
		$td  = self::TEXT_DOMAIN;
		$pro = $this->pro_plugin_api();

		if ( false === $is_onboarding ) {
			$utm_params  = '?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=docs&utm_content=dashboard';
			$utm_params2 = '?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=dashboard';
		} else {
			$utm_params  = '?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=docs&utm_content=onboarding';
			$utm_params2 = '?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=onboarding';
		}

		$cards   = array();
		$cards[] = $this->card(
			'<span class="dashicons dashicons-editor-help"></span>',
			__( 'Support', $td ),
			__( 'Need help? Our team can assist with setup and troubleshooting.', $td ),
			array(
				array(
					'label' => __( 'Get Support', $td ),
					'class' => 'cpo-button cpo-button-secondary cpo-button-small',
					'url'   => 'https://coolplugins.net/support/' . $utm_params,
				),
			)
		);
		$cards[] = $this->card(
			'<span class="dashicons dashicons-book"></span>',
			__( 'Documentation', $td ),
			'',
			array(
				array(
					'label' => __( 'How to Add Timeline Module', $td ),
					'class' => 'ctl_doc_link',
					'url'   => 'https://cooltimeline.com/doc/add-timeline-module/' . $utm_params,
				),
				array(
					'label' => __( 'FAQs', $td ),
					'class' => 'ctl_doc_link',
					'url'   => 'https://cooltimeline.com/doc/faqs-timeline-module-for-divi/' . $utm_params,
				),
				array(
					'label' => __( 'View All Documentation', $td ),
					'class' => 'ctl_doc_link',
					'url'   => 'https://cooltimeline.com/docs/timeline-module-pro-for-divi/' . $utm_params,
				),
			)
		);

		if ( false === $is_onboarding ) {
			$cards[] = $this->card(
				'<span class="dashicons dashicons-star-filled"></span>',
				__( 'Your Feedback Matters', $td ),
				__( 'If you\'re happy with the plugin, we\'d greatly appreciate a quick review. Your feedback helps us continue improving it.', $td ),
				array(
					array(
						'label' => __( 'Leave a Review', $td ),
						'url'   => 'https://wordpress.org/support/plugin/timeline-module-for-divi/reviews/#new-post',
						'class' => 'cpo-button cpo-button-secondary cpo-button-small',
					),
				)
			);
		} elseif ( ! $pro['active'] ) {
			if ( $pro['installed'] ) {
				$cards[] = $this->card(
					'<span class="dashicons dashicons-cart">',
					__( 'Timeline Module Pro For Divi', $td ),
					__( 'The Pro plugin is already installed. Activate it to unlock premium layouts and features.', $td ),
					array(
						array(
							'label' => __( 'Activate', $td ),
							'url'   => $pro['activate'],
							'class' => 'cpo-button cpo-button-secondary cpo-button-small',
						),
					)
				);
			} else {
				$cards[] = $this->card(
					'<span class="dashicons dashicons-cart">',
					__( 'Upgrade to Pro', $td ),
					__( 'Unlock horizontal layouts, Advanced Settings, and more timeline designs.', $td ),
					array(
						array(
							'label' => __( 'Buy Timeline Module Pro', $td ),
							'url'   => 'https://cooltimeline.com/plugin/timeline-module-for-divi/' . $utm_params2,
							'class' => 'cpo-button cpo-button-secondary cpo-button-small',
						),
						array(
							'label' => __( 'View Live Demo', $td ),
							'url'   => 'https://cooltimeline.com/divi/' . $utm_params,
							'class' => 'cpo-button cpo-button-secondary cpo-button-small',
						),
					)
				);
			}
		}

		return $cards;
	}

	/**
	 * Build a single footer card.
	 *
	 * @param string $icon  Icon HTML.
	 * @param string $title Card title.
	 * @param string $text  Card body text.
	 * @param array  $links Link rows.
	 * @return array
	 */
	private function card( $icon, $title, $text, array $links ) {
		return array(
			'icon'  => $icon,
			'title' => $title,
			'text'  => $text,
			'links' => $links,
		);
	}
}

$telemetry_data = get_option( 'tmdivi_onboarding_telemetry', array() );
$telemetry_data = isset( $telemetry_data['counters']['cta_clicked.divi-based'] )
	? $telemetry_data['counters']['cta_clicked.divi-based']
	: 0;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only screen mode.
$is_onboarding = isset( $_GET['mode'] ) && 'onboarding' === $_GET['mode'];

$builder   = new TMDIVI_Onboarding_Config();
$config    = new Config( $builder->build( $telemetry_data, $is_onboarding ) );
$framework = new Framework( $config );
$framework->init();

add_action(
	'admin_init',
	static function () use ( $framework ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only screen detection.
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		if ( $framework->page_slug() !== $page ) {
			return;
		}

		global $title;

		if ( ! empty( $title ) ) {
			return;
		}

		$menu_title = $framework->config()->page( 'menu_title' );
		if ( empty( $menu_title ) ) {
			$menu_title = __( 'Getting Started', 'timeline-module-for-divi' );
		}

		$title = $menu_title;
	}
);

add_filter(
	$config->prefix() . '_onboarding_script_data',
	static function ( $data ) {
		$data['action'] = 'tmdivi_onboarding_create_page';

		if ( isset( $data['install']['labels'] ) ) {
			$data['install']['labels'] = array(
				'installing' => __( 'Installing…', 'timeline-module-for-divi' ),
				'activating' => __( 'Activating…', 'timeline-module-for-divi' ),
				'activated'  => __( 'Activated', 'timeline-module-for-divi' ),
				'setupGuide' => __( 'Check Setup Guide', 'timeline-module-for-divi' ),
				'error'      => __( 'Plugin could not be installed. Please try again.', 'timeline-module-for-divi' ),
			);
		}

		return $data;
	}
);

add_filter(
	$config->prefix() . '_onboarding_labels',
	static function ( $labels ) {
		$labels['loading']     = __( 'Please wait…', 'timeline-module-for-divi' );
		$labels['redirecting'] = __( 'Redirecting…', 'timeline-module-for-divi' );
		$labels['error']       = __( 'Something went wrong. Please try again.', 'timeline-module-for-divi' );
		return $labels;
	}
);

add_action(
	'wp_ajax_' . $config->ajax_action( 'track' ),
	static function () use ( $config ) {
		check_ajax_referer( $config->option( 'track' ), 'nonce' );

		if ( current_user_can( $config->capability() ) ) {
			delete_option( $config->new_user_option() );
		}
	},
	5
);

add_action(
	'wp_ajax_tmdivi_onboarding_create_page',
	static function () use ( $framework ) {
		$cfg = $framework->config();

		check_ajax_referer( $cfg->option( 'prepare' ), 'nonce' );

		if ( ! current_user_can( $cfg->capability() ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'timeline-module-for-divi' ) ), 403 );
		}

		$stored = (int) get_option( 'tmdivi_onboarding_demo_page_id', 0 );
		if ( $stored && tmdivi_onboarding_page_has_timeline( $stored ) ) {
			$page_id = $stored;
		} else {
			$page_id = tmdivi_onboarding_create_timeline_page();
		}

		if ( is_wp_error( $page_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Could not create the page.', 'timeline-module-for-divi' ) ), 500 );
		}

		wp_send_json_success( array( 'redirectUrl' => tmdivi_onboarding_divi_edit_url( $page_id ) ) );
	}
);

add_action(
	'divi_visual_builder_assets_before_enqueue_scripts',
	static function () {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- VB onboarding trigger.
		if ( ! isset( $_GET['tmdivi_onboarding'] ) || '1' !== $_GET['tmdivi_onboarding'] ) {
			return;
		}

		wp_enqueue_script(
			'tmdivi-vb-inserter',
			( defined( 'TMDIVI_URL' ) ? TMDIVI_URL : plugin_dir_url( dirname( __DIR__ ) . '/onboarding-config.php' ) ) . 'admin/cp-onboarding/assets/vb-inserter.js',
			array(),
			defined( 'TMDIVI_V' ) ? TMDIVI_V : '1.0.0',
			true
		);
	}
);

if ( ! function_exists( 'tmdivi_onboarding_page_has_legacy_guide_stories' ) ) {
	/**
	 * Detect older onboarding pages that baked setup-guide copy into story fields.
	 *
	 * @param string $content Page post_content.
	 * @return bool
	 */
	function tmdivi_onboarding_page_has_legacy_guide_stories( $content ) {
		if ( ! is_string( $content ) || '' === $content ) {
			return false;
		}

		$markers = array(
			'Add the Timeline Module',
			'Add the Timeline Widget',
			'Configure Timeline Settings',
			'label_date="Step 1"',
			'label_date="Step 2"',
			'label_date="Step 3"',
		);

		foreach ( $markers as $marker ) {
			if ( false !== strpos( $content, $marker ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'tmdivi_onboarding_create_timeline_page' ) ) {
	/**
	 * Create a draft Divi page for onboarding (empty shell; VB inserter adds the module).
	 *
	 * @return int|\WP_Error Post ID on success.
	 */
	function tmdivi_onboarding_create_timeline_page() {
		$ver     = defined( 'ET_BUILDER_VERSION' ) ? ET_BUILDER_VERSION : '4.0.0';
		$attr    = '_builder_version="' . $ver . '"';
		$content = sprintf(
			'[et_pb_section fb_built="1" %1$s][et_pb_row %1$s][et_pb_column type="4_4" %1$s][/et_pb_column][/et_pb_row][/et_pb_section]',
			$attr
		);

		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'draft',
				'post_title'   => __( 'My Timeline', 'timeline-module-for-divi' ),
				'post_content' => $content,
				'post_author'  => get_current_user_id(),
			),
			true
		);

		if ( is_wp_error( $page_id ) || ! $page_id ) {
			return is_wp_error( $page_id )
				? $page_id
				: new WP_Error( 'tmdivi_onboarding_create_failed', __( 'Could not create the page.', 'timeline-module-for-divi' ) );
		}

		update_post_meta( $page_id, '_et_pb_use_builder', 'on' );
		update_post_meta( $page_id, '_et_pb_built_for_post_type', 'page' );
		update_option( 'tmdivi_onboarding_demo_page_id', (int) $page_id, false );

		return (int) $page_id;
	}
}

if ( ! function_exists( 'tmdivi_onboarding_divi_edit_url' ) ) {
	/**
	 * Build the Divi Visual Builder URL for an onboarding demo page.
	 *
	 * @param int $page_id Page ID.
	 * @return string
	 */
	function tmdivi_onboarding_divi_edit_url( $page_id ) {
		$page_url = get_permalink( $page_id ) ?: get_preview_post_link( $page_id );
		$redirect = function_exists( 'et_fb_get_vb_url' )
			? et_fb_get_vb_url( $page_url )
			: add_query_arg( array( 'post' => $page_id, 'action' => 'edit', 'et_fb' => '1' ), admin_url( 'post.php' ) );

		if ( function_exists( 'et_fb_prepare_ssl_link' ) ) {
			$redirect = et_fb_prepare_ssl_link( $redirect );
		}

		return esc_url_raw( add_query_arg( 'tmdivi_onboarding', '1', $redirect ) );
	}
}

if ( ! function_exists( 'tmdivi_onboarding_page_has_timeline' ) ) {
	/**
	 * Whether an onboarding demo page can be reused.
	 *
	 * @param int $page_id Page ID.
	 * @return bool
	 */
	function tmdivi_onboarding_page_has_timeline( $page_id ) {
		$post = get_post( (int) $page_id );
		if ( ! $post || 'page' !== $post->post_type || 'trash' === $post->post_status ) {
			return false;
		}

		if ( 'on' !== get_post_meta( $page_id, '_et_pb_use_builder', true ) ) {
			return false;
		}

		if ( tmdivi_onboarding_page_has_legacy_guide_stories( $post->post_content ) ) {
			return false;
		}

		if ( false !== strpos( $post->post_content, 'tmdivi_timeline' ) ) {
			return true;
		}

		return false !== strpos( $post->post_content, 'et_pb_section' );
	}
}
