import StickySidebar from 'sticky-sidebar-v2/src/sticky-sidebar';

STUDIP.ready(() => {
    const sidebar = new StickySidebar('#sidebar', {
        topSpacing: 50,
        bottomSpacing: 15,
        containerSelector: '#page-content-wrapper',
        innerWrapperSelector: '#sidebar-inner'
    });
});
