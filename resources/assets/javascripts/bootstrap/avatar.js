STUDIP.domReady(() => {
    const avatarTypes = ['courses', 'institutes', 'studygroups', 'users'];

    avatarTypes.forEach((type) => {
        if (document.getElementById(`avatar-${type}-app`)) {
            Promise.all([
                STUDIP.loadChunk('avatar'),
                import(
                    /* webpackChunkName: "avatar-app" */
                    '@/vue/avatar-app.js'
                ),
            ]).then(([{ createApp }, { default: mountApp }]) => {
                return mountApp(STUDIP, createApp, `#avatar-${type}-app`);
            });
        }
    });
});
