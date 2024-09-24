import { $gettext } from '../lib/gettext';

$(document).on('click', 'a.copyable-link', function (event) {
    event.preventDefault();

    // Create dummy element and position it off screen
    // This element must be "visible" (as in "not hidden") or otherwise
    // the copy command will fail
    let dummy = $('<textarea>').val(this.href).css({
        position: 'absolute',
        left: '-9999px'
    }).appendTo('body');

    // Select text and copy it to clipboard
    dummy[0].select();
    document.execCommand('Copy');
    dummy.remove();

    STUDIP.eventBus.emit(
        'push-system-notification',
        { type: 'success', message: $gettext('Link wurde kopiert') }
    );
});
