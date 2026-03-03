(function ($, global) {
	'use strict';

	var cfg = global.cfeAdminNotice || {};
	if (!cfg || !cfg.ajaxurl) {
		return;
	}

	function postAjax(action, payload) {
		var data = $.extend({}, payload || {}, { action: action });

		if (action === 'cfe_plugin_install') {
			data._ajax_nonce = cfg.installNonce;
		} else if (action === 'cfe_plugin_activate') {
			data.security = cfg.activateNonce;
		} else if (action === 'cfe_dismiss_contact_form_notice') {
			data.nonce = cfg.dismissNonce;
		}

		return $.post(cfg.ajaxurl, data);
	}

	$(function () {
		var $notice = $('.cfe-contact-form-admin-notice');
		if (!$notice.length) {
			return;
		}

		$notice.on('click', '.cfe-admin-install-btn', function (e) {
			e.preventDefault();

			var $btn  = $(this);
			var slug  = $notice.data('slug') || cfg.pluginSlug;
			if(slug !== 'contact-form-extender-for-divi-builder') return;
			var init  = $notice.data('init') || cfg.pluginInit;
			if (!slug || !init) {
				return;
			}

			var original = $btn.text();
			$btn.prop('disabled', true).text('Installing...');

			postAjax('cfe_plugin_install', { slug: slug })
				.done(function (res) {
					if (!res || !res.success) {
						alert((res && res.data && res.data.message) ? res.data.message : 'Installation failed. Please install from the Plugins page.');
						$btn.prop('disabled', false).text(original);
						return;
					}

					$btn.text('Activating...');

					postAjax('cfe_plugin_activate', { init: init })
						.done(function (res2) {
							if (!res2 || !res2.success) {
								alert((res2 && res2.data && res2.data.message) ? res2.data.message : 'Activation failed. Please activate from the Plugins page.');
								$btn.prop('disabled', false).text(original);
								return;
							}

							$notice.addClass('cfe-notice-active');
							$notice.find('.cfe-admin-notice-content').html(
								'<p><strong>Contact Form Extender for Divi is now active.</strong> Unlock powerful advanced fields and enhance your forms with Contact Form Extender for Divi.</p>'
							);
						});
				});
		});

		$notice.on('click', '.cfe-admin-activate-btn', function (e) {
			e.preventDefault();

			var $btn = $(this);
			var init = $notice.data('init') || cfg.pluginInit;
			if (!init) {
				return;
			}

			var original = $btn.text();
			$btn.prop('disabled', true).text('Activating...');

			postAjax('cfe_plugin_activate', { init: init })
				.done(function (res) {
					if (!res || !res.success) {
						alert((res && res.data && res.data.message) ? res.data.message : 'Activation failed. Please activate from the Plugins page.');
						$btn.prop('disabled', false).text(original);
						return;
					}

					$notice.addClass('cfe-notice-active');
					$notice.find('.cfe-admin-notice-content').html(
						'<p><strong>Contact Form Extender for Divi is now active.</strong> Unlock powerful advanced fields and enhance your forms with Contact Form Extender for Divi.</p>'
					);
				});
		});

		$notice.on('click', '.cfe-admin-reload-btn', function (e) {
			e.preventDefault();
			if (global.location) {
				global.location.reload();
			}
		});

		$(document).on('click', '.cfe-contact-form-admin-notice .notice-dismiss', function () {
			postAjax('cfe_dismiss_contact_form_notice', { context: 'admin' });
		});
	});
})(jQuery, window);

