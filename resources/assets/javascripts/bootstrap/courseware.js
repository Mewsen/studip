STUDIP.domReady(() => {
    if (document.getElementById('courseware-public-app')) {
        Promise.all([
            STUDIP.loadChunk('courseware'),
            import(
                /* webpackChunkName: "courseware-public-app" */
                '@/vue/courseware-public-app.js'
            ),
        ]).then(([{ createApp, store }, { default: mountApp }]) => {
            return mountApp(STUDIP, createApp, store, '#courseware-public-app');
        });
    }

    if (document.getElementById('courseware-content-releases-app')) {
        Promise.all([
            STUDIP.loadChunk('courseware'),
            import(
                /* webpackChunkName: "courseware-content-releases-app" */
                '@/vue/courseware-content-releases-app.js'
            ),
        ]).then(([{ createApp, store }, { default: mountApp }]) => {
            return mountApp(STUDIP, createApp, store, '#courseware-content-releases-app');
        });
    }

    if (document.getElementById('courseware-comments-app')) {
        Promise.all([
            STUDIP.loadChunk('courseware'),
            import(
                /* webpackChunkName: "courseware-comments-app" */
                '@/vue/courseware-comments-app.js'
            ),
        ]).then(([{ createApp, store }, { default: mountApp }]) => {
            return mountApp(STUDIP, createApp, store, '#courseware-comments-app');
        });
    }

    if (document.getElementById('contents-courseware-courses_overview')) {
        Promise.all([
            STUDIP.loadChunk('courseware')
        ]);
    }
});
