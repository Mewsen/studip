import eventBus from '../lib/event-bus.ts';

STUDIP.ready(() => {
    if (!document.documentElement.classList.contains('responsive-display')) {
        // Manually nudge sidebar under main header.
        STUDIP.Sidebar.place();
        STUDIP.Sidebar.observeBody();
        STUDIP.Sidebar.observeFooter();
        STUDIP.Sidebar.observeSidebar();

        document.defaultView.addEventListener('resize', () => {
            STUDIP.Sidebar.reset();
        });
    }
});
