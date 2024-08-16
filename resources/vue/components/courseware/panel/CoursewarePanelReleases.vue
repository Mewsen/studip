<template>
    <div class="cw-panel-releases">
        <h2>{{ $gettext('Freigaben') }}</h2>
        <a class="button" :href="releasesLink">{{ $gettext('Zu den Freigaben') }}</a>
    </div>
</template>
<script>
export default {
    name: 'CoursewarePanelReleases',
    computed: {
        releasesLink() {
            return STUDIP.URLHelper.getURL('dispatch.php/contents/courseware/releases/');
        },
    },
};
</script>
