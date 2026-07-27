<?php
/*
Plugin Name: Timeline Module For Divi
Plugin URI:  https://cooltimeline.com/divi/?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=product_site&utm_content=plugins_list
Description: A timeline module for Divi
Version:     1.3.0
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


define('TMDIVI_V', '1.3.0');
define('TMDIVI_DIR', plugin_dir_path(__FILE__));
define('TMDIVI_URL', plugin_dir_url(__FILE__));
define('TMDIVI_MODULE_URL', plugin_dir_url(__FILE__) . 'includes/modules');
define('TMDIVI_MODULE_DIR', plugin_dir_path(__FILE__) . 'includes/modules');

register_activation_hook( __FILE__, array( 'TMDIVI_Timeline_Module_For_Divi', 'tmdivi_activate_plugin' ) );

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

		if ( tmdivi_use_ctl_getting_started() ) {
			add_action( 'admin_init', array( $this, 'tmdivi_redirect_getting_started_to_ctl' ) );
		} else {
			add_action( 'admin_menu', array( $this, 'tmdivi_register_timeline_addons_menu' ), 9 );
			add_action( 'admin_head', array( $this, 'tmdivi_hide_getting_started_settings_submenu_css' ) );
			add_filter( 'parent_file', array( $this, 'tmdivi_highlight_addons_menu' ), 999 );
			add_filter( 'submenu_file', array( $this, 'tmdivi_highlight_addons_submenu' ), 999 );
		}
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
        if ( is_admin() ) {
            require_once TMDIVI_DIR . 'admin/feedback/admin-feedback-form.php';
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
	}

}

new TMDIVI_Timeline_Module_For_Divi();