const SkipLinks = {
    navigationStatus: 0,

    /**
     * Displays the skip link navigation after first hitting the tab-key
     * @param event: event-object of type keyup
     */
    showSkipLinkNavigation: function(event) {
        if (event.keyCode === 9) {
            //tab-key
            SkipLinks.moveSkipLinkNavigationIn();
        }
    },

    /**
     * shows the skiplink-navigation window by moving it from the left
     */
    moveSkipLinkNavigationIn: function() {
        //Show the skip link navigation only if it hasn't been shown before or if it
        //is focused after it has been shown.
        if (SkipLinks.navigationStatus === 0 ||
            SkipLinks.navigationStatus === 2 && jQuery('#skip_link_navigation:focus').length) {
            //Make the menu itself unfocusable:
            jQuery('#skip_link_navigation').attr('tabindex', '-1');
            //Make the skip link items focusable:
            jQuery('#skip_link_navigation li button').attr('tabindex', '0');
            jQuery('#skip_link_navigation li:first button').focus();
            jQuery('#skip_link_navigation').removeClass('inactive');
            jQuery('#skip_link_navigation').addClass('active');
            SkipLinks.navigationStatus = 1;
        }
    },

    /**
     * removes the skiplink-navigation window by moving it out of viewport
     */
    moveSkipLinkNavigationOut: function() {
        if (SkipLinks.navigationStatus === 1) {
            //Make the skip link items unfocusable:
            jQuery('#skip_link_navigation li button').attr('tabindex', '-1');
            jQuery('#skip_link_navigation').removeClass('active');
            jQuery('#skip_link_navigation').addClass('inactive');
            //Make the menu focusable:
            jQuery('#skip_link_navigation').attr('tabindex', '0');
        }
        SkipLinks.navigationStatus = 2;
    },

    /**
     * Inserts the list with skip links
     */
    insertSkipLinks: function() {
        jQuery('#skip_link_navigation').prepend(jQuery('#skiplink_list'));
        jQuery('#skiplink_list').show();
        jQuery('#skip_link_navigation').attr('aria-busy', 'false');
        jQuery('#skip_link_navigation').attr('tabindex', '-1');
        SkipLinks.insertHeadLines();
        return false;
    },

    /**
     * sets the area (of the id) as the current area for tab-navigation
     * and highlights it
     */
    setActiveTarget (id) {
        let fragment = '';
        // set active area only if skip links are activated
        if (!document.getElementById('skip_link_navigation')) {
            return false;
        }
        if (id) {
            fragment = id;
        } else {
            fragment = document.location.hash;
        }

        if (fragment.length > 0 && jQuery(fragment).length > 0) {
            SkipLinks.moveSkipLinkNavigationOut();
            if (jQuery(fragment).is(':focusable')) {
                jQuery(fragment).click().focus();
            } else {
                //Set the focus on the first focusable element:
                jQuery(fragment).find(':focusable').eq(0).focus();
            }
            return true;
        } else {
            jQuery('#skip_link_navigation li button').first().focus();
        }
        return false;
    },

    insertHeadLines: function() {
        let target = null;
        jQuery('#skip_link_navigation a').each(function() {
            target = jQuery(this);
            if (jQuery(target).is('li,td')) {
                jQuery(target).prepend(
                    '<h2 id="' +
                        jQuery(target).attr('id') +
                        '_landmark_label" class="skip_target">' +
                        jQuery(this).text() +
                        '</h2>'
                );
            } else {
                jQuery(target).before(
                    '<h2 id="' +
                        jQuery(target).attr('id') +
                        '_landmark_label" class="skip_target">' +
                        jQuery(this).text() +
                        '</h2>'
                );
            }
            jQuery(target).attr('aria-labelledby', jQuery(target).attr('id') + '_landmark_label');
        });
    },

    initialize: function() {
        SkipLinks.insertSkipLinks();
        SkipLinks.setActiveTarget();
    }
};

export default SkipLinks;
