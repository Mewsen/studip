 import { setLocale } from './lib/gettext';

import 'multiselect';
import './studip-jquery-tweaks.js';
import './studip-jquery.multi-select.tweaks.js';
import './studip-jquery-selection-helper.js';

import 'blueimp-file-upload';
import 'blueimp-file-upload/js/jquery.iframe-transport.js';

import './jquery/autoresize.jquery.min.js';

// Create jQuery "plugin" that just reverses the elements' order. This is
// neccessary since the navigation is built and afterwards, we need to
// check the navigation's open status in reverse order (from bottom to top)
jQuery.fn.reverse = [].reverse;

$.fn.extend({
    showAjaxNotification(position) {
        position = position || 'left';
        return this.each(function () {
            if ($(this).data('ajax_notification')) {
                return;
            }

            $(this).wrap('<span class="ajax_notification" />');
            const thisHeight = $(this).height();
            const thisPosition = $(this).position();
            const notification = $('<span class="notification" />')
                .hide()
                .insertBefore(this);
            const changes = {
                marginLeft: 0,
                marginRight: 0
            };

            changes[position === 'right' ? 'marginRight' : 'marginLeft'] = notification.outerWidth(true);

            $(this)
                .data({
                    ajax_notification: notification
                })
                .parent()
                .animate(changes, 'fast', function () {
                    const offset = thisPosition;
                    const styles = {
                        left: offset.left - notification.outerWidth(true),
                        top:
                            offset.top +
                            Math.max(0, Math.floor((thisHeight - notification.outerHeight(true)) / 2))
                    };
                    if (position === 'right') {
                        styles.left += $(this).outerWidth(true);
                    }
                    notification.css(styles).fadeIn('fast');
                });
        });
    },
    hideAjaxNotification() {
        return this.each(function () {
            var $this = $(this).stop(),
                notification = $this.data('ajax_notification');
            if (!notification) {
                return;
            }

            notification.stop().fadeOut('fast', function () {
                $this.animate({marginLeft: 0, marginRight: 0}, 'fast', function () {
                    $this.unwrap();
                });
                $(this).remove();
            });
            $(this).removeData('ajax_notification');
        });
    }
});

$(document).ready(async () => {
    await setLocale();
    STUDIP.ready.trigger('dom');
}).on('dialog-update', (event, data) => {
    STUDIP.ready.trigger('dialog', data.dialog);
});
