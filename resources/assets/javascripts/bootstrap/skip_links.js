STUDIP.domReady(STUDIP.SkipLinks.initialize);

jQuery(document).on('keyup', STUDIP.SkipLinks.showSkipLinkNavigation);
jQuery(document).on('click', '#skip_link_navigation button', STUDIP.SkipLinks.moveSkipLinkNavigationOut)

jQuery(document).on('focusin', '#skip_link_navigation .hidden', STUDIP.SkipLinks.moveSkipLinkNavigationIn);
