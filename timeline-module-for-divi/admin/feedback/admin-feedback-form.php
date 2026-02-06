<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

class TMDIVI_feedback {

		private $plugin_url     = TMDIVI_URL;
		private $plugin_version = TMDIVI_V;
		private $plugin_name    = 'Timeline Module For Divi';
		private $plugin_slug    = 'timeline-module-for-divi';
		private $installation_date_option = 'tmdivi-installDate';
		private $review_option = 'tmdivi-Boxes-ratingDiv';
		private $buy_link = 'https://cooltimeline.com/plugin/timeline-module-for-divi/?utm_source=tmdivi_plugin&utm_medium=inside&utm_campaign=get_pro&utm_content=admin_review_notice';
		private $review_link = 'https://wordpress.org/support/plugin/timeline-module-for-divi/reviews/#new-post';
		private $plugin_logo = 'assets/image/divi-timeline-logo.png';


	/*
	|-----------------------------------------------------------------|
	|   Use this constructor to fire all actions and filters          |
	|-----------------------------------------------------------------|
	*/
	public function __construct() {
		// $this->plugin_url = plugin_dir_url( $this->plugin_url );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_feedback_scripts' ) );
		add_action( 'admin_head', array( $this, 'show_deactivate_feedback_popup' ) );
		add_action( 'wp_ajax_' . $this->plugin_slug . '_submit_deactivation_response', array( $this, 'submit_deactivation_response' ) );
		add_action( 'admin_notices', array( $this, 'tmdivi_admin_notice_for_review' ) );
		add_action( 'wp_ajax_tmdivi_dismiss_notice', array( $this, 'tmdivi_dismiss_review_notice' ) );

	}

	/*
	|-----------------------------------------------------------------|
	|   Enqueue all scripts and styles to required page only          |
	|-----------------------------------------------------------------|
	*/
	function enqueue_feedback_scripts() {
		$screen = get_current_screen();
		if ( isset( $screen ) && $screen->id == 'plugins' ) {
			wp_enqueue_script( 'tmdivi-feedback-script', $this->plugin_url . 'admin/feedback/js/admin-feedback.js', array( 'jquery' ), $this->plugin_version, true );
			wp_enqueue_style( 'tmdivi-feedback-css', $this->plugin_url . 'admin/feedback/css/admin-feedback.css', null, $this->plugin_version );
		}

		$alreadyRated = get_option( $this->review_option ) != false ? get_option( $this->review_option ) : 'no';

		// check user already rated
		if ($alreadyRated == 'yes') {
			return;
		}

		wp_register_style( 'tmdivi-feedback-notice', $this->plugin_url . 'admin/feedback/css/tmdivi-feedback-notice.css', array(), $this->plugin_version, 'all' );

		wp_register_script( 'tmdivi-feedback-notice', $this->plugin_url . 'admin/feedback/js/tmdivi-feedback-notice.js', array( 'jquery' ), $this->plugin_version, true );
	}

	public function tmdivi_dismiss_review_notice(){
		if ( check_ajax_referer( 'tmdivi_dismiss_notice_nonce', 'nonce' ) ){
			$rs = update_option( $this->review_option, 'yes' );
			echo json_encode( array( 'success' => 'true' ) );
		}else {
			wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
		}
		exit;
	}

	public function tmdivi_admin_notice_for_review(){
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		// get installation dates and rated settings
		$installation_date = get_option( $this->installation_date_option );
		$alreadyRated      = get_option( $this->review_option ) != false ? get_option( $this->review_option ) : 'no';

		// check user already rated
		if ( $alreadyRated == 'yes' ) {
			return;
		}

		// grab plugin installation date and compare it with current date
		// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date		
		$display_date = date( 'Y-m-d h:i:s' );
		$install_date = new DateTime( $installation_date );
		$current_date = new DateTime( $display_date );
		$difference   = $install_date->diff( $current_date );
		$diff_days    = $difference->days;

		// check if installation days is greator then week
		if ( isset( $diff_days ) && $diff_days >= 3 ) {
			wp_enqueue_style('tmdivi-feedback-notice');
			wp_enqueue_script('tmdivi-feedback-notice');
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
			echo $this->tmdivi_create_notice_content();
		}
	}

	function tmdivi_create_notice_content() {
		$wp_nonce = wp_create_nonce('tmdivi_dismiss_notice_nonce');
		$ajax_url      = esc_url( admin_url( 'admin-ajax.php' ) );
		$ajax_callback      = 'tmdivi_dismiss_notice';
		$wrap_cls           = 'notice notice-info is-dismissible';
		$img_path      = '';
		$p_name             = esc_html('Timeline Module For Divi');
		$like_it_text       =  esc_html('Rate Now! ★★★★★');
		$already_rated_text = esc_html__('Already Reviewed', 'timeline-module-for-divi');
		$not_interested     = esc_html__('Not Interested', 'timeline-module-for-divi');
		$not_like_it_text   = esc_html__('No, not good enough, i do not like to rate it!', 'timeline-module-for-divi');
		$p_link             = esc_url($this->review_link);
		$pro_url            = $this->buy_link;

		$raw_message = "Thanks for using <b>$p_name</b> WordPress plugin. We hope it meets your expectations! <br/>Please give us a quick rating, it works as a boost for us to keep working on more <a href='https://coolplugins.net' target='_blank'><strong>Cool Plugins</strong></a>!<br/>";

		$allowed_html = array(
			'b'      => array(),
			'br'     => array(),
			'a'      => array(
				'href'   => array(),
				'target' => array(),
			),
			'strong' => array(),
		);

		$message = wp_kses($raw_message, $allowed_html);


		$html = '<div data-ajax-url="%8$s"  data-ajax-callback="%9$s" class="cool-feedback-notice-wrapper %1$s" data-wp-nonce="%12$s">
			<div class="message_container">%4$s
				<div class="callto_action">
					<ul>
						<li class="love_it"><a href="%5$s" class="like_it_btn button button-primary" target="_new" title="%6$s">%6$s</a></li>
						<li class="already_rated"><a href="javascript:void(0);" class="already_rated_btn button tmdivi_dismiss_notice" title="%7$s">%7$s</a></li>
						<li class="already_rated"><a href="javascript:void(0);" class="already_rated_btn button tmdivi_dismiss_notice" title="%11$s">%11$s</a></li>
					</ul>
				<div class="clrfix"></div>
				</div>
			</div>
        </div>';

		return sprintf(
			$html,
			$wrap_cls,
			$img_path,
			$p_name,
			$message,
			$p_link,
			$like_it_text,
			$already_rated_text,
			$ajax_url, // 8
			$ajax_callback, // 9
			$pro_url, // 10
			$not_interested,
			$wp_nonce
		);
	}
	/*
	|-----------------------------------------------------------------|
	|   HTML for creating feedback popup form                         |
	|-----------------------------------------------------------------|
	*/
	public function show_deactivate_feedback_popup() {
		$screen = get_current_screen();
		if ( ! isset( $screen ) || $screen->id != 'plugins' ) {
			return;
		}
		$deactivate_reasons = array(
			'didnt_work_as_expected'         => array(
				'title'             => __( 'The plugin didn\'t work as expected.', 'timeline-module-for-divi' ),
				'input_placeholder' => 'What did you expect?',
			),
			'found_a_better_plugin'          => array(
				'title'             => __( 'I found a better plugin.', 'timeline-module-for-divi' ),
				'input_placeholder' => __( 'Please share which plugin.', 'timeline-module-for-divi' ),
			),
			'couldnt_get_the_plugin_to_work' => array(
				'title'             => __( 'The plugin is not working.', 'timeline-module-for-divi' ),
				'input_placeholder' => 'Please share your issue. So we can fix that for other users.',
			),
			'temporary_deactivation'         => array(
				'title'             => __( 'It\'s a temporary deactivation.', 'timeline-module-for-divi' ),
				'input_placeholder' => '',
			),
			'other'                          => array(
				'title'             => __( 'Other reason.', 'timeline-module-for-divi' ),
				'input_placeholder' => __( 'Please share the reason.', 'timeline-module-for-divi' ),
			),
		);

		?>
		<div id="cool-plugins-feedback-<?php echo esc_attr( $this->plugin_slug ); ?>" class="hide-feedback-popup">
						
			<div class="cp-feedback-wrapper">

			<div class="cp-feedback-header">
				<div class="cp-feedback-title"><?php echo esc_html__( 'Quick Feedback', 'timeline-module-for-divi' ); ?></div>
				<div class="cp-feedback-title-link">A plugin by <a href="https://coolplugins.net/?utm_source=<?php echo esc_url( $this->plugin_slug ); ?>_plugin&utm_medium=inside&utm_campaign=coolplugins&utm_content=deactivation_feedback" target="_blank">CoolPlugins.net</a></div>
			</div>

			<div class="cp-feedback-loader">
				<img src="<?php echo esc_url( $this->plugin_url ); ?>admin/feedback/images/cool-plugins-preloader.gif">
			</div>

			<div class="cp-feedback-form-wrapper">
				<div class="cp-feedback-form-title"><?php echo esc_html__( 'If you have a moment, please share the reason for deactivating this plugin.', 'timeline-module-for-divi' ); ?></div>
				<form class="cp-feedback-form" method="post">
					<?php
					wp_nonce_field( '_cool-plugins_deactivate_feedback_nonce' );
					?>
					<input type="hidden" name="action" value="cool-plugins_deactivate_feedback" />
					
					<?php foreach ( $deactivate_reasons as $reason_key => $reason ) : ?>
						<div class="cp-feedback-input-wrapper">
							<input id="cp-feedback-reason-<?php echo esc_attr( $reason_key ); ?>" class="cp-feedback-input" type="radio" name="reason_key" value="<?php echo esc_attr( $reason_key ); ?>" />
							<label for="cp-feedback-reason-<?php echo esc_attr( $reason_key ); ?>" class="cp-feedback-reason-label"><?php echo esc_html( $reason['title'] ); ?></label>
							<?php if ( ! empty( $reason['input_placeholder'] ) ) : ?>
								<textarea class="cp-feedback-text" type="textarea" name="reason_<?php echo esc_attr( $reason_key ); ?>" placeholder="<?php echo esc_attr( $reason['input_placeholder'] ); ?>"></textarea>
							<?php endif; ?>
							<?php if ( ! empty( $reason['alert'] ) ) : ?>
								<div class="cp-feedback-text"><?php echo esc_html( $reason['alert'] ); ?></div>
							<?php endif; ?>	
						</div>
					<?php endforeach; ?>
					
					<div class="cp-feedback-terms">
					<input class="cp-feedback-terms-input" id="cp-feedback-terms-input" type="checkbox"><label for="cp-feedback-terms-input"><?php echo esc_html__( 'I agree to share my feedback with Cool Plugins, including site URL and admin email, to enable them to address my inquiry.', 'timeline-module-for-divi' ); ?></label>
					</div>

					<div class="cp-feedback-button-wrapper">
						<a class="cp-feedback-button cp-submit" id="cool-plugin-submitNdeactivate">Submit and Deactivate</a>
						<a class="cp-feedback-button cp-skip" id="cool-plugin-skipNdeactivate">Skip and Deactivate</a>
					</div>
				</form>
			</div>


		   </div>
		</div>
		<?php
	}

	function tmdivi_get_user_info(){
		global $wpdb;
        // Server and WP environment details
        $server_info = [
            'server_software'        => isset($_SERVER['SERVER_SOFTWARE']) ? sanitize_text_field(wp_unslash($_SERVER['SERVER_SOFTWARE'])) : 'N/A',
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching		
            'mysql_version'          => $wpdb ? sanitize_text_field($wpdb->get_var("SELECT VERSION()")) : 'N/A',
            'php_version'            => sanitize_text_field(phpversion() ?: 'N/A'),
            'wp_version'             => sanitize_text_field(get_bloginfo('version') ?: 'N/A'),
            'wp_debug'               => (defined('WP_DEBUG') && WP_DEBUG) ? 'Enabled' : 'Disabled',
            'wp_memory_limit'        => sanitize_text_field(ini_get('memory_limit') ?: 'N/A'),
            'wp_max_upload_size'     => sanitize_text_field(ini_get('upload_max_filesize') ?: 'N/A'),
            'wp_permalink_structure' => sanitize_text_field(get_option('permalink_structure') ?: 'Default'),
            'wp_multisite'           => is_multisite() ? 'Enabled' : 'Disabled',
            'wp_language'            => sanitize_text_field(get_option('WPLANG') ?: get_locale()),
            'wp_prefix'              => isset($wpdb->prefix) ? sanitize_key($wpdb->prefix) : 'N/A',
        ];
        // Theme details
        $theme = wp_get_theme();
        $theme_data = [
            'name'      => sanitize_text_field($theme->get('Name')),
            'version'   => sanitize_text_field($theme->get('Version')),
            'theme_uri' => esc_url($theme->get('ThemeURI')),
        ];
        // Ensure plugin functions are loaded
        if ( ! function_exists('get_plugins') ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        // Active plugins details
        $active_plugins = get_option('active_plugins', []);
        $plugin_data = [];
        foreach ( $active_plugins as $plugin_path ) {
            $plugin_info = get_plugin_data(WP_PLUGIN_DIR . '/' . sanitize_text_field($plugin_path));
            $plugin_data[] = [
                'name'       => sanitize_text_field($plugin_info['Name']),
                'version'    => sanitize_text_field($plugin_info['Version']),
                'plugin_uri' => esc_url($plugin_info['PluginURI']),
            ];
        }
        return [
            'server_info'   => $server_info,
            'extra_details' => [
                'wp_theme'       => $theme_data,
                'active_plugins' => $plugin_data,
            ],
        ];
	}

	function submit_deactivation_response() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), '_cool-plugins_deactivate_feedback_nonce' ) ) {
			wp_send_json_error();
		} else {
			$reason             = isset( $_POST['reason'] ) ? sanitize_text_field( wp_unslash( $_POST['reason'] ) ) : '';
			$deactivate_reasons = array(
				'didnt_work_as_expected'         => array(
					'title'             => __( 'The plugin didn\'t work as expected', 'timeline-module-for-divi' ),
					'input_placeholder' => 'What did you expect?',
				),
				'found_a_better_plugin'          => array(
					'title'             => __( 'I found a better plugin', 'timeline-module-for-divi' ),
					'input_placeholder' => __( 'Please share which plugin.', 'timeline-module-for-divi' ),
				),
				'couldnt_get_the_plugin_to_work' => array(
					'title'             => __( 'The plugin is not working', 'timeline-module-for-divi' ),
					'input_placeholder' => 'Please share your issue. So we can fix that for other users.',
				),
				'temporary_deactivation'         => array(
					'title'             => __( 'It\'s a temporary deactivation.', 'timeline-module-for-divi' ),
					'input_placeholder' => '',
				),
				'other'                          => array(
					'title'             => __( 'Other', 'timeline-module-for-divi' ),
					'input_placeholder' => __( 'Please share the reason.', 'timeline-module-for-divi' ),
				),
			);

			$plugin_initial =  get_option( 'tmdivi_initial_version' );

			$deativation_reason = array_key_exists( $reason, $deactivate_reasons ) ? $reason : 'other';

			$deativation_reason = esc_html($deativation_reason);
			$sanitized_message = empty( $_POST['message'] ) || sanitize_text_field( wp_unslash($_POST['message']) ) == '' ? 'N/A' : sanitize_text_field( wp_unslash($_POST['message']) );
			$admin_email       = sanitize_email( get_option( 'admin_email' ) );
			$site_url          = esc_url( site_url() );
			$feedback_url      = esc_url( 'https://feedback.coolplugins.net/wp-json/coolplugins-feedback/v1/feedback' );
			$install_date 		= get_option('tmdivi-installDate');
			$unique_key     	= '56';
			$site_id        	= $site_url . '-' . $install_date . '-' . $unique_key;
			$response          = wp_remote_post(
				$feedback_url,
				array(
					'timeout' => 30,
					'body'    => array(
						'server_info' => serialize($this->tmdivi_get_user_info()['server_info']),
                        'extra_details' => serialize($this->tmdivi_get_user_info()['extra_details']),
						'plugin_initial'  => isset($plugin_initial) ? sanitize_text_field($plugin_initial) : 'N/A',
						'plugin_version' => sanitize_text_field($this->plugin_version),
						'plugin_name'    => sanitize_text_field($this->plugin_name),
						'reason'         => $deativation_reason,
						'review'         => $sanitized_message,
						'email'          => $admin_email,
						'domain'         => $site_url,
						'site_id'    	 => md5($site_id),
					),
				)
			);

			// die( json_encode( array( 'response' => $response ) ) );
		}

	}
}
new TMDIVI_feedback();
