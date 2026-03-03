<?php
/**
 * Marketing promo for Contact Form Extender for Divi (Divi 4 & 5).
 *
 * This file is designed to be safely reused across multiple plugins.
 * The class and hooks will only be registered once, even if included
 * from several plugins.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CFE_Marketing' ) ) {

	class CFE_Marketing {

		/**
		 * Target plugin slug on WordPress.org.
		 *
		 * @var string
		 */
		private const TARGET_PLUGIN_SLUG = 'contact-form-extender-for-divi-builder';

		/**
		 * Target plugin main file for activation.
		 *
		 * @var string
		 */
		private const TARGET_PLUGIN_INIT = 'contact-form-extender-for-divi-builder/contact-form-extender-for-divi-builder.php';


		private const INSTALL_SOURCE_OPTION = 'cfefd_install_source';

		public function __construct() {
			add_filter( 'et_builder_get_parent_modules', array( $this, 'add_toggles' ), 10, 2 );
			add_filter( 'et_pb_all_fields_unprocessed_et_pb_contact_form', array( $this, 'add_promo_field' ), 20 );
			add_action( 'divi_visual_builder_assets_before_enqueue_scripts', array( $this, 'enqueue_vb_scripts' ), 999 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_vb_scripts' ), 999 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_notice_assets' ) );
			add_action( 'admin_notices', array( $this, 'maybe_show_admin_notice' ) );

			// AJAX handlers for install, activate & notice dismiss.
			add_action( 'wp_ajax_cfe_plugin_install', array( $this, 'validate_plugin_install_slug' ), 1 );
			add_action( 'wp_ajax_cfe_plugin_install', 'wp_ajax_install_plugin' );
			add_action( 'wp_ajax_cfe_plugin_activate', array( $this, 'ajax_activate_plugin' ) );
			add_action( 'wp_ajax_cfe_dismiss_contact_form_notice', array( $this, 'ajax_dismiss_contact_form_notice' ) );

			$this->cool_run_global_divi_contact_form_scan();
		}

		/**
		 * Determine which plugin folder this marketing code is running from.
		 *
		 * Example:
		 *  wp-content/plugins/timeline-module-for-divi/admin/marketing/...
		 *  -> timeline-module-for-divi
		 *
		 * @return string
		 */
		private function get_install_source_value() {
			static $value = null;
		
			if ( null === $value ) {
				// Go two levels up from /admin/marketing/ to reach plugin root folder.
				$plugin_dir = dirname( dirname( dirname( __FILE__ ) ) );
				$value      = basename( $plugin_dir );
			}

			$prefix = 'tmdivi';
			switch($value){
				case "timeline-module-for-divi":
					$prefix = "tmdivi";
					break;
				case "events-calendar-modules-for-divi":
					$prefix = "ecmd";
					break;
			}
		
			return $prefix;
		}

		public function cool_run_global_divi_contact_form_scan() {
			if ( ! is_admin() ) {
				return;
			}

			$using_contactform_module = get_option( 'cool_divi_contact_form_exists', '' );
			if ( 'yes' === $using_contactform_module || 'no' === $using_contactform_module ) {
				return;
			}

			// Run a one-time scan across posts to detect any Divi contact form usage.
			$found = $this->cool_scan_database_for_divi_contact_forms();

			// Cache the result so subsequent requests can use the option only.
			update_option( 'cool_divi_contact_form_exists', $found ? 'yes' : 'no' );
		}

		/**
		 * Enqueue small JS helper for the admin notice (install/activate/dismiss).
		 */
		public function enqueue_admin_notice_assets( $hook ) {
			if ( ! is_admin() ) {
				return;
			}

			if ( ! $this->is_cfe_admin_notice_screen() ) {
				return;
			}

			// Only enqueue when our notice might be shown.
			$exists = get_option( 'cool_divi_contact_form_exists', '' );
			if ( 'yes' !== $exists ) {
				return;
			}

			if ( 'active' === $this->get_plugin_status() ) {
				return;
			}

			// Respect per-user admin notice dismissal.
			$user_id   = get_current_user_id();
			$dismissed = $user_id ? get_user_meta( $user_id, 'cfe_admin_notice_dismissed', true ) : '';
			if ( 'yes' === $dismissed ) {
				return;
			}
			$base = plugins_url( '', __FILE__ );
			$ver  = defined( 'CFE_V' ) ? CFE_V : '1.0';

			wp_enqueue_script(
				'cfe-marketing-admin-notice',
				$base . '/assets/marketing-contact-form-extender-admin.js',
				array( 'jquery' ),
				$ver,
				true
			);

			$vars = array(
				'installNonce'  => wp_create_nonce( 'updates' ),
				'activateNonce' => wp_create_nonce( 'cfe_plugin_activate' ),
				'dismissNonce'  => wp_create_nonce( 'cfe_dismiss_notice' ),
				'ajaxurl'       => admin_url( 'admin-ajax.php' ),
				'pluginSlug'    => self::TARGET_PLUGIN_SLUG,
				'pluginInit'    => self::TARGET_PLUGIN_INIT,
				'status'        => $this->get_plugin_status(),
			);

			wp_localize_script(
				'cfe-marketing-admin-notice',
				'cfeAdminNotice',
				$vars
			);
		}

		/**
		 * Show admin notice when a Divi contact form is detected and CFE is not active.
		 */
		public function maybe_show_admin_notice() {
			if ( ! is_admin() ) {
				return;
			}

			if ( ! $this->is_cfe_admin_notice_screen() ) {
				return;
			}

			if ( 'active' === $this->get_plugin_status() ) {
				return;
			}

			$exists = get_option( 'cool_divi_contact_form_exists', '' );
			if ( 'yes' !== $exists ) {
				return;
			}

			$user_id = get_current_user_id();
			if ( $user_id ) {
				$dismissed = get_user_meta( $user_id, 'cfe_admin_notice_dismissed', true );
				if ( 'yes' === $dismissed ) {
					return;
				}
			}

			$status = $this->get_plugin_status();

			if ( ! current_user_can( 'install_plugins' ) && ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$action_html = '';
			if ( 'inactive' === $status ) {
				$action_html = sprintf(
					'<button type="button" class="button button-primary cfe-admin-activate-btn">%s</button>',
					esc_html__( 'Activate Now', 'events-calendar-modules-for-divi' )
				);
			} elseif ( 'not_installed' === $status ) {
				$action_html = sprintf(
					'<button type="button" class="button button-primary cfe-admin-install-btn">%s</button>',
					esc_html__( 'Install Now', 'events-calendar-modules-for-divi' )
				);
			}

			if ( empty( $action_html ) ) {
				return;
			}

			?>
			<div class="notice notice-info is-dismissible cfe-contact-form-admin-notice"
				data-slug="<?php echo esc_attr( self::TARGET_PLUGIN_SLUG ); ?>"
				data-init="<?php echo esc_attr( self::TARGET_PLUGIN_INIT ); ?>">
				<div class="cfe-admin-notice-content">
					<p>
						<strong><?php esc_html_e( 'Improve your Divi Contact Form.', 'events-calendar-modules-for-divi' ); ?></strong>
						<?php esc_html_e( 'Save submissions, add file upload, and unlock advanced fields with Contact Form Extender for Divi.', 'events-calendar-modules-for-divi' ); ?>

						<?php echo $action_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> 
					</p>
				</div>
			</div>
			<?php
		}

		/**
		 * Whether the current admin screen should display the CFE admin notice.
		 *
		 * Restricts the notice to:
		 * - Plugins list.
		 * - Plugin install page.
		 * - Specific Divi dashboard pages.
		 * - Divi Library list.
		 * - Divi theme customizer.
		 *
		 * @return bool
		 */
		private function is_cfe_admin_notice_screen() {
			if ( ! is_admin() ) {
				return false;
			}

			global $pagenow;

			// Core plugin screens.
			if ( in_array( $pagenow, array( 'plugins.php', 'plugin-install.php' ), true ) ) {
				return true;
			}

			// Divi dashboard/admin pages (admin.php?page=...).
			$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
			if ( in_array(
				$page,
				array(
					'et_onboarding',
					'et_divi_options',
					'et_theme_builder',
					'et_divi_role_editor',
					'et_support_center_divi',
					'et_d5_readiness',
				),
				true
			) ) {
				return true;
			}

			// Divi Library list: edit.php?post_type=et_pb_layout.
			$post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';
			if ( 'et_pb_layout' === $post_type && 'edit.php' === $pagenow ) {
				return true;
			}

			// Divi theme customizer.
			$option_set = isset( $_GET['et_customizer_option_set'] ) ? sanitize_key( wp_unslash( $_GET['et_customizer_option_set'] ) ) : '';
			if ( 'customize.php' === $pagenow && 'theme' === $option_set ) {
				return true;
			}

			return false;
		}

		/**
		 * AJAX: Persist dismissal of the admin or editor notice per user.
		 * Uses separate meta keys so admin and editor notices can be dismissed independently.
		 */
		public function ajax_dismiss_contact_form_notice() {
			check_ajax_referer( 'cfe_dismiss_notice', 'nonce' );

			if ( ! is_user_logged_in() ) {
				wp_send_json_error();
			}

			$user_id = get_current_user_id();
			$context = isset( $_POST['context'] ) ? sanitize_text_field( wp_unslash( $_POST['context'] ) ) : '';

			if ( 'admin' === $context ) {
				update_user_meta( $user_id, 'cfe_admin_notice_dismissed', 'yes' );
			} elseif ( 'editor' === $context ) {
				update_user_meta( $user_id, 'cfe_editor_notice_dismissed', 'yes' );
			} else {
				// Fallback: update both for backward compatibility with old callers.
				update_user_meta( $user_id, 'cfe_admin_notice_dismissed', 'yes' );
				update_user_meta( $user_id, 'cfe_editor_notice_dismissed', 'yes' );
			}

			wp_send_json_success();
		}

		public function cool_scan_database_for_divi_contact_forms() {
			global $wpdb;
		
			$like_shortcode = '%et_pb_contact_form%';      // Divi 4
			$like_block     = '%wp:divi/contact-form%';    // Divi 5 blocks
		
			$sql = $wpdb->prepare("
				SELECT ID FROM $wpdb->posts
				WHERE post_status = 'publish'
				AND post_type IN ('page','post','et_pb_layout')
				AND (
					post_content LIKE %s
					OR post_content LIKE %s
				)
				LIMIT 1
			", $like_shortcode, $like_block);
		
			$result = $wpdb->get_var($sql);
			return ! empty($result);
		}
		/**
		 * Enqueue builder scripts/styles and expose config variables.
		 */
		public function enqueue_vb_scripts() {
			if ( ! function_exists( 'et_core_is_fb_enabled' ) || ! et_core_is_fb_enabled() ) {
				return;
			}

			static $done = false;
			if ( $done ) {
				return;
			}
			$done = true;

			$vars = $this->get_js_vars();
			

			if ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() ) {
				$this->enqueue_editor_script( $vars );
			} 
		}

		/**
		 * Build shared JS config.
		 *
		 * @return array
		 */
		private function get_js_vars() {
			return array(
				'ajaxurl'          => admin_url( 'admin-ajax.php' ),
				'installNonce'     => wp_create_nonce( 'updates' ),
				'activateNonce'    => wp_create_nonce( 'cfe_plugin_activate' ),
				'dismissNonce'     => wp_create_nonce( 'cfe_dismiss_notice' ),
				'pluginSlug'       => self::TARGET_PLUGIN_SLUG,
				'pluginInit'       => self::TARGET_PLUGIN_INIT,
				'status'           => $this->get_plugin_status(),
				'editorDismissed'  => $this->has_user_dismissed_editor_promo(),
			);
		}

		/**
		 * Whether the current user has dismissed the editor promo (Divi builder/contact form settings).
		 *
		 * @return bool
		 */
		private function has_user_dismissed_editor_promo() {
			$user_id = get_current_user_id();
			if ( ! $user_id ) {
				return false;
			}
			return get_user_meta( $user_id, 'cfe_editor_notice_dismissed', true ) === 'yes';
		}

		/**
		 * Enqueue assets for Divi 4 builder.
		 *
		 * @param array $vars
		 */
		public function enqueue_editor_script( $vars ) {
			$base = plugins_url( '', __FILE__ );
			$ver  = defined( 'CFE_V' ) ? CFE_V : '1.0';

			wp_enqueue_style(
				'cfe-marketing-contact-form-extender',
				$base . '/assets/marketing-contact-form-extender.css',
				array(),
				$ver
			);

			wp_enqueue_script(
				'cfe-marketing-contact-form-extender',
				$base . '/assets/marketing-contact-form-extender.js',
				array( 'jquery' ),
				$ver,
				true
			);

			wp_localize_script( 'cfe-marketing-contact-form-extender', 'cfe_plugin_vars', $vars );
		}

		/**
		 * Add custom toggle in Divi 4 contact form settings.
		 *
		 * @param array  $modules
		 * @param string $post_type
		 *
		 * @return array
		 */
		public function add_toggles( $modules, $post_type ) {
			// Do not show marketing section if plugin is already active.
			if ( 'active' === $this->get_plugin_status() ) {
				return $modules;
			}

			// Do not show if user has dismissed the promo (admin or editor).
			if ( $this->has_user_dismissed_editor_promo() ) {
				return $modules;
			}

			if ( isset( $modules['et_pb_contact_form'] ) ) {
				$modules['et_pb_contact_form']->settings_modal_toggles['general']['toggles']['cfe_marketing_promo'] = array(
					'title'    => esc_html__( 'Save submissions', 'events-calendar-modules-for-divi' ),
					'priority' => 200,
				);
			}

			return $modules;
		}

		/**
		 * Add the promo field into contact form settings (Divi 4).
		 *
		 * Button label and type depend on plugin status.
		 *
		 * @param array $fields_unprocessed
		 *
		 * @return array
		 */
		public function add_promo_field( $fields_unprocessed ) {
			static $field = null;
			$status       = $this->get_plugin_status();

			// If already active, do not show the marketing field at all.
			if ( 'active' === $status ) {
				return $fields_unprocessed;
			}

			// Do not show if user has dismissed the promo (admin or editor).
			if ( $this->has_user_dismissed_editor_promo() ) {
				return $fields_unprocessed;
			}

			if ( $field === null ) {
				if ( 'inactive' === $status ) {
					$button_html = sprintf(
						'<button type="button" class="cfe-d4-promo__btn cfe-activate-plugin-btn" data-init="%s" style="display:inline-block;padding:10px 16px;background:#007cba;border:none;color:#fff;border-radius:4px;font-weight:bold;width:100%%;text-align:center;box-sizing:border-box;cursor:pointer;">%s</button>',
						esc_attr( self::TARGET_PLUGIN_INIT ),
						esc_html__( 'Activate Plugin', 'events-calendar-modules-for-divi' )
					);
				} else {
					$button_html = sprintf(
						'<button type="button" class="cfe-d4-promo__btn cfe-install-plugin-btn" data-slug="%s" data-init="%s" style="display:inline-block;padding:10px 16px;background:#007cba;border:none;color:#fff;border-radius:4px;font-weight:bold;width:100%%;text-align:center;box-sizing:border-box;cursor:pointer;">%s</button>',
						esc_attr( self::TARGET_PLUGIN_SLUG ),
						esc_attr( self::TARGET_PLUGIN_INIT ),
						esc_html__( 'Install & Activate', 'events-calendar-modules-for-divi' )
					);
				}

				$field = array(
					'label'           => '',
					'type'            => 'text',
					'default'         => '',
					'option_category' => 'basic_option',
					'description'     => sprintf(
						'<div style="text-align:left;color:#666;"><div style="font-weight:700;color:#333;margin-bottom:5px;">%s</div>%s<br><br>%s</div>',
						esc_html__( 'Enhance Your Contact Form', 'events-calendar-modules-for-divi' ),
						esc_html__( 'Want better form management? Save submissions, country code add file upload, and extend your Divi Form with Contact Form Extender for Divi.', 'events-calendar-modules-for-divi' ),
						$button_html
					),
					'toggle_slug'     => 'cfe_marketing_promo',
				);
			}

			// Internal option key, used only for this marketing field.
			$fields_unprocessed['cfe_marketing_promo_field'] = $field;

			return $fields_unprocessed;
		}

		/**
		 * Validate that only the allowed plugin slug can be installed via our AJAX.
		 * Runs at priority 1 before wp_ajax_install_plugin; exits with error if slug is invalid.
		 */
		public function validate_plugin_install_slug() {
			$slug = isset( $_REQUEST['slug'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['slug'] ) ) : '';
			if ( $slug !== self::TARGET_PLUGIN_SLUG ) {
				wp_send_json_error( array( 'message' => __( 'Invalid plugin. Only Contact Form Extender for Divi can be installed from here.', 'events-calendar-modules-for-divi' ) ) );
			}

			if ( ! get_option( self::INSTALL_SOURCE_OPTION ) ) {
				update_option( self::INSTALL_SOURCE_OPTION, $this->get_install_source_value() );
			}
		}

		/**
		 * AJAX: Activate the target plugin.
		 */
		public function ajax_activate_plugin() {
			check_ajax_referer( 'cfe_plugin_activate', 'security' );

			if ( ! current_user_can( 'activate_plugins' ) ) {
				wp_send_json_error( array( 'message' => __( 'You do not have permission to activate plugins.', 'events-calendar-modules-for-divi' ) ) );
			}

			if ( empty( $_POST['init'] ) ) {
				wp_send_json_error( array( 'message' => __( 'Plugin init file missing.', 'events-calendar-modules-for-divi' ) ) );
			}

			include_once ABSPATH . 'wp-admin/includes/plugin.php';

			$init_file = sanitize_text_field( wp_unslash( $_POST['init'] ) );
			$activate  = activate_plugin( $init_file, '', false, true );

			if ( is_wp_error( $activate ) ) {
				wp_send_json_error( array( 'message' => $activate->get_error_message() ) );
			}

			wp_send_json_success( array( 'message' => __( 'Plugin activated successfully.', 'events-calendar-modules-for-divi' ) ) );
		}

		/**
		 * Determine plugin status.
		 *
		 * @return string not_installed|inactive|active
		 */
		private function get_plugin_status() {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$plugin_file = self::TARGET_PLUGIN_INIT;
			$plugins     = get_plugins();
			$installed   = isset( $plugins[ $plugin_file ] );

			if ( $installed && is_plugin_active( $plugin_file ) ) {
				return 'active';
			}

			if ( $installed ) {
				return 'inactive';
			}

			return 'not_installed';
		}
	}

	new CFE_Marketing();
}
