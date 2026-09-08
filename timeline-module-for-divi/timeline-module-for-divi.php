<?php
/*
Plugin Name: Timeline Module For Divi
Plugin URI:  https://cooltimeline.com/divi/?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=product_site&utm_content=plugins_list
Description: A timeline module for Divi
Version:     1.3.2
Author:      CoolPlugins
Author URI:  https://coolplugins.net/?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=author_page&utm_content=plugins_list
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: timeline-module-for-divi

Timeline Module For Divi is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Timeline Module For Divi is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Timeline Module For Divi. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 



define('TMDIVI_V', '1.3.2');
define('TMDIVI_DIR', plugin_dir_path(__FILE__));
define('TMDIVI_URL', plugin_dir_url(__FILE__));
define('TMDIVI_MODULE_URL', plugin_dir_url(__FILE__) . 'includes/modules');
define('TMDIVI_MODULE_DIR', plugin_dir_path(__FILE__) . 'includes/modules');
define('TMDIVI_PLUGIN_FILE', __FILE__);
define('TMDIVI_FEEDBACK_API', 'https://feedback.coolplugins.net/');
define('TMDIVI_CPFM_ID', 'tmdivi');
define('TMDIVI_CPFM_CONSENT_CATEGORY', 'cool-timeline');
define('TMDIVI_CPFM_CONSENT_MASTER_OPTION', 'cpfm_opt_in_choice_cool-timeline');
define('TMDIVI_CPFM_CONSENT_OVERRIDE_OPTION', 'tmdivi-cpfm-data-sharing');
define('TMDIVI_CPFM_CRON_HOOK', 'tmdivi_extra_data_update');

register_activation_hook( __FILE__, array( 'TMDIVI_Timeline_Module_For_Divi', 'tmdivi_activate_plugin' ) );
register_deactivation_hook( __FILE__, array( 'TMDIVI_Timeline_Module_For_Divi', 'tmdivi_deactivate_plugin' ) );

if ( ! function_exists( 'tmdivi_use_ctl_getting_started' ) ) {
	/**
	 * Cool Timeline Free > 3.3.6 owns the hub (ctl-getting-started).
	 * Free <= 3.3.6 / Pro / Divi-only → Settings → Timeline Addons.
	 *
	 * @return bool
	 */
	function tmdivi_use_ctl_getting_started() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( ! is_plugin_active( 'cool-timeline/cooltimeline.php' ) ) {
			return false;
		}

		$version = defined( 'CTL_V' ) ? (string) CTL_V : '';
		if ( '' === $version && function_exists( 'get_plugin_data' ) ) {
			$data    = get_plugin_data( WP_PLUGIN_DIR . '/cool-timeline/cooltimeline.php', false, false );
			$version = isset( $data['Version'] ) ? (string) $data['Version'] : '';
		}

		// Old Free (<= 3.3.6) has no shared hub — keep Divi Settings menu.
		return '' !== $version && version_compare( $version, '3.3.6', '>' );
	}
}

// Lightweight — only registers this copy as a version candidate.
require_once TMDIVI_DIR . 'admin/cp-onboarding/loader.php';
cpo_onboarding_register( '1.1.4', TMDIVI_DIR . 'admin/cp-onboarding' );

require_once TMDIVI_DIR . 'admin/cpfm-feedback/class-cpfm-loader.php';
CPFM_Loader::load();

class TMDIVI_Timeline_Module_For_Divi {

    public function __construct() {
        self::includes();
        add_action('divi_extensions_init', array($this, 'initialize_extension'));
        add_action( 'admin_init', array( $this, 'is_divi_theme_exist' ) );
        add_action('wp_loaded', array($this, 'load_child_items'));
        add_action( 'wp_enqueue_scripts', array($this,'d5_extension_example_module_enqueue_frontend_scripts') );
        add_action('send_headers',array($this,'stop_browser_cache'));
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'tmdivi_pro_plugin_link' ) );
        add_action( 'activated_plugin', array( $this, 'tmdivi_plugin_redirection' ) );
		add_action( 'init', array( $this, 'tmdivi_register_cpfm' ), 5 );
		add_action( 'cpfm_register_notice', array( $this, 'tmdivi_register_cpfm_feedback_notice' ) );
		add_action( 'cpfm_after_opt_in_' . TMDIVI_CPFM_ID, array( $this, 'tmdivi_cpfm_after_opt_in' ) );
		add_action( 'cpfm_after_opt_out_' . TMDIVI_CPFM_ID, array( $this, 'tmdivi_cpfm_after_opt_out' ) );
		// Cool Timeline's shared panel fires the ctl suffix when that plugin is active.
		add_action( 'cpfm_after_opt_in_ctl', array( $this, 'tmdivi_cpfm_after_opt_in' ) );
		add_action( 'cpfm_after_opt_out_ctl', array( $this, 'tmdivi_cpfm_after_opt_out' ) );

		if ( tmdivi_use_ctl_getting_started() ) {
			add_action( 'admin_init', array( $this, 'tmdivi_redirect_getting_started_to_ctl' ) );
		} else {
			add_action( 'admin_menu', array( $this, 'tmdivi_register_timeline_addons_menu' ), 9 );
			add_action( 'admin_head', array( $this, 'tmdivi_hide_getting_started_settings_submenu_css' ) );
			add_filter( 'parent_file', array( $this, 'tmdivi_highlight_addons_menu' ), 999 );
			add_filter( 'submenu_file', array( $this, 'tmdivi_highlight_addons_submenu' ), 999 );
		}
    }

	public function tmdivi_register_cpfm() {
		if ( class_exists( 'CPFM_Deactivation_Feedback' ) ) {
			CPFM_Deactivation_Feedback::cpfm_register(
				array(
					'id'                     => TMDIVI_CPFM_ID,
					'slug'                   => 'timeline-module-for-divi',
					'plugin_name'            => __( 'Timeline Module For Divi', 'timeline-module-for-divi' ),
					'version'                => TMDIVI_V,
					'api'                    => TMDIVI_FEEDBACK_API,
					'site_key'               => '561',
					'install_date_option'    => 'tmdivi-install-date',
					'initial_version_option' => 'tmdivi_initial_version',
					'onboarding_data'        => 'tmdivi_onboarding_data',
					'reasons'                => array(
						'not_working'  => array(
							'title'       => __( 'The plugin is not working.', 'timeline-module-for-divi' ),
							'placeholder' => __( 'Please share your issue so we can fix it for other users.', 'timeline-module-for-divi' ),
						),
						'not_expected' => array(
							'title'       => __( 'The plugin did not work as expected.', 'timeline-module-for-divi' ),
							'placeholder' => __( 'What did you expect?', 'timeline-module-for-divi' ),
						),
						'found_better' => array(
							'title'       => __( 'I found a better plugin.', 'timeline-module-for-divi' ),
							'placeholder' => __( 'Please share which plugin.', 'timeline-module-for-divi' ),
						),
						'temporary'    => array(
							'title'       => __( 'It is a temporary deactivation.', 'timeline-module-for-divi' ),
							'placeholder' => '',
						),
						'other'        => array(
							'title'       => __( 'Other reason.', 'timeline-module-for-divi' ),
							'placeholder' => __( 'Please share the reason.', 'timeline-module-for-divi' ),
						),
					),
					'i18n'                   => array(
						'title'        => __( 'Quick Feedback', 'timeline-module-for-divi' ),
						'intro'        => __( 'What made you deactivate %s? Your answer helps us fix it.', 'timeline-module-for-divi' ),
						'submit'       => __( 'Submit and Deactivate', 'timeline-module-for-divi' ),
						'skip'         => __( 'Skip and Deactivate', 'timeline-module-for-divi' ),
						'pick_reason'  => __( 'Please choose a reason.', 'timeline-module-for-divi' ),
						'deactivating' => __( 'Deactivating...', 'timeline-module-for-divi' ),
						'close_label'  => __( 'Close', 'timeline-module-for-divi' ),
						'byline'       => __( 'A plugin by %s', 'timeline-module-for-divi' ),
						'consent'      => __( 'Submitting shares your reason plus your site URL, admin email and basic environment details. Skip and Deactivate sends nothing.', 'timeline-module-for-divi' ),
					),
				)
			);
		}

		// Cool Timeline rewrites this row at priority 999. Same restore as TWAE
		// so CPFM can still find .deactivate a when Cool Timeline is active.
		add_filter(
			'plugin_action_links_' . plugin_basename( TMDIVI_PLUGIN_FILE ),
			array( $this, 'tmdivi_order_plugin_action_links' ),
			1000
		);

		if ( class_exists( 'CPFM_Review' ) ) {
			CPFM_Review::cpfm_register(
				array(
					'id'          => TMDIVI_CPFM_ID,
					'plugin_file' => TMDIVI_PLUGIN_FILE,
					'plugin_name' => __( 'Timeline Module For Divi', 'timeline-module-for-divi' ),
					'review_url'  => 'https://wordpress.org/support/plugin/timeline-module-for-divi/reviews/#new-post',
					'trigger'     => array(
						'type'  => 'install_age',
						'hours' => 72,
					),
					'own_screens' => array(
						'plugins',
						'admin_page_tmdivi-getting-started',
						'toplevel_page_tmdivi-getting-started',
						'settings_page_tmdivi-getting-started',
						'settings_page_cool-plugins-timeline-addon',
						'settings_page_tmdivi-timeline-addons',
					),
					'notice'      => array(
						'enabled' => true,
						'screens' => array(
							'plugins',
							'admin_page_tmdivi-getting-started',
							'toplevel_page_tmdivi-getting-started',
							'settings_page_tmdivi-getting-started',
							'settings_page_cool-plugins-timeline-addon',
							'settings_page_tmdivi-timeline-addons',
						),
					),
					'row'         => array(
						'enabled' => true,
					),
					'legacy'      => array(
						'done_options'  => array(
							'tmdivi-Boxes-ratingDiv' => 'yes',
						),
						'install_dates' => array(
							'tmdivi-installDate',
							'tmdivi-install-date',
						),
						'mirror_write'  => array(
							'tmdivi-Boxes-ratingDiv' => 'yes',
						),
					),
					'i18n'        => array(
						'like_question' => __( 'Do you like the %s plugin?', 'timeline-module-for-divi' ),
						'thanks_line'   => __( 'Great to hear! A quick review on WordPress.org would really help us.', 'timeline-module-for-divi' ),
						'direct_line'   => __( 'Enjoying %s? A short review really helps.', 'timeline-module-for-divi' ),
						'row_question'  => __( 'Do you like this plugin?', 'timeline-module-for-divi' ),
						'yes_button'    => __( 'Yes, I like it', 'timeline-module-for-divi' ),
						'submit_button' => __( 'Submit review', 'timeline-module-for-divi' ),
						'dismiss_link'  => __( 'No thanks', 'timeline-module-for-divi' ),
						'later_link'    => __( 'Ask me later', 'timeline-module-for-divi' ),
						'no_link'       => __( 'I do not like it, dismiss', 'timeline-module-for-divi' ),
						'close_label'   => __( 'Close', 'timeline-module-for-divi' ),
					),
				)
			);
		}

		if ( class_exists( 'CPFM_Usage_Cron' ) ) {
			CPFM_Usage_Cron::cpfm_register(
				array(
					'id'                      => TMDIVI_CPFM_ID,
					'plugin_name'             => __( 'Timeline Module For Divi', 'timeline-module-for-divi' ),
					'version'                 => TMDIVI_V,
					'api'                     => TMDIVI_FEEDBACK_API,
					'cron_hook'               => TMDIVI_CPFM_CRON_HOOK,
					'consent_master_option'   => TMDIVI_CPFM_CONSENT_MASTER_OPTION,
					'consent_override_option' => TMDIVI_CPFM_CONSENT_OVERRIDE_OPTION,
					'install_date_option'     => 'tmdivi-install-date',
					'initial_version_option'  => 'tmdivi_initial_version',
					'onboarding_data'         => 'tmdivi_onboarding_data',
					'site_key'                =>  '561',
				)
			);

			// Family consent may already be yes (e.g. Cool Timeline opted in earlier).
			self::tmdivi_maybe_schedule_tracking_cron();
		}
	}

	/**
	 * Whether usage-data sharing is allowed for this plugin.
	 *
	 * Per-plugin override when set; else the shared Timeline family master
	 * (cpfm_opt_in_choice_cool-timeline), same as Cool Timeline Free.
	 *
	 * @return bool
	 */
	public static function tmdivi_has_cpfm_consent() {
		$override = get_option( TMDIVI_CPFM_CONSENT_OVERRIDE_OPTION );
		if ( in_array( $override, array( 'yes', 'no' ), true ) ) {
			return ( 'yes' === $override );
		}

		return ( 'yes' === get_option( TMDIVI_CPFM_CONSENT_MASTER_OPTION ) );
	}

	/**
	 * Schedule the usage cron when family/plugin consent is already yes.
	 *
	 * @return void
	 */
	public static function tmdivi_maybe_schedule_tracking_cron() {
		if ( ! class_exists( 'CPFM_Usage_Cron' ) ) {
			return;
		}

		if ( self::tmdivi_has_cpfm_consent() ) {
			CPFM_Usage_Cron::cpfm_schedule_event( TMDIVI_CPFM_CRON_HOOK );
		}
	}

	public function tmdivi_register_cpfm_feedback_notice() {
		if ( ! class_exists( 'CPFM_Feedback_Notice' ) ) {
			return;
		}

		CPFM_Feedback_Notice::cpfm_register_notice(
			TMDIVI_CPFM_CONSENT_CATEGORY,
			array(
				'plugin_name'    => TMDIVI_CPFM_ID,
				'title'          => __( 'Timeline Plugins by Cool Plugins', 'timeline-module-for-divi' ),
				'message'        => __( 'Help us make this plugin more compatible with your site by sharing non-sensitive site data.', 'timeline-module-for-divi' ),
				'pages'          => array(
					'tmdivi-getting-started',
					'cool-plugins-timeline-addon',
					'tmdivi-timeline-addons',
				),
				'always_show_on' => array(
					'tmdivi-getting-started',
				),
				'i18n'           => array(
					'panel_title'         => __( 'Help Improve Plugins', 'timeline-module-for-divi' ),
					'more_info'           => __( 'More info', 'timeline-module-for-divi' ),
					'consent_intro'       => __( 'Opt in to receive email updates about security improvements, new features, helpful tutorials, and occasional special offers. We will collect:', 'timeline-module-for-divi' ),
					'consent_item_site'   => __( 'Your website home URL and WordPress admin email.', 'timeline-module-for-divi' ),
					'consent_item_compat' => __( 'To check plugin compatibility, we will collect the following: list of active plugins and themes, PHP, MySQL and WordPress versions, memory limit, whether the site is multisite, and the site language.', 'timeline-module-for-divi' ),
					'consent_link'        => __( 'Click here', 'timeline-module-for-divi' ),
					'yes_label'           => __( 'Yes, it is OK', 'timeline-module-for-divi' ),
					'no_label'            => __( 'No, thanks', 'timeline-module-for-divi' ),
				),
			)
		);
	}

	public function tmdivi_cpfm_after_opt_in( $category = '' ) {
		if ( '' !== $category && TMDIVI_CPFM_CONSENT_CATEGORY !== $category ) {
			return;
		}

		static $handled = false;
		if ( $handled ) {
			return;
		}
		$handled = true;

		update_option( TMDIVI_CPFM_CONSENT_OVERRIDE_OPTION, 'yes', false );
		self::tmdivi_maybe_schedule_tracking_cron();

		if ( class_exists( 'CPFM_Usage_Cron' ) ) {
			do_action( TMDIVI_CPFM_CRON_HOOK );
		}
	}

	public function tmdivi_cpfm_after_opt_out( $category = '' ) {
		if ( '' !== $category && TMDIVI_CPFM_CONSENT_CATEGORY !== $category ) {
			return;
		}

		static $handled = false;
		if ( $handled ) {
			return;
		}
		$handled = true;

		update_option( TMDIVI_CPFM_CONSENT_OVERRIDE_OPTION, 'no', false );
		wp_clear_scheduled_hook( TMDIVI_CPFM_CRON_HOOK );
	}

	public function tmdivi_redirect_getting_started_to_ctl() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only screen detection.
		if ( empty( $_GET['page'] ) || 'tmdivi-getting-started' !== $_GET['page'] ) {
			return;
		}

		$url = admin_url( 'admin.php?page=ctl-getting-started' );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- preserve onboarding mode only.
		if ( isset( $_GET['mode'] ) && 'onboarding' === $_GET['mode'] ) {
			$url = add_query_arg( 'mode', 'onboarding', $url );
		}

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Settings submenu slug for Divi's Timeline Addons entry.
	 * Unique when another product owns cool-plugins-timeline-addon (same label, different page).
	 * Avoid remapping that slug under Settings — old Block Pro License belongs under Timeline Addons.
	 *
	 * @return string
	 */
	private function tmdivi_timeline_addons_menu_slug() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$shared_owner = defined( 'CTL_V' ) || defined( 'CTLPV' )
			|| is_plugin_active( 'timeline-widget-addon-for-elementor/timeline-widget-addon-for-elementor.php' )
			|| is_plugin_active( 'timeline-widget-addon-for-elementor-pro/timeline-widget-addon-pro-for-elementor.php' )
			|| is_plugin_active( 'timeline-block-pro-for-gutenberg/timeline-block-pro-for-gutenberg.php' )
			|| is_plugin_active( 'timeline-builder/timeline-builder-pro.php' );

		return $shared_owner ? 'tmdivi-timeline-addons' : 'cool-plugins-timeline-addon';
	}

    public function tmdivi_register_timeline_addons_menu() {
		global $_wp_real_parent_file;

		$slug = $this->tmdivi_timeline_addons_menu_slug();

		if ( 'cool-plugins-timeline-addon' === $slug ) {
			$_wp_real_parent_file['cool-plugins-timeline-addon'] = 'options-general.php';
		}

		$hook = add_submenu_page(
			'options-general.php',
			__( 'Timeline Addons', 'timeline-module-for-divi' ),
			__( 'Timeline Addons', 'timeline-module-for-divi' ),
			'manage_options',
			$slug,
			'__return_null'
		);

		add_action( 'load-' . $hook, array( $this, 'tmdivi_redirect_addons_menu_to_getting_started' ) );
	}

    public function tmdivi_redirect_addons_menu_to_getting_started() {
		wp_safe_redirect( admin_url( 'admin.php?page=tmdivi-getting-started' ) );
		exit;
	}

    public function tmdivi_hide_getting_started_settings_submenu_css() {
		echo '<style id="tmdivi-hide-getting-started-settings-submenu">
#menu-settings .wp-submenu li:has(> a[href="options-general.php?page=tmdivi-getting-started"]) {
	display: none !important;
}
</style>';
	}

    public function tmdivi_highlight_addons_menu( $parent_file ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only screen detection.
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

		if ( in_array( $page, array( 'tmdivi-getting-started', 'cool-plugins-timeline-addon', 'tmdivi-timeline-addons' ), true ) ) {
			return 'options-general.php';
		}

		return $parent_file;
	}

    public function tmdivi_highlight_addons_submenu( $submenu_file ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only screen detection.
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

		if ( in_array( $page, array( 'tmdivi-getting-started', 'cool-plugins-timeline-addon', 'tmdivi-timeline-addons' ), true ) ) {
			return $this->tmdivi_timeline_addons_menu_slug();
		}

		return $submenu_file;
	}

    public function tmdivi_pro_plugin_link($links){
		$page = tmdivi_use_ctl_getting_started() ? 'ctl-getting-started' : 'tmdivi-getting-started';
        $get_started ='<a href="' . esc_url( 'admin.php?page=' . $page . '&mode=onboarding' ) . '">Getting Started</a>';
        $get_pro_link = '<a href="https://cooltimeline.com/plugin/timeline-module-for-divi/?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=plugin_list" style="font-weight: bold; color: green;" target="_blank">Get Pro</a>';
		array_push( $links, $get_pro_link,$get_started );
		return $links;
    }

	/**
	 * Restore the deactivate row-action key after Cool Timeline reindexes it.
	 *
	 * Same as Timeline Widget Addon: Cool Timeline's addon filter runs at 999
	 * and can drop the 'deactivate' key. CPFM binds `.deactivate a`, so the
	 * key must be restored at 1000.
	 *
	 * @param array $links Plugin row action HTML.
	 * @return array
	 */
	public function tmdivi_order_plugin_action_links( $links ) {
		if ( ! is_array( $links ) ) {
			return $links;
		}

		$deactivate = '';
		$get_pro    = '';
		$started    = '';
		$rest       = array();

		foreach ( $links as $key => $html ) {
			if ( ! is_string( $html ) ) {
				$rest[ $key ] = $html;
				continue;
			}

			$is_deactivate = ( 'deactivate' === $key )
				|| false !== strpos( $html, 'action=deactivate' )
				|| false !== strpos( $html, 'id="deactivate-' );

			if ( $is_deactivate && '' === $deactivate ) {
				$deactivate = $html;
				continue;
			}

			if ( false !== stripos( $html, 'Get Pro' ) && '' === $get_pro ) {
				$get_pro = $html;
				continue;
			}

			if (
				'' === $started
				&& (
					false !== stripos( $html, 'Getting Started' )
					|| false !== stripos( $html, 'tmdivi-getting-started' )
					|| false !== stripos( $html, 'ctl-getting-started' )
				)
			) {
				$started = $html;
				continue;
			}

			$rest[ $key ] = $html;
		}

		$ordered = array();

		if ( '' !== $deactivate ) {
			$ordered['deactivate'] = $deactivate;
		}

		foreach ( $rest as $key => $html ) {
			$ordered[ $key ] = $html;
		}

		if ( '' !== $get_pro ) {
			$ordered['tmdivi_get_pro'] = $get_pro;
		}

		if ( '' !== $started ) {
			$ordered['tmdivi_getting_started'] = $started;
		}

		return $ordered;
	}

    public function tmdivi_plugin_redirection() {

        // Don't redirect if Divi is not active.
        if ( ! self::is_theme_activate( 'Divi' ) ) {
            delete_transient( 'tmdivi_activation_redirect' );
            return;
        }
    
        if ( ! get_transient( 'tmdivi_activation_redirect' ) ) {
            return;
        }
    
        delete_transient( 'tmdivi_activation_redirect' );

		$page = tmdivi_use_ctl_getting_started() ? 'ctl-getting-started' : 'tmdivi-getting-started';
        wp_safe_redirect( admin_url( 'admin.php?page=' . $page . '&mode=onboarding' ) );
        exit;
    }

    public function stop_browser_cache(){
        $post = get_post();
        if ( ! $post || ! isset( $post->post_content ) ) {
            return;
        }
        if ( is_singular() && false !== strpos( $post->post_content, '[tmdivi_timeline_story' ) && ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() ) ) {
            if ( ! headers_sent() ) {
                header( 'Cache-Control: no-cache, no-store, must-revalidate' );
                header( 'Pragma: no-cache' );
                header( 'Expires: 0' );
            }
        }
    }
    
    public function d5_extension_example_module_enqueue_frontend_scripts() {
        if(version_compare( wp_get_theme('Divi')->get('Version'), '5', '>=' )){
            $plugin_dir_url = TMDIVI_URL;
            wp_register_script( 'd5-timeline-line-filling', "{$plugin_dir_url}assets/js/tm_divi_vertical.min.js", array(), TMDIVI_V, true );
    
            wp_enqueue_style( 'd5-timeline-style', "{$plugin_dir_url}styles/style.min.css", array(), TMDIVI_V);

            wp_register_style( 'd5-timeline-helper-style', "{$plugin_dir_url}assets/css/divi-5-helper-css.css", array(), TMDIVI_V );

            wp_register_style('tmdivi-fontawesome-css', "{$plugin_dir_url}assets/css/fontawesome.min.css", array(), TMDIVI_V);
        }
    }

    public function is_divi_theme_exist(){
        if (!self::is_theme_activate('Divi')) {
            // Divi theme is not activated, display admin notice
            add_action('admin_notices', array($this, 'admin_notice_missing_divi_theme'));
        }   
    }
    /**
     * Initializes the extension.
     */
    public function initialize_extension() {
        require_once TMDIVI_DIR . '/includes/TimelineModuleForDivi.php';
    }
    
    public static function includes(){
        if(wp_get_theme('Divi')->get('Version') >= 5){
            require_once TMDIVI_DIR . '/divi-5/divi-5.php';
            new Divi5_Visual_Builder_Assets();
        }        
        require_once TMDIVI_MODULE_DIR . '/assets-loader.php';
        new TMDIVI_AssetsLoader();

        // Load marketing file upload option for Divi Contact Form
		require_once TMDIVI_DIR . 'admin/marketing/marketing-contact-form-extender.php';   

        add_action( 'cpo_onboarding_loaded', function () {
            require_once TMDIVI_DIR . '/admin/cp-onboarding/onboarding-config.php';
            } );
    
            require_once TMDIVI_DIR . 'admin/tmdivi-timeline-header.php';
    }

    public static function is_theme_activate($target){
        $theme = wp_get_theme();
        if ($theme->name == $target || stripos($theme->parent_theme, $target) !== false) {
            return true;
        }
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound	
        if (apply_filters('divi_ghoster_ghosted_theme', '') == $target) {
            return true;
        }
        return false;
    }

    public function admin_notice_missing_divi_theme(){
        $message = esc_html__(
            'Timeline Module For Divi requires Divi (Theme) to be installed and activated.',
            'timeline-module-for-divi'
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', esc_html( $message ) );
        deactivate_plugins(__FILE__);
    }  
    
    public function load_child_items()
    {
        require_once TMDIVI_MODULE_DIR . '/default-data-helper.php';
        if (!function_exists('et_fb_process_shortcode') || !class_exists(TMDIVI_DefaultDataHelper::class)) {
            return;
        }
        $data_helpers = new TMDIVI_DefaultDataHelper();
        $this->registerFiltersAndActions($data_helpers);
    }

    private function registerFiltersAndActions(TMDIVI_DefaultDataHelper $data_helpers)
    {
        add_filter('et_fb_backend_helpers', [$data_helpers, 'default_items_helpers'], 11);
        add_filter('et_fb_get_asset_helpers', [$data_helpers, 'asset_helpers'], 11);

        $enqueueScriptsCallback = function () use ($data_helpers) {
            wp_localize_script('et-frontend-builder', 'DCLBuilderBackend', $data_helpers->default_items_helpers());
        };

        add_action('wp_enqueue_scripts', $enqueueScriptsCallback);
        add_action('admin_enqueue_scripts', $enqueueScriptsCallback);
    }

    public static function tmdivi_activate_plugin() {
		update_option( 'tmdivi-v', TMDIVI_V );
		update_option( 'tmdivi-type', 'free' );

        $is_new_user = ( false === get_option( 'tmdivi-installDate' ) )
			&& ( false === get_option( 'tmdivi_initial_version' ) );
					
		// Only show welcome redirect for genuine first-time installs.
		if ( $is_new_user ) {
			update_option( 'tmdivi_is_new_user', 'yes' );
			update_option( 'tmdivi_onboarding_method', 'default', false );
			set_transient( 'tmdivi_activation_redirect', 1, 5 * MINUTE_IN_SECONDS );
		}
		update_option( 'tmdivi-installDate', gmdate( 'Y-m-d h:i:s' ) );
		update_option( 'tmdivi-defaultPlugin', true );

        if (!get_option( 'tmdivi_initial_version' ) ) {
            add_option( 'tmdivi_initial_version', TMDIVI_V );
        }

        if(!get_option( 'tmdivi-install-date' ) ) {
            add_option( 'tmdivi-install-date', gmdate('Y-m-d h:i:s') );
        }

        if ( ! get_option( 'tmdivi-Boxes-ratingDiv' ) ) {
            update_option( 'tmdivi-Boxes-ratingDiv', 'no' );  // Update rating div
        }

		if ( self::tmdivi_has_cpfm_consent() ) {
			self::tmdivi_maybe_schedule_tracking_cron();
		}
	}

	public static function tmdivi_deactivate_plugin() {
		wp_clear_scheduled_hook( TMDIVI_CPFM_CRON_HOOK );
	}

}

new TMDIVI_Timeline_Module_For_Divi();
