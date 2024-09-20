STUDIP.domReady(() => {
    const avatarTypes = ['courses', 'institutes', 'studygroups', 'users'];

    avatarTypes.forEach((type) => {
        if (document.getElementById(`avatar-${type}-app`)) {
            Promise.all([
                STUDIP.loadChunk('vue'),
                import(
                    /* webpackChunkName: "avatar-app" */
                    '@/vue/avatar-app.js'
                ),
            ]).then(([{ createApp, store }, { default: mountApp }]) => {
                return mountApp(STUDIP, createApp, store, `#avatar-${type}-app`);
            });
        }
    });
});
