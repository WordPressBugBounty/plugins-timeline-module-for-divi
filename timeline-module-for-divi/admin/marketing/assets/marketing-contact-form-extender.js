(function (global) {
    'use strict';

    var config = global.cfe_plugin_vars || {};
    var PLUGIN_SLUG = config.pluginSlug || 'contact-form-extender-for-divi-builder';
    var PLUGIN_INIT = config.pluginInit || 'contact-form-extender-for-divi-builder/contact-form-extender-for-divi-builder.php';
    var STATUS = config.status || 'not_installed';
    var AJAX_URL = config.ajaxurl || (typeof ajaxurl !== 'undefined' ? ajaxurl : '');
    var INSTALL_NONCE = config.installNonce || '';
    var ACTIVATE_NONCE = config.activateNonce || '';
    var DISMISS_NONCE = config.dismissNonce || '';
    var EDITOR_DISMISSED = !!config.editorDismissed;

    // Divi 4: when the marketing toggle is opened, ensure UI reflects current STATUS.
    jQuery(document).on('mousedown click', '.et-fb-form__toggle[data-name="cfe_marketing_promo"]', function (event) {
        setTimeout(function () {
            var $mainButton = jQuery(event.currentTarget).find('button.cfe-d4-promo__btn');
            if (!$mainButton.length) {
                return;
            }
            
            if (STATUS === 'active') {
                var boxEl = $mainButton.closest('.cfe-d4-promo, .cfe-promo-notice').get(0);
                if (boxEl) {
                    showReloadNotice(boxEl);
                }
            }
        }, 100);
    });

    // Divi 5: when the "Save submissions" marketing group is opened,
    // ensure the notice reflects current STATUS (e.g., reload state).
    jQuery(document).on('mousedown click', '.et-vb-modal-group .et-vb-modal-group-title[data-name="cfeMarketingPromoGroup"]', function (event) {
        setTimeout(function () {
            var $group = jQuery(event.currentTarget).closest('.et-vb-modal-group');
            if (!$group.length) {
                return;
            }

            var $promoBox = $group.find('.cfe-promo-notice');
            if (!$promoBox.length) {
                return;
            }

            if (STATUS === 'active') {
                var boxEl = $promoBox.get(0);
                if (boxEl) {
                    showReloadNotice(boxEl);
                }
            }
        }, 100);
    });

    function buildButtonHtml(status, extraClasses) {

        extraClasses = extraClasses || '';
        if (status === 'inactive') {
            return '<button type="button" class="cfe-d4-promo__btn cfe-activate-plugin-btn ' + extraClasses + '" data-init="' + PLUGIN_INIT + '">' +
                'Activate Plugin' +
                '</button>';
        }else if(status === 'active'){
            return '<button type="button" class="cfe-d4-promo__btn cfe-reload-page-btn">Activated</button>'
        }
        return '<button type="button" class="cfe-d4-promo__btn cfe-install-plugin-btn ' + extraClasses + '" data-slug="' + PLUGIN_SLUG + '" data-init="' + PLUGIN_INIT + '">' +
            'Install &amp; Activate' +
            '</button>';
    }

    function postAjax(action, payload) {
        if (!AJAX_URL) {
            return Promise.reject(new Error('Missing AJAX URL'));
        }
        var data = Object.assign({}, payload || {}, { action: action });
        if (action === 'cfe_plugin_install') {
            data._ajax_nonce = INSTALL_NONCE;
        } else if (action === 'cfe_plugin_activate') {
            data.security = ACTIVATE_NONCE;
        } else if (action === 'cfe_dismiss_contact_form_notice') {
            data.nonce = DISMISS_NONCE;
        }

        var body = Object.keys(data)
            .map(function (k) {
                return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]);
            })
            .join('&');

        return fetch(AJAX_URL, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body
        }).then(function (res) {
            return res.json();
        });
    }

    function showReloadNotice(container) {
        if (!container) return;
        container.innerHTML =
            '<h4 class="cfe-d4-promo__title">Want better form management?</h4>' +
            '<p class="cfe-d4-promo__text">Save submissions, add file upload, and extend your Divi Form with Contact Form Extender for Divi.</p>' +
            '<button type="button" class="cfe-d4-promo__btn cfe-reload-page-btn">Activated</button>' +
            '<p class="cfe-d4-promo__reload-text">Save and reload the page to start using the features.</p>';
    }

    function showErrorInNotice(box, message) {
        if (!box) return;
        var errorMsg = message || 'An error occurred. Please try again.';
        var existing = box.querySelector('.cfe-promo-error');
        if (existing) existing.remove();
        var errorEl = document.createElement('p');
        errorEl.className = 'cfe-promo-error';
        errorEl.style.cssText = 'margin:10px 0;padding:10px;background:#fef2f2;border:1px solid #fecaca;border-radius:4px;color:#991b1b;font-size:13px;line-height:1.5;';
        errorEl.textContent = errorMsg;
        var btn = box.querySelector('.cfe-install-plugin-btn, .cfe-activate-plugin-btn');
        if (btn && btn.parentNode) {
            btn.parentNode.insertBefore(errorEl, btn);
        } else {
            box.appendChild(errorEl);
        }
    }

    function clearErrorInNotice(box) {
        if (!box) return;
        var existing = box.querySelector('.cfe-promo-error');
        if (existing) existing.remove();
    }

    function handleEditorCloseClick(event) {
        var target = event.target;
        if (!target || ( !target.classList.contains('cfe-editor-promo-close') && !target.classList.contains('cfe-d4-promo__close') )) {
            return;
        }
        var box = target.closest('.cfe-d4-promo') || target.closest('.cfe-promo-notice');
        if (!box) return;

        postAjax('cfe_dismiss_contact_form_notice', { context: 'editor' })
            .then(function (res) {
                if (res && res.success) {
                    EDITOR_DISMISSED = true;
                    box.style.display = 'none';
                    var $box = (typeof jQuery !== 'undefined') ? jQuery(box) : null;
                    if ($box && $box.length) {
                        var $section = $box.closest('.et-vb-modal-group, .et-fb-form__toggle, [data-name="cfe_marketing_promo"]');
                        if ($section && $section.length) {
                            $section.hide();
                        }
                    }
                }
            })
            .catch(function () {
                box.style.display = 'none';
            });
    }

    function handleButtonClick(event) {
        var target = event.target;
        if (!target) return;

        if (target.classList.contains('cfe-install-plugin-btn')) {
            var slug = target.getAttribute('data-slug') || PLUGIN_SLUG;
            if(slug !== 'contact-form-extender-for-divi-builder') return;
            var init = target.getAttribute('data-init') || PLUGIN_INIT;
            if (!slug || !init) return;

            var box = target.closest('.cfe-d4-promo') || target.closest('.cfe-promo-notice');
            var originalText = target.textContent;
            target.disabled = true;
            target.textContent = 'Installing...';
            clearErrorInNotice(box);

            postAjax('cfe_plugin_install', { slug: slug })
                .then(function (res) {
                    if (!res || !res.success) {
                        throw new Error(res && res.data && res.data.message ? res.data.message : 'Installation failed');
                    }
                    target.textContent = 'Activating...';
                    return postAjax('cfe_plugin_activate', { init: init });
                })
                .then(function (res) {
                    if (!res || !res.success) {
                        throw new Error(res && res.data && res.data.message ? res.data.message : 'Activation failed');
                    }
                    STATUS = 'active';
                    showReloadNotice(box);
                })
                .catch(function (err) {
                    var errMsg = err && err.message ? err.message : 'Installation/activation failed. Please try again from Plugins page.';
                    showErrorInNotice(box, errMsg);
                    target.disabled = false;
                    target.textContent = originalText;
                });
        } else if (target.classList.contains('cfe-activate-plugin-btn')) {
            var initFile = target.getAttribute('data-init') || PLUGIN_INIT;
            if (!initFile) return;

            var box = target.closest('.cfe-d4-promo') || target.closest('.cfe-promo-notice');
            var original = target.textContent;
            target.disabled = true;
            target.textContent = 'Activating...';
            clearErrorInNotice(box);

            postAjax('cfe_plugin_activate', { init: initFile })
                .then(function (res) {
                    if (!res || !res.success) {
                        throw new Error(res && res.data && res.data.message ? res.data.message : 'Activation failed');
                    }
                    STATUS = 'active';
                    showReloadNotice(box);
                })
                .catch(function (err) {
                    var errMsg = err && err.message ? err.message : 'Activation failed. Please try again from Plugins page.';
                    showErrorInNotice(box, errMsg);
                    target.disabled = false;
                    target.textContent = original;
                });
        } else if (target.classList.contains('cfe-reload-page-btn')) {
            if (typeof location !== 'undefined') {
                location.reload();
            }
        }
    }

    if (global.vendor && global.vendor.wp && global.vendor.wp.hooks) {
        // Divi 5 – if plugin already active or user dismissed, do not inject the marketing group at all.
        if (STATUS === 'active' || EDITOR_DISMISSED) {
            if (typeof document !== 'undefined') {
                document.addEventListener('click', handleButtonClick, false);
                document.addEventListener('click', handleEditorCloseClick, false);
            }
            return;
        }

        var PROMO_MSG = '<div class="cfe-promo-notice" style="position:relative;padding:16px 40px 16px 16px;background:#f8fafc;border:1px solid #1959ff;border-radius:6px;color:#475569;font-size:13px;line-height:1.6;">' +
            '<button type="button" class="cfe-editor-promo-close" style="position:absolute;top:8px;right:8px;width:28px;height:28px;padding:0;border:none;background:#e2e8f0;color:#64748b;border-radius:4px;cursor:pointer;font-size:20px;line-height:26px;text-align:center;" aria-label="Close">×</button>' +
            '<h4 style="margin:0 0 8px;color:#334155;font-size:14px;font-weight:600;">Want better form management?</h4>' +
            '<p style="margin:0 0 14px;color:#475569;font-size:13px;line-height:1.5;">Save submissions, add file upload, and extend your Divi Form with Contact Form Extender for Divi.</p>' +
            buildButtonHtml(STATUS, '') +
            '</div>';
        var GROUP = { panel: 'content', priority: 250, multiElements: true, groupName: 'cfeMarketingPromoGroup', component: { name: 'divi/composite', props: { groupLabel: 'Save submissions' } } };
        var ATTR = { type: 'string', default: '', settings: { innerContent: { groupType: 'group-item', item: { groupSlug: 'cfeMarketingPromoGroup', priority: 10, render: true, attrName: 'cfeMarketingPromoAttr', label: '', component: { type: 'field', name: 'divi/warning', props: { message: PROMO_MSG } } } } } };
        var addGroups = function (g, m) { return (m && m.name === 'divi/contact-form') ? Object.assign({}, g || {}, { cfeMarketingPromoGroup: GROUP }) : g; };
        var addAttrs = function (a) { return Object.assign({}, a || {}, { cfeMarketingPromoAttr: ATTR }); };
        var h = global.vendor.wp.hooks;

        if (h) {
            h.addFilter('divi.moduleLibrary.moduleSettings.groups.divi.contact-form', 'cfe_marketing', addGroups);
            h.addFilter('divi.moduleLibrary.moduleAttributes.divi.contact-form', 'cfe_marketing', addAttrs);
        }
    } else if (typeof global.jQuery !== 'undefined') {
        var $ = global.jQuery;

        var PROMO_HTML = '<div class="cfe-d4-promo">' +
            '<button type="button" class="cfe-d4-promo__close cfe-editor-promo-close" aria-label="Close">×</button>' +
            '<h4 class="cfe-d4-promo__title">Want better form management?</h4>' +
            '<p class="cfe-d4-promo__text">Save submissions, add file upload, and extend your Divi Form with Contact Form Extender for Divi.</p>' +
            buildButtonHtml(STATUS, '') +
            '</div>';

        var injectPromo = function () {
            if (EDITOR_DISMISSED) return false;
            var $container = $('[data-name="cfe_marketing_promo"]');
            if (!$container.length || $container.find('.cfe-d4-promo').length) return false;
            $container.find('input[name="cfe_marketing_promo_field"]').closest('.et-fb-settings-options').remove();
            var $content = $container.find('.et-fb-form__group').first();
            return $content.length ? ($content.prepend(PROMO_HTML), true) : false;
        };

        $(document).on('click', '[data-name="cfe_marketing_promo"] .et-fb-form__toggle-title', function () {
            requestAnimationFrame(injectPromo);
        });
        $(function () { injectPromo(); });
    }

    if (typeof document !== 'undefined') {
        document.addEventListener('click', handleButtonClick, false);
        document.addEventListener('click', handleEditorCloseClick, false);
    }

})(typeof window !== 'undefined' ? window : this);
